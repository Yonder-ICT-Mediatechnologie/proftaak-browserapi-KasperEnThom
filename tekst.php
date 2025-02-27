<?php
session_start();
$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    try {
        if ($_POST["token"] !== "b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3") {
            $_SESSION["error"] = "De token is incorrect";
        } else {
            $host = "localhost";
            // voor Thom, verander de $username naar root, je gebruikt MAMP
            $username = "";
            $password = "root";
            // Voor Thom, verander de $database naar de databse die jij gebruikt
            $database = "web";

            $connection = new mysqli($host, $username, $password);

            if ($connection->connect_error) {
                throw new Exception($connection->error);
            };

            
        }
    } catch (Exception $e) {
        echo "de error is: " . $e->getMessage();
    }
}


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
    <title>Invoeren</title>
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
    <form action="index.php" method="POST" class="morseInput">
        <div class="voerIn">Voer hier je morse code in</div>
        <input type="hidden" name="token" value="b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3">
        <textarea required name="input" id="tekstInput" placeholder="Typ hier je tekst..."></textarea>
        <input type="submit" value="Voer in" class="submitMorse">
    </form>
</body>
</html>