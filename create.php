<?php
$method = $_SERVER["REQUEST_METHOD"];
if (isset($_COOKIE["Error"])) {
echo "<div class='error'>" . $_COOKIE["Error"] . "</div>";
}
if (isset($_COOKIE["Success"])) {
echo "<div class='success'>" . $_COOKIE["Success"] . "</div>";
}

if ($method === "POST") {
try {
// Validate token first
if (!isset($_POST["token"]) || $_POST["token"] !== "asdlkjfapoiewjrpoiasjvp98asje09rjapsoedjfpoaisdjfpasodjfpoiaesjtpaijsdpvoianspodifjpasd") {
throw new Exception("De token is incorrect");
}

$host = "localhost";
$username = "root";
$password = "";
$database = "web";

$connection = new mysqli($host, $username, $password, $database);

// Set the character set to UTF-8
$connection->set_charset("utf8mb4");

if ($connection->connect_error) {
throw new Exception($connection->error);
}

$letter = $_POST["letter"]; // We'll keep the raw input for database queries
$sanitized_letter = htmlspecialchars($letter, ENT_QUOTES, 'UTF-8'); // For display purposes
$nummer = filter_var($_POST["lengte"], FILTER_VALIDATE_INT);

if ($nummer === false || $nummer < 1) {
    throw new Exception("Lengte moet een geldig positief getal zijn");
    }

    // Verify the letter is only one character in UTF-8
    if (mb_strlen($letter, 'UTF-8' ) !==1) {
    throw new Exception("Voer precies één letter in");
    }

    // Check if the letter already exists using a binary comparison
    $searchQuery="SELECT * FROM morseletter WHERE letter = ? COLLATE utf8mb4_bin" ;

    $statement=$connection->prepare($searchQuery);

    if ($statement === false) {
    throw new Exception("Statement preparation failed: " . $connection->error);
    }

    $statement->bind_param("s", $letter);
    $statement->execute();

    $result = $statement->get_result();

    if ($result->num_rows > 0) {
    setcookie("Error", "De letter " . $sanitized_letter . " bestaat al", time() + 10);
    header("Location: create.php");
    exit();
    } else {
    // Now insert the new letter
    $insertQuery = "INSERT INTO morseletter (letter, lengte) VALUES (?, ?)";
    $insertStmt = $connection->prepare($insertQuery);

    if ($insertStmt === false) {
    throw new Exception("Insert statement preparation failed: " . $connection->error);
    }

    $insertStmt->bind_param("si", $letter, $nummer);
    $insertStmt->execute();

    setcookie("Success", "Letter " . $sanitized_letter . " succesvol toegevoegd", time() + 10);
    header("Location: create.php");
    exit();
    }
    } catch (Exception $e) {
    setcookie("Error", "Error: " . $e->getMessage(), time() + 10);
    header("Location: create.php");
    exit();
    }
    }
    ?>

    <!DOCTYPE html>
    <html lang="nl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Letter Toevoegen</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
            }

            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f4f4f4;
            }

            form {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                display: flex;
                flex-direction: column;
                gap: 15px;
                width: 300px;
            }

            label {
                font-weight: bold;
            }

            input {
                padding: 10px;
                border: 2px solid #007bff;
                border-radius: 5px;
                font-size: 1rem;
                transition: 0.3s;
            }

            input:focus {
                border-color: #0056b3;
                outline: none;
                box-shadow: 0 0 5px rgba(0, 91, 187, 0.5);
            }

            .submit-btn {
                background-color: #007bff;
                color: white;
                font-size: 1rem;
                font-weight: bold;
                cursor: pointer;
                border: none;
                padding: 12px;
                transition: 0.3s;
            }

            .submit-btn:hover {
                background-color: #0056b3;
                transform: scale(1.05);
            }

            .error {
                background-color: #ffdddd;
                border: 1px solid #ff0000;
                color: #ff0000;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 10px;
                text-align: center;
            }

            .success {
                background-color: #ddffdd;
                border: 1px solid #00ff00;
                color: #008800;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 10px;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <form action="create.php" method="POST">
            <input type="hidden" name="token" value="asdlkjfapoiewjrpoiasjvp98asje09rjapsoedjfpoaisdjfpasodjfpoiaesjtpaijsdpvoianspodifjpasd">
            <label for="letter">Letter</label>
            <input type="text" id="letter" name="letter" required placeholder="Voer hier je letter in" maxlength="1" />

            <label for="lengte">Lengte</label>
            <input type="number" id="lengte" name="lengte" required placeholder="Voer hier je lengte in" min="1" />

            <input type="submit" value="Toevoegen" class="submit-btn">
        </form>
    </body>

    </html>