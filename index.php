<?php
function processConnexion()
{
    if ($_POST["username"] === '' || $_POST["password"] === '' || !isset($_POST["username"]) || !isset($_POST["password"])) {
        $message = "Veuillez remplir les deux champs";
        return $message;
    }

    $database = new mysqli("localhost", "root", "", "si_aida_eau");

    if ($database->connect_error) {
        $message = "Veuillez remplir les deux champs";
        return $message;
    }

    $request = $database->prepare("SELECT role, idPersonne FROM personne WHERE identifiant=? AND motdepasse=?");
    $request->bind_param('ss', $_POST['username'], $_POST['password']);

    $request->execute();
    $request->bind_result($role, $userId);
    $request->fetch();

    if (isset($userId)) {
        session_start();
        $_SESSION['connectedId'] = $userId;
        $_SESSION['role'] = $role;
        var_dump($role);
        if ($role === 1) {
            header("Location: admin/index.php");
        } else {
            header("Location: membre/index.php");
        }
    } else {
        $message = "Identifiants incorrects";
        return $message;
    }
}

if (isset($_POST["connect"])) {
    $message = processConnexion();
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
    <title>Connexion</title>
</head>

<body>
    <form class="login" method="post">
        <h1>Se connecter</h1>
        <div>
            <label for="username">Nom d'utilisateur</label>
            <input name="username" id="username" type="text">
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input name="password" id="password" type="password">
        </div>
        <p class="inscription">Pas encore de compte ? <a href="./signin/index.php">Inscrivez vous !</a></p>
        <p class="message" <?php if (isset($message)) echo 'style="display: initial !important"' ?>><?php if (isset($message)) echo $message; ?></p>
        <input type="submit" id="connect" name="connect" value="Se connecter" />
    </form>
</body>

</html>