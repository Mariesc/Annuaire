<?php
/**
 * Classe de base de toutes les classes du site
**/

abstract class Element {

	private $ligne ;

	/**
	 * Constructeur de la classe Element
	 *
	 * @param $theLigne 
	 * Stockage des données de l'instance, issues de la BDD
	**/
	protected function __construct($theLigne) {
		$this->ligne = $theLigne;
	}
	
	/**
	 * @param $nom 
	 * renvoie la valeur du champ spécifié en paramètre
	**/
	protected function getField($nom) {
		return $this->ligne[$nom] ;
	}

	/**
	 * @return ID
	 * renvoie l'id de la ligne
	**/
	public function getID() { 
		return $this->ligne[static::champID()];
	}

	/**
	 * @return $nom 
	 * renvoie le getselect suivi de 'WHERE condition sur clé primaire'
	**/
	public static function getSELECTOne() {
		return static::getSELECT().' WHERE '.static::champID().'=?';
	}
}

?>
