<?php
// Start de sessie om gegevens van morseSubmit.php te kunnen gebruiken
session_start();
$method = $_SERVER["REQUEST_METHOD"];

// Toon eventuele foutmeldingen die in de sessie zijn opgeslagen
if (isset($_SESSION["error"])) {
    echo "<div class='error'>";
    echo $_SESSION["error"];
    echo "</div>";
}

// Database configuratie
$host = "localhost";
$username = "root";
$password = "root"; // Voor Thom: verander naar 'root' als je MAMP gebruikt
$database = "web"; // Voor Thom: verander naar jouw database

// Maak verbinding met de database
$connection = new mysqli($host, $username, $password, $database);

// Controleer of de verbinding succesvol is
if ($connection->connect_error) {
    die("Database connectiefout: " . $connection->connect_error);
}

// Haal de vertaalde tekst op uit de sessie
$text = isset($_SESSION["text"]) ? $_SESSION["text"] : "Geen tekst gevonden.";

// Debugging code (uitgecommentarieerd)
// print_r($_SESSION);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tekst</title>
</head>
<!-- Laad Google Fonts voor de speciale koptekst-stijl -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poetsen+One&display=swap" rel="stylesheet">
<style>
    /* Reset standaard browser-stijlen */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Basisstijl voor de pagina */
    body {
        font-family: Arial, sans-serif;
        color: #333;
        background-color: #F8F9FA;
        line-height: 1.6;
    }

    /* Stijl voor koppen */
    .heading {
        font-family: 'Poetsen One', sans-serif;
        font-size: 1.5rem;
        color: #222;
    }

    /* Stijl voor de header */
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #fff;
        padding: 15px 20px;
        border-bottom: 4px solid #4C88EF;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Stijl voor logo */
    header img {
        height: 50px;
    }

    /* Stijl voor bedrijfsnaam */
    .bedrijfNaam {
        flex-grow: 1;
        text-align: center;
    }

    /* Stijl voor navigatielinks */
    .links {
        display: flex;
        gap: 15px;
    }

    /* Stijl voor individuele links */
    .link {
        text-decoration: none;
        color: black;
        font-size: 1rem;
        font-weight: 500;
        transition: color 0.3s ease-in-out, transform 0.2s;
    }

    /* Hover-effect voor links */
    .link:hover {
        color: #0056b3;
        transform: scale(1.05);
    }

    /* Container voor de tekstweergave */
    .textContainer {
        max-width: 600px;
        margin: 50px auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        gap: 15px;
        border: 4px solid #007bff;
        text-align: center;
        background-color: #ECECEC;
    }

    /* Stijl voor de titel boven het tekstvak */
    .voerIn {
        font-size: 1.3rem;
        font-weight: bold;
        color: #222;
    }

    /* Stijl voor het tekstvak */
    textarea {
        width: 100%;
        height: 200px;
        padding: 12px;
        font-size: 1rem;
        border-radius: 8px;
        resize: none;
        transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        background-color: #fff;
        border: 3px solid #007bff;
    }

    /* Focus-effect voor het tekstvak */
    textarea:focus {
        border-color: #0056b3;
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
        outline: none;
    }

    /* Stijl voor de voorleesknop */
    .speakButton {
        background-color: #007bff;
        color: white;
        font-size: 1rem;
        font-weight: bold;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out, transform 0.2s;
    }

    /* Hover-effect voor de voorleesknop */
    .speakButton:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
    @media screen and (max-width: 940px) {
        header {
          flex-direction: column;
        }
    }
</style>

<body>
    <!-- JavaScript voor de spraakfunctionaliteit -->
    <script>
        // Haal de tekst uit PHP en zorg dat deze veilig in JavaScript wordt gebruikt
        let text = "<?php echo addslashes($text); ?>"; 

        // Functie om de tekst voor te lezen met de Web Speech API
        function speakText() {
            if ('speechSynthesis' in window) {
                let utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'nl-NL'; // Nederlandse stem
                utterance.rate = 1; // Normale snelheid
                window.speechSynthesis.speak(utterance);
            } else {
                alert("Spraakfunctie wordt niet ondersteund in deze browser.");
            }
        }
    </script>

    <!-- Header met logo, naam en navigatie -->
    <header>
        <img src="logo.png" alt="Ons logo">
        <div class="bedrijfNaam heading">MorseXpress</div>
        <div class="links">
            <a href="index.php" class="link heading">Voer tekst in</a>
            <a href="morseSubmit.php" class="link heading">Voer morse code in</a>
        </div>
    </header>

    <!-- Formulier om de vertaalde tekst weer te geven -->
    <form action="index.php" method="POST" class="textContainer">
        <div class="voerIn">Uw tekst:</div>
        <input type="hidden" name="token" value="b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3">
        <textarea readonly name="input" id="textInput" placeholder="Geen tekst gevonden."><?php
                                                                                            // Toon de vertaalde tekst en bescherm tegen XSS
                                                                                            echo htmlspecialchars($text);
                                                                                            ?></textarea>
        <!-- Knop om de tekst voor te lezen -->
        <input class="speakButton" onclick="speakText()" value="Lees de tekst voor" type="button">
    </form>

</body>

</html>

<?php
// Sluit de databaseverbinding
$connection->close();
// Verwijder de sessie om te voorkomen dat tekst opnieuw wordt weergegeven bij vernieuwen
session_destroy();
?>