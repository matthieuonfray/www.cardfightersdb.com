<?php
require("functions.php");
//ONFRAY Matthieu www.cardfightersdb.com
afficher_entete(retourner_nom_jeu($_SESSION["svc"]) . " : stats");

function calculer_stats_cha($version)
{	
	echo "\n\n<br>";
	//REQUETES
	$req_cha_version = sql_fetch_array(sql_query("SELECT count(*) FROM " . TABLE_CHA . " WHERE version LIKE '$version' AND idgame=" . $_SESSION["svc"]));
	$req_cha_bp = sql_fetch_array(sql_query("SELECT MIN(bp), AVG(bp),MAX(bp), SUM(bp) FROM " . TABLE_CHA . " WHERE version LIKE '$version' AND idgame=" . $_SESSION["svc"]));
	$req_cha_sp = sql_fetch_array(sql_query("SELECT MIN(sp), AVG(sp),MAX(sp), SUM(sp) FROM " . TABLE_CHA . " WHERE version LIKE '$version' AND idgame=" . $_SESSION["svc"]));
	$req_cha_ability = sql_query("SELECT COUNT(*), type_capacite FROM " . TABLE_CHA . " WHERE version LIKE '$version' AND idgame=" . $_SESSION["svc"] . " GROUP BY type_capacite");
	$req_cha_rarity = sql_query("SELECT COUNT(*), rarete FROM " . TABLE_CHA . " WHERE version LIKE '$version' AND idgame=" . $_SESSION["svc"] . " GROUP BY rarete");
	$req_cha_bckp = sql_fetch_array(sql_query("SELECT COUNT(*) FROM " . TABLE_CHA . " WHERE version LIKE '$version' AND idgame=" . $_SESSION["svc"] . " AND (backup IS NULL OR backup LIKE 'None')"));
	//AFFICHAGES
	echo "\n<br><img src=i/version/" . $version . ".gif>";//logo
	echo "\n<br>Number of cards : " . $req_cha_version[0];
	echo "\n<br>BP (low / mid / high / sum) : " . $req_cha_bp[0] . " / " . number_format($req_cha_bp[1],2) . " / " . $req_cha_bp[2]. " / " . $req_cha_bp[3];
	echo "\n<br>SP (low / mid / high / sum)  : ". $req_cha_sp[0] . " / " . number_format($req_cha_sp[1],2) . " / " . $req_cha_sp[2] . " / " . $req_cha_sp[3];
	echo "\n<br>Abilities : ";
	while ($req_cha_abilities = sql_fetch_array($req_cha_ability))
	{
		if (! empty($req_cha_abilities[1])) echo $req_cha_abilities[0]  . afficher_type_capacite($req_cha_abilities[1]). " ";
	}
	echo "\n<br>Rarities : ";
		while ($req_cha_rarities = sql_fetch_array($req_cha_rarity)) 
	{
		echo $req_cha_rarities[0] . afficher_rarete($req_cha_rarities[1]). " ";
	}
	echo "\n<br>Cards without backup : " . $req_cha_bckp[0];
}


function calculer_stats_a($initiale)
{
	$res_req_a = sql_fetch_array(sql_query("SELECT COUNT(*) FROM " . TABLE_A . " WHERE idgame=" . $_SESSION["svc"] . " AND id LIKE \"$initiale%\""));
	echo "\n<br>\n<br><h2>";
	if ($initiale == "A") echo "Action";
	else echo "Reaction";
	echo " cards</h2>Number : " . $res_req_a[0];

	$req_a_sp = sql_fetch_array(sql_query("SELECT MIN(sp), AVG(sp), MAX(sp), SUM(sp) FROM " . TABLE_A . " WHERE idgame=" . $_SESSION["svc"] . " AND id LIKE \"$initiale%\""));
	echo "\n<br>SP (low / mid / high / sum)  : ". $req_a_sp[0] . " / " . number_format($req_a_sp[1],2) . " / " . $req_a_sp[2] . " / " . $req_a_sp[3];

	$req_a_rarity = sql_query("SELECT COUNT(*), rarete FROM " . TABLE_A . " WHERE idgame=" . $_SESSION["svc"] . " AND id LIKE \"$initiale%\" GROUP BY rarete");
	echo "\n<br>Rarities : ";
	while ($req_a_rarities = sql_fetch_array($req_a_rarity)) 
	{
		echo $req_a_rarities[0] . afficher_rarete($req_a_rarities[1]). " ";
	}
}

$res_req_cha = @sql_fetch_array(sql_query("SELECT COUNT(*) FROM " . TABLE_CHA . " WHERE idgame=" . $_SESSION["svc"]));

//on cherche la présence de données en base
if ($res_req_cha)
{
	echo "<h2>Characters cards</h2>Number : " . $res_req_cha[0];
	//personnages
	calculer_stats_cha("SNK");
	calculer_stats_cha("Capcom");
	//action
	calculer_stats_a("A");
	//reaction
	if ($_SESSION["svc"] == 2) calculer_stats_a("R");
}
else echo "<p class=\"text-danger\">No stats available.</p>";
afficher_bas();
?>