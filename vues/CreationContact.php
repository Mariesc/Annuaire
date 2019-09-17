<?php
/**
* Page de formulaire de creation d'un contact
*/
?>
<div class="blockFormulaire">
	<?php echo $erreur; ?>
	<form method="post" action="index.php" enctype="multipart/form-data">
		<h2>Creation d'un Contact</h2>
		<input type="hidden" name="MAX_FILE_SIZE" value="256000" />
		<p> Image contact :  <input  type="file"  name="avatar" /></p>
		</br>
		<label>Nom : </label>
        <input type="text" class="champ" name="Nom" id="Nom" placeholder="Smith" autofocus>
		<label>Prénom : </label>
        <input type="text" class="champ" name="Prenom" id="Prenom" placeholder="John">
		</br>
		<label>Date de Naissance : </label>
        <input type="date" class="champ" name="DateNaiss" id="DateNaiss" placeholder="00/00/0000">
		</br>
		<label>Numero de la voie : </label>
        <input type="number" class="champ" name="NumVoie" id="NumVoie" placeholder="3">
		</br>
		<label>Nom de la voie : </label>
        <input type="text" class="champ" name="NomVoie" id="NomVoie" placeholder="Rue Régis">
		</br>
		<label>Complément d'adresse : </label>
        <Textarea  type="textera" name="ComplAdresse"  id="ComplAdresse" rows=1 cols=30 wrap=physical placeholder="Résidence,Batiment,Etage,Appartement"></Textarea>
		</br>
		<label>Ville : </label>
        <input type="text" class="champ" name="Ville" id="Ville" placeholder="Paris">	
		<label>Code Postal : </label>
        <input type="text" class="champ" name="CodePostal" id="CodePostal" placeholder="00000">	
		</br>
		<label>Pays : </label>
		<?php
	    $ListePays = new ListPays();
    	$ListePays->remplir(null,"P_Nom ASC");
		Pays::getInstances()->displaySelect("Pays");
		?>
		<label>Societe : </label>
        <input type="text" class="champ" name="Societe" id="Societe" placeholder="MyCompany">
		<Textarea  type="textera" name="Commentaire" id="Commentaire" rows=2 cols=40 wrap=physical placeholder="Commentaire"></Textarea>
		<br></br>
		<input class="boutonFormulaire" type="submit" value="Valider" id="boutonValider" name="Valider" class="bouton" />
	</form>
</div>

