<?php
/**
 * Classe SI permet de gérer la connexion à la BDD
**/

class SI {
	private $cnx ;
	private static $theSI;

	/**
	 * Constructeur de la classe SI
	 * Permet la connexion à la BDD annuairebdd
	**/
	private function __construct() {
		$this->cnx = new PDO('mysql:host=127.0.0.1; dbname=annuairebdd',
										'root', '',
										array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES latin1'));
		$this->cnx->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		static::$theSI=$this; // memorisation au static
	}

	/**
	 * @return $theSI
	 * renvoie le SI Singleton
	**/
	public static function getSI() {
		if (static::$theSI==null) {
			static::$theSI = new SI();
		}
		return static::$theSI;
	}

	/**
	 * @param String $req 
	 * @return cnx->prepare($req)
	 * prepare la requête $req dans la BDD
	**/
	public function SGBDgetPrepare($req) {
		return $this->cnx->prepare($req);
	}
	
	/**
	 * @param String $req 
	 * @return $stmt
	 * prepare et execute la requête $req dans la BDD
	**/
	public function SGBDgetPrepareExecute($req) {
		$stmt = $this->SGBDgetPrepare($req);
		$stmt->execute() ;
		return $stmt ;
	}
	
	/**
	 * @param String $req , @param int $id
	 * @return $work
	 * Permet de renvoyer une seule ligne
	**/
	public function SGBDgetLigne($req,$id){
		$work = $this->SGBDgetPrepare($req);
		$work->bindParam(1,$id);
		$work->execute();
		return $work->fetch();
	}

	/**
	 * @param String $requete , @param array $valeurs
	 * @return $R
	 * Permet de renvoyer une confirmation ou erreur de la requête $requete vers la BDD
	**/
	public function SGBDexecuteQuery($requete, array $valeurs) {
		$work = $this->SGBDgetPrepare($requete) ;
		$i=0;
		foreach ($valeurs as &$v) {
			$i++;
			$work->bindParam($i, $v);
		}
		$R = array();
		try {
			$work->execute();
			$tberr = $work->errorInfo();
			if ($tberr[0]=='00000') {
				$tmp = $work->rowCount();
				if ($tmp==0) {
					$R = array(	'pgstatus' => 0,
									'pgerror' => 0,
									'pgcomment' => 'aucune information modifiée');
				} else {
					$R = array(	'pgstatus' => $tmp,
									'pgerror' => 0,
									'pgcomment' => "l'opération a affecté $tmp occurrence(s)");
				}
			} else {
				$R = array(	'pgstatus' => -1,
								'pgerror' => $tberr[0],
								'pgcomment' => $tberr[2]);
			}
		} catch (Exception $e) {
				$R = array(	'pgstatus' => -3,
								'pgerror' => 0,
								'pgcomment' => $e->getMessage());
		}
		return $R;
	}

}
?>
