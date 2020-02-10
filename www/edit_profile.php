<?php
require_once("functions.php");

//ONFRAY Matthieu www.cardfightersdb.com

//pas encore connecté
if (! isset($_SESSION["player"])) 
{
	header("Location: login.php"); 
	exit();
}

if (isset($_POST["validate"]))
{
	//mise à jour des données
	$req = "UPDATE ". TABLE_PERSONNAL . " SET email='" . $_POST["email"] ."', country='" . $_POST["pays"] . "', location='" . $_POST["ville"] . "', year='" . $_POST["annee"] . "', preferredgame='" . $_POST["preferredgame"] . "' WHERE id=". $_SESSION["player"];
	$res = sql_query($req);
	if ($res) header("Location: profile.php");
	else
	{
		afficher_entete("error");
		echo "<p class=\"text-danger\">WHOOPS, an error occurred, the webmaster has been warned.</p>";
		//préparation du mail à envoyer
		$sujet = "Bug sur le site $NOM_SITE";
		$mail = MAIL_WEBMASTER;
		$message = "Un membre n'a pas pu mettre à jour ses données sur " . NOM_SITE . " !\n\rLa requête était : $req\n\rUrl du site : http://" . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
		//envoyer l'email 
		emailog($mail,$sujet,$message,$header,1);
		//affichage du bas de page et chargement du script JS si besoin
		afficher_bas();
	}			
}
else
{
	$req = "SELECT * FROM " . TABLE_PERSONNAL . " WHERE id= " . $_SESSION["player"];
	$res_player = sql_fetch_array(sql_query($req));
	afficher_entete("Edit your profile");
	echo "<form method=post>";
	echo "Your email : you'll receive your password here and it will be useful to send and receive messages from community. Your email will always be hidden.<br><input type=text name=email size=50 value=\"";
	if (isset($res_player["email"])) echo $res_player["email"];
	echo "\"><br>";
			
	echo "\nYour country : to be displayed on a map and find local players to play with<br>";
	//on va générer une liste déroulante de tous les pays
	$lines = @file('country/country_list.txt');
	$pays_desire = $res_player["country"];
	//écrit les pays
	echo "<select name=pays>";
	foreach ($lines as $lineNumber => $ligne) echo "<option " . marquer_champs(trim($ligne), $pays_desire) . ">" . trim($ligne) . "</option>\n";
	echo "</select><br>";

	echo "\nYour location<br><input type=text name=ville size=45 value=\"";
	echo $res_player["location"];
	echo "\"><br>";

	echo "\nYear of birth : to display your age on your profile (i'm not the NSA, be confident)<br><select name=annee>";
	//année de naissance liste déroulante
	for ($i=1950; $i<date('Y'); $i++) echo "<option " . marquer_champs($i, $res_player["year"]) . ">" . $i . "</option>\n";
	echo "</select><br>";

	echo "\nPreferred game ? Select one !<br><select name=preferredgame>";
	//versions de jeux en liste déroulante
	echo "<option " . marquer_champs("none", $res_player["preferredgame"]) . ">None</option>\n";
	echo "<option " . marquer_champs("1", $res_player["preferredgame"]) . ">" . retourner_nom_jeu(1) . "</option>\n";
	echo "<option " . marquer_champs("2", $res_player["preferredgame"]) . ">" . retourner_nom_jeu(2) . "</option>\n";
	echo "<option " . marquer_champs("3", $res_player["preferredgame"]) . ">" . retourner_nom_jeu(3) . "</option>\n";
	echo "</select><br>";

	echo "<br><input type=hidden name=validate value=true><button class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-edit\"></span> Update</button></form>";
	afficher_bas();
}

?>