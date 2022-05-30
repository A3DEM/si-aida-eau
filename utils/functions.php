<?php
if (isset($_POST["Import"])) {

    session_start();
    if (!isset($_SESSION['connectedId']) || $_SESSION['role'] !== 1) {
        header("Location: ../index.php");
        exit();
    }


    $id = $_SESSION['connectedId'];
    if (isset($_GET['specificId']) && $_SESSION['role'] === 1) {
        $id = $_GET['specificId'];
    }

    $database = new mysqli("localhost", "root", "", "si_aida_eau");

    if ($database->connect_error) {
        die("Connection failed: " . $database->connect_error);
    }

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
