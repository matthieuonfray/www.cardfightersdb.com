<?php
//prépare les sessions
session_start();
//charge les noms des tables, les emails, réglages avancés
require_once("conf.php");

/*
function lire_cache($fcache)
{
	if (! @file_exists($fcache) || @filemtime($fcache) < (time()-CACHE))
	{
		//ouverture du tampon
		ob_start(); 
		require("cache_" . basename($_SERVER['PHP_SELF']));
		// stockage du tampon dans une chaîne de caractères
		$tampon = ob_get_contents(); 
		// fermeture de la temporisation de sortie et effacement du tampon
		ob_end_clean(); 
		file_put_contents($fcache, $tampon);
	}
	readfile($fcache);
}
*/

function connecter_joueur($idplayer=0, $mdp="")
{
	// MODIF COOKIE
	if ($idplayer == 0) 
	{
		$idplayer = $_COOKIE["cookie_id"];
		$mdp = $_COOKIE["cookie_mdp"];
	}
	$req_recherche = "SELECT pseudo, email FROM " . TABLE_PERSONNAL . " WHERE id=" . $idplayer . " AND password LIKE '" . $mdp . "'";
	// FIN MODIF COOKIE
	$res_req_recherche = sql_query($req_recherche);
	//un joueur trouvé par l'identifiant
	if (sql_num_rows($res_req_recherche) == 1)
	{
		$res_player = sql_fetch_array($res_req_recherche);
		//enregistre dans la session les infos du profil pour ne plus les requeter par la suite
		$_SESSION["player"] = $idplayer;
		$_SESSION["playername"] = $res_player["pseudo"];
		$_SESSION["playermail"] = $res_player["email"];
		//sauve la date de connexion dans le profil et l'IP
		$date_jour = date("Y-m-d H:i:s");
		$req = "UPDATE " . TABLE_PERSONNAL . " SET lastseen='" . $date_jour . "', ip='" . $_SERVER['REMOTE_ADDR'] . "' WHERE id=" . $idplayer;
		sql_query($req);
	}
}

function afficher_entete($titre="")
{	
	require_once("entete.txt");
	//gestion du titre
	echo "\n<title>CardFightersDB : " . ucfirst($titre) . "</title>";
	require_once("entete-t.txt");
	echo "<div class=\"page-header fspan12\"><h2 class=\"features\">". ucfirst($titre)."</h2></div><br><br><br><p>";
}

function afficher_bas($jsinclude="")
{
	require_once("basdepage1.txt");
	echo "\nThis site is not affiliated with SNK or Capcom. All trademarks, trade names, services marks and logos belong to their respective companies.";
	if (DEBUG===true)
	{
			$timeend=microtime(true);
			$time=$timeend-TIME_START;
			//Afficher le temps d'éxecution
			$page_load_time = number_format($time, 3);
			echo "\n<br>SQL: ". SQL . ". Computing time: " . $page_load_time . "s. Generated @ " . date(DATE_RFC2822) . "\n";
	}
	require_once("basdepage2.txt");
	if ($jsinclude != "") require_once("js/$jsinclude");
	echo "</body></html>";
	sql_close();
}

	
function generer_motdepasse ($longueur = 6)
{
    $mdp = "";
     // Définir tout les caractères possibles dans le mot de passe,
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
     // obtenir le nombre de caractères dans la chaîne précédente
    $longueurMax = strlen($possible);
    if ($longueur > $longueurMax) $longueur = $longueurMax;
    // initialiser le compteur
    $i = 0;

    // ajouter un caractère aléatoire à $mdp jusqu'à ce que $longueur soit atteint
    while ($i < $longueur) 
	{
        // prendre un caractère aléatoire
        $caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);
        // vérifier si le caractère est déjà utilisé dans $mdp
        if (!strstr($mdp, $caractere)) 
		{
            // Si non, ajouter le caractère à $mdp et augmenter le compteur
            $mdp .= $caractere;
            $i++;
        }
    }
    return $mdp;
}

/*
function geo_localiser()
{
	require_once("./geoloc/geoipcity.inc");
	require_once("./geoloc/geoipregionvars.php");
	$gi = geoip_open(realpath("./geoloc/GeoLiteCity.dat"),GEOIP_STANDARD);
	//récupère l'IP du visiteur
	$record = geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);	
	geoip_close($gi);
	//renvoie un tableau de données : country_name,city,postal_code,latitude,longitude
	return $record;
}
*/

function creer_captcha()
{
	//un nom de carte tirées du jeu va servir de captcha aléatoire
	switch (SQL)
	{
		case "mysql" : $req = "SELECT nom FROM " . TABLE_CAPTCHA . " ORDER BY RAND() LIMIT 1"; break;
		case "sqlite" : $req = "SELECT nom FROM " . TABLE_CAPTCHA . " ORDER BY RANDOM() LIMIT 1"; break;
	}
	$res = sql_query($req);
	//sauve dans le contexte mémoire
	$res_req = sql_fetch_array($res);
    $_SESSION['captcha'] = $res_req["nom"];
	//renvoie pour pouvoir l'afficher à l'écran
    return $res_req["nom"];
}

function creer_url_characters($id, $nom, $fin=null)
{
	//formatage d'url pour la fiche de personnage
	return "<a href=\"svc" . $_SESSION["svc"] . "-characters-" . $id . "-" . nettoyer_chaine($nom) . "\"" . $fin;
}

function creer_url_action($id, $nom, $fin=null, $reaction=false)
{
	//formatage d'url pour la fiche d'action
	if ($reaction) $re="re";
	else $re=null;
	return "<a href=\"svc" . $_SESSION["svc"] . "-" . $re ."action-" . $id . "-" . nettoyer_chaine($nom). "\"" . $fin;
}

function nettoyer_chaine($chaine)
{
	//virer les caractères à la con
	setlocale(LC_ALL, 'fr_FR');
	$chaine = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $chaine);
	return preg_replace("#[^a-zA-Z]#", "", trim($chaine));
}

function emailog($mail,$sujet,$message,$header,$err=0)
{
	$reussi = true;
	//$encodage = "Content-Type: text/plain; charset=\"utf-8\" Content-Transfer-Encoding: 8bit\n\r";
	//si y a pas d'erreur ou alors on est en mode DEBUG: on envoie le mail
	if ($err == 0) 
	{
		//serveur de DEV
		if (DEBUG===true) echo "\nSimulation d'envoi de mail(\"$mail\",\"$sujet\",\"$message\",\"$header\");";
		//serveur de PROD
		else $reussi = mail($mail,$sujet,$message,$header);	
	}
	//efface les mots de passe du message avant l'enregistrement en base
	$message = preg_replace("#password \([a-zA-Z0-9]{6}\)#", "password (fake : " . generer_motdepasse().")",$message);
	//enregistre les mails envoyés
	$req = "INSERT INTO " . TABLE_EMAILS . " (mail, sujet, message, header, ip, error) VALUES (\"" . $mail . "\",\"" . $sujet . "\",\"" . $message . "\",\"" . $header . "\",\"" . $_SERVER['REMOTE_ADDR'] . "\"," . $err . ")";
	$res = sql_query($req);
	//echo "<br><br>$res = $req<br><br>";
	//sql_query renvoye FALSE en cas d'échec 
	//if ($res === FALSE) return false;
	//mais pas forcément TRUE en cas de réussite, ca peut-être un objet résultat
	//else return true;
	return $reussi;
}


function retourner_nom_jeu($num=1)
{
	//les jeux connus
	switch ($num)
	{
		case 1 : return "SvC Card Fighter's Clash";
		case 2 : return "SvC Card Fighters 2 Expand Edition";
		case 3 : return "SvC Card Fighters DS";
		default : return $num;
	}
}

function marquer_champs($tri, $choix)
{
	//sélectionner un élement dans une liste déroulante
	$msg = " value=\"" . $tri . "\" ";
	if ($choix == $tri) $msg .= " selected";
	return $msg;
}

function marquer_bouton($texte, $tri, $choix, $url="characters")
{
	$msg = "";
	if ($choix == $tri) $msg = "active";
	return "\n<a href=\"svc" . $_SESSION["svc"] . "-" . $url . "-" . $tri . "\" class=\"btn btn-default btn-lg ". $msg . "\">". $texte ."</a>";
}

function afficher_type_capacite($cap)
{
	//formatage des capacités des fiches de personnages
	switch ($cap)
	{
		case '(' : case '()' : $type_img = "rond.gif"; break;
		case '[' : case '[]' : $type_img = "carre.gif"; break;
		case '/' : case '/\\' : $type_img = "triangle.gif"; break;
	}
	if (empty($type_img)) return $cap;
	else return "\n<img src=i/type_of_ability/$type_img>";
}	

function afficher_rarete($rar)
{
	//mise en page de la rareté d'une carte
	return "\n<img src=i/rarity/$rar.gif>";
}

function capturer_type_capacite($texte)
{
	//formatage des capacités des fiches de personnages dans un texte
	$texte = str_replace("[]","<img src=i/type_of_ability/carre.gif>",$texte);
	$texte = str_replace("()","<img src=i/type_of_ability/rond.gif>",$texte);
	$texte = str_replace("/\\","<img src=i/type_of_ability/triangle.gif>",$texte);
	return "\n" . $texte;
}

function capturer_rarete($texte)
{
	//mise en page de la rareté d'une carte dans un texte 
	$texte = str_replace("[A]","<img src=i/rarity/A.gif>",$texte);
	$texte = str_replace("[B]","<img src=i/rarity/B.gif>",$texte);
	$texte = str_replace("[C]","<img src=i/rarity/C.gif>",$texte);
	$texte = str_replace("[D]","<img src=i/rarity/D.gif>",$texte);
	$texte = str_replace("[S]","<img src=i/rarity/S.gif>",$texte);
	return "\n" . $texte;
}

function recuperer_gravatar($email, $s=80, $d='retro', $r='g', $img=true)
{
    //renvoie le gravatar en fonction de l'email du membre
	$url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=$s&amp;d=$d&amp;r=$r";
    if ($img) $url = "<img src=\"" . $url . "\" alt=\"Gravatar\">";
    return $url;
}

function ajouter_commentaire($carte)
{
	echo "<br><br>Comments : \n";
	//affichage des commentaire déjà saisis
	$req_com = "SELECT * FROM " . TABLE_SNK_COMMENT . " WHERE idcard LIKE '$carte' AND idgame=" . $_SESSION["svc"];
	$res_req_com = sql_query($req_com);
	$chn = null;
	while ($com = sql_fetch_array($res_req_com))
	{
		$chn .= "<li>";
		$req_player = "SELECT pseudo FROM " . TABLE_PERSONNAL . " WHERE id=" . $com["idplayer"];
		$res_req_player = sql_query($req_player);
		$res_player = sql_fetch_array($res_req_player);
		if ($_SESSION["player"] != $com["idplayer"]) $chn .= " <a href=send.php?id=" . $com["idplayer"] . ">";
		else $chn .= "<a href=edit_profile.php title=\"Hey it's you\"> ";
		$chn .= $res_player["pseudo"] . "</a> (" . $com["time"] . ") said : " . stripslashes(capturer_type_capacite(capturer_rarete($com["comment"]))) . "</li>";
	}
	if (is_null($chn)) echo "none<br>";
	else echo "<ul>$chn</ul>";
	//proposer la saisie si on est connecté sinon afficher lien vers la page de login
	if (! isset($_SESSION["player"])) echo "<br><a href=login.php>Log-in</a> to add a comment about this card !";
	else 
	{
		//ligne dynamique du résultat d'ajout/suppression de commentaire 
		echo "<div id=\"rescomment\"></div><br>";
		//ajout d'un formulaire pour ajouter un commentaire
		echo "<form method=post>Add a comment about this card <a href=\"#null\" onclick=\"javascript:window.open('help.php','Help','menubar=no, scrollbars=no, width=200, height=300')\">(?)</a><br>\n<textarea id=formcomment name=texte></textarea><br>";
		echo "<input type=hidden id=idcard value='$carte'>";
		echo "<button class=\"btn btn-primary addbtn\" type=button><span class=\"glyphicon glyphicon-pencil\"></span> Write</button></form>";
	}
}

function afficher_proprietaires($carte)
{
	//liste les proprio d'une carte donnée
	$req_own = "SELECT idplayer FROM " . TABLE_SNK_MISS . " WHERE idcard LIKE '" . $carte . "' AND idgame=" . $_SESSION["svc"] . " ORDER BY idplayer";
	$res_own = sql_query($req_own);
	echo "<br><br>Who got these card ? ";
	$i=0;
	$chn = null;
	while ($proprio = sql_fetch_array($res_own))
	{
		if ($i>0) $chn .= ", ";
		$req_who = "SELECT pseudo FROM " . TABLE_PERSONNAL . " WHERE id=" . $proprio["idplayer"];
		$res_who = sql_fetch_array(sql_query($req_who));
		$chn .= "<a href=send.php?id=" . $proprio["idplayer"] . ">" . $res_who["pseudo"] . "</a>";	
		$i++;
	}
	if (is_null($chn)) echo " no one :(";
	else echo $chn;
}

//FUNCTIONS SQL : asbtraction SGBD minimaliste
function sql_connect()
{
	switch (SQL)
	{
		case "mysql" :
		//conf mySQL
		$_SESSION["LIEN_BASE_SQL"] = mysqli_connect(IP, USER, PASS, NOM_BASE) or die("Connexion impossible au serveur"); 
		break;
		
		case "sqlite" :
		//serveur SQLite
		$_SESSION["LIEN_BASE_SQL"] = new SQLite3(FIC_SQLITE);
		break;
	}
}

function sql_close()
{
	switch (SQL)
	{
		case "mysql" : return mysqli_close($_SESSION["LIEN_BASE_SQL"]);
		case "sqlite" : return $_SESSION["LIEN_BASE_SQL"]->close();
	}
}

function sql_query($req)
{
	switch (SQL)
	{
		case "mysql" : return mysqli_query($_SESSION["LIEN_BASE_SQL"], $req);
		case "sqlite" : return $_SESSION["LIEN_BASE_SQL"]->query($req);
	}
}

function sql_error()
{
	switch (SQL)
	{
		case "mysql" : return null;
		case "sqlite" : return $_SESSION["LIEN_BASE_SQL"]->lastErrorMsg() . "\n";;
	}
}

function sql_num_rows($res)
{ 
	switch (SQL)
	{
		case "mysql" : return mysqli_num_rows($res);
		case "sqlite" : //cette fonction n'existe pas, on l'implémente
			$numRows = 0;
			while ($rowR = $res->FetchArray()) $numRows++;
			$res->reset();
			return $numRows;
	}
}

function sql_fetch_array($res)
{ 
	switch (SQL)
	{
		case "mysql" : return mysqli_fetch_array($res);
		case "sqlite" : return $res->fetchArray();
	}
}

?>
