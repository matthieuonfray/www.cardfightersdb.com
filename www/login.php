<?php
require_once("functions.php");

//déjà connecté, on renvoie vers la page de profil
if (isset($_SESSION["player"])) 
{
	header("Location: profile.php");
	exit();
}

//pas connecté
if (empty($_POST['playerlogin']) && empty($_POST['playerpass']))
{
	afficher_entete("Login");
	echo "Password lost ? Get <a href=new_pas.php>a new one now</a> !";
	echo "<br>Don't have an account ? Please <a href=register.php>register</a>, you'll be able to send messages to others players.";
	//erreur de login : mauvais couple login/passwd
	if (isset($_GET["err"])) echo "<p class=\"text-danger\">Login error, please try again.</p>"; 
	//formulaire
	echo "<form method=post>Login<br><input type=text name=playerlogin><br>Password<br><input type=password name=playerpass><br><input type=checkbox value=1 name=cookieyes>Stay connected<br><br><button class=\"btn btn-primary\" type=submit><span class=\"glyphicon glyphicon-log-in\"></span> Login</button></form>";
	afficher_bas();
}
else
{
	//vérification du couple login/mdp
	$req_id = "SELECT id FROM " . TABLE_PERSONNAL . " WHERE pseudo LIKE '" . $_POST['playerlogin'] . "' AND password LIKE '" . md5($_POST['playerpass']) . "'";
	$res_id = sql_query($req_id);
	if (sql_num_rows($res_id) == 1)
	{
		$res_player = sql_fetch_array($res_id);
		// on envoie le cookie avec le mode httpOnly
		// MODIF COOKIE 
		if ($_POST["cookieyes"]) 
		{
			setcookie("cookie_id", $res_player["id"], time()+COOKIE_EXPIRE, null, null, false, true);  
			setcookie("cookie_mdp", md5($_POST['playerpass']), time()+COOKIE_EXPIRE, null, null, false, true);  
		}
		//on le connecte
		connecter_joueur($res_player["id"], md5($_POST['playerpass']));
		// FIN MODIF COOKIE
		
		header("Location: profile.php");
		exit();
	}
	//pour tous les cas foireux, renvoie vers la page
	else header("Location: login.php?err=true");
	exit();
}

?>
