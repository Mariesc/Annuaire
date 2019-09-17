<?php
require_once 'Modeles/Pluriel.php';
require_once 'Modeles/Element.php';
require_once 'Modeles/Contact.php';
require_once 'Modeles/Telephone.php';
require_once 'Modeles/TypesTelephone.php';
require_once 'Modeles/Adresse.php';
require_once 'Modeles/Pays.php';
require_once 'Vues/Accueil.php';
/**
 * Definition des options de pagination (de style et fonctionnelle)
 */

// limitation des liens avant et après la page actuelle
define('LIMIT_PAGINATION',10);
// limitation du nombre de contact par page
define('LIMIT_PER_PAGE',10);
// classe css du lien de la page actuelle
define('CLASS_PRECEDENT','previous');
define('CLASS_SUIVANT','next');
define('CLASS_PRESENT','active');
define('CLASS_NORMAL','page');
//variable de filtre
$nbFiltre = $_SESSION['filtre'];
// format du lien généré
define('LIEN_PAGE','/Annuaire-PHP/index.php?page={nb}&filtre='.$nbFiltre);


/* Définition de la fonction getLink()
. Paramètre :
. @page Int : Page
.*/
function getLink($nb) {
    return str_replace('{nb}', (string) $nb, LIEN_PAGE  );
}




/* Définition de la fonction pagination()
. Paramètres :
. @table String : Liste d'objet
. @current Int : Page actuelle
.*/
function pagination($liste, $current = 1) {
    echo '<ul class="pagination">';

    /*
     * Le nombre de page
     */
    $nbObjets = $liste->getNombre();
    $nbPage = ceil($nbObjets / LIMIT_PER_PAGE);


    /* Afficher le lien "Précédent" si la page actuelle n'est pas la première */
    if($current !== 1) {
        echo '<li><a href="'.getLink($current - 1).'" class="'.CLASS_PRECEDENT.'">Précédent</a></li>';
    }

    /* Afficher les liens avant la page actuelle */
    for($i = ($current - LIMIT_PAGINATION) ; $i < $current ; $i++) {
        if($i > 0) {
            echo '<li><a href="'.getLink($i).'" class="'.CLASS_NORMAL.'">'.$i.'</a></li>';
        }
    }

    /* Afficher le lien de la page actuelle */
    echo '<li><a href="'.getLink($current).'" class="'.CLASS_PRESENT.'">'.$current.'</a></li>';

    /* Afficher les liens suivants */
    $nb = 0;
    for($i = ($current + 1) ; $i <= $nbPage ; $i++) {
        if($nb < LIMIT_PAGINATION) {
            echo '<li><a href="'.getLink($i).'" class="'.CLASS_NORMAL.'">'.$i.'</a></li>';
            $nb++;
        }
    }

    /* Afficher le lien "Suivant" si la page actuelle n'est pas la dernière */
    if($current < $nbPage) {
        echo '<li><a href="'.getLink($current + 1).'" class="'.CLASS_SUIVANT.'">Suivant</a></li>';
    }

    echo '</ul>';
}

if(isset($_GET['page']) && (int) $_GET['page'] !== 0) {
    $page = (int) $_GET['page'];
} else {
    $page = 1;
}


$premiereEntreeCalcul = ($page-1)*LIMIT_PER_PAGE;
$premiereEntreeCalcul = strval($premiereEntreeCalcul);

/**
 * Affichage des contact
 */
function getLIMIT_PER_PAGE(){
    return LIMIT_PER_PAGE;
}




?>