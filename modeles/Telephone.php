<?php
/**
* Classe Telephone
*/

class Telephone extends Element{
	
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
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new Telephones();}
		//voir si l'objet existe avec la clef
		$tmp = static::$o_INSTANCES->getObject($ligne[static::champID()]);
		if($tmp!=null){return $tmp;}
		//n'existe pas : donc INSTANCIER Telephone et mémoriser
		$tmp = new Telephone($ligne);
		static::$o_INSTANCES->doAddObject($tmp);
		return $tmp;
	}
	
	/**
	 * @return $o_INSTANCES
	 * renvoie liste instances
	**/
	public static function getInstances(){
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new Telephones();}
		return static::$o_INSTANCES;
	}
		
	/**
	 * @param $id
	 * @return Adresse $objet
	 * doit impérativement trouver Telephone ayant pour id le paramètre
	**/
	public static function mustFind($id){
		if (static::$o_INSTANCES == null){static::$o_INSTANCES = new Telephones();}
		// regarder si instance existe
		$tmp = static::$o_INSTANCES->getObject($id);
		if($tmp!=null) {return $tmp;}
		//sinon pas trouver; chercher dans la BDD
		$req = static::getSELECT().' where T_numero =?';
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
	public function getNumero(){
		return $this->getField('T_numero');
	}
	
	public function getTypeTelID(){
		return $this->getField('T_TypeTelID');
	}
	
	public function getContactID(){
		return $this->getField('T_ContactID');
	}
	
	private $o_typeTelephone;

	/**
	 * @return o_typeTelephone
	 * renvoie le type du Telephone
	**/
	public function getTypeTelephone(){
		if($this->o_typeTelephone == null){
			$this->o_typeTelephone = new TypeTelephones();
			$this->o_typeTelephone->remplir('TY_ID="'.$this->getTypeTelID().'"',null);
		}
		return $this->o_typeTelephone;
	}
	

	/**
	 * @return 
	 * Affiche une ligne formulaire avec le numero du telephone
	**/
	public function displayRow(){
		echo '<td align="center">'.$this->getNumero().'</td>';
	}
	
	/**
	 * @return 
	 * Affiche un input de formulaire avec le numero du telephone
	**/
	public function displayInput($name){
		echo '<input class="champ" type="text" value="' . $this->getNumero() .'" id="bouton" name="' . $name . '" />';
		echo '<input class="champ" type="hidden" value="' . $this->getNumero() .'" id="bouton" name="TelInit" />';
	}
	
	/**
	 * @return 
	 * Affiche un bouton modifier de formulaire
	**/
	public function displayButton(){
		echo '<input class="boutonFormulaire" type="submit" value="Modifier" id="boutonValider" name="modifier" class="bouton" />';
	}

	/**
	 * @return 
	 * Affiche un bouton supprimer de formulaire
	**/
	public function displayDelete(){
		echo '<input class="boutonFormulaire" type="submit" value="Supprimer" id="boutonValider" name="supprimer" class="bouton" />';
	}
	
	/** 
	 * @return $id de l'objet Telephone
	**/
	public static function champID() {
		return 'T_numero';
	}
	
	/** 
	 * @return String $req 
	 * retourne la requête de la classe Telephone
	**/
	public static function getSELECT() {
		return 'SELECT T_numero,T_TypeTelID,T_ContactID FROM telephone';
	}	

	/** 
	 * @return $R 
	 * retourne le message de la requête insertion de la classe Telephone
	**/
	public static function SQLInsert(array $valeurs){
		$req = 'INSERT INTO telephone (T_numero,T_TypeTelID,T_ContactID) VALUES(?,?,?)';
		return SI::getSI()->SGBDexecuteQuery($req,$valeurs);
	}
	
	/** 
	 * @return $R
	 * retourne le message de la requête de suppression de la classe Telephone
	**/
	public static function SQLDelete($valeur){
		$req = 'DELETE FROM telephone WHERE T_numero = ?';
		return SI::getSI()->SGBDexecuteQuery($req,array($valeur));
	}

	/** 
	 * @return $R
	 * retourne le message de la requête de modification de la classe Telephone
	**/
	public static function SQLUpdate(array $valeurs){
		$req = 'UPDATE telephone SET T_numero = ? WHERE T_numero = ? and T_ContactID = ?';
		return SI::getSI()->SGBDexecuteQuery($req,$valeurs);
	}

}

/**
 * Classe Telephones
**/
class Telephones extends Pluriel{

	/**
	 * constructeur : repose sur le constructeur parent
	**/
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @param String $condition
	 * @param String $ordre
	 * Permet la creation d'objet Telephone avec les lignes retournées de la BDD
	**/
	public function remplir($condition=null, $ordre=null) {
		$req = Telephone::getSELECT();
		//ajouter condition si besoin est
		if ($condition != null) {
			$req.= " WHERE $condition"; // remplace $condition car guillemet et pas simple quote
		}
		if ($ordre != null){
			$req.=" ORDER BY $ordre";
		}
		$curseur = SI::getSI()->SGBDgetPrepareExecute($req);
		foreach ($curseur as $uneLigne){
			$this->doAddObject(Telephone::ajouterObjet($uneLigne));
		}
	}

	/**
	 * Parcour la liste d'objet Telephone
	 * Appel des afficheurs pour chaque Telephone
	**/
	public function displayTableWithButton($name){
		echo'<center>';
		echo '<h2>Téléphones du contact</h2>';
		echo'<ul style="list-style: none;">';
		
			
		// dire à chaque élément de mon tableau : afficher le row
		foreach ($this->getArray() as $untelephone) {
			echo '<form method="post" >';
			echo '<li>';
			$TypeTelephone= $untelephone->getTypeTelephone()->displayTypeTel();
			echo '<label>' . $TypeTelephone . '</label>';
			$untelephone->displayInput($name);
			$untelephone->displayButton();
			$untelephone->displayDelete($untelephone->getNumero());
			echo '</li>';
			echo '</form>';
		}
		echo'</ul>';
		echo'</center>';
	}

	public function displaySelect($name){
		echo'<select style="width:auto" class="form-control" type="Text" required="required" name="'.$name.'">';
		echo '<option>  </option>';
		// dire à chaque élément de mon tableau : afficher le row
		foreach ($this->getArray() as $untelephone) {
			$untelephone->option();
		}
		echo '</select>';
	}


	public function getTelOfType($typeTel){
		$this->remplir("T_TypeTelID = " . $typeTel);
		$this->getFirst()->getNumero();
	}
}
?>
