<?php
include('lab_164413_isi2.php');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
$strona = 'html/glowna.html';

if ($_GET['idp'] == 'Ciekawostki')
{
    $strona = 'html/pods1.html';
} 
elseif ($_GET['idp'] == 'Jak budowac') 
{
    $strona = 'html/pods2.html';
} 
elseif ($_GET['idp'] == 'Rodzaje mostow') 
{
    $strona = 'html/pods3.html';
}
elseif ($_GET['idp'] == 'Z czego?')
 {
    $strona = 'html/pods4.html';
}


?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="pl">
    <meta name="Author" content="Radoslaw Mydlo">
    <title>Największe mosty świata</title>
    <link rel="stylesheet" type="text/css" href="css\cw1.css">
    <script src="js/kolorujtlo.js"></script>
	<script src="js/timedate.js"></script>

	
</head>
<body onload="startclock()">

<section id="menu">
<nav>
<ul>
<li><a href="index.html">Strona główna</a></li>
<li><a href="html/pods1.html">Ciekawostki</a></li>
<li><a href="html/pods2.html">Jak budowac</a></li>
<li><a href="html/pods3.html">Rodzaje mostow</a></li>
<li><a href="html/pods4.html">Z czego?</a></li>

</ul>
</nav>
</section>

   <section id="kontakt">
<h1> Kontakt </h1>
    <form action="mailto:someone@example.com" method="post" enctype="text/plain">
        <label for="name">Imię:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Adres e-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Wiadomość:</label>
        <textarea id="message" name="message" rows="1" required></textarea>

        <input type="submit" value="Wyślij">
    </form>
</section>



		<center><h2>Przykladowe najwieksze mosty</h2></center>

<div id="zdj">

            <center><img src="img/grudzionc.webp" alt="Zdjęcie mostu"></center>

            <center><img src="img/baling.jpg" alt="Zdjęcie mostu"></center>
 
            <center><img src="img/beipan.jpg" alt="Zdjęcie mostu"></center>

            <center><img src="img/duge.jpg" alt="Zdjęcie mostu"></center>

            <center><img src="img/jinan.jpg" alt="Zdjęcie mostu"></center>

            <center><img src="img/liug.jpg" alt="Zdjęcie mostu"></center>

            <center><img src="img/puli.jpg" alt="Zdjęcie mostu"></center>

            <center><img src="img/qingshui.jpg" alt="Zdjęcie mostu"></center>

            <center><img src="img/redzinski.webp"Zdjęcie mostu"></center>

            <center><img src="img/sidu.jpg" alt="Zdjęcie mostu"></center>
 
            <center><img src="img/solid.webp" alt="Zdjęcie mostu"></center>

            <center><img src="img/wwa.webp" alt="Zdjęcie mostu"></center>
 
            <center><img src="img/yachi.jpg" alt="Zdjęcie mostu"></center>
       
            <center><img src="img/Baluarte.jpg" alt="Zdjęcie mostu"></center>
</div>

 <center><form method="post" name="background">
        <input type="button" value="żółty" onclick="changeBackground('#FFFF00')">
        <input type="button" value="czarny" onclick="changeBackground('#000000')">
        <input type="button" value="biały" onclick="changeBackground('#FFFFFF')">
        <input type="button" value="zielony" onclick="changeBackground('#00FF00')">
        <input type="button" value="niebieski" onclick="changeBackground('#0000FF')">
        <input type="button" value="pomarańczowy" onclick="changeBackground('#FF8000')">
        <input type="button" value="szary" onclick="changeBackground('#C0C0C0')">
        <input type="button" value="czerwony" onclick="changeBackground('#FF0000')">
    </form></center>
	<center><div id="zegarek"></div>
	<div id="data"></div></center>
	
	
	
	
    <main>
        <?php
        if (file_exists($strona)) {
            include($strona);
        } else {
            echo "<p>Strona nie istnieje.</p>";
        }
        ?>
    </main>
	
	
	 <?php
    echo "<h2> </h2>";
 
    $nr_indeksu = '164413';
    $nr_grupy = 'ISI2';
 
    echo 'Radoslaw Mydlo, ' . $nr_indeksu . ' grupa: ' . $nr_grupy . '<br><br>';
?>
</body
</html>

