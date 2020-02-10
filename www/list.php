<?php
require_once("functions.php");

//ONFRAY Matthieu www.cardfightersdb.com
//pas encore connecté
if (! isset($_SESSION["player"])) 
{
	header("Location: login.php");
	exit();
}

function afficher_carte_cliquable($carte)
{
	//Capcom ou SNK ? Orange ou bleu ?
	switch (substr($carte,0,1))
	{
		case "C" : $couleur = "btn-warning"; break;
		case "S" : $couleur = "btn-info"; break;
		case "A" : $couleur = "btn-danger"; break;
		default : $couleur = ""; break;
	}
	echo "\n<button type=\"button\" class=\"btn $couleur btn-xs cardbtons\" data-toggle=\"button\" id=\"" . $carte . "\">" . $carte . "</button>";
}

afficher_entete("my list of cards for " . retourner_nom_jeu($_SESSION["svc"]));
echo "Toggle buttons to add ou remove a card from your collection.<br><br>\n";

//RECUPERE TOUS LES CARTES POSSEDEES CHA+A, MISE EN TABLEAU
$req_own = "SELECT idcard FROM " . TABLE_SNK_MISS . " WHERE idplayer=" . $_SESSION["player"] . " AND idgame=" . $_SESSION["svc"] . " ORDER BY idcard";
$res_own = sql_query($req_own);
//empile dans un tableau les cartes du joueurs
$tab_mes_cartes = array();
while ($ma_carte = @sql_fetch_array($res_own))
{
	array_push($tab_mes_cartes,$ma_carte["idcard"]);
}

//RECUPERE TOUS LES CARTES DE PERSO DU JEU
$req_cha = "SELECT id FROM " . TABLE_CHA . " WHERE idgame=" . $_SESSION["svc"] . " ORDER BY id";
$res_cha = sql_query($req_cha);

while ($carte = @sql_fetch_array($res_cha))
{
	//séparation entre les cartes SNK et Capcom
	if ($carte["id"] == "S001") echo "<br>\n";
	afficher_carte_cliquable($carte["id"]);	
	if (in_array($carte["id"],$tab_mes_cartes)) echo "\n<script type=\"text/javascript\">	$(document).ready(function() { $(\"#" . $carte["id"] . "\").button('toggle');});</script>";
}

//séparation entre les cartes SNK et action
echo "<br>\n";

//RECUPERE TOUS LES CARTES D'ACTION DU JEU
$req_a = "SELECT id FROM " . TABLE_A . " WHERE idgame=" . $_SESSION["svc"] . " ORDER BY id";
$res_a = sql_query($req_a);
while ($carte = @sql_fetch_array($res_a))
{
	//séparation entre les cartes action et reaction
	if ($carte["id"] == "R001") echo "<br>\n";
	afficher_carte_cliquable($carte["id"]);
	if (in_array($carte["id"],$tab_mes_cartes)) echo "\n<script type=\"text/javascript\">	$(document).ready(function() { $(\"#" . $carte["id"] . "\").button('toggle');});</script>";
}

//ligne dynamique du résultat d'ajout/suppression de cartes
echo "<div id=\"resultat\"></div>";
afficher_bas("insert.js");
?>
