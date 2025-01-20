<?php
// Połączenie z bazą danych

$conn = new mysqli('localhost', 'root', '', 'moja_strona');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pobranie aliasu z URL
$alias = isset($_GET['alias']) ? $_GET['alias'] : 'Glowna';

// Zapytanie do bazy danych
$stmt = $conn->prepare("SELECT page_content FROM page_list WHERE alias = ?");
$stmt->bind_param("s", $alias);
$stmt->execute();
$result = $stmt->get_result();

// Pobranie i wyświetlenie zawartości strony
if ($row = $result->fetch_assoc()) {
    $page_content = $row['page_content'];
} else {
    $page_content = "<p>Strona nie istnieje.</p>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dynamiczna Strona</title>
	   <link rel="stylesheet" type="text/css" href="css\cw1.css">
	   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" 
	   integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" 
	   crossorigin="anonymous"
	   referrerpolicy="no-referrer">
</script>
</head>
<body onload="startclock()">

	<section id="menu">
<nav>
<ul>
			   <li><a href="index.php">Strona Główna</a></li>
                <li><a href="index.php?alias=Ciekawostki">Ciekawostki</a></li>
                <li><a href="index.php?alias=JakBudowac">Jak Budować</a></li>
                <li><a href="index.php?alias=RodzajeMostow">Rodzaje mostów</a></li>
                <li><a href="index.php?alias=ZCzego">Z czego ?</a></li>
                <li><a href="index.php?alias=Filmy">Filmy</a></li>
</ul>
</nav>
</section>

    <!-- Główna treść -->
    <main>
        <?php
        echo $page_content; // Wyświetl dynamiczną zawartość strony
        ?>
    </main>
</html>
