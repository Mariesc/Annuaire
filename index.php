
<?php
/**
* Index du site
* Permet le lien avec le Header/Footer, la connexion à la BDD
* et le renvoie vers le Controleur Principal
*/

/**
* Début de Session
*/
session_start();

/**
* Header des pages du site
*/
require_once 'vues/Header.php';

/**
* Appel classe Si
*/
require_once 'modeles/Si.php';

/**
* Connexion SI
*/
$MySI = SI::getSI();


/**
* Appel Controleur Principal
*/

require_once 'controleurs/ControleurPrincipal.php';

/**
* Footer des pages du site
*/
include 'vues/Footer.php';
?>

