<?php
require_once 'php/session.php';

// Specify which roles can access this page
verificarSesion(['empresa', 'admin']);

// Rest of your page code...
?>