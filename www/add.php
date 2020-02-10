<?php
require_once("functions.php");

//pas encore connecté
if (! isset($_SESSION["player"])) 
{
	header("Location: login.php");
	exit();
}


//variables
$carte = $_POST["idcard"];
$comment = $_POST["comment"];
$player = $_SESSION["player"];
$game = $_SESSION["svc"];

//première vérification : Capcom ou SNK ? ou Action ?
switch (substr($carte,0,1))
{
	case "C" : $table  = TABLE_CHA; $type_carte = "characters"; break;
	case "S" : $table = TABLE_CHA; $type_carte = "characters"; break;
	case "A" : $table = TABLE_A; $type_carte = "action"; break;
	default : $table = ""; echo "$carte inconnue"; break;
}

if ($table != "" && $comment != "")
{
	//vérifie l'existence en base
	$req = "SELECT * FROM " . $table . " WHERE id LIKE '" . $carte . "' AND idgame=" . $game;
	$res = sql_query($req);
	if (@sql_num_rows($res) == 0) 
	{
		//deuxième vérification : introuvable, erreur !
		echo "$carte inexistante";
	}
	else
	{
		//trouvée : insertion du commentaire
		$req = "INSERT INTO " . TABLE_SNK_COMMENT . " (idplayer, idcard, comment, idgame, ip) VALUES (" .  $player . ", '" . $carte . "', '" . addslashes($comment) . "', " . $game . ", '" . $_SERVER['REMOTE_ADDR'] . "')";
		$res = sql_query($req);
		echo "You've just said : " . capturer_type_capacite(capturer_rarete($comment));
		//préparation du mail à envoyer
		$sujet = "Nouveau commentaire sur " . NOM_SITE;
		$mail = MAIL_WEBMASTER;
		$message = "Un nouveau commentaire a été ajouté sur la carte $carte : $comment\n\rUrl du site : http://" . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
		//envoyer l'email 
		emailog($mail,$sujet,$message,$header,0);
	}
}
?>