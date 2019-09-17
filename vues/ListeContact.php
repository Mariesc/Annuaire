<?php
require_once 'controleurs/ControleurPagination.php';
/**
* Page de formulaire de récapitualif des contacts
*/
?>
<form method="get" action="index.php" class="blockFormulaire">
    <h2>Liste des Contacts : </h2>
	<a href="http://localhost/Annuaire-PHP/index.php?filtre=1" >Filtrer par code postal</a>
	<a href="http://localhost/Annuaire-PHP/index.php?filtre=0" >Annuler filtre</a>
	<?php
    $ListeContactsTotal = new Contacts();
    $ListeContactsTotal->remplirAVECRequete($req,null,null);

    // récupèration de la constante LIMIT_PER_PAGE à partir du controleur
    $limit_contact_per_page = getLIMIT_PER_PAGE();

    $ListeContactsPage = new Contacts();
    $ListeContactsPage->remplirAVECRequete($req,$premiereEntreeCalcul,$limit_contact_per_page);

    $ListeContactsPage->displayTable();
    pagination($ListeContactsTotal,$page)

    // affichage des liens
	?>
</form>
