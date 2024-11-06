<?php
    echo "<h2>1. </h2>";

    $nr_indeksu = '164113';
    $nr_grupy = 'ISI2';

    echo 'Radoslaw Mydlo, ' . $nr_indeksu . ' grupa: ' . $nr_grupy . '<br><br>';
?>

<?php
    echo "<h2>2. a) Funkcje include() i require_once()</h2>";
    echo "<p>Funkcja <strong>include()</strong> dołącza plik do skryptu.</p>";
    echo "<p>Funkcja <strong>require_once()</strong> dołącza plik do skryptu tak jak <em>include()</em>, ale sprawdza, czy plik nie był dołączony wcześniej.</p>";

    echo "<h2>2. b) Przykład if, elseif, else oraz switch</h2>";
    $str = '
    <pre>
    // if, elseif, else
    if ($var > 10) 
	{
        echo "Greater than 10";
    } elseif ($var == 10) 
	{
        echo "Equal to 10";
    } else 
	{
        echo "Less than 10";
    }

    // switch 
    switch ($var)
	{
        case 5:
            echo "Value is 5";
            break;
        case 10:
            echo "Value is 10";
            break;
        default:
            echo "Value is neither 5 nor 10";
    }
    </pre>';
    echo $str;

    echo "<h2>2. c) Przykład pętli for oraz while</h2>";

    echo '<pre>for ($i = 0; $i < 5; $i++) {
        echo "Iteration: $i\n";
    }</pre>';

    echo '<pre>$count = 0;
    while ($count < 5) {
        echo "Count: $count\n";
        $count++;
    }
	</pre>';
?>
    
<?php
    echo "<h2>Typy zmiennych w PHP: \$_GET, \$_POST, \$_SESSION</h2>";

    // Wyjaśnienie $_GET
    echo "<h3>\$_GET</h3>";
    echo "<p>Służy do pobierania danych przekazywanych w adresie URL (query string).</p>";
    echo "<p>Dane są widoczne w adresie URL.</p>";
    echo "<pre>
    &lt;?php
    // URL: example.com/page.php?name=John
    \$name = \$_GET['name']; // 'John'
    ?&gt;
    </pre>";

    // Wyjaśnienie $_POST
    echo "<h3>\$_POST</h3>";
    echo "<p>Służy do pobierania danych wysyłanych przez formularze HTTP metodą POST.</p>";
    echo "<p>Dane są niewidoczne w adresie URL, bezpieczniejsze do przekazywania np. haseł.</p>";
    echo "<pre>
    &lt;?php
    // Formularz z metodą POST
    \$username = \$_POST['username'];
    ?&gt;
    </pre>";

    // Wyjaśnienie $_SESSION
    echo "<h3>\$_SESSION</h3>";
    echo "<p>Przechowuje dane sesji użytkownika, które są dostępne na wielu stronach.</p>";
    echo "<p>Dane przechowywane na serwerze, pozwalają na zachowanie stanu między żądaniami.</p>";
    echo "<pre>
    &lt;?php
    session_start(); // Inicjalizacja sesji
    \$_SESSION['user'] = 'John'; // Zapis danych do sesji
    echo \$_SESSION['user']; // Odczyt danych z sesji
    ?&gt;
    </pre>";
?>