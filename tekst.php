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
$password = ""; // Voor Thom: verander naar 'root' als je MAMP gebruikt
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
        transition: color 0.3s ease-in-out;
    }

    .link:hover {
        color: #0056b3;
    }
</style>

<body>
    <header>
        <img src="logo.png" alt="Onze logo">
        <div class="bedrijfNaam heading">MorseXpress</div>
        <div class="links">
            <a href="index.php" class="link heading"><b>Invoer</b></a>
            <a href="tekst.php" class="link heading">Morse code</a>
        </div>
    </header>
    <div class="morse-code">
        <?php
        if (!empty($morseCode)) {
            echo "<p>Morse Code: " . htmlspecialchars($morseCode) . "</p>";
        } else {
            echo "<p>Geen morse code gevonden.</p>";
        }
        ?>
        <button onclick="playMorseCode(morseCode)">Speel Morse-code af</button>
    </div>
    <script>
        function playMorseCode(morseCode) {
    if (!morseCode) return;
    const dotDuration = 200;
    const dashDuration = dotDuration * 3;
    const gapDuration = dotDuration;
    let currentTime = 1000;

    morseCode.split('').forEach((symbol, index) => {
        setTimeout(() => {
            if (symbol === '.') {
                beep(dotDuration);
            } else if (symbol === '-') {
                beep(dashDuration);
            }
            console.log("Beep voor:", symbol, "op tijd", currentTime);
        }, currentTime);

        currentTime += (symbol === '.' ? dotDuration : dashDuration) + gapDuration;
    });
}

        function beep(duration, nextCallback) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioCtx.createOscillator();
    oscillator.type = 'sine';
    oscillator.frequency.setValueAtTime(600, audioCtx.currentTime);
    oscillator.connect(audioCtx.destination);
    oscillator.start();
    setTimeout(() => {
        oscillator.stop();
        if (nextCallback) nextCallback();
    }, duration);
}


        <?php
        if (!empty($morseCode)) {
            echo "playMorseCode('" . $morseCode . "');";
        }
        ?>
    </script>
</body>

</html>

<?php
$connection->close();
?>