<?php
require_once("functions.php");

//déjà connecté, on renvoie vers la page de profil
if (isset($_SESSION["player"])) 
{
	header("Location: profile.php");
	exit();
}

//ONFRAY Matthieu www.cardfightersdb.com
afficher_entete("Register");
$inscription_faite = false;

//si la protection spambot est vérifiée
if (isset($_POST["bot"]))
{
	if ($_SESSION["captcha"] == $_POST["bot"])
	{
		if (! empty($_POST['bot']) && ! empty($_POST['email']) && ! empty($_POST['pseudo']))
		{
			//géo localise le visiteur
			/*$lieu_visiteur = geo_localiser();
			//trouvé !
			if (isset($lieu_visiteur))
			{
				$pays = $lieu_visiteur->country_name;
				$ville = $lieu_visiteur->city;
			}
			else
			{
				$pays = "null";
				$ville = "null";
			}*/
			
			$pays = null;
			$ville = null;
			
			//mdp aléatoire 
			$mdp = generer_motdepasse();

			//nouvel id: pour sqlite
			$req_id = "SELECT MAX(id) FROM " . TABLE_PERSONNAL;
			$res_id = sql_fetch_array(sql_query($req_id));
			$nouvel_id = intval($res_id[0]) + 1;
			//sauve la date de connexion dans le profil et l'IP
			$date_jour = date("Y-m-d");
			//enregistre le nouvel user
			$req_insert = "INSERT INTO " . TABLE_PERSONNAL . " (id, pseudo, email, password, country, location,preferredgame,year,ip, lastseen) VALUES ($nouvel_id, \"". $_POST["pseudo"] . "\",\"" . $_POST["email"] . "\",\""  . md5($mdp) . "\",\"" . $pays . "\",\"" .$ville . "\", 1, 1980, \"" . $_SERVER['REMOTE_ADDR'] . "\", \"" . $date_jour . "\")";
			$res = sql_query($req_insert);
			//ça marche
			if ($res)
			{
				//préparation du mail à envoyer
				$sujet = "Welcome to " . NOM_SITE;
				$mail = $_POST["email"];
				$message = "Hi ". $_POST['pseudo'] . ",\r\nThank you for your interest about SNK vs Capcom Card Fighters Clash.\n\rPlease use your login name (" . $_POST['pseudo'] . ") and your password (" . $mdp . ") to login on my website " . URL_SITE . "\n\rHave fun !\n\r The webmaster";
				$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
				//envoyer l'email 
				$inscription_faite = emailog($mail,$sujet,$message,$header);
				if ($inscription_faite) echo "<p class=\"text-success\">You're registered. A password has been sent to your email.</p> If any problem, <a href=\"contact.php\">contact me</a>.\n<br>Please <a href=\"login.php\">login</a>.";			
			}
			else
			{
				echo "<p class=\"text-danger\">An error occurred.</p>";
				//préparation du mail à envoyer
				$sujet = "Bug sur le site " . NOM_SITE;
				$mail = MAIL_WEBMASTER;
				$message = "Un membre n'a pas pu s'inscrire sur " . NOM_SITE . "!\n\rLe pseudo était : " . $_POST['pseudo'] . " (" . $_POST["email"] . ")\n\rLa requête était : $req_insert\n\rUrl du site : " . URL_SITE . "\n\rL'erreur SQL est: " . sql_error();
				$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
				//envoyer l'email 
				if (emailog($mail,$sujet,$message,$header,0)) echo "<p class=\"text-danger\">The webmaster has been warned.</p>";
			}			
		}
	}
	//captcha faux
	else echo "<p class=\"text-danger\">WHOOPS, the captcha is wrong !</p>";
}

if (! $inscription_faite)
{
	echo "<html><body>";
	//affiche le formulaire d'inscription
	echo "<form method=post>";
	
	echo "Your pseudo : to login and identify to others players<br><input type=text name=pseudo size=15 value=\"";
	if (isset($_POST["pseudo"])) echo $_POST["pseudo"];
	echo "\"><br>";
	
	echo "Your email : you'll receive your password here and it will be useful to send and receive messages to/from others players<br><input type=text name=email size=50 value=\"";
	if (isset($_POST["email"])) echo $_POST["email"];
	echo "\"><br>";
	
	//protection par captcha	
	echo "<p class=\"text-warning\">Spambot protection ! Please enter this word : <b>" . creer_captcha() . "</b></p>";
	echo " <input type=text name=bot size=10>";
	echo "<br><br><button class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-new-window\"></span> Register</button></form>";
}
	
afficher_bas();
?>
