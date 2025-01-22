<?php

         // GŁÓWNY PLIK STRONY - INDEX.PHP
         // Obsługuje dynamiczne ładowanie treści strony na podstawie parametru alias
         // oraz przekierowanie do panelu administracyjnego.


session_start();
include('./php/cfg.php'); // Ładowanie konfiguracji bazy danych

// Pobranie parametru alias z URL lub ustawienie domyślnej wartości 'Glowna'
$alias = isset($_GET['alias']) ? $_GET['alias'] : 'Glowna';

// Obsługa dostępu do panelu administracyjnego
if ($alias === 'Admin') {
    include('./admin/admin.php'); // Ładowanie panelu admina
    exit; // Zatrzymaj przetwarzanie strony głównej
}


// POBRANIE TREŚCI STRONY Z BAZY DANYCH
// 1. Przygotowanie zapytania z użyciem prepared statement
// 2. Zabezpieczenie przed SQL injection przez bind_param
// 3. Obsługa błędów zapytania

try {
    $stmt = $conn->prepare("SELECT page_content FROM page_list WHERE alias = ?");
    $stmt->bind_param("s", $alias);
    $stmt->execute();
    $result = $stmt->get_result();

    $page_content = "<p>Strona nie istnieje.</p>"; // Domyślna treść
    if ($row = $result->fetch_assoc())
	{
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
                <li><a href="index.php?alias=Ciekawostki">Ciekawostki</a></li>
                <li><a href="index.php?alias=JakBudowac">Jak Budować</a></li>
                <li><a href="index.php?alias=RodzajeMostow">Rodzaje mostów</a></li>
                <li><a href="index.php?alias=ZCzego">Z czego?</a></li>
                <li><a href="index.php?alias=Filmy">Filmy</a></li>
                <li><a href="admin/admin.php">Panel Admina</a></li>
                <li><a href="php/contact.php">Kontakt</a></li>
            </ul>
        </nav>
    </section>

    <!-- GŁÓWNA ZAWARTOŚĆ STRONY -->
    <main>
        <?php echo $page_content; // Wyświetlenie dynamicznej treści ?>
    </main>
</body>
</html>