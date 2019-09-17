<?php
/**
* Page de formulaire de creation d'un telephone
*/
?>


<div class="blockFormulaire">
    <?php foreach ($erreurs as $erreur){
        echo $erreur;
    }
    ?>
    <form method="post" action="index.php" class="blockFormulaire">
    <h2>Création d'un téléphone</h2>
    <label>Type de téléphone</label>

    <?php
    $ListeTypeTel = new TypeTelephones();
    $ListeTypeTel->remplir();
    TypeTelephone::getInstances()->displaySelect('typeTel');

    ?>

    </br>
    <label>Numéro tel.</label>
    <input type="tel" class="champ" name="telephone" placeholder="0666225544">
    </br>
    <input class="boutonFormulaire" type="submit" value="Valider" id="boutonValider" name="Valider" class="bouton" />
</form>
</div>
