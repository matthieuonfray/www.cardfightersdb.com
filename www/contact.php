<?php
require_once("functions.php");
afficher_entete("contact");
$mail_envoye = false;

//si la protection spambot est vérifiée
if (isset($_POST["bot"]))
{
	if ($_SESSION["captcha"] == $_POST["bot"])
	{
		if (! empty($_POST['bot']) && ! empty($_POST['email']) && ! empty($_POST['texte']))
		{
			//connexion pour préparer un enregistrement en base
			//préparation du mail à envoyer
			$message = $_POST["texte"];
			if (isset($_SESSION["player"])) $header = "From: " . $_SESSION["playername"] . "<" . $_POST["email"] . ">\r\n";
			else $header = "From: " . $_POST["email"] . "\r\n";
			//envoyer l'email 
			$mail_envoye = emailog(MAIL_WEBMASTER,"Message reçu depuis le site " . NOM_SITE,$message,$header);
			if ($mail_envoye) echo "<p class=\"text-success\">Email sent. Thank You 4 Your Interest.</p>";
		}
	}
	//captcha faux
	else echo "<p class=\"text-danger\">The captcha is wrong !</p>";
}

if (! $mail_envoye)
{
	echo "Hi, i'm the webmaster of " . NOM_SITE . "<br>To send me a message, use this form.";
	//pour le captcha
	//affiche le formulaire de contact
	echo "<form method=post>Your email<br><input class=\"form-control\" type=text name=email size=45 value=\"";
	//écrit l'email du membre dans le champs
	if (isset($_SESSION["playermail"])) echo $_SESSION["playermail"];
	//ou le mail saisi
	elseif (isset($_POST["email"])) echo $_POST["email"];
	echo "\"><br>Your message<br><textarea cols=90 rows=10 name=texte>";
	if (isset($_POST["texte"])) echo $_POST["texte"];
	echo "</textarea><p class=\"text-warning\">Spambot protection ! Please enter this word : <b>" . creer_captcha() . "</b></p>";
	echo " <input type=text name=bot size=10>";
	echo "<br><br><button id=\"submit\" class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-envelope\"></span> Send</button></form>";
}
	
afficher_bas();
?>
