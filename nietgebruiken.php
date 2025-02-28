<?php
try {
    session_start();
    $_SESSION['morse'] = '';

    if (!isset($_SESSION['result']) || empty($_SESSION['result'])) {
        throw new Exception("Geen resultaat gevonden in de sessie.");
    }

    $results = str_split($_SESSION['result']);

    $host = "localhost";
    $username = "root";
    // voor Thom, verander de $password naar root, je gebruikt MAMP
    $password = "";
    // Voor Thom, verander de $database naar de database die jij gebruikt
    $database = "web";
    $connection = new mysqli($host, $username, $password, $database);

    if ($connection->connect_error) {
        throw new Exception("Database connectiefout: " . $connection->connect_error);
    }

    foreach ($results as $result) {
        $query = "SELECT symbool FROM morsepiep WHERE id = ?";
        $statement = $connection->prepare($query);

        if (!$statement) {
            throw new Exception("Statement preparation failed: " . $connection->error);
        }

        $statement->bind_param("s", $result);
        $statement->execute();
        $statement->bind_result($symbool);

        while ($statement->fetch()) {
            $_SESSION['morse'] .= $symbool;
        }

        $statement->close();
    }

    $connection->close();
} catch (Exception $e) {
    echo "De error is: " . $e->getMessage();
}

print_r($_SESSION);
