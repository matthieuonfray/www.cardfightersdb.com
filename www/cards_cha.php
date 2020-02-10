<?php
require("functions.php");
//ONFRAY Matthieu www.cardfightersdb.com

//configure le tri
if (isset($_GET['order'])) $tri=$_GET['order'];
else $tri="id";

//gestion entre toutes les cartes et exclusives
if (isset($_GET["exclusive"])) $url_btn ="exclusive";
else $url_btn = "characters";
afficher_entete(retourner_nom_jeu($_SESSION["svc"]) . " > " . $url_btn);

//liste de boutons
echo "<div class=\"btn-group\">";
echo marquer_bouton("Number", "id", $tri,$url_btn);
echo marquer_bouton("Version", "version", $tri,$url_btn);
echo marquer_bouton("BP", "bp", $tri,$url_btn);
echo marquer_bouton("SP", "sp", $tri,$url_btn);
echo marquer_bouton("Rarity", "rarete", $tri,$url_btn);
echo marquer_bouton("Name", "nom", $tri,$url_btn);
echo marquer_bouton("Ability", "type_capacite", $tri,$url_btn);
echo "</div>";
//fin des boutons
echo "<br>\n";

//JUSTE LES EXCLU
if (isset($_GET["exclusive"])) $req = "SELECT * FROM " . TABLE_CHA . " WHERE exclu IS NOT NULL AND idgame=" . $_SESSION["svc"] . " ORDER BY " . $tri . ", id";
//TOUTES LES CARTES PAR LEURS IMAGES 
else $req = "SELECT * FROM " . TABLE_CHA . " WHERE idgame=" . $_SESSION["svc"] . " ORDER BY " . $tri . ", id";

$res = sql_query($req);
while ($carte = @sql_fetch_array($res))
{
	//VERSION
	$cont = "<img src=i/version/" . $carte['version'] . ".gif><br>";
	//RARETE
	$cont .= "Rarity <img src=i/rarity/". $carte['rarete'] . ".gif><br>";
	$cont .= "Number " . $carte['id'] ."<br><font color=red>BP</font> " . $carte['bp'] . "<br><font color=blue>SP</font> +". $carte['sp'];
	//CAPACITE
	if ($carte['capacite'] != "None")
	{
		$cont .= "\n<br><br>" . afficher_type_capacite($carte['type_capacite']) . " " . $carte['capacite'];
		$cont .= "\n<br>" . capturer_type_capacite($carte['description_capacite']);
	}
	$cont .= "\n<br><br>Backup : " . $carte['backup'];
	if ($carte['exclu']) $cont .= "\n<br><br><font color=red>Exclu: " . $carte['exclu']. " ONLY</font>";
	
	//affiche tout...
	echo creer_url_characters($carte["id"],$carte['nom']) . " data-toggle=\"popover\" title=\"" . $carte['nom'] . "\" data-content=\"$cont\"><img border=0 src=\"cards/" .  $_SESSION["svc"] . "/" . $carte['image'] . "\"></a>\n";
}

//affichage du bas de page et chargement du script JS si besoin
afficher_bas("popover.js");
?>
