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
    $password = "root"; // Voor Thom: verander naar 'root' als je MAMP gebruikt
    $database = "web"; // Voor Thom: verander naar jouw database

    $connection = new mysqli($host, $username, $password, $database);

    if ($connection->connect_error) {
        throw new Exception("Database connectiefout: " . $connection->connect_error);
    }

    $query = "SELECT symbool FROM morsepiep WHERE id = ?";
    $statement = $connection->prepare($query);

    if (!$statement) {
        throw new Exception("Statement preparation failed: " . $connection->error);
    }

    foreach ($results as $result) {
        $statement->bind_param("s", $result);
        $statement->execute();
        $statement->bind_result($symbool);

        while ($statement->fetch()) {
            $_SESSION['morse'] .= $symbool;
        }
    }

    $statement->close();
} catch (Exception $e) {
    echo "De error is: " . $e->getMessage();
} finally {
    if (isset($connection)) {
        $connection->close();
    }
    if (isset($session)) {
        $session->close();
    }
}

print_r($_SESSION);
