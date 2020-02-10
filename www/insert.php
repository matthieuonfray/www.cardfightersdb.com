<?php
require_once("functions.php");

//pas encore connecté
if (! isset($_SESSION["player"])) 
{
	header("Location: login.php");
	exit();
}

//ONFRAY Matthieu www.cardfightersdb.com
//variables
$carte = $_POST["idcard"];
$player = $_SESSION["player"];

//vérifie l'existence en base
$req = "SELECT * FROM " . TABLE_SNK_MISS . " WHERE idplayer = ". $player. " AND idcard LIKE '" . $carte . "' AND idgame=" . $_SESSION["svc"];
$res = @sql_query($req);
if (sql_num_rows($res) == 0) 
{
	//INSERTION
	$req = "INSERT INTO '" . TABLE_SNK_MISS . "' ('idplayer', 'idcard', 'idgame') VALUES (" . $player . ",\"" . $carte . "\", " . $_SESSION["svc"] . ")";
	//echo $req;
	echo "$carte added";
}
else
{
	//EFFACEMENT
	$req = "DELETE FROM '" . TABLE_SNK_MISS . "' WHERE idplayer=" . $player . " AND idcard LIKE \"" . $carte . "\" AND idgame=" . $_SESSION["svc"];
	echo "$carte removed";
	//echo $req;
}
//exécution de la requête
$res = sql_query($req);

?>
