<?php
/**
* Page de formulaire de récapitualif et modification d'un Contact et de ses telephones
*/
?>


<div class="blockFormulaire">
    <?php foreach ($erreurs as $erreur){
        echo $erreur;
    }
    ?>
    <form method="post" action="index.php" class="">
        <h2>Récapitulatif du contact</h2>
        <?php
          $contact = Contact::mustFind($idContact);
		  echo' ';
		  $contact->displayAvatar();
          $contact->displayFormulaire();
        ?>

    </form>
</div>



