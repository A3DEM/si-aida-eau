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

$database = new mysqli("localhost", "root", "", "si_aida_eau");

if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

if (isset($_POST["import"])) {

    $filename = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        while (($getData = fgetcsv($file, 10000, ";", ";")) !== FALSE) {
            $sql = "INSERT into quantiteeau (dateConso, jour, heureDebut, nbReleve, heureFin, consoInit, consoFinale, idPersonne) 
                   values ('" . $getData[0] . "','" . $getData[1] . "','" . $getData[2] . "','" . $getData[3] . "','" . $getData[4] . "','" . $getData[5] . "','" . $getData[6] . "','" . $_GET['specificId'] . "')";
            $result = $database->query($sql);
        }

        fclose($file);
    }
}

$database->set_charset("UTF8");

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
    <script src="js/script.js"></script>

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
            <?php

            $id = ($_SESSION["role"] == 1) ? $_GET["specificId"] : $_SESSION["connectedId"];

            $limiteDebut = (new DateTime("today"))->setTime(0, 30, 0);
            $limietFin = (new DateTime("today"))->setTime(23, 30, 0);

            $previousDate;
            $nbDate = 1;

            $nbLun = 0;
            $nbMar = 0;
            $nbMer = 0;
            $nbJeu = 0;
            $nbVen = 0;
            $nbSam = 0;
            $nbDim = 0;

            $moyLun = 0;
            $moyMar = 0;
            $moyMer = 0;
            $moyJeu = 0;
            $moyVen = 0;
            $moySam = 0;
            $moyDim = 0;

            $nbJours = 0;
            $nbJoursManquants = 0;

            $chartLabels = [];
            $chartDatas = [];

            $nbLitres = 0;

            $requestInformations = $database->prepare("SELECT dateConso, jour, heureDebut, nbReleve, heureFin, consoInit, consoFinale, (consoFinale - consoInit) AS ecart FROM `quantiteeau` WHERE idPersonne = ?");
            $requestInformations->bind_param('i', $id);
            $requestInformations->execute();
            $requestInformations->bind_result($dateConso, $jour, $heureDebut, $nbReleve, $heureFin, $consoInit, $consoFinale, $ecart);

            while ($requestInformations->fetch()) {

                if ($nbDate === 1) {

            ?>

                    <table>
                        <tr>
                            <th>Date de consommation</th>
                            <th>Jour dans la semaine</th>
                            <th>Heure de début</th>
                            <th>Nombre de relevés</th>
                            <th>Heure de fin</th>
                            <th>Consommaition initiale</th>
                            <th>Consommaition finale</th>
                            <th>Écart</th>
                        </tr>

                        <?php

                    }

                    $nbJours++;
                    $chartLabels[] = $dateConso;
                    $chartDatas[] = $ecart;
                    $nbLitres += $ecart;

                    $heureDebut = date_create_from_format("G:i:s", $heureDebut);
                    $heureFin = date_create_from_format("G:i:s", $heureFin);
                    $diffDebut = $heureDebut < $limiteDebut;
                    $diffFin = $heureFin > $limietFin;

                    // Calculs des moyennes journalières 

                    switch ($jour) {
                        case 2:
                            $moyLun += $ecart;
                            $nbLun++;
                            break;
                        case 3:
                            $moyMar += $ecart;
                            $nbMar++;
                            break;
                        case 4:
                            $moyMer += $ecart;
                            $nbMer++;
                            break;
                        case 5:
                            $moyJeu += $ecart;
                            $nbJeu++;
                            break;
                        case 6:
                            $moyVen += $ecart;
                            $nbVen++;
                            break;
                        case 7:
                            $moySam += $ecart;
                            $nbSam++;
                            break;
                        case 1:
                            $moyDim += $ecart;
                            $nbDim++;
                            break;
                    }

                    $currentDate = date_create_from_format("d/m/Y", $dateConso);

                    if ($nbDate > 1) {

                        $previousDateTest = $previousDate->modify("+1 day");

                        if ($previousDateTest->format('d/m/Y') !== $currentDate->format('d/m/Y')) {
                            $diffDate = $previousDate->diff($currentDate, true)->d;
                            $nbJoursManquants += $diffDate;
                            $previousJour++;


                            for ($i = 0; $i < $diffDate; $i++) {
                        ?>

                                <tr>
                                    <td class="missing"><?php echo $previousDate->format('d/m/Y'); ?></td>
                                    <td class="missing">
                                        <?php

                                        if ($previousJour % 8 == 0) {
                                            echo $previousJour = 1;
                                            $previousJour++;
                                        } else {
                                            echo $previousJour;
                                            $previousJour++;
                                        }
                                        ?>
                                    </td>
                                    <td class="missing">0</td>
                                    <td class="missing">0</td>
                                    <td class="missing">0</td>
                                    <td class="missing">0</td>
                                    <td class="missing">0</td>
                                    <td class="missing">0</td>
                                </tr>

                    <?php
                                $previousDate = $previousDate->modify("+1 day");
                            }
                        }
                        $previousJour = $jour;
                    }

                    $nbDate++;
                    $previousDate = $currentDate;
                    $previousJour = $jour;

                    ?>

                    <tr>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $dateConso; ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $jour; ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $heureDebut->format('H:i:s'); ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $nbReleve; ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $heureFin->format('H:i:s'); ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $consoInit; ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $consoFinale; ?></td>
                        <td class="<?php if (!$diffDebut || !$diffFin) {
                                        echo "alert";
                                    } ?>"><?php echo $ecart; ?></td>
                    </tr>
                <?php

            }

                ?>
                    </table>

                    <?php

                    if ($requestInformations->num_rows !== 0) {

                    ?>
                        <h3>Moyennes journalières</h3>
                        <table>
                            <tr>
                                <th>Lundi</th>
                                <th>Mardi</th>
                                <th>Mercredi</th>
                                <th>Jeudi</th>
                                <th>Vendredi</th>
                                <th>Samedi</th>
                                <th>Dimanche</th>
                            </tr>
                            <tr>
                                <td><?php echo round($moyLun / $nbLun); ?></td>
                                <td><?php echo round($moyMar / $nbMar); ?></td>
                                <td><?php echo round($moyMer / $nbMer); ?></td>
                                <td><?php echo round($moyJeu / $nbJeu); ?></td>
                                <td><?php echo round($moyVen / $nbVen); ?></td>
                                <td><?php echo round($moySam / $nbSam); ?></td>
                                <td><?php echo round($moyDim / $nbDim); ?></td>
                            </tr>
                        </table>
                        <h3>Pourcentage de jours manquants</h3>

                        <?php
                        $percentage = round($nbJoursManquants / ($nbJours + $nbJoursManquants) * 100, 2);
                        ?>
                        <h4><?php echo $percentage . " %"; ?></h4>
                        <h3>Graphique de consommation complète</h3>
                        <canvas id="myChart" style="position: relative; height:40vh; width:70vw"></canvas>
                        <h3>Facture</h3>
                        <h4><?php echo $nbLitres * 0.00345; ?> €</h4>
                    <?php

                    } else {
                    ?>

                        <h3>Votre fournisseur n'a pas ajouté vos informations</h3>

                    <?php
                    }

                    ?>
        </div>

        <div class="donnees">
            <h2>Ajouter des données</h2>
            <form method="POST" action="<?php $_SERVER['PHP_SELF'] . '?specificId=' . $_GET['specificId'] ?>" name="upload_excel" enctype="multipart/form-data">
                <input type="file" name="file" id="file">
                <input type="submit" name="import" value="Ajouter les données" class="connect">
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Consommation d\'eau',
                    data: <?php echo json_encode($chartDatas); ?>,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.35
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>