<?php
// Start een nieuwe sessie of hervat de bestaande sessie
session_start();
// Haal de HTTP-methode op die voor het huidige verzoek wordt gebruikt
$method = $_SERVER["REQUEST_METHOD"];

// Controleer of er een foutmelding in de sessie is opgeslagen en toon deze zo nodig
if (isset($_SESSION["error"])) {
    echo "<div class='error'>";
    echo $_SESSION["error"];
    echo "</div>";
}

// Database configuratie
$host = "localhost";
$username = "root";
$password = ""; // Opmerking voor Thom over wachtwoord voor MAMP
$database = "web"; // Opmerking voor Thom over database naam

// Maak een nieuwe MySQL database verbinding
$connection = new mysqli($host, $username, $password, $database);

// Controleer of de database verbinding succesvol is
if ($connection->connect_error) {
    // Stop de uitvoering en toon een foutmelding bij verbindingsproblemen
    die("Database connectiefout: " . $connection->connect_error);
}

// Initialiseer de variabele voor de morse code
$morseCode = '';
// Controleer of er een resultaat in de sessie staat en of deze niet leeg is
if (isset($_SESSION['result']) && !empty($_SESSION['result'])) {
    // Splits het resultaat op in individuele karakters
    $results = str_split($_SESSION['result']);
    // Voorbereid een SQL query om symbolen op te halen uit de morsepiep tabel
    $query = "SELECT symbool FROM morsepiep WHERE id = ?";
    // Bereid het prepared statement voor
    $statement = $connection->prepare($query);

    // Controleer of het statement correct is voorbereid
    if ($statement) {
        // Loop door elk karakter in het resultaat
        foreach ($results as $result) {
            // Als het karakter een scheidingsteken is, voeg dit direct toe en ga door naar het volgende karakter
            if ($result == "\\") {
                $morseCode .= "\\";
                continue;
            }

            // Bind de parameter aan het statement
            $statement->bind_param("s", $result);
            // Voer het statement uit
            $statement->execute();
            // Bind het resultaat aan de variabele $symbool
            $statement->bind_result($symbool);

            // Haal het resultaat op en voeg het symbool toe aan de morse code
            while ($statement->fetch()) {
                $morseCode .= $symbool;
            }
        }
        // Sluit het statement om resources vrij te geven
        $statement->close();
    }
    // Sla de gegenereerde morse code op in de sessie
    $_SESSION['morse'] = $morseCode;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tekst</title>
</head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poetsen+One&display=swap" rel="stylesheet">
<style>
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        color: #333;
        background-color: #F8F9FA;
        line-height: 1.6;
    }

    .heading {
        font-family: 'Poetsen One', sans-serif;
        font-size: 1.5rem;
        color: #222;
    }

    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #fff;
        padding: 15px 20px;
        border-bottom: 4px solid #4C88EF;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    header img {
        height: 50px;
    }

    .bedrijfNaam {
        flex-grow: 1;
        text-align: center;
    }

    .links {
        display: flex;
        gap: 15px;
    }

    .link {
        text-decoration: none;
        color: black;
        font-size: 1rem;
        font-weight: 500;
        transition: color 0.3s ease-in-out, transform 0.2s;
    }

    .link:hover {
        color: #0056b3;
        transform: scale(1.05);
    }

    /* Formuliercontainer */
    .morseInput {
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

    /* Formulier header */
    .voerIn {
        font-size: 1.3rem;
        font-weight: bold;
        color: #222;
    }

    /* Tekstvak */
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

    textarea:focus {
        border-color: #0056b3;
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
        outline: none;
    }

    /* Verzendknop */
    .submitMorse {
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

    .submitMorse:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    /* Error bericht */
    .error {
        max-width: 600px;
        margin: 20px auto;
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
    }
    @media screen and (max-width: 940px) {
        header {
          flex-direction: column;
        }
    }
</style>

<body>
    
<script>
        let morseCode = "<?php echo addslashes($morseCode); ?>"; // Zorgt ervoor dat de morse code goed in JavaScript wordt geplaatst

let isPlaying = false; // Voorkomt meerdere overlappende afspeelsessies

function playMorseCode(morseCode) {
    if (isPlaying || !morseCode) return; // Stop als er al iets afspeelt of als er geen morse-code is
    isPlaying = true; // Zet de vlag aan

    const dotDuration = 200; // Duur van een punt
    const dashDuration = dotDuration * 3; // Duur van een streep
    const gapDuration = dotDuration; // Pauze tussen symbolen
    let currentTime = 1000;

    morseCode.split('').forEach((symbol, index) => {
        setTimeout(() => {
            if (symbol === '.') {
                beep(dotDuration);
            } else if (symbol === '-') {
                beep(dashDuration);
            }
        }, currentTime);

        currentTime += (symbol === '.' ? dotDuration : dashDuration) + gapDuration;
    });

    // Zet de vlag weer uit nadat alle piepjes zijn afgespeeld
    setTimeout(() => {
        isPlaying = false;
    }, currentTime);
}

function beep(duration) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain(); // Create a gain node

    oscillator.type = 'sine'; 
    oscillator.frequency.setValueAtTime(600, audioCtx.currentTime); 

    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime); // Adjust volume (0.2 is softer)

    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    oscillator.start();
    setTimeout(() => {
        oscillator.stop();
    }, duration);
}

    </script>
    <header>
        <img src="logo.png" alt="Onze logo">
        <div class="bedrijfNaam heading">MorseXpress</div>
        <div class="links">
            <a href="index.php" class="link heading">Voer tekst in</a>
            <a href="morseSubmit.php" class="link heading">Voer morse code in</a>
        </div>
    </header>
    <form action="index.php" method="POST" class="morseInput">
        <div class="voerIn">Uw morse code:</div>
        <input type="hidden" name="token" value="b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3">
        <textarea readonly name="input" id="tekstInput" placeholder="Geen morse code gevonden."><?php
        if (!empty($morseCode)) {
            echo htmlspecialchars($morseCode);
        } else {
            echo "Geen morse code gevonden.";
        }
        ?></textarea>
        <input class="submitMorse" onclick="playMorseCode(morseCode)" value="Speel Morse-code af" type="button">
    </form>
    <div class="morse-code">
        
    </div>
</body>

</html>

<?php
// Sluit de database verbinding om resources vrij te geven
$connection->close();
?>