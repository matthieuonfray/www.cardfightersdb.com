<?php
require("functions.php");

//ONFRAY Matthieu www.cardfightersdb.com

//pas encore connecté
if (! isset($_SESSION["player"])) 
{
	header("Location: login.php"); 
	exit();
}


afficher_entete("Your profile");

$req = "SELECT * FROM " . TABLE_PERSONNAL . " WHERE id=" . $_SESSION["player"];
$res = sql_fetch_array(sql_query($req));
echo "Hi " . $_SESSION["playername"] . " !";
echo "<br>\n";
//affichage des pays et ville si disponibles
if (isset($res["location"]) && isset($res["country"])) 
	if (! empty($res["location"]) && !empty($res["country"])) echo "You live in " . $res["location"] . " <img src=\"country/" . str_replace(" ","_",$res["country"]) . ".png\" title=\"" . $res["country"] . "\"><br>\n";
//année de naissance
if (isset($res["year"]) && $res["year"]>0) echo "You are about " . (date('Y') - $res["year"]) . " years old.<br>\n";
//email
echo "Your email is " . $res["email"] . " (hidden to others).<br>\n";
//jeu préféré
if ($res["preferredgame"] == "none") echo "You don't have a preferred game.";
else echo "Your preferred game is " . retourner_nom_jeu($res["preferredgame"]) . ".";
echo "<br><br>\n";
echo "To share your location, show your age, etc<br><a href=edit_profile.php><button class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-edit\"></span> Complete your profile</button></a>\n";

//log out
echo "<br><br> <a href=\"logout.php\"><span class=\"glyphicon glyphicon-log-out\"></span> Logout</a>";
afficher_bas();
?>