<?php
// Rozpoczęcie sesji
session_start();

// Ścieżki bezwzględne do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Wymagane pliki PHPMailer
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// Konfiguracja SMTP
define('SMTP_HOST', 'smtp.gmail.com'); // Host SMTP
define('SMTP_PORT', 587); // Port SMTP
define('SMTP_USERNAME', 'RadekMojaStrona@gmail.com'); // Nazwa użytkownika SMTP
define('SMTP_PASSWORD', 'fohf cdgn riih crmp'); // Hasło SMTP

// Funkcja wyświetlająca formularz kontaktowy
function PokazKontakt() {
    echo '
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Kontakt</title>
        <link rel="stylesheet" href="../css/cw1.css">
    </head>
    <body>
        <section id="kontakt">
            <h1>Kontakt</h1>
            <div class="form-container">
                <form action="" method="post">
                    <label class="form-label" for="name">Imię:</label>
                    <input class="form-input" type="text" id="name" name="name" required>

                    <label class="form-label" for="email">Adres e-mail:</label>
                    <input class="form-input" type="email" id="email" name="email" required>

                    <label class="form-label" for="temat">Temat:</label>
                    <input class="form-input" type="text" id="temat" name="temat" required>

                    <label class="form-label" for="tresc">Wiadomość:</label>
                    <textarea class="form-input" id="tresc" name="tresc" rows="5" required></textarea>

                    <input class="form-button" type="submit" name="submit_kontakt" value="Wyślij">
                    <a href="../index.php" class="back-button">Powrót do strony głównej</a>
                </form>
                <a href="?przypomnij_haslo=1">Przypomnij hasło do panelu admina</a>
            </div>
        </section>
    </body>
    </html>';
}

// Funkcja wysyłająca e-mail kontaktowy
function WyslijMailKontakt($odbiorca) {
    $mail = new PHPMailer(true);

    try {
        // Konfiguracja SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Ustawienie nadawcy i odbiorcy
        $mail->setFrom(SMTP_USERNAME, $_POST['name']);
        $mail->addReplyTo($_POST['email'], $_POST['name']);
        $mail->addAddress($odbiorca);

        // Treść wiadomości
        $mail->isHTML(false);
        $mail->Subject = $_POST['temat'];
        $mail->Body    = "Od: " . $_POST['name'] . " (" . $_POST['email'] . ")\n\n" . $_POST['tresc'];

        // Wysłanie wiadomości
        $mail->send();
        echo '<p class="success">[wiadomosc_wyslana]</p>';
    } catch (Exception $e) {
        echo '<p class="error">[blad_wysylania]: ' . $mail->ErrorInfo . '</p>';
    }
}

// Funkcja przypominająca hasło
function PrzypomnijHaslo() {
    $mail = new PHPMailer(true);

    try {
        // Konfiguracja SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Ustawienie nadawcy i odbiorcy
        $mail->setFrom(SMTP_USERNAME, 'System przypomnień');
        $mail->addAddress("RadekMojaStrona@gmail.com");
        $mail->Subject = 'Przypomnienie hasła';
        $mail->Body    = "Twoje hasło to: haslo";

        // Wysłanie wiadomości
        $mail->send();
        echo '<p class="success">[haslo_wyslane]</p>';
    } catch (Exception $e) {
        echo '<p class="error">[blad_wysylania_hasla]: ' . $mail->ErrorInfo . '</p>';
    }
}

// Obsługa formularza kontaktowego
if (isset($_POST['submit_kontakt'])) {
    WyslijMailKontakt("biuro@firma.pl");
}

// Obsługa przypomnienia hasła
if (isset($_GET['przypomnij_haslo'])) {
    PrzypomnijHaslo();
}

// Wyświetlenie formularza kontaktowego
PokazKontakt();
?>