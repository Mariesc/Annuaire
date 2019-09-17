<?php
/**
 * Controleur Telephone du site
 * Permet le lien vers le Formulaire de Creation de Téléphone
 */
require_once 'Modeles/Pluriel.php';
require_once 'Modeles/Element.php';
require_once 'Modeles/Contact.php';
require_once 'Modeles/Telephone.php';
require_once 'Modeles/TypesTelephone.php';
require_once 'Modeles/Adresse.php';
require_once 'Modeles/Pays.php';


$erreur = "";
$erreurs = array();

/**
* $contactId id du contact de la liste du formulaire précédent
*/
$contactId = $_SESSION['IDContact'];

if(isset($_POST['Valider'])) {
    if (isset($_POST['telephone']) != "") {
        $telephone = $_POST['telephone'];
        //$patternTelephone = '/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/';
        $patternTelephone = '/^\+[1-9]{1}[0-9]{3,14}$/';
        preg_match($patternTelephone, $telephone, $matches);
        $typeTel = $_POST['typeTel'];

        /****************************************
         * Gestion des erreurs avant tout traitements (afin de récupéré toutes les erreurs possible)
         ****************************************/
        $telephonesListParTypes = new Telephones();
        $telephonesListParTypes->remplir("T_TypeTelID = " . $typeTel . " AND T_ContactID = " . $contactId);

        // dans cette liste on stocke par numéro
        $telephonesListParTelephone = new Telephones();
        $telephonesListParTelephone->remplir("T_numero = " . $telephone . " AND T_ContactID = " . $contactId);

        if ($telephonesListParTypes->getNombre() >= 1){
            $erreur = "<p class=erreur>Le type de tel est déjà présent.</p>";
            array_push($erreurs, $erreur);
        }

        if ( $telephonesListParTelephone->getNombre() >= 1){
            $erreur = "<p class=erreur>Le numéro est déjà présent</p>";
            array_push($erreurs, $erreur);
        }

        if (!empty($telephone)) {
            if (!$matches) {
                $erreur = $erreur.'<p class="erreur" > Le format du numéro de téléphone est incorrect. Ex: 0658552211 (sans espaces, symboles ou tabulations) </p>';
                array_push($erreurs, $erreur);
            }
        }
        /*******************************************
         *
         *******************************************/

        if (empty($erreurs)) {

            //recupération de l'ID adresse qui vient d'être créé

            // on vérifie que le type de tel n'existe pas déjà
            $contact = Contact::mustFind($contactId);
            //création du telephone en récuperant l'id du contact en question
            print_r($telephone);
            print_r($typeTel);
            print_r($contactId);
            Telephone::SQLInsert(array($telephone,$typeTel, $contactId));
            echo '<div class="blockFormulaire">';
            echo '<p >Téléphone Créé ! </p>';
            echo '</div>';
        }
    }
}

require_once 'vues/CreationTelephone.php';
?>