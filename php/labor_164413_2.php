<?php
$nrIndeksu = 164413;
$nrGrupy = 2;
$a = 5;
$b = 7;
echo "Radoslaw Mydlo . $nrIndeksu . grupa: $nrGrupy <br/><br/>";
echo 'Zastosowanie metody include() <br>';
include 'include.php' ;
echo "A $color $fruit <br>";

echo 'Zastosowanie metod if, else,elseif,switch <br>';
if ($a > $b)
{
 echo "$a is bigger than $b <br>";
}
else
{
	echo "$b is bigger than $a <br>";
}

if(5 > 7)
{
	echo 'dwa <br>';
}
elseif(5 < 7)
{
	echo 'trzy <br>';
}


switch ($nrGrupy)
 {
    case 0:
        echo "nrGrupy equals 0<br>";
        break;
    case 1:
        echo "nrGrupy equals 1<br>";
        break;
    case 2:
        echo "nrGrupy equals 2<br>";
        break;
}

echo 'zastosowanie petli while
?>