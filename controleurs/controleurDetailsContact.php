<?php
/**
 * Controleur Details du Contact du site
 * Permet le lien vers le Formulaire de Creation de Téléphone
 */
require_once 'Modeles/Pluriel.php';
require_once 'Modeles/Element.php';
require_once 'Modeles/Contact.php';
require_once 'Modeles/Telephone.php';
require_once 'Modeles/TypesTelephone.php';
require_once 'Modeles/Adresse.php';
require_once 'Modeles/Pays.php';

/**
* $contactId id du contact de la liste du formulaire précédent
* Variable de session pour garder cette valeur
*/
if (isset($_GET['details'])) {
    $_SESSION['modifierContact'] = $_GET['details'];
}




$erreur = "";
$erreurs = array();

//ID du contact selectionné dans la liste des contacts afficher en ammont
$idContact = $_SESSION["modifierContact"];
$contacts = new Contacts();
// récupération de l'objet Contact crée à partir de l'id récupéré
$contact= Contact::mustFind($idContact);
// récupération de son adresse à partir de l'objet Contact crée
$adresseContact = $contact->getAdresseID();
$adresseFounded = Adresse::mustFind($adresseContact);

// récupération de son pays à partir de l'objet Adresse récupéré à partir du Contact
$pays = Pays::mustFind($adresseFounded->getPaysID());

//Suppression d'un Contact de la liste
if(isset($_POST["supprimer"])) {
    $telephone = $_REQUEST["telephone"];
    var_dump($telephone);
    echo'<div class="blockFormulaire">';
    echo 'Voulez-vous vraiment supprimer le numero '.$telephone.' ?
    <a href="http://localhost/Annuaire-PHP/index.php?confirmationSuppTel='.$telephone.'&contactId='.$idContact.'">Oui</a>
	ou <a href="http://localhost/Annuaire-PHP/index.php">Non</a>';
    echo'</div>';
}

if(isset($_GET["confirmationSuppTel"])) {
    $IDTelephone = intval($_GET["confirmationSuppTel"]);
    Telephone::SQLDelete($IDTelephone);
    echo'<div class="blockFormulaire">';
    echo '<p>Téléphone supprimé ! </p>';
    echo'</div>';
    require_once 'index.php';
}

if(isset($_POST["modifier"])) {
    $telephoneAvant = $_REQUEST["TelInit"];
    $telephoneModifier = $_REQUEST["telephone"];
    if(intval($telephoneModifier) != 0){
        if(strlen($telephoneModifier) == 10){
            $TRAV = Telephone::SQLUpdate(array($telephoneModifier,$telephoneAvant,$idContact));
            var_dump($TRAV);
            echo '<div class="blockFormulaire">';
            echo '<p >Téléphone modifié ! </p>';
            echo '</div>';
        }else{
            $erreur = $erreur.'<p class=erreur > Le numéro doit être sur 10 chiffres </p>';
        }
    }else{
        $erreur = $erreur.'<p class=erreur > Le téléphone doit être en chiffes </p>';
    }
}

if(isset($_POST['Valider'])) {
// Récup des attributs de variable Session

    $contact = Contact::mustFind($idContact);
    $nomContact = $_POST['Nom'];
    $prenomContact = $_POST['Prenom'];
    $societe = $_POST['Societe'];
    $commentaire = $_POST['Commentaire'];
    $dateNaiss = $_POST['dateNaiss'];
    // la date une fois bien formaté et utilisable pour ajout en base
    $dateVerified = "";

    //partie adresse
    $numVoie = $_POST['NumVoie'];
    $nomVoie = $_POST['NomVoie'];
    $ville = $_POST['Ville'];
    $codePostal = $_POST['CodePostal'];
    $complement = $_POST['ComplAdresse'];

    // on vérifie que la date est bonne
    // et on vérifie toute les autres erreurs possible pour pouvoir toutes les récupéré avant n'importe quel traitement
    /*********************
     * Gestion des erreurs
     *********************/
    if (!empty($dateNaiss)) {
        $dt = DateTime::createFromFormat("d-m-Y", $dateNaiss);
        if (($dt == false) && count(DateTime::getLastErrors()) > 0) {
            $erreur = "<p class=erreur>Le format de la date entrée est incorrect.</p>";
            array_push($erreurs, $erreur);
        }
        else{
            // on change le format de la date pour être compatible avec la table
            $dateVerified = date("Y-m-d", strtotime($dateNaiss));
        }
    }

    if (!empty($codePostal)) {
        if (!preg_match(" /^[0-9]{5,5}$/", $codePostal)) {
            $erreur = "<p class=erreur>Le format du code postal entré est incorrect.</p>";
            array_push($erreurs, $erreur);
        }
    }
    /***********************
     *
     ***********************/

    if (empty($erreurs)){

        // on récupere l'adresse du contact
        $adresseContact = intval(Contact::getInstances()->RechercheObjet($idContact, "adresse"));
        // mise à jour de l'adresse
        $contactAdrId = $contact->getAdresseID();
        $conditionRequeteAdr = "A_ID = $contactAdrId";

        // on modifie les telephones (ici on parcours les types de telephones et on verifie via la variable de session qui a comme
        // le type de telephone si celle ci est initialisé comme variable de session ou pas et on la modifie si elle a une valeur
        // on fait comme cela sachant qu'on a que 4 valeurs possibles donc pas lourd de boucler pour tester
        $TypeTelephones = new TypeTelephones();
        $TypeTelephones->remplir();
        foreach ($TypeTelephones->getArray() as $unType) {
            if (isset($_POST[$unType->getTypeTel()])) {
                $telephone = $_POST[$unType->getTypeTel()];
                var_dump($telephone);
                Telephone::SQLUpdate(array($telephone), "T_TypeTelID = " . $unType->getID());
                var_dump($unType->getID());
            }
        }

        Adresse::SQLUpdate(array($numVoie, $nomVoie, $complement, $ville, $codePostal), $conditionRequeteAdr);

        // attributs à ajouter dans l'ordre de la requête..
        $conditionRequeteCont = "C_ID = $idContact";

        //mise à jour du contact
        Contact::SQLUpdate(array($nomContact, $prenomContact, $dateVerified, $societe, $commentaire), $conditionRequeteCont);

        echo '<div class="blockFormulaire">';
        echo '<p>Contact modifié ! </p>';
        echo '</div>';
    }
    $_SESSION['modifierContact'] = null;
}

require_once 'vues/DetailsContact.php';
?>