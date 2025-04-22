<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$response = [
    'logged_in' => isset($_SESSION['logged_in']) && $_SESSION['logged_in'],
    'rol' => isset($_SESSION['rol']) ? $_SESSION['rol'] : null
];
echo json_encode($response);
exit();
?>