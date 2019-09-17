<?php

/**
* Controleur Contact du site
* Permet le lien vers le Formulaire de Creation de Contact
*/
//référencer les classes utiles
require_once 'Modeles/Pluriel.php';
require_once 'Modeles/Element.php';
require_once 'Modeles/Contact.php';
require_once 'Modeles/Telephone.php';
require_once 'Modeles/TypesTelephone.php';
require_once 'Modeles/Adresse.php';
require_once 'Modeles/Pays.php';

$erreur = '';




//validation du formulaire de saisie
if(isset($_POST['Valider'])){
	if(isset($_POST['Nom'])!="" or isset($_POST['Prenom'])!="" ){
		$Nom = $_POST['Nom'];
		$Prenom = $_POST['Prenom'];
		if(isset($_POST['DateNaiss'])!="" ){
			//initialisation des variables non obligatoires à zero
			$DateNaiss = $_POST['DateNaiss'];
			$NumV = null;
			$NomV = null;
			$ComplA = null;
			$Ville = null;
			$CD = null;
			$CDOK = false;
			$Societe = null;
			$Commentaire = null;
			$Pays = null;
			
			//test si une des variables non obligatoires est différentes null
			if(isset($_POST['Societe'])!=""){
				$Societe = $_POST['Societe'];
			}

			if(isset($_POST['NumVoie'])!= 0){
				$NumV = $_POST['NumVoie'];
			}
			
			if (isset($_POST['NomVoie'])!=""){
				$NomV = $_POST['NomVoie'];
			}
			if (isset($_POST['ComplAdresse'])!=""){
				$ComplA = $_POST['ComplAdresse'];
			}
			if (isset($_POST['Ville'])!=""){
				$Ville = $_POST['Ville'];
			}
			if (isset($_POST['Commentaire'])!=""){
				$Commentaire = $_POST['Commentaire'];
			}
			if (isset($_POST['Pays'])!=""){
				$Pays = $_POST['Pays'];				
			}
			
			//boolean $CDOK empêche la creation d'un contact si erreur de saisie du code postal
			if(isset($_POST['CodePostal']) and !empty($_POST['CodePostal'])){
				if(intval($_POST['CodePostal'])!= 0){
					if(strlen($_POST['CodePostal'])==5){
						$CDOK = true;
						$CD = $_POST['CodePostal'];
						
					}else{
						$CD = null;
						$CDOK = false;
						$erreur = $erreur.'<p class=erreur > Le code postal doit être à 5 chiffres </p>';
					}
				}else{
					$CD = null;
					$CDOK = false;
					$erreur = $erreur.'<p class=erreur > Le code postal ne doit pas contenir de lettre </p>';
					require_once 'vues/CreationContact.php';
				}
			}
			
			

			
			//if($CDOK == true){
				$fileUpload = false;
				$CheminFichier ="";
				$maxsize = 200;
				if ($_FILES['avatar']['tmp_name'] <= $maxsize){
						$image_sizes = getimagesize($_FILES['avatar']['tmp_name']);
						$maxwidth = 200;
						$maxheight = 200;
						if ($image_sizes[0] <= $maxwidth OR $image_sizes[1] <= $maxheight){
							$fileUpload = true;
							//récupération IDcontact pour index de l'image
							$Contact = new Contacts();
							$Contact->remplir(" 1 "," C_ID DESC Limit 1");
							$IDContact = Contact::getInstances()->RechercheID()+1;
							$nom = $IDContact.$_FILES['avatar']['name'];
							//enregistrement dans le dossier
							move_uploaded_file($_FILES['avatar']['tmp_name'], 'img/' .basename($IDContact.$_FILES['avatar']['name']));
							$CheminFichier = 'img/'.$nom;

						}else{ 
							$erreur = "Image trop grande";
							echo '<div class="blockFormulaire">';
							echo $erreur;
							echo '</div>';
						}
								
				}else{ 
					$erreur = "Le fichier est trop gros";
					echo '<div class="blockFormulaire">';
					echo $erreur;
					echo '</div>';
				}

				//création de l'adresse du contact
				//même si l'adresse a ses attributs null, il est possible de modifier un contact par la suite !
				Adresse::SQLInsert(array($NumV,$NomV,$ComplA,$Ville,$CD,$Pays));
				//recupération de l'ID adresse qui vient d'être créé
				$IDAdresse=0;
				$adresseContact = new Adresses();
				$adresseContact->remplir(null," A_ID DESC  Limit 1");
				$IDAdresse = Adresse::getInstances()->displayAdresse();
				
				//creation du contact
				$TRAV = Contact::SQLInsert(array($Nom,$Prenom,$DateNaiss,$IDAdresse,$Societe,$Commentaire,$CheminFichier));
				var_dump($TRAV);
				$Contact = new Contacts();
				$Contact->remplir(" 1 "," C_ID DESC Limit 1");
				$IDContact = Contact::getInstances()->RechercheID();
				echo '<div class="blockFormulaire">';
				echo '<p> Contact créé ! </p>';
				echo 'Ajouter un téléphone au contact ? <a href="http://localhost/Annuaire-PHP/index.php?ajouterNumero='.$IDContact.'"> oui</a> /<a href="http://localhost/Annuaire-PHP/index.php"> non</a>';
				echo '</div>';
			//}
		}else{
			$erreur = $erreur.'<p class=erreur > Pas de date de naissance saisie </p>';
		}
		
	}else{
		$erreur = $erreur.'<p class=erreur > Merci de saisir votre Nom et Prénom </p>';
	}
}

require_once 'vues/CreationContact.php';
?>