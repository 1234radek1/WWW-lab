<?php
session_start();
include(__DIR__ . '/cfg.php'); // Poprawna ścieżka do cfg.php

// Pobierz wszystkie podstrony z bazy danych
try {
    $query = "SELECT page_title, alias FROM page_list ORDER BY page_title ASC";
    $result = $conn->query($query);
} catch (Exception $e) {
    die("Błąd podczas pobierania podstron: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wszystkie podstrony</title>
    <link rel="stylesheet" type="text/css" href="../css/cw1.css">
</head>
<body>

    <!-- GŁÓWNE MENU NAWIGACYJNE -->
    <section id="menu">
        <nav>
            <ul>
                <li><a href="../index.php">Strona Główna</a></li>
                <li><a href="podstrony.php">Wszystkie podstrony</a></li> <!-- Tylko link do listy podstron -->
                <li><a href="../admin/admin.php">Panel Admina</a></li>
                <li><a href="contact.php">Kontakt</a></li>
                <li><a href="sklep.php">Sklep</a></li>
            </ul>
        </nav>
    </section>

    <!-- GŁÓWNA ZAWARTOŚĆ STRONY -->
    <main>
        <h1>Wszystkie podstrony</h1>
        <?php if ($result->num_rows > 0): ?>
            <ul class="podstrony-lista">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <a href="../index.php?alias=<?= htmlspecialchars($row['alias']) ?>">
                            <?= htmlspecialchars($row['page_title']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Brak podstron do wyświetlenia.</p>
        <?php endif; ?>
    </main>
</body>
</html>