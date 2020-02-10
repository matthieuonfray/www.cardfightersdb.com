<?php
// Initialisation de la session.
session_start();

// Détruit toutes les variables de session
$_SESSION = array();

// Finalement, on détruit la session.
session_destroy();

//supprime le cookie éventuel
setcookie("cookie_player", NULL, -1);
header("Location: index.php");
?>
