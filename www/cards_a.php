<?php
require("functions.php");
//prépare des variables pour les cartes de REACTION
if (isset($_GET["reaction"])) 
{
	$not_req = null;
	$re_url = true;
	$re_titre = "re";
}
else 
{
	//pour les cartes ACTION
	$not_req = "NOT";
	$re_url = false;
	$re_titre = null;
}

afficher_entete(retourner_nom_jeu($_SESSION["svc"]) . " > " . $re_titre . "action");

//liste de boutons
if (isset($_GET['order'])) $tri=$_GET['order'];
else $tri="id";
//liste de boutons
echo "<div class=\"btn-group\">";
echo marquer_bouton("Number", "id", $tri, $re_titre."action");
echo marquer_bouton("SP", "sp", $tri, $re_titre."action");
echo marquer_bouton("Rarity", "rarete", $tri, $re_titre."action");
echo marquer_bouton("Name", "capacite", $tri, $re_titre."action");
echo "</div>";
//fin des boutons
echo "<br>\n";

//TOUTES LES CARTES PAR LEURS IMAGES


$req = "SELECT * FROM " . TABLE_A . " WHERE idgame=" . $_SESSION["svc"] . " AND id $not_req LIKE 'R%' ORDER BY " . $tri . ", id";
$res = sql_query($req);

while ($carte = @sql_fetch_array($res))
{
	//RARETE
	$cont = "\nRarity " . afficher_rarete($carte["rarete"]);
	//CAPACITE
	$cont .= "<br>Number " . $carte['id'] ."<br><font color=blue>SP</font> -". $carte['sp'];
	//DETAIL		
	$cont .= "<br>\n".capturer_type_capacite($carte['description_capacite']);
	echo creer_url_action($carte["id"], $carte['capacite'], null, $re_url) . " data-toggle=\"popover\" title=\"" . $carte['capacite'] . "\" data-content=\"$cont\"><img border=0 src=\"cards/" . $_SESSION["svc"] . "/". $carte['image'] . "\"></a>";
}

//affichage du bas de page et chargement du script JS si besoin
afficher_bas("popover.js");
?>