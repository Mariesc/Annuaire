<?php
/**
* Classe Contact
*/
require_once 'VCard.php';
class Contact extends Element{
	
	 
	/**
	 * Singleton memorise les instances
	**/
	private static $o_INSTANCES;
	
	/**
	 * @param $ligne
	 * renvoie $tmp l'objet créé
	**/
	public static function ajouterObjet($ligne){
		//créer (instancier) la liste si nécessaire
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new Contacts();}
		//voir si l'objet existe avec la clef
		$tmp = static::$o_INSTANCES->getObject($ligne[static::champID()]);
		if($tmp!=null){return $tmp;}
		//n'existe pas : donc INSTANCIER Contact et mémoriser
		$tmp = new Contact($ligne);
		static::$o_INSTANCES->doAddObject($tmp);
		return $tmp;
	}
	
	/**
	 * @return $o_INSTANCES
	 * renvoie liste instances
	**/
	public static function getInstances(){
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new Contacts();}
		return static::$o_INSTANCES;
	}
		
	/**
	 * @param $id
	 * @return Adresse $objet
	 * doit impérativement trouver le Contact ayant pour id le paramètre
	**/
	public static function mustFind($id){
		if (static::$o_INSTANCES == null){static::$o_INSTANCES = new Contacts();}
		// regarder si instance existe
		$tmp = static::$o_INSTANCES->getObject($id);
		if($tmp!=null) {return $tmp;}
		//sinon pas trouver; chercher dans la BDD
		$req = static::getSELECT().' where C_ID =?';
		//echo "<br/>recherche $id";
		$ligne = SI::getSI()->SGBDgetLigne($req, $id);
		return static::ajouterObjet($ligne);
	}

	/**
	 * constructeur : repose sur le constructeur parent
	**/
	protected function __construct($theLigne) {parent::__construct($theLigne);}
	
	/**
	 * @return getField
	 * renvoie la valeur du champ spécifié en paramètre
	**/
	public function getID(){
		return $this->getField('C_ID');
	}
	
	public function getNom(){
		return $this->getField('C_Nom');
	}
	
	
	public function getPrenom(){
		return $this->getField('C_Prenom');
	}
	
	
	public function getDateNais(){
		return $this->getField('C_DateNais');
	}
	
	public function getSociete(){
		return $this->getField('C_Societe');
	}
	
	
	public function getCommentaire(){
		return $this->getField('C_Commentaire');
	}
	
	public function getAvatar(){
		return $this->getField('C_Image');
	}
	
	public function getAdresseID(){
		return $this->getField('C_AdresseID');
	}

	private $o_MesTelephones;
	private $o_MonAdresse;
	
	/**
	 * @return $o_MesTelephones
	 * renvoie les telephones du contact en question
	**/
	public function getTelephones(){
		if($this->o_MesTelephones == null){
			$this->o_MesTelephones = new Telephones();
			$this->o_MesTelephones->remplir('T_ContactID="'.$this->getID().'"',null);
		}
		return $this->o_MesTelephones;
	}

	/**
	 * @return $o_MonAdresse
	 * renvoie l'adresse du contact en question
	**/
	public function getMonAdresse(){
		if($this->o_MonAdresse == null){
			$this->o_MonAdresse = new Adresses();
			$this->o_MonAdresse->remplir('A_ID="'.$this->getAdresseID().'"',null);
		}
		return $this->o_MonAdresse;
	}

	/**
	 * @return String Nom, String Prenom du contact
	**/
	public function displayNomContact(){
		return $this->getNom().' '.$this->getPrenom();
	}

	/**
	 * @return int IDAdresse pour le contact
	**/	
	public function displayIDAdresse(){
		return $this->getAdresseID();
	}

	/**
	 * @return 
	 * Affiche des lignes de tableau avec les nom prénom des contacts
	 * et les liens de suppression de contact, details du contact, ajout de numero de téléphone
	**/
	public function displayRow(){
		echo '<tr>';
		echo '<td >'.$this->getNom().' '.$this->getPrenom().'</td>';
		echo '<td > <a href="http://localhost/Annuaire-PHP/index.php?details='.$this->getID().'" >details</a> </td>';
		echo '<td ><a href="http://localhost/Annuaire-PHP/index.php?supprimer='.$this->getID().'">supprimer</a>  </td>';
		echo '<td ><a href="http://localhost/Annuaire-PHP/index.php?ajouterNumero='.$this->getID().'">ajouter tel</a>  </td>';
		echo '</tr>';
	}

	public function displayAvatar(){
		echo '<img class="photo" src="'.$this->getAvatar().'"style="width:200px height:200px> ';
	}
	/**
	 * @return 
	 * Affiche des lignes de tableau avec les attibuts du Contact
	 * Appel le formulaire d'adresse du contact
	 * Appel le formulaire de téléphone du contact
	**/
	public function displayFormulaire(){
		echo '<input type="hidden"></>';
		echo '<label>Nom : </label>';
        echo '<input type="text" class="champ" name="Nom" id="Nom" value='.$this->getNom().'>';
		echo '</br>';
		echo '<label>Prénom : </label>';
        echo '<input type="text" class="champ" name="Prenom" id="Prenom" placeholder="" value='.$this->getPrenom().'>';
		echo '</br>';
		echo '<label>Date de naissance:</label>';
        echo '<input type="date" name="dateNaiss" class="champ" placeholder="" value='.$this->getDateNais().'>';
		echo '</br>';
		echo '<label>Societe : </label>';
        echo '<input type="text" class="champ" name="Societe" id="Societe" value='.$this->getSociete().'>';
		echo '</br>';
		echo '<label>Commentaire : </label>';
		echo '<Textarea  type="textera" name="Commentaire" id="Commentaire" rows=2 cols=40 wrap=physical value='.$this->getCommentaire().'></Textarea>';
		echo '</br>';
		$adresse = $this->getMonAdresse();
		$adresse->displayAdresseObject()->displayFormulaire();

		// affichage du bouton d'export de la fiche détail contact pour génération VCard
		echo '</br>';
		echo '<input class="boutonFormulaire" type="submit" value="Valider" id="boutonValider" name="Valider" class="bouton" />';
		VCard::displayButtonExport();

		$ListeTelContact= new Telephones();
		$ListeTelContact->remplir("T_ContactID = " . $this->getID());
		$ListeTelContact->displayTableWithButton("telephone");

	}
	
	
	/** 
	 * @return $id de l'objet Contact
	**/
	public static function champID() {
		return 'C_ID';
	}

	/** 
	 * @return String $req 
	 * retourne la requête de la classe Contact
	**/
	public static function getSELECT() {
		return 'SELECT C_ID, C_Nom, C_Prenom, C_DateNais, C_AdresseID, C_Societe, C_Commentaire, C_Image FROM contact '; 
	}	

	/** 
	 * @return $R 
	 * retourne le message de la requête insertion de la classe Contact
	**/
	public static function SQLInsert(array $valeurs){
		$req = 'INSERT INTO contact (C_Nom,C_Prenom,C_DateNais,C_AdresseID,C_Societe,C_Commentaire,C_Image) VALUES(?,?,?,?,?,?,?)';
		return SI::getSI()->SGBDexecuteQuery($req,$valeurs);
	}
	
	/** 
	 * @return $R
	 * retourne le message de la requête de suppression de la classe Contact
	**/
	public static function SQLDelete($valeur){
		$req = 'DELETE FROM contact WHERE C_ID = ?';
		return SI::getSI()->SGBDexecuteQuery($req,array($valeur));
	}

	/** 
	 * @return $R
	 * retourne le message de la requête de modification de la classe Contact
	**/
	public static function SQLUpdate(array $valeurs, $condition = null){
		$req = 'UPDATE contact SET C_Nom = ? ,C_Prenom = ?, C_DateNais = ?, C_Societe = ?,C_Commentaire = ? ';
		if ($condition != null)
			$req.= " WHERE $condition";
		return SI::getSI()->SGBDexecuteQuery($req,$valeurs);
	}

}

/**
 * Classe Contacts
**/
class Contacts extends Pluriel{

	/**
	 * constructeur : repose sur le constructeur parent
	**/
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @param String $condition
	 * @param String $ordre
	 * Permet la creation d'objet Contact avec les lignes retournées de la BDD
	**/
	public function remplir($condition=null, $ordre=null) {
		$req = Contact::getSELECT();
		//ajouter condition si besoin est
		if ($condition != null) {
			$req.= " WHERE $condition"; // remplace $condition car guillemet et pas simple quote
		}
		if ($ordre != null){
			$req.=" ORDER BY $ordre";
		}
		$curseur = SI::getSI()->SGBDgetPrepareExecute($req);
		//var_dump($curseur);
		foreach ($curseur as $uneLigne){
			$this->doAddObject(Contact::ajouterObjet($uneLigne));
		}
	}

	/**
	 * @param String $req
	 * @param String $limit
	 * @param String $offset
	 * Permet la creation d'objet Contact avec les lignes retournées de la BDD
	**/
	public function remplirAVECRequete($req, $limit=null, $offset=null) {

		if ($limit != null){
			$req.=" LIMIT $limit";
		}
		if ($offset != null){
			$req.=", $offset";
		}

		$curseur = SI::getSI()->SGBDgetPrepareExecute($req);
		//var_dump($curseur);
		foreach ($curseur as $uneLigne){
			$this->doAddObject(Contact::ajouterObjet($uneLigne));
		}
	}

	/**
	 * @param Int $id
	 * @param String $choix
	 * Renvoie le nom de l'objet Contact et son adresse trouvée
	**/	
	public function RechercheObjet($id,$choix){
		if($choix =="nom"){
			return $this->getObject($id)->displayNomContact();
		}
		if($choix =="adresse"){
			return $this->getObject($id)->displayIDAdresse();
		}
	}
	

	/**
	 * @return ContactID
	 * Renvoie ll'ID des contacts de la liste
	**/		
	public function RechercheID(){
		foreach ($this->getArray() as $uncontact) {
			return $uncontact->getID();
		}
	}

	/**
	 * Parcour la liste d'objet Contact
	 * Appel un afficheur pour chaque Contact
	**/	
	public function displayTable(){
		echo'<center>';
		echo'<table align="center" class="table" cellspacing="20px"  >';
		// dire à chaque élément de mon tableau : afficher le row

		foreach ($this->getArray() as $uncontact) {
			$uncontact->displayRow();
		}
		echo'</table>';
		echo'</center>';
	}

	/**
	 * Récupère un tableau de Contact en entrée et en génère un tableau en sortie
	 * @param $array tableau (array) de Contact
	 */
	public function displayTableCertainsContact($array){
		echo'<center>';
		echo'<table align="center" class="table" cellspacing="20px"  >';
		// dire à chaque élément de mon tableau : afficher le row

		foreach ($array as $uncontact) {
			$uncontact->displayRow();
		}
		echo'</table>';
		echo'</center>';
	}
	
}

?>



