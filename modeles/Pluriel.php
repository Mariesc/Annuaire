<?php 
/**
 * Classe de base de toutes les classes pluriel du site
**/

abstract class Pluriel {
	
	/**
	 * Tableau Associatif
	**/
	private $TB ;
	
	/**
	 * Constructeur de la classe Pluriel
	 *
	 * Stockage des données de la liste issues de la BDD
	**/
	public function __construct () { $this->TB = array() ;}
	
	/**
	 * @return liste 
	 * renvoie une liste
	**/
	public function getArray() {
		return $this->TB;
	}

	/**
	 * @return $TB
	 * renvoie le nombre d'elements dans la liste
	**/
	public function getNombre() {
		return count($this->TB) ;
	}
	
	/**
	 * @return $sonId
	 * renvoie l'objet correspondant à l'ID (peut renvoyer null)
	**/
	public function getObject($sonId) {
		if (!$this->isKey($sonId)) {return null;}
		return $this->TB[$sonId];
	}
	
	/**
	 * @return TB[0]
	 * renvoie la première valeur de la liste TB -> NON SECURISE
	**/
	public function getFirst() {
		return array_values($this->TB)[0] ;
	}
	
	/**
	 * @param Element $objElement
	 * mémorisation dans le TB associatif de l'objet avec son ID
	**/
	public function doAddObject(Element $objElement) {		
		$this->TB[$objElement->getID()] = $objElement ;
	}
	
	/**
	 * @return $id
	 * renvoie true si le paramètre est KEY dans le Tableau associatif
	**/
	public function isKey($id) {
		return array_key_exists($id, $this->TB) ;
	}
	
	/**
	 * reffacement de la liste
	**/
	public function clear() {
		array_splice($this->TB,0) ;
	}
	
  
}
?>