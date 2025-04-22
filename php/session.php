<?php
session_start();

function iniciarSesion($email, $rol) {
    $_SESSION['email'] = $email;
    $_SESSION['rol'] = $rol;
    $_SESSION['logged_in'] = true;

    // Redirect based on role
    switch($rol) {
        case 'cliente':
            header("Location: ../index.html");
            break;
        case 'empresa':
            header("Location: ../perfil-empresa.html");
            break;
        default:
            header("Location: ../index.html");
    }
    exit();
}

function verificarSesion() {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header("Location: ../login 2.html");
       
        exit();
    }
}

function cerrarSesion() {
    session_unset();
    session_destroy();
    header("Location: ../index.html");
    exit();
}
?>