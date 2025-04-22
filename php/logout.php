<?php
session_start();
session_unset();
session_destroy();
header("Location: ../elegir.html");
session_write_close();
exit();
?>