<?php

$database = new mysqli("localhost", "root", "", "si_aida_eau");

if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

$database->set_charset("UTF8");
header('Content-type: text/html; charset=utf-8');

$months = [
    "1" => "janvier",
    "2" => "février",
    "3" => "mars",
    "4" => "avril",
    "5" => "mai",
    "6" => "juin",
    "7" => "juillet",
    "8" => "août",
    "9" => "septembre",
    "10" => "octobre",
    "11" => "novembre",
    "12" => "décembre"
];


if (isset($_POST) && !empty($_POST)) {

    if (isset($_POST['prenom']) && isset($_POST['nom']) && $_POST['adresse'] && isset($_POST['identifiant']) && isset($_POST['motdepasse'])) {

        $queryInsert = [];

        $queryInsert["nom"] = $_POST["nom"];
        $queryInsert["prenom"] = $_POST["prenom"];
        $queryInsert["adresse"] = $_POST["adresse"];
        $queryInsert["role"] = 1;
        $queryInsert["identifiant"] = $_POST["identifiant"];
        $queryInsert["motdepasse"] = $_POST["motdepasse"];

        $query = "INSERT INTO `personne`(`nom`, `prenom`, `adresse`, `role`, `identifiant`, `motdepasse`) VALUES (?,?,?,?,?,?)";
        $requestInsert = $database->prepare($query);
        $requestInsert->bind_param('sssiss', $queryInsert["nom"], $queryInsert["prenom"], $queryInsert["adresse"], $queryInsert["role"], $queryInsert["identifiant"], $queryInsert["motdepasse"]);
        // $result = $request->execute();

        if ($requestInsert->execute()) {
            $hasError = false;
        }
    } else {
        $hasError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Inscription</title>
</head>

<body>

    <main>
        <form class="login" method="post">
            <h1>Inscription</h1>
            <div>
                <label for="username">Prénom<sup class="redstar">*</sup></label>
                <input name="prenom" id="prenom" type="text">
            </div>
            <div>
                <label for="username">Nom<sup class="redstar">*</sup></label>
                <input name="nom" id="nom" type="text">
            </div>
            <div>
                <label for="adresse">Adresse<sup class="redstar">*</sup></label>
                <input name="adresse" id="adresse" type="text">
            </div>
            <div>
                <label for="identifiant">Identifiant<sup class="redstar">*</sup></label>
                <input name="identifiant" id="identifiant" type="text">
            </div>
            <div>
                <label for="motdepasse">Mot de passe<sup class="redstar">*</sup></label>
                <input name="motdepasse" id="motdepasse" type="password">
            </div>
            <p class="message active"><sup class="redstar">*</sup> : Champs obligatoires</p>
            <?php
            if (isset($hasError)) {
            ?>
                <p class="message <?php if ($hasError) {
                                        echo "active error";
                                    } else {
                                        echo "active valid";
                                    } ?>"><?php if ($hasError) {
                                                echo "Veuillez remplir tous les champs";
                                            } else {
                                                echo "Inscription réussie";
                                            } ?></p>
            <?php
            }
            ?>
            <input type="submit" value="S'inscrire" id="connect">
            <p class="message active">Déjà un compte ? <a href="../index.php">Connectez-vous</a>.</p>
        </form>
    </main>

    <script src="js/script.js"></script>
</body>

</html>