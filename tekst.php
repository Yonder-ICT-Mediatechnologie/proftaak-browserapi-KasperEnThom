<?php
session_start();
$method = $_SERVER["REQUEST_METHOD"];

if (isset($_SESSION["error"])) {
    echo "<div class='error'>";
    echo $_SESSION["error"];
    echo "</div>";
}

$host = "localhost";
$username = "root";
$password = "root"; // Voor Thom: verander naar 'root' als je MAMP gebruikt
$database = "web"; // Voor Thom: verander naar jouw database

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Database connectiefout: " . $connection->connect_error);
}

$morseCode = '';
if (isset($_SESSION['result']) && !empty($_SESSION['result'])) {
    $results = str_split($_SESSION['result']);
    $query = "SELECT symbool FROM morsepiep WHERE id = ?";
    $statement = $connection->prepare($query);

    if ($statement) {
        foreach ($results as $result) {
            $statement->bind_param("s", $result);
            $statement->execute();
            $statement->bind_result($symbool);

            while ($statement->fetch()) {
                $morseCode .= $symbool;
            }
        }
        $statement->close();
    }
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
    oscillator.type = 'sine'; // Geluidstype
    oscillator.frequency.setValueAtTime(600, audioCtx.currentTime); // Frequentie van de toon
    oscillator.connect(audioCtx.destination);
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
            <a href="index.php" class="link heading"><b>Invoer</b></a>
            <a href="tekst.php" class="link heading">Morse code</a>
        </div>
    </header>
    <form action="index.php" method="POST" class="morseInput">
        <div class="voerIn">Uw morse code:</div>
        <input type="hidden" name="token" value="b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3">
        <textarea readonly name="input" id="tekstInput" placeholder="Geen morse code gevonden."><?php
        if (!empty($morseCode)) {
            echo htmlspecialchars($morseCode);
        } else {
            echo "<p>Geen morse code gevonden.</p>";
        }
        ?></textarea>
        <input class="submitMorse" onclick="playMorseCode(morseCode)" value="Speel Morse-code af" type="button">
    </form>
    <div class="morse-code">
        
    </div>
</body>

</html>

<?php
$connection->close();
?>