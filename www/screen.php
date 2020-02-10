<?php
require_once("functions.php");
afficher_entete(retourner_nom_jeu($_SESSION["svc"]) . " : screenshots");
//parcours le dossier du jeu
if($dossier = @opendir("./screen/svc" . $_SESSION["svc"]))
{
	//liste les fichiers trouvés
	while(false !== ($fichier = readdir($dossier)))
	{
		if (substr($fichier,-4) == ".png")      echo "<img src=\"screen/svc" . $_SESSION["svc"] . "/" . $fichier . "\" title=\"screenshot : " . retourner_nom_jeu($_SESSION["svc"]) . "\"> ";
	}
	closedir($dossier);
}
else echo "<p class=\"text-danger\">No screenshots available.</p>"; 
afficher_bas();
?>