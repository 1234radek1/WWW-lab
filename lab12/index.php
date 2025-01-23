<?php
session_start();
include(__DIR__ . '/php/cfg.php'); // Poprawna ścieżka do cfg.php

// Pobranie parametru alias z URL lub ustawienie domyślnej wartości 'Glowna'
$alias = isset($_GET['alias']) ? $_GET['alias'] : 'Glowna';

// Obsługa dostępu do panelu administracyjnego
if ($alias === 'Admin') {
    include(__DIR__ . '/admin/admin.php'); // Ładowanie panelu admina
    exit; // Zatrzymaj przetwarzanie strony głównej
}

// POBRANIE TREŚCI STRONY Z BAZY DANYCH
try {
    $stmt = $conn->prepare("SELECT page_content FROM page_list WHERE alias = ?");
    $stmt->bind_param("s", $alias);
    $stmt->execute();
    $result = $stmt->get_result();

    $page_content = "<p>Strona nie istnieje.</p>"; // Domyślna treść
    if ($row = $result->fetch_assoc()) {
        $page_content = $row['page_content'];
    }
} catch (Exception $e) {
    $page_content = "<p class='error'>Błąd ładowania treści: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dynamiczna Strona</title>
    <link rel="stylesheet" type="text/css" href="css/cw1.css">
</head>
<body>

    <!-- GŁÓWNE MENU NAWIGACYJNE -->
    <section id="menu">
        <nav>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="php/podstrony.php">Wszystkie podstrony</a></li> <!-- Przekierowanie do podstrony z listą -->
                <li><a href="admin/admin.php">Panel Admina</a></li>
                <li><a href="php/contact.php">Kontakt</a></li>
                <li><a href="php/sklep.php">Sklep</a></li>
            </ul>
        </nav>
    </section>

    <!-- GŁÓWNA ZAWARTOŚĆ STRONY -->
    <main>
        <?php echo $page_content; // Wyświetlenie dynamicznej treści ?>
    </main>
</body>
</html>