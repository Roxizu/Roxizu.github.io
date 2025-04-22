<?php
require_once 'php/session.php';

// Specify which roles can access this page
verificarSesion(['cliente', 'admin']);

// Rest of your page code...
?>