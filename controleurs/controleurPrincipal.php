<?php
/**
	* Controleur Principal du site
	* Permet le lien vers les autres Formulaires
*/
require_once 'Modeles/Pluriel.php';
require_once 'Modeles/Element.php';
require_once 'Modeles/Contact.php';
require_once 'Modeles/Telephone.php';
require_once 'Modeles/TypesTelephone.php';
require_once 'Modeles/Adresse.php';
require_once 'Modeles/Pays.php';
require_once 'Vues/Accueil.php';

//requête d'affichage des contacts par defaut
$req =  Contact::getSELECT();


//Suppression d'un Contact de la liste
if(isset($_GET["supprimer"])) {
	$_SESSION['Menu'] = "Accueil";
	$IDContactSuppression = $_GET["supprimer"];
	$listeContacts = new Contacts();
    $listeContacts->remplir();
	echo'<div class="blockFormulaire">';
    echo 'Voulez-vous vraiment supprimer le contact '.Contact::getInstances()->RechercheObjet($IDContactSuppression,"nom").' ?
    <a href="http://localhost/Annuaire-PHP/index.php?confirmation='.$IDContactSuppression.'" > Oui</a>
	ou <a href="http://localhost/Annuaire-PHP/index.php?non" name="non">Non</a>';
	echo'</div>';
}

//confirmation de suppression
if(isset($_GET["confirmation"])) {
	$IDContact = intval($_GET["confirmation"]);
	$IDAdresse = null;
	$adresseContact = 0;
	//on récupère l'IDAdresse du contact
	$Contact = new Contacts();
    $Contact->remplir();
	$adresseContact = intval(Contact::getInstances()->RechercheObjet($IDContact,"adresse"));
	//on supprime l'adresse du contact puis le contact
	Adresse::SQLDelete($adresseContact);
	Contact::SQLDelete($IDContact);
	echo'<div class="blockFormulaire">';
	echo '<p>Contact supprimé ! </p>';
	echo'</div>';
	require_once 'index.php';
}

//permet d'avoir l'idcontact pour la page de récapitulatif d'un contact
if(isset($_GET["details"])) {
	$_SESSION['Menu'] = "DetailsContact";
	require_once 'controleurs/ControleurDetailsContact.php';
}

//permet d'avoir l'idcontact pour la page de récapitulatif d'un contact
if(isset($_GET["ajouterNumero"])) {
	$_SESSION['Menu'] = "AjoutTelephone";
	$_SESSION['IDContact'] = $_GET["ajouterNumero"];
	require_once 'controleurs/ControleurTelephone.php';
}

//gère le filtre de la feuille quelque soit la page
if(isset($_GET["filtre"])){
	if($_GET["filtre"] == 1){
		$req = "SELECT C_ID, C_Nom, C_Prenom, C_DateNais, C_AdresseID, C_Societe, C_Commentaire FROM contact As C,adresse As A WHERE C.C_AdresseID = A.A_ID ORDER BY A.A_CodePostal";
		$_SESSION['filtre'] = 1;
	}else{
		$_SESSION['filtre'] = 0;
		$req =  Contact::getSELECT();
	}
	require_once 'vues/ListeContact.php';
}

//gère la page
if (isset($_GET["page"])){
	$req =  Contact::getSELECT();
	if($_GET["page"] > 1 and $_SESSION['filtre'] == 1){
		$req = "SELECT C_ID, C_Nom, C_Prenom, C_DateNais, C_AdresseID, C_Societe, C_Commentaire FROM contact As C,adresse As A WHERE C.C_AdresseID = A.A_ID ORDER BY A.A_CodePostal";
		$_SESSION['filtre'] = 1;
	}else{
		$_SESSION['filtre'] = 0;
	}
	require_once 'vues/ListeContact.php';
	
}

//lien vers le formulaire d'ajout d'un contact
if(isset($_POST["ajouterContact"])) {
	$_SESSION['Menu'] = "Contact";
    require_once 'controleurs/ControleurContact.php';
}


//renvoie vers accueil si confirmation de suppression -> non
//sinon session accueil
if(isset($_POST["Exporter"])) {
	$_SESSION['Menu'] = "VCard";
	require_once 'controleurs/ControleurVCard.php';
}

if(isset($_POST["Accueil"]) or isset($_GET["non"]) ) {
	$_SESSION['Menu'] = "Accueil";
	$_SESSION['filtre'] = 0;
	require_once 'vues/ListeContact.php';
	require_once 'vues/Accueil.php';
}


//mernu du site
if (!isset($_SESSION['Menu'])) {
	$_SESSION['Menu']="Accueil";
	require_once 'vues/Accueil.php';

}


//redirection vers controleurs via variable de session
switch ($_SESSION['Menu']) {
	case "Contact":
		require_once 'controleurs/ControleurContact.php';
		break;
    case "Accueil":
        require_once 'controleurs/ControleurPrincipal.php';
		break;
	case "VCard":
		require_once 'controleurs/ControleurDetailsContact.php';
		require_once 'controleurs/ControleurVCard.php';
		break;
	case "DetailsContact";
	    require_once 'controleurs/ControleurDetailsContact.php';
        break;
	case "AjoutTelephone";
		require_once 'controleurs/ControleurTelephone.php';
		break;

}



?>
