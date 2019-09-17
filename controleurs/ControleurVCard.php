<?php

/**
 * Classe permettant de récupérer tout les attribut du contact pour la génération du VCard
 */


include_once('modeles/VCard.php');
$erreurs = array();
$vCard = new VCard('');
// on récupère l'id du contact pour récupérer ses attribut
$idContact = $_SESSION['IDContact'];
$contact = Contact::mustFind($idContact);

$nomContact = $contact->getNom();
$prenomContact = $contact->getPrenom();
$societe = $contact->getSociete();
$commentaire = $contact->getCommentaire();
$dateNaiss = $contact->getDateNais();
$adresseContact = $contact->getAdresseID();
$adresse = Adresse::mustFind($adresseContact);
$idPays = $adresse->getPaysID();
$pays= Pays::mustFind($idPays);
$paysNom = $pays->getNom();

$numVoie = $adresse->getNumVoie();
$nomVOie = $adresse->getNomVoie();
$cptAdresse = $adresse->getComplementAdresse();
$ville = $adresse->getVille();
$cp = $adresse->getCodePostal();

/**
 * Téléphones
 */
$telFixe = $contact->getTelephones()->getTelOfType("Fixe");
print_r($telFixe);
$telFaxe = $contact->getTelephones()->getTelOfType("Faxe");
$telPortable = $contact->getTelephones()->getTelOfType("Portable");
$telPersonnel = $contact->getTelephones()->getTelOfType("Personnel");



$vCard->setNom($nomContact);
$vCard->setPrenom($prenomContact);
$vCard->setAdresse($numVoie,$nomVOie,$cptAdresse,$ville,$cp,$pays);
$vCard->setCommentaire($commentaire);
$vCard->setDateNaissance($dateNaiss);
$vCard->setSociete($societe);
$vCard->setTelFaxe($telFaxe);
$vCard->setTelFixe($telFixe);
$vCard->setTelPersonnel($telPersonnel);
$vCard->setTelPortable($telPortable);


/*
OR
header('Content-Type: text/x-vcard');
header('Content-Disposition: inline; filename=vCard_' . date('Y-m-d_H-m-s') . '.vcf');
echo $vCard->getCardOutput();
*/
$vCard->writeVCardFile();
header('Location:' . $vCard->getCardFilePath());
require_once 'vues/DetailsContact.php';

exit;

?>
