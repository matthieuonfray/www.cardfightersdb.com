<?php
require_once("functions.php");

//ONFRAY Matthieu www.cardfightersdb.com

//pas encore connecté
if (! isset($_SESSION["player"])) 
{
	header("Location: login.php");
	exit();
}

$req = "SELECT email, pseudo FROM " . TABLE_PERSONNAL . " WHERE id=" . $_GET["id"];
$res = sql_query($req);
$res_player = sql_fetch_array($res);

afficher_entete("contact " . $res_player["pseudo"]);
$mail_envoye = false;

//si le formulaire est soumis
if (isset($_POST["bot"]))
{
		if (! empty($_POST['bot']) && ! empty($_POST['texte']))
		{
			//préparation du mail à envoyer
			$sujet = "Message from " . $_SESSION["playername"] . " (" . NOM_SITE . ")";
			$mail = $res_player["email"];
			$message = $_POST["texte"];
			//c'est l'utilisateur courant qui écrit
			$header = "From: " . $_SESSION["playername"] . "<" . $_SESSION["playermail"] . ">\r\n";
			//envoyer l'email 
			if (emailog($mail,$sujet,$message,$header)) echo "<p class=\"text-success\">Email sent. </p>";
			$mail_envoye = true;
		}
		else echo "<p class=\"text-danger\">The message is empty !</p>";
}

if (! $mail_envoye)
{
	//affiche le formulaire de contact
	echo "<form method=post>Your message<br><textarea cols=90 rows=10 name=texte>";
	if (isset($_POST["texte"])) echo $_POST["texte"];
	echo "</textarea><input type=hidden name=bot value=true>";
	echo "<br><br><button class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-envelope\"></span> Send</button></form>";
}
//affichage du bas de page et chargement du script JS si besoin
afficher_bas();
?>