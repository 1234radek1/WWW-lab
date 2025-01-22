<?php
session_start();
include('../php/cfg.php');

// Funkcje pomocnicze ---------------------------------------------------------

// Generuje formularz logowania do panelu administracyjnego
function FormularzLogowania() {
    return '
    <div class="login-container">
        <a href="../index.php" class="btn-back">← Strona główna</a>
        <h1>Panel CMS</h1>
        <form method="post">
            <div class="form-group">
                <label for="login_email">Login:</label>
                <input type="text" id="login_email" name="login_email" required>
            </div>
            <div class="form-group">
                <label for="login_pass">Hasło:</label>
                <input type="password" id="login_pass" name="login_pass" required>
            </div>
            <button type="submit" name="login_submit" class="btn btn-primary">Zaloguj</button>
        </form>
    </div>';
}

// Tworzy przycisk i formularz do wylogowania
function Wylogowanie() {
    return '
    <div class="logout-container">
        <form method="post">
            <a href="../index.php" class="btn-back">← Strona główna</a>
            <button type="submit" name="logout_submit" class="btn btn-danger">Wyloguj</button>
        </form>
    </div>';
}

// Generuje formularz do edycji/dodawania podstron z możliwością zapisu stanu
function FormularzPodstrony($row = null) {
    $title = $row['page_title'] ?? '';
    $content = $row['page_content'] ?? '';
    $alias = $row['alias'] ?? '';
    $checked = isset($row['status']) && $row['status'] ? 'checked' : '';
    $action = $row ? 'save_changes' : 'add_new';
    $button = $row ? 'Zapisz zmiany' : 'Dodaj podstronę';
    
    return '
    <div class="form-container">
        <form method="post">
            <div class="form-group">
                <label for="page_title">Tytuł podstrony:</label>
                <input type="text" id="page_title" name="page_title" value="'.htmlspecialchars($title).'" required>
            </div>
            <div class="form-group">
                <label for="alias">Alias podstrony:</label>
                <input type="text" id="alias" name="alias" value="'.htmlspecialchars($alias).'" required>
            </div>
            <div class="form-group">
                <label for="page_content">Treść podstrony:</label>
                <textarea id="page_content" name="page_content" rows="5" required>'.htmlspecialchars($content).'</textarea>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="status" name="status" '.$checked.'>
                <label for="status">Status</label>
            </div>
            '.(isset($row['id']) ? '<input type="hidden" name="edit_id" value="'.$row['id'].'">' : '').'
            <button type="submit" name="'.$action.'" class="btn btn-primary">'.$button.'</button>
        </form>
    </div>';
}

// Wyświetla listę wszystkich podstron w formie tabeli z przyciskami akcji
function ListaPodstron($conn) {
    $query = "SELECT id, page_title, alias FROM page_list ORDER BY id ASC";
    $result = $conn->query($query);

    echo '<div class="table-container">';
    if ($result->num_rows > 0) {
        echo '<table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tytuł</th>
                        <th>Alias</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.htmlspecialchars($row['page_title']).'</td>
                    <td>'.htmlspecialchars($row['alias']).'</td>
                    <td>
                        <form method="post" style="display:inline;" onsubmit="return confirmDelete();">
                            <input type="hidden" name="delete_id" value="'.$row['id'].'">
                            <button type="submit" name="delete_submit" class="btn btn-danger small-btn">Usuń</button>
                        </form>
                        <form method="get" style="display:inline;">
                            <input type="hidden" name="edit_id" value="'.$row['id'].'">
                            <button type="submit" class="btn btn-warning small-btn">Edytuj</button>
                        </form>
                    </td>
                </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p class="no-data">Brak podstron do wyświetlenia.</p>';
    }
    echo '</div>';
}

// Generuje formularz do dodawania kategorii
function FormularzDodajKategorie($conn) {
    return '
    <div class="form-container">
        <form method="post">
            <div class="form-group">
                <label for="nazwa_kategorii">Nazwa kategorii:</label>
                <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" required>
            </div>
            <div class="form-group">
                <label for="matka_kategorii">Kategoria nadrzędna:</label>
                <select id="matka_kategorii" name="matka_kategorii">
                    <option value="0">Brak (kategoria główna)</option>
                    '.GenerujOpcjeKategorii($conn).'
                </select>
            </div>
            <button type="submit" name="dodaj_kategorie" class="btn btn-primary">Dodaj kategorię</button>
        </form>
    </div>';
}

// Generuje opcje kategorii nadrzędnych dla selecta, z wykluczeniem aktualnie edytowanej kategorii
function GenerujOpcjeKategorii($conn, $selected = 0, $excludeId = null) {
    $query = "SELECT id, nazwa FROM categories ORDER BY nazwa ASC";
    $result = $conn->query($query);
    $options = '';
    while ($row = $result->fetch_assoc()) {
        // Wyklucz aktualnie edytowaną kategorię z listy możliwych kategorii nadrzędnych
        if ($excludeId !== null && $row['id'] == $excludeId) {
            continue;
        }
        $selectedAttr = ($row['id'] == $selected) ? 'selected' : '';
        $options .= '<option value="'.$row['id'].'" '.$selectedAttr.'>'.htmlspecialchars($row['nazwa']).'</option>';
    }
    return $options;
}

// Dodaje nową kategorię
function DodajKategorie($conn, $nazwa, $matka = 0) {
    $nazwa = $conn->real_escape_string($nazwa);
    $matka = intval($matka);
    $query = "INSERT INTO categories (nazwa, matka) VALUES ('$nazwa', $matka)";
    if ($conn->query($query)) {
        return true;
    } else {
        error_log("Błąd SQL: " . $conn->error);
        return false;
    }
}

// Funkcja rekurencyjna do usuwania kategorii i ich podkategorii
function UsunKategorieRekurencyjnie($conn, $id) {
    $id = intval($id);

    // Najpierw usuń wszystkie podkategorie
    $query = "SELECT id FROM categories WHERE matka = $id";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        UsunKategorieRekurencyjnie($conn, $row['id']); // Rekurencyjne usuwanie podkategorii
    }

    // Następnie usuń samą kategorię
    $query = "DELETE FROM categories WHERE id = $id";
    if ($conn->query($query)) {
        return true;
    } else {
        error_log("Błąd SQL: " . $conn->error);
        return false;
    }
}

// Usuwa kategorię i jej podkategorie
function UsunKategorie($conn, $id) {
    return UsunKategorieRekurencyjnie($conn, $id);
}

// Edytuje istniejącą kategorię
function EdytujKategorie($conn, $id, $nazwa, $matka = 0) {
    $id = intval($id);
    $nazwa = $conn->real_escape_string($nazwa);
    $matka = intval($matka);
    $query = "UPDATE categories SET nazwa = '$nazwa', matka = $matka WHERE id = $id";
    if ($conn->query($query)) {
        return true;
    } else {
        error_log("Błąd SQL: " . $conn->error);
        return false;
    }
}

// Wyświetla formularz do edycji kategorii
function FormularzEdytujKategorie($conn, $id) {
    $id = intval($id);
    $query = "SELECT * FROM categories WHERE id = $id";
    $result = $conn->query($query);
    if ($row = $result->fetch_assoc()) {
        return '
        <div class="form-container">
            <form method="post">
                <div class="form-group">
                    <label for="nazwa_kategorii">Nazwa kategorii:</label>
                    <input type="text" id="nazwa_kategorii" name="nazwa_kategorii" value="'.htmlspecialchars($row['nazwa']).'" required>
                </div>
                <div class="form-group">
                    <label for="matka_kategorii">Kategoria nadrzędna:</label>
                    <select id="matka_kategorii" name="matka_kategorii">
                        <option value="0">Brak (kategoria główna)</option>
                        '.GenerujOpcjeKategorii($conn, $row['matka'], $id).' <!-- Dodano $id jako excludeId -->
                    </select>
                </div>
                <input type="hidden" name="edit_id" value="'.$row['id'].'">
                <button type="submit" name="edytuj_kategorie" class="btn btn-primary">Zapisz zmiany</button>
            </form>
        </div>';
    } else {
        return '<p class="error-message">Nie znaleziono kategorii.</p>';
    }
}

// Funkcja rekurencyjna do wyświetlania kategorii
function PokazKategorieRekurencyjnie($kategorie, $matka = 0) {
    if (isset($kategorie[$matka])) {
        echo '<ul>';
        foreach ($kategorie[$matka] as $kategoria) {
            echo '<li>' . htmlspecialchars($kategoria['nazwa']);
            echo '<div class="kategorie-actions">';
            echo '<form method="get" style="display:inline;">
                    <input type="hidden" name="edit_kategoria_id" value="'.$kategoria['id'].'">
                    <input type="hidden" name="panel" value="categories">
                    <button type="submit" class="btn btn-warning small-btn">Edytuj</button>
                  </form>';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Czy na pewno chcesz usunąć tę kategorię?\');">
                    <input type="hidden" name="delete_id" value="'.$kategoria['id'].'">
                    <button type="submit" name="delete_kategorie" class="btn btn-danger small-btn">Usuń</button>
                  </form>';
            echo '</div>';
            // Rekurencyjne wywołanie dla podkategorii
            PokazKategorieRekurencyjnie($kategorie, $kategoria['id']);
            echo '</li>';
        }
        echo '</ul>';
    }
}

// Wyświetla listę wszystkich kategorii w formie drzewa
function PokazKategorie($conn) {
    $query = "SELECT id, nazwa, matka FROM categories ORDER BY matka ASC, id ASC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $kategorie = [];
        while ($row = $result->fetch_assoc()) {
            $kategorie[$row['matka']][] = $row;
        }

        echo '<div class="kategorie-container">';
        PokazKategorieRekurencyjnie($kategorie); // Rozpocznij od kategorii głównych (matka = 0)
        echo '</div>';
    } else {
        echo '<p class="no-data">Brak kategorii do wyświetlenia.</p>';
    }
}

// Obsługa akcji formularzy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logowanie użytkownika
    if (isset($_POST['login_submit'])) {
        if ($_POST['login_email'] === $login && $_POST['login_pass'] === $pass) {
            $_SESSION['logged_in'] = true;
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = '<p class="error-message">Nieprawidłowy login lub hasło.</p>';
        }
    }

    // Wylogowywanie i czyszczenie sesji
    if (isset($_POST['logout_submit'])) {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        session_destroy();
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }

    // Dodawanie nowej lub aktualizacja istniejącej podstrony
    if (isset($_POST['add_new']) || isset($_POST['save_changes'])) {
        $title = $conn->real_escape_string($_POST['page_title']);
        $content = $conn->real_escape_string($_POST['page_content']);
        $alias = $conn->real_escape_string($_POST['alias']);
        $status = isset($_POST['status']) ? 1 : 0;

        if (isset($_POST['save_changes'])) {
            $id = intval($_POST['edit_id']);
            $query = "UPDATE page_list SET page_title = '$title', page_content = '$content', alias = '$alias', status = $status WHERE id = $id";
        } else {
            $query = "INSERT INTO page_list (page_title, page_content, alias, status) VALUES ('$title', '$content', '$alias', $status)";
        }

        if ($conn->query($query)) {
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        } else {
            echo '<p class="error-message">Błąd: '.$conn->error.'</p>';
        }
    }

    // Usuwanie wybranej podstrony
    if (isset($_POST['delete_submit'])) {
        $id = intval($_POST['delete_id']);
        $query = "DELETE FROM page_list WHERE id = $id";
        
        if ($conn->query($query)) {
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        } else {
            echo '<p class="error-message">Błąd: '.$conn->error.'</p>';
        }
    }

    // Dodawanie nowej kategorii
    if (isset($_POST['dodaj_kategorie'])) {
        $nazwa = $conn->real_escape_string($_POST['nazwa_kategorii']);
        $matka = intval($_POST['matka_kategorii']);
        if (DodajKategorie($conn, $nazwa, $matka)) {
            header('Location: '.$_SERVER['PHP_SELF'].'?panel=categories');
            exit;
        } else {
            echo '<p class="error-message">Błąd podczas dodawania kategorii.</p>';
        }
    }

    // Edycja istniejącej kategorii
    if (isset($_POST['edytuj_kategorie'])) {
        $id = intval($_POST['edit_id']);
        $nazwa = $conn->real_escape_string($_POST['nazwa_kategorii']);
        $matka = intval($_POST['matka_kategorii']);
        if (EdytujKategorie($conn, $id, $nazwa, $matka)) {
            header('Location: '.$_SERVER['PHP_SELF'].'?panel=categories');
            exit;
        } else {
            echo '<p class="error-message">Błąd podczas edycji kategorii.</p>';
        }
    }

    // Usuwanie kategorii
    if (isset($_POST['delete_kategorie'])) {
        $id = intval($_POST['delete_id']);
        if (UsunKategorie($conn, $id)) {
            header('Location: '.$_SERVER['PHP_SELF'].'?panel=categories');
            exit;
        } else {
            echo '<p class="error-message">Błąd podczas usuwania kategorii.</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel CMS</title>
    <link rel="stylesheet" href="../css/cw1.css">
    <script>
        // Funkcja do potwierdzenia usunięcia strony
        function confirmDelete() {
            return confirm("Czy na pewno chcesz usunąć tę stronę?");
        }

        // Funkcja do przełączania paneli
        function switchPanel(panel) {
            window.location.href = '?panel=' + panel;
        }
    </script>
</head>
<body>
    <div class="admin-wrapper">
        <?php
        // Wyświetlanie odpowiedniego interfejsu w zależności od statusu logowania
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            // Widok logowania
            if (isset($error)) echo $error;
            echo FormularzLogowania();
        } else {
            // Widok zarządzania
            echo Wylogowanie();

            // Przełącznik paneli
            echo '
            <div class="panel-switcher">
                <button onclick="switchPanel(\'pages\')" class="'.((!isset($_GET['panel']) || $_GET['panel'] === 'pages') ? 'active' : '').'">Zarządzaj podstronami</button>
                <button onclick="switchPanel(\'categories\')" class="'.((isset($_GET['panel']) && $_GET['panel'] === 'categories') ? 'active' : '').'">Zarządzaj kategoriami</button>
            </div>';

            // Sprawdzamy, który panel ma być wyświetlony
            $currentPanel = isset($_GET['panel']) ? $_GET['panel'] : 'pages';

            if ($currentPanel === 'pages') {
                // Panel zarządzania podstronami
                if (isset($_GET['edit_id'])) {
                    $id = intval($_GET['edit_id']);
                    $result = $conn->query("SELECT * FROM page_list WHERE id = $id");
                    if ($row = $result->fetch_assoc()) {
                        echo FormularzPodstrony($row);
                    } else {
                        echo '<p class="error-message">Nie znaleziono podstrony.</p>';
                    }
                } else {
                    ListaPodstron($conn);
                    echo FormularzPodstrony();
                }
            } elseif ($currentPanel === 'categories') {
                // Panel zarządzania kategoriami
                echo '<h2>Zarządzanie kategoriami</h2>';
                if (isset($_GET['edit_kategoria_id'])) {
                    echo FormularzEdytujKategorie($conn, $_GET['edit_kategoria_id']);
                } else {
                    echo FormularzDodajKategorie($conn);
                }
                echo PokazKategorie($conn);
            }
        }
        ?>
    </div>
</body>
</html>