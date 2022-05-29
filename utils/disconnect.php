<?php

session_start();
if (!isset($_SESSION['connectedId'])) {
    echo json_encode(["response" => "error", "message" => "Aucun compte connecté"]);
    die;
}


session_destroy();
echo json_encode(["response" => "success"]);
