<?php
require_once("functions.php");
//ONFRAY Matthieu www.cardfightersdb.com
	
afficher_entete("community");
echo "All the registered players !<br>";
$req = "SELECT * FROM " . TABLE_PERSONNAL;

//boucle sur tous les joueurs
$resb=sql_query($req);
while ($res = sql_fetch_array($resb))
{
	echo " " . recuperer_gravatar($res["email"]) . " ";
	if (isset($_SESSION["player"])) 
	{
		//écrire aux autres membres
		if ($_SESSION["player"] != $res["id"]) echo " <a href=send.php?id=" . $res["id"] ." title=\"Send him a message\"> ";
		//modifier son profil
		else echo "<a href=edit_profile.php title=\"Hey it's you\"> ";
		echo $res["pseudo"] . " ";
		echo "</a>";
	}	
	if (! empty($res["location"]) && ! empty($res["country"])) echo " lives in " . $res["location"] . " <img src=\"country/" . str_replace(" ","_",$res["country"]) . ".png\" title=\"" . $res["country"] . "\">,";
	if ($res["year"]>0) echo " is about " . (date('Y') - $res["year"]) . " years old, ";
	if ($res["preferredgame"] == "none") echo " doesn't have a preferred game.";
	else echo "he prefers to play " . retourner_nom_jeu($res["preferredgame"]) . ". ";	
	echo "<br>\n";
	//fin boucle
}

if (! isset($_SESSION["player"])) echo "Don't have an account ? Please <a href=register.php>register</a>, you'll be able to send messages to others players.";
//@sql_free_result($resb);
afficher_bas();
?>
