<?php
session_start();
if (!isset($_SESSION['connectedId'])) {
    header("Location: ../index.php");
    exit();
}


$database = new mysqli("localhost", "root", "", "si_aida_eau");

if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
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
    <title>Blog</title>
</head>

<body>
    <header>
        <ul>
            <li class="disconnect">
                <svg onclick="disconnect()" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#071768" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                    <line x1="12" y1="2" x2="12" y2="12"></line>
                </svg>
            </li>
        </ul>
    </header>

    <main>

        <div class="content">
            <h2>Membres</h2>
            <div class="personnes">

                <?php

                $postsPerPage = 6;

                $query = "SELECT nom, prenom, idPersonne FROM personne GROUP BY idPersonne";
                $numberOfPostsQuery = $database->query($query);
                $numberOfPosts = $numberOfPostsQuery->num_rows;
                $numberOfPages = (int)ceil($numberOfPosts / $postsPerPage);
                $currentPage = (int)($_GET['page'] ?? 1);
                ($currentPage < 1 || $currentPage > $numberOfPages) ? $currentPage = 1 : "";
                $offset = $postsPerPage * ($currentPage - 1);

                $query = "SELECT nom, prenom, idPersonne, adresse FROM personne GROUP BY idPersonne ORDER BY nom ASC LIMIT $postsPerPage OFFSET $offset";
                $requestPost = $database->query($query);

                if ($requestPost->num_rows !== 0) {

                    foreach ($requestPost as $row) {

                ?>
                        <a href=<?php echo "../membre/index.php?specificId=" . $row['idPersonne']; ?>>
                            <div class="personne">
                                <h3 class="name"><?php echo $row['nom'] . " " . $row['prenom']; ?></h3>
                                <hr>
                                <div class="infos">
                                    <?php
                                    echo $row['adresse'];
                                    $query  = "SELECT publication.idPublication, titre, YEAR(publishedAt), type.nom as nomDomaine FROM `publication`
                    INNER JOIN type ON publication.idType = type.idType
                    INNER JOIN publie ON publication.idPublication = publie.idPublication "
                                    ?>

                                </div>
                                <div class="info">
                                </div>
                            </div>
                        </a>

                <?php
                    }
                } else {
                    echo "<h3>Il n'y a pas de personne correspondant à votre recherche</h3>";
                }
                ?>
            </div>
            <div class="pagination">
                <?php

                for ($i = 1; $i <= $numberOfPages; $i++) {

                    $getFilters = "";
                    if (isset($_GET)) {
                        if (isset($_GET['age'])) {
                            $getFilters .= "&age=" . $_GET['age'];
                        }
                        if (isset($_GET['maladies'])) {
                            $getFilters .= "&maladies=" . $_GET['maladies'];
                        }
                        if (isset($_GET['doses'])) {
                            $getFilters .= "&doses=" . $_GET['doses'];
                        }
                    }
                ?>
                    <a href="<?php echo "./index.php?page=$i$getFilters"; ?>" <?php if ($currentPage == $i) {
                                                                                    echo "class='active'";
                                                                                } ?>><?php echo $i; ?></a>
                <?php
                }

                ?>
                <!-- <a class="active" href="#">1</a>
                <a href="#">2</a>
                <a href="#">3</a> -->
            </div>
        </div>
    </main>

    <script src="js/script.js"></script>
</body>

</html>