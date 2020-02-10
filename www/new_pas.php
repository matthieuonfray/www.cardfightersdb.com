<?php
require_once("functions.php");
//déjà connecté, on renvoie vers la page de profil
if (isset($_SESSION["player"])) 
{
	header("Location: profile.php");
	exit();
}

//ONFRAY Matthieu www.cardfightersdb.com

//pas connecté
if (empty($_POST['mail']))
{
	afficher_entete("Password Lost ?");
	//erreur de login : mauvais couple login/passwd
	if (isset($_GET["err"])) echo "<p class=\"text-danger\">Email unknow, dude.</p>"; 
	//formulaire
	echo "<form method=post>Your email<br><input type=text name=mail><br><button class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-envelope\"></span> Go get it</button></form>";
	afficher_bas();
}
else
{
	//vérification de l'email dans la base
	$req = "SELECT pseudo, id FROM " . TABLE_PERSONNAL . " WHERE email LIKE '" . $_POST['mail'] . "'";
	$res = sql_query($req);
	//echo "<br>debug: num_rows= " . sql_num_rows($res) . "<br>";
	if (sql_num_rows($res) == 1)
	{
		$res_player = sql_fetch_array($res);		
		afficher_entete("New password");
		//mdp aléatoire 
		$mdp = generer_motdepasse();
		//met à jour le mdp
		$req = "UPDATE " . TABLE_PERSONNAL . " SET password='" . md5($mdp) . "' WHERE id=". $res_player["id"];
		$maj_pass = sql_query($req);
		if ($maj_pass) 
		{
			//préparation du mail à envoyer
			$sujet = "Welcome back to " . NOM_SITE;
			$mail = $_POST['mail'];
			$message = "Hi ". $res_player['pseudo'] . ",\r\nThank you for your interest about SNK vs Capcom Card Fighters Clash.\n\rPlease use your login name (" .$res_player['pseudo'] . ") and your password (" . $mdp . ") to login on my website " . URL_SITE . "\n\rHave fun !\n\r The webmaster";
			$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
			//envoyer l'email 
			emailog($mail,$sujet,$message,$header);
			echo "<p class=\"text-success\">A new password has been sent to your email.</p> If any problem occurs, <a href=\"contact.php\">contact me</a>.\n<br>Please <a href=\"login.php\">login</a>.";					
		}
		else 
		{
			echo "<p class=\"text-danger\">An error occurred, the webmaster has been warned.</p>";
			//préparation du mail à envoyer
			$sujet = "Bug sur le site "  . NOM_SITE;
			$mail = $MAIL_WEBMASTER;
			$message = "Un membre n'a pas obtenir un nouveau mot de passe depuis " . NOM_SITE . " !\n\rLa requête était : $req\n\rUrl du site : " .URL_SITE;
			$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
			//envoyer l'email 
			emailog($mail,$sujet,$message,$header,1);
		}	
		//affichage du bas de page et chargement du script JS si besoin
		afficher_bas();		
	}
	//pour tous les cas foireux, renvoie vers la page
	else header("Location: new_pas.php?err=true");
}
?>
