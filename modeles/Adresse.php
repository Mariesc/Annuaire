<?php
/**
 * Classe Adresse d'un contact
**/

class Adresse extends Element{
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
		if (static::$o_INSTANCES ==null){static::$o_INSTANCES = new Adresses();}
		//voir si l'objet existe avec la clef
		$tmp = static::$o_INSTANCES->getObject($ligne[static::champID()]);
		if($tmp!=null){return $tmp;}
		//n'existe pas : donc INSTANCIER Adresse et mémoriser
		$tmp = new Adresse($ligne);
		static::$o_INSTANCES->doAddObject($tmp);
		return $tmp;
	}
	
	/**
	 * @return $o_INSTANCES
	 * renvoie liste instances
	**/
	public static function getInstances(){
		if (static::$o_INSTANCES ==null){
			static::$o_INSTANCES = new Adresses();
		}
		return static::$o_INSTANCES;
	}
		
	/**
	 * @param $id
	 * @return Adresse $objet
	 * doit impérativement trouver l'Adresse ayant pour id le paramètre
	**/
	public static function mustFind($id){
		if (static::$o_INSTANCES == null){static::$o_INSTANCES = new Adresses();}
		// regarder si instance existe
		$tmp = static::$o_INSTANCES->getObject($id);
		if($tmp!=null) {return $tmp;}
		//sinon pas trouver -> chercher dans la BDD
		$req = static::getSELECT().' where A_ID =?';
		$ligne = SI::getSI()->SGBDgetLigne($req, $id);
		return static::ajouterObjet($ligne);
	}
	
	/**
	 * constructeur : repose sur le constructeur parent
	**/
	protected function __construct($theLigne) {
		parent::__construct($theLigne);
	}
	
	/**
	 * @return getField
	 * renvoie la valeur du champ spécifié en paramètre
	**/
	public function getID(){
		return $this->getField('A_ID');
	}
	
	public function getNumVoie(){
		return $this->getField('A_NumVoie');
	}
	
	public function getNomVoie(){
		return $this->getField('A_NomVoie');
	}
	
	public function getComplementAdresse(){
		return $this->getField('A_ComplementAdresse');
	}
	
	public function getVille(){
		return $this->getField('A_Ville');
	}
	
	public function getCodePostal(){
		return $this->getField('A_CodePostal');
	}

	public function getPaysID(){
		if ($this->getField('A_PaysID') != null)
			return $this->getField('A_PaysID');
		else
			return null;
	}

	private $o_pays;
	
	/**
	 * @return o_pays
	 * renvoie le pays de l'adresse en question
	**/
	public function getPays(){
		if($this->o_pays == null){
			$this->o_pays = new ListPays();
			$this->o_pays->remplir('P_ID="'.$this->getPaysID().'"',null);
		}
		return $this->o_pays;
	}
	
	/**
	 * @return 
	 * renvoie les données de l'Adresse
	**/
	public function getAdresse(){
		return $this->getNumVoie() . " " . $this->getNomVoie() . " " . $this->getComplementAdresse() . " "
			. $this->getCodePostal() . " " . $this->getVille() . " " . $this->getPays()->RechercheNom();
	}

	/**
	 * @return 
	 * Affiche un formulaire avec les attributs de l'Adresse
	**/
	public function displayFormulaire(){
		echo '<label>Numero de la voie : </label>';
        echo '<input type="number" class="champ" name="NumVoie" id="NumVoie" value='.$this->getNumVoie().'>';
		echo '</br>';
		echo '<label>Nom de la voie : </label>';
        echo '<input style="width:200px" type="text" class="champ" name="NomVoie" id="NomVoie" value='.$this->getNomVoie().'>';
		echo '</br>';
		echo '<label>Complément d\'adresse : </label>';
        echo '<Textarea  type="textera" name="ComplAdresse"  id="ComplAdresse" rows=1 cols=30 wrap=physical value='.$this->getComplementAdresse().'></Textarea>';
		echo '</br>';
		echo '<label>Ville : </label>';
        echo '<input type="text" class="champ" name="Ville" id="Ville" value='.$this->getVille().'>';
		echo '<label>Code Postal : </label>';
        echo '<input type="text" class="champ" name="CodePostal" id="CodePostal" value='.$this->getCodePostal().'>';
		echo '</br>';
		echo '<label>Pays : </label>';
		$ListePays = new ListPays();
        $ListePays->remplir(null,"P_Nom ASC");

        $nomPays = Pays::mustFind($this->getPaysID())->getNom();
        Pays::getInstances()->displaySelect("Pays",$nomPays);
	}
	
	/** 
	 * Affiche les attributs de l'Adresse en ligne
	**/
	public function displayRow(){
		echo '<td align="center">'.$this->getNumVoie().'</td>';
		echo '<td align="center">'.$this->getNomVoie().'</td>';
		echo '<td align="center">'.$this->getVille().'</td>';
		echo '<td align="center">'.$this->getCodePostal().'</td>';
	}
	

	/** 
	 * @return $id de l'objet Adresse
	**/
	public static function champID() {
		return 'A_ID';
	}
	
	/** 
	 * @return String $req 
	 * retourne la requête de la classe Adresse
	**/
	public static function getSELECT() {
		return 'SELECT A_ID,A_NumVoie,A_NomVoie,A_ComplementAdresse,A_Ville,A_CodePostal,A_PaysID FROM adresse';
	}

	/** 
	 * @return $R 
	 * retourne le message de la requête insertion de la classe Adresse
	**/
	public static function SQLInsert(array $valeurs){
		$req = 'INSERT INTO adresse (A_NumVoie,A_NomVoie,A_ComplementAdresse,A_Ville,A_CodePostal,A_PaysID) VALUES(?,?,?,?,?,?)';
		return SI::getSI()->SGBDexecuteQuery($req,$valeurs);
	}
	
	/** 
	 * @return $R
	 * retourne le message de la requête de suppression de la classe Adresse
	**/
	public static function SQLDelete($valeur){
		$req = 'DELETE FROM adresse WHERE A_ID = ?';
		return SI::getSI()->SGBDexecuteQuery($req,array($valeur));
	}

	/** 
	 * @return $R
	 * retourne le message de la requête de modification de la classe Adresse
	**/
	public static function SQLUpdate(array $valeurs, $condition = null){
		$req = 'UPDATE Adresse SET A_NumVoie = ?,A_NomVoie = ? ,A_ComplementAdresse = ? ,A_Ville = ? ,A_CodePostal = ? ';
		if ($condition != null)
			$req.= " WHERE $condition";
		return SI::getSI()->SGBDexecuteQuery($req,$valeurs);
	}

}


/**
 * Classe Adresses
**/
class Adresses extends Pluriel{

	/**
	 * constructeur : repose sur le constructeur parent
	**/
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @param String $condition
	 * @param String $ordre
	 * Permet la creation d'objet Adresse avec les lignes retournées de la BDD
	**/
	public function remplir($condition=null, $ordre=null) {
		$req = Adresse::getSELECT();
		//ajouter condition si besoin est
		if ($condition != null) {
			$req.= " WHERE $condition"; // remplace $condition car guillemet et pas simple quote
		}
		if ($ordre != null){
			$req.=" ORDER BY $ordre";
		}
		$curseur = SI::getSI()->SGBDgetPrepareExecute($req);
		foreach ($curseur as $uneLigne){
			$this->doAddObject(Adresse::ajouterObjet($uneLigne));
		}
	}

	/**
	 * @param Int $id
	 * @param String $choix
	 * Renvoie le pays de l'objet Adresse trouvée
	**/
	public function RechercheObjet($id,$choix){
		if($choix =="pays"){
			return $this->getObject($id)->getPays();
		}
	}
	
	/**
	 * Parcour la liste d'objet Adresse
	 * Appel un afficheur pour chaque Adresse
	**/
	public function displayTable(){
		foreach ($this->getArray() as $uneadresse) {
			$uneadresse->displayRow();
		}
	}
	
	/**
	 * @return $idAdresse
	 * Renvoie l'ID des adresses de la liste
	**/
	public function displayAdresse(){
		foreach ($this->getArray() as $uneadresse) {
			return $uneadresse->getID();
		}
	}

	/**
	 * @return mixed
	 * Renvoie les adresses en format lisible.
	 */
	public function displayFormatedAdresse(){
		foreach ($this->getArray() as $uneadresse) {
			return $uneadresse->getAdresse();
		}
	}

	/**
	 * @return $idAdresse
	 * Renvoie les adresse sous forme d'objet
	**/
	public function displayAdresseObject(){
		foreach ($this->getArray() as $uneadresse) {
			return $uneadresse;
		}
	}

	
}
?>
