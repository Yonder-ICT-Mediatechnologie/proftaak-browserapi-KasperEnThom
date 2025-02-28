<?php
session_start();
$method = $_SERVER["REQUEST_METHOD"];

if (isset($_SESSION["error"])) {
    echo "<div class='error'>";
    echo $_SESSION["error"];
    echo "</div>";
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
        if (isset($_SESSION['morse'])) {
            echo "<p>Morse Code: " . $_SESSION['morse'] . "</p>";
        }
        ?>
    </div>
    <script>
        function playMorseCode(morseCode) {
            const dotDuration = 200; // duration of a dot
            const dashDuration = dotDuration * 3; // duration of a dash
            const gapDuration = dotDuration; // gap between dots and dashes
            const letterGapDuration = dotDuration * 3; // gap between letters
            const wordGapDuration = dotDuration * 7; // gap between words

            let currentTime = 0;

            morseCode.split('').forEach(symbol => {
                if (symbol === '.') {
                    setTimeout(() => beep(dotDuration), currentTime);
                    currentTime += dotDuration + gapDuration;
                } else if (symbol === '-') {
                    setTimeout(() => beep(dashDuration), currentTime);
                    currentTime += dashDuration + gapDuration;
                } else if (symbol === ' ') {
                    currentTime += wordGapDuration;
                }
            });
        }

        function beep(duration) {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(600, audioCtx.currentTime);
            oscillator.connect(audioCtx.destination);
            oscillator.start();
            setTimeout(() => oscillator.stop(), duration);
        }

        <?php
        if (isset($_SESSION['morse'])) {
            echo "playMorseCode('" . $_SESSION['morse'] . "');";
        }
        ?>
    </script>
</body>

</html>