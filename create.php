<?php
$method = $_SERVER["REQUEST_METHOD"];
if (isset($_COOKIE["Error"])) {
    echo "<div class='error'>" . $_COOKIE["Error"] . "</div>";
}
if (!isset($_POST["token"]) || $_POST["token"] !== "asdlkjfapoiewjrpoiasjvp98asje09rjapsoedjfpoaisdjfpasodjfpoiaesjtpaijsdpvoianspodifjpasd") {
    $_SESSION["error"] = "De token is incorrect";
}
if ($method === "POST") {
    try {
        $host = "localhost";
        $username = "root";
        // voor Thom verander naar root
        $password = "";
        $database = "web";

        $connection = new mysqli($host, $username, $password, $database);

        if ($connection->connect_error) {
            throw new Exception($connection->error);
        }

        $letter = htmlspecialchars($_POST["letter"]);
        $nummer = htmlspecialchars($_POST["lengte"]);

        $searchQuery = "SELECT * FROM morseletter WHERE letter = ?";

        $statment = $connection->prepare($searchQuery);

        if ($statment === false) {
            throw new Exception("Statement preparation failed: " . $connection->error);
        }
        $statment->bind_param("s", $letter); // "s" staat voor string
        $statment->execute();

        if ($statment->fetch()) {
            setcookie("Error", "De letter bestaat al", time() + 10);
            header("location: create.php");
            exit();
        }
        else{
            echo "NIGGER";
        }
    } catch (Exception $e) {
        echo "De error is" . $e;
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