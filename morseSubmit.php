<?php
// Start de sessie om gegevens tussen pagina's te kunnen delen
session_start();
$method = $_SERVER["REQUEST_METHOD"];

// Controleer of het een POST-verzoek is met een geldig token
if ($method === "POST" && isset($_POST["token"]) && $_POST["token"] === "b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3") {
    try {
        // Database configuratie
        $host = "localhost";
        $username = "root";
        $password = ""; // Voor Thom: verander naar 'root' als je MAMP gebruikt
        $database = "web"; // Voor Thom: verander naar jouw database

        // Maak verbinding met de database
        $connection = new mysqli($host, $username, $password, $database);

        // Controleer of de verbinding succesvol is
        if ($connection->connect_error) {
            throw new Exception("Database connectiefout: " . $connection->connect_error);
        }

        // Sessie opschonen: verwijder eerdere morsecode-gerelateerde sessievariabelen
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, "teken") === 0 || strpos($key, "letter") === 0) {
                unset($_SESSION[$key]);
            }
        }
        // Initialiseer belangrijke sessievariabelen
        $i = 1;
        $_SESSION["teken1"] = $_SESSION["text"] = $_SESSION["nummer" . $i] = "";

        // Haal de ingevoerde morsecode op uit het formulier
        $input = isset($_POST["input"]) ? trim($_POST["input"]) : "";
        // Split de input in losse tekens
        $characters = str_split($input);

        // Bereid de SQL-query voor om morse symbolen op te zoeken
        $query = "SELECT id FROM morsepiep WHERE symbool = ?";
        $statement = $connection->prepare($query);
        if (!$statement) {
            throw new Exception("Statement preparation failed: " . $connection->error);
        }

        // Verwerk elk teken van de ingevoerde morsecode
        foreach ($characters as $character) {
            // Controleer of het teken een geldige morsecode is (punt, streepje of scheidingsteken)
            if ($character !== "-" && $character !== "." && $character !== "\\") {
                throw new Exception("Correcte karakters niet ingevuld");
            }

            // Als het een scheidingsteken is tussen letters
            if ($character === "\\") {
                // Sla de huidige letter op
                $_SESSION["letter" . $i] = $_SESSION["teken" . $i] ?? "";
                // Ga naar de volgende letter
                $i++;
                $_SESSION["teken" . $i] = "";
            } else {
                // Voeg het teken toe aan de huidige lettercombinatie
                $_SESSION["teken" . $i] = ($_SESSION["teken" . $i] ?? "") . $character;

                // Zoek de numerieke waarde van het morse-symbool in de database
                $statement->bind_param("s", $character);
                $statement->execute();
                $statement->bind_result($nummer);

                // Als het symbool gevonden is, voeg de numerieke waarde toe aan de sessie
                if ($statement->fetch()) {
                    $_SESSION["nummer" . $i] .= $nummer;
                }
                $statement->free_result();
            }
        }

        // Sla de laatste letter op als er geen scheidingsteken na staat
        if (!empty($_SESSION["teken" . $i])) {
            $_SESSION["letter" . $i] = $_SESSION["teken" . $i];
        }

        // Sla het totale aantal letters op
        $_SESSION["count"] = $i;
        
        // Vertaal elke morse-letter naar een normale letter
        for ($j = 1; $j <= $i; $j++) {
            // Maak een nieuwe verbinding voor elke letter (kan efficiënter)
            $connectionLetter = new mysqli($host, $username, $password, $database);

            // Controleer of de verbinding succesvol is
            if ($connectionLetter->connect_error) {
                throw new Exception("Database connectiefout: " . $connectionLetter->connect_error);
            }
            
            // Query om de letter op basis van de numerieke code op te zoeken
            $queryLetter = "SELECT * FROM morseletter WHERE lengte = ?";
            $statementLetter = $connectionLetter->prepare($queryLetter);

            if (!$statementLetter) {
                throw new Exception("Statement preparation failed: " . $connectionLetter->error);
            }

            // Haal de numerieke code voor deze letter op
            $nummer = $_SESSION["nummer" . $j];

            // Bind de parameter en voer de query uit
            $statementLetter->bind_param("s", $nummer);
            $statementLetter->execute();
            $statementLetter->bind_result($id, $letter, $lengte);
            
            // Voeg de gevonden letter toe aan de vertaalde tekst
            while ($statementLetter->fetch()) {
                $_SESSION["text"] .= $letter;
            }
        }
        
        // Stuur de gebruiker door naar de resultaatpagina
        header("Location: tekst.php");
        exit();
    } catch (Exception $e) {
        // Toon foutmelding als er iets misgaat
        echo "<div class='error'> De error is: " . $e->getMessage() . "</div>";
    } finally {
        // Sluit de database verbindingen
        if (isset($statement)) {
            $statement->close();
        }
        $connection->close();
    }
} elseif ($method === "GET") {
    // Bij een GET-verzoek: maak de sessie schoon voor een nieuwe invoer
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, "teken") === 0 || strpos($key, "letter") === 0) {
            unset($_SESSION[$key]);
        }
    }
    $_SESSION["teken1"] = "";
}

// Debugging-code - verwijder in productie
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

?>


<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morse Code Invoeren</title>
</head>
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

    /* Focus-effect voor tekstvak */
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

    /* Hover-effect voor verzendknop */
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
    <!-- Header met logo, naam en navigatie -->
    <header>
        <img src="logo.png" alt="Onze logo">
        <div class="bedrijfNaam heading">MorseXpress</div>
        <div class="links">
            <a href="index.php" class="link heading">Voer tekst in</a>
            <a href="morseSubmit.php" class="link heading"><b>Voer morse code in</b></a>
        </div>
    </header>

    <!-- Toon foutmelding als die is ingesteld -->
    <?php if (isset($_SESSION["error"])): ?>
        <div class="error">
            <?php echo $_SESSION["error"]; ?>
            <?php unset($_SESSION["error"]); ?>
        </div>
    <?php endif; ?>

    <!-- Formulier voor het invoeren van morsecode -->
    <form action="morseSubmit.php" method="POST" class="morseInput">
        <div class="voerIn">Voer hier je morse code in, zet een \ tussen letters en een spatie tussen woorden</div>

        <input type="hidden" name="token" value="b3f44c1eb885409c222fdb78c125f5e7050ce4f3d15e8b15ffe51678dd3a33d3a18dd3">
        <textarea required name="input" id="tekstInput" placeholder="Typ hier je morse code (bijv. ...\---\... voor 'sos')..."></textarea>
        <input type="submit" value="Voer in" class="submitMorse">
    </form>
</body>

</html>