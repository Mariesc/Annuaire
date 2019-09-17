<?php
/**
	* Classe Pays
*/
class Pays extends Element{
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
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new ListPays();}
		//voir si l'objet existe avec la clef
		$tmp = static::$o_INSTANCES->getObject($ligne[static::champID()]);
		if($tmp!=null){return $tmp;}
		//n'existe pas : donc INSTANCIER Pays et mémoriser
		$tmp = new Pays($ligne);
		static::$o_INSTANCES->doAddObject($tmp);
		return $tmp;
	}
	
	/**
	 * @return $o_INSTANCES
	 * renvoie liste instances
	**/
	public static function getInstances(){
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new ListPays();}
		return static::$o_INSTANCES;
	}
		
	/**
	 * @param $id
	 * @return Pays $objet
	 * doit impérativement trouver le Pays ayant pour id le paramètre
	**/
	public static function mustFind($id){
		if (static::$o_INSTANCES == null){static::$o_INSTANCES = new ListPays();}
		// regarder si instance existe
		$tmp = static::$o_INSTANCES->getObject($id);
		if($tmp!=null) {return $tmp;}
		//sinon pas trouver; chercher dans la BDD
		$req = static::getSELECT().' where P_ID =?';
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
		return $this->getField('P_ID');
	}
	
	public function getNom(){
		return $this->getField('P_Nom');
	}
	
	/**
	 * @return 
	 * Affiche une ligne de formulaire avec le nom du Pays
	**/
	public function displayRow(){
		echo '<td align="center">'.$this->getNom().'</td>';

	}
	
	/**
	 * @return 
	 * Affiche une liste déroulante des Pays
	**/
	public function option(){
		$tmp = $this->getID();
		echo '<option value ="'.$tmp.'">';
		echo utf8_encode ($this->getNom());
		echo '</option>';
	}
	

	/** 
	 * @return $id de l'objet Pays
	**/
	public static function champID() {
		return 'P_ID';
	}
	
	/** 
	 * @return String $req 
	 * retourne la requête de la classe Pays
	**/
	public static function getSELECT() {
		return 'SELECT P_ID,P_Nom FROM Pays';
	}	


}

/**
 * Classe pour gérer des Pays
**/
class ListPays extends Pluriel{

	/**
	 * constructeur : repose sur le constructeur parent
	**/
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @param String $condition
	 * @param String $ordre
	 * Permet la creation d'objet Pays avec les lignes retournées de la BDD
	**/
	public function remplir($condition=null, $ordre=null) {
		$req = Pays::getSELECT();
		//ajouter condition si besoin est
		if ($condition != null) {
			$req.= " WHERE $condition"; // remplace $condition car guillemet et pas simple quote
		}
		if ($ordre != null){
			$req.=" ORDER BY $ordre";
		}
		$curseur = SI::getSI()->SGBDgetPrepareExecute($req);
		foreach ($curseur as $uneLigne){
			$this->doAddObject(Pays::ajouterObjet($uneLigne));
		}
	}

	/**
	 * Renvoie l'ID d'un pays de la liste
	**/
	public function RechercheID(){
		foreach ($this->getArray() as $unpays) {
			return $unpays->getID();
		}
	}

	/**
	 * Renvoie le nom d'un pays de la liste
	**/
	public function RechercheNom(){
		foreach ($this->getArray() as $unPays) {
			return $unPays->getNom();
		}
	}



	/**
	 * Parcour la liste d'objet  Pays
	 * Appel un afficheur pour chaque Pays
	**/	
	public function displayTable(){
		foreach ($this->getArray() as $unpays) {
			$unpays->displayRow();
		}
	}

	/**
	 * @param String $name, String $selection
	 * Appel un afficheur de liste déroulante pour la liste de Pays
	**/	
	public function displaySelect($name, $selection = null){
		echo'<select style="width:auto" class="form-control"  type="Text" required="required" name="'.$name.'">';
		if ($selection == null)
			echo '<option selected="selected">pas de sélection</option>';
		else
			echo '<option selected="selected">' . $selection . '</option>';
		 /**
		 * Evite de devoir tester pour chaque occurence du tableau de pays.
		 */
		$paysList = $this->getArray();

		$listeContacts = new ListPays();
		$listeContacts->remplir("P_Nom = " . $selection, "DESC Limit 1");
		$paysId = Pays::getInstances()->RechercheID();
		$pays = $this->getObject($paysId);
		$indexPaysASelectionner = array_search($pays, $paysList);
		unset($paysList[$indexPaysASelectionner]);

		// dire à chaque élément de mon tableau : afficher le row
		foreach ($paysList as $unpays) {
			$unpays->option();
		}
		echo '</select>';
	}
	
}
?>
