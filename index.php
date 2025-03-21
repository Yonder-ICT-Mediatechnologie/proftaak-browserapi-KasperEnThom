<?php
session_start();
$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    try {
        if (!isset($_POST["token"]) || $_POST["token"] !== "b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3") {
            $_SESSION["error"] = "De token is incorrect";
        } else {
            $host = "localhost";
            $username = "root";
            // voor Thom, verander de $password naar root, je gebruikt MAMP
            $password = "";
            // Voor Thom, verander de $database naar de database die jij gebruikt
            $database = "web";

            $connection = new mysqli($host, $username, $password, $database);

            if ($connection->connect_error) {
                throw new Exception( "Database connectiefout: " . $connection->connect_error);
            }

            $input = isset($_POST["input"]) ? $_POST["input"] : '';
            $letters = str_split(strtolower($input));
            $_SESSION['result'] = '';
            $_SESSION['debug'] = '';
            $i = 1;
            foreach ($letters as $letter) {
                // Gebruik een prepared statement om SQL-injectie te voorkomen
                $query = "SELECT lengte FROM morseletter WHERE letter = ?";
                $statement = $connection->prepare($query);

                if ($statement === false) {
                    throw new Exception("Statement preparation failed: " . $connection->error);
                }

                $statement->bind_param("s", $letter); // "s" staat voor string
                $statement->execute();
                $statement->bind_result($lengte);

                if ($statement->fetch()) {
                    $_SESSION['result'] .= $lengte;
                    $_SESSION['debug'] .= $letter;
                } else {
                    $_SESSION["letter" . $letter] = "Niet gevonden";
                }

                $statement->close();

                $i++;
            }
        }
    } catch (Exception $e) {
        echo "De error is: " . $e->getMessage();
    } finally {
        if (isset($connection)) {
            $connection->close();
        }
        if (isset($session)) {
            $session->close();
        }
        header("Location: morse.php");
    }
} elseif ($method === "GET") {
    session_destroy();
}

if (isset($_SESSION["error"])) {
    echo "<div class='error'>";
    echo $_SESSION["error"];
    echo "</div>";
}
// print_r($_SESSION);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text invoeren</title>
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
    <header>
        <img src="logo.png" alt="Onze logo">
        <div class="bedrijfNaam heading">MorseXpress</div>
        <div class="links">
            <a href="index.php" class="link heading"><b>Voer tekst in</b></a>
            <a href="tekst.php" class="link heading">Morse code</a>
            <a href="morseSubmit.php" class="link heading">Voer morse code in</a>
        </div>
    </header>
    <form action="index.php" method="POST" class="morseInput">
        <div class="voerIn">Voer hier je text in</div>
        <input type="hidden" name="token" value="b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3">
        <textarea required name="input" id="tekstInput" placeholder="Typ hier je tekst..."></textarea>
        <input type="submit" value="Voer in" class="submitMorse">
    </form>
</body>

</html>