<?php
session_start();
if (!isset($_SESSION['connectedId']) || (isset($_GET['specificId']) && $_SESSION['role'] !== 1)) {
    header("Location: ../index.php");
    exit();
}


$id = $_SESSION['connectedId'];
if (isset($_GET['specificId']) && $_SESSION['role'] === 1) {
    $id = $_GET['specificId'];
}

$database = new mysqli("localhost", "root", "", "si_aida_covid");

if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

var_dump($_POST["file"]);
var_dump($_FILES);

if (isset($_POST["file"])) {

    $filename = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        while (($getData = fgetcsv($file, 10000, ";", ";")) !== FALSE) {
            $sql = "INSERT into quantiteeau (dateConso, jour, lastheureDebut, nbReleve, heureFin, consoInit, consoFinale, idPersonne) 
                   values ('" . $getData[0] . "','" . $getData[1] . "','" . $getData[2] . "','" . $getData[3] . "','" . $getData[4] . "','" . $_GET['specificId'] . ")";
                   echo $sql;
            $result = $database->query($sql);
            if (!isset($result)) {
                echo "<script type=\"text/javascript\">
              alert(\"Invalid File:Please Upload CSV File.\");
              window.location = \"index.php\"
              </script>";
            } else {
                echo "<script type=\"text/javascript\">
            alert(\"CSV File has been successfully Imported.\");
            window.location = \"index.php\"
          </script>";
            }
        }

        fclose($file);
    }
}

$database->set_charset("UTF8");
header('Content-type: text/html; charset=utf-8');

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
    <title>Page membre</title>
</head>

<body>

    <header>
        <ul>
            <?php if ($_SESSION['role'] === 1 && isset($_GET['specificId'])) {
            ?>
                <li class="return">
                    <a href="../admin/index.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#071768" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </a>
                </li>
            <?php
            }
            ?>
            <li class="disconnect">
                <svg onclick="disconnect()" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#071768" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                    <line x1="12" y1="2" x2="12" y2="12"></line>
                </svg>
            </li>
        </ul>
    </header>

    <main>
        <div class="informations">
            <h2>Vos informations</h2>

        </div>

        <div class="donnees">
            <h2>Ajouter des données</h2>
            <form method="POST" action="../utils/functions.php">
                <input type="file" name="file" id="file">
                <input type="submit" value="Ajouter les données" class="connect">
            </form>
        </div>
    </main>
    <script src="js/script.js"></script>
</body>

</html>