<?php
require_once("functions.php");
//ONFRAY Matthieu www.cardfightersdb.com

//remonte les infos de la carte demandée
$req = "SELECT * FROM " . TABLE_CHA . " WHERE id LIKE '" . $_GET['id'] . "' AND idgame=" . $_SESSION["svc"];
$res = sql_query($req);
if (@sql_num_rows($res) == 0) 
{	
	afficher_entete("error");
	echo "<p class=\"text-danger\">An error occurred, the webmaster has been warned.</p>";
	//préparation du mail à envoyer
	$sujet = "Bug sur le site " . NOM_SITE;
	$mail = MAIL_WEBMASTER;
	$message = "Une carte de personnage inconnue a été cherchée sur " . NOM_SITE . " !\n\rLa requête était : $req\n\rUrl du site : http://" . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
	//envoyer l'email 
	if (DEBUG===true) emailog($mail,$sujet,$message,$header,1);
}
else
{
	$carte = sql_fetch_array($res);
	//le TITRE de la page
	afficher_entete(retourner_nom_jeu($_SESSION["svc"]) . " > character > " . $carte['nom']);
	//la CARTE
	echo "\n<img src=cards/".$_SESSION["svc"] . "/". $carte["image"]." alt=\"" . $carte['nom'] . "\">";
	//VERSION
	echo "<br><img src=\"i/version/" . $carte['version'] . ".gif\" alt=\"" . $carte['version'] . "\">";
	//RARETE
	echo "<br>Rarity <img src=\"i/rarity/". $carte['rarete'] . ".gif\" alt=\"" . $carte['rarete'] . "\">";
	//NOM
	echo "<br>Number " . $carte['id'] ."<br><font color=red>BP</font> " . $carte['bp'] . "<br><font color=blue>SP</font> +". $carte['sp'];
	//CAPACITE
	if (strtolower($carte['capacite']) != "none")
	{
		echo "\n<br><br>" . afficher_type_capacite($carte['type_capacite']) . " " . $carte['capacite'];
		echo "\n<br>" . capturer_type_capacite($carte['description_capacite']);
	}
	//Backup
	$bckp = "";
	//parcours des backups pour les lier dans la mini fiche
	$persos = explode(",",$carte['backup']);
	for ($i=0; $i<count($persos); $i++)
	{
		if (! empty($bckp)) $bckp .= ", ";
		$req_perso = "SELECT id FROM " . TABLE_CHA . " WHERE nom LIKE '" . addslashes(trim($persos[$i])) . "' AND idgame=" . $_SESSION["svc"];
		//echo $req_perso;
		$res_perso = sql_query($req_perso);
		//recherche le backup
		if (sql_num_rows($res_perso) > 0) //perso trouvé
		{
			$res_perso_n = sql_fetch_array($res_perso) ;
			$bckp .= creer_url_characters($res_perso_n['id'],trim($persos[$i]),">") . trim($persos[$i]) . "</a>";
		}
		else 
			{
				//enregistre dans la liste des backup, à la suite
				$bckp .= trim($persos[$i]);
				//si ce n'est pas "None", c'est que le nom est foireux
				if (strtolower(trim($persos[$i])) != "none")
				{
					//préparation d'un mailog d'erreur
					$mail = MAIL_WEBMASTER;
					$sujet = "Bug sur le site (backup) " . NOM_SITE;
					$header = "From: webmaster <" . MAIL_JUNK . ">\r\n";
					$message = "La carte " . $carte['nom'] . " (" . $carte['id'] . ") du jeu " . $_SESSION["svc"] . " a un backup foireux: " . trim($persos[$i]);
					//envoyer l'email 
					emailog($mail,$sujet,$message,$header,0);
				}
			}
	}
	
	echo "\n<br>Backup : " . $bckp;
	if ($carte['exclu']) echo "\n<br><br><font color=red>Exclu: " . $carte['exclu']. " ONLY</font>";
	echo "<br><br>\nAdvice : ". capturer_type_capacite(capturer_rarete($carte['commentaire']));
	afficher_proprietaires($carte["id"]);
	ajouter_commentaire($carte["id"]);
}	
//affichage du bas de page et chargement du script JS si besoin
afficher_bas("add.js");
?>
