<?php
require_once("functions.php");
//ONFRAY Matthieu www.cardfightersdb.com

//remonte les infos de la carte demandée
$req = "SELECT * FROM " . TABLE_A . " WHERE id LIKE '" . $_GET['id'] . "' AND idgame=" . $_SESSION["svc"];
$res = sql_query($req);
if (@sql_num_rows($res) == 0) 
{
	afficher_entete("error");
	echo "<p class=\"text-danger\">An error occurred, the webmaster has been warned.</p>";
	//préparation du mail à envoyer
	$sujet = "Bug sur le site " . NOM_SITE;
	$mail = MAIL_WEBMASTER;
	$message = "Une carte d'action inconnue a été cherchée sur " . NOM_SITE . " !\n\rLa requête était : $req\n\rUrl du site : http://" . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
	//envoyer l'email 
	if (DEBUG===true) emailog($mail,$sujet,$message,$header,1);
}
else
{
	$carte = sql_fetch_array($res);
	//prépare des variables pour les cartes de REACTION
	if (isset($_GET["reaction"])) $re_titre="re";
	else $re_titre=null;
	afficher_entete(retourner_nom_jeu($_SESSION["svc"]) . " > " . $re_titre . "action > " . $carte["capacite"]);
	echo "<img src=\"cards/".$_SESSION["svc"] . "/".$carte["image"]."\">";
	//RARETE
	echo "<br>\nRarity " . afficher_rarete($carte["rarete"]);
	echo "\n<br>Number " . $carte["id"];
	echo "<br>\n<font color=blue>SP</font> -". $carte['sp'];
	echo "<br>\n".capturer_type_capacite($carte['description_capacite']);
	echo "<br><br>\nAdvice : ". capturer_type_capacite(capturer_rarete($carte['commentaire']));
	afficher_proprietaires($carte["id"]);
	ajouter_commentaire($carte["id"]);
}	
//affichage du bas de page et chargement du script JS si besoin
afficher_bas("add.js");
?>
