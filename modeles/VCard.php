<?php

define('DS', DIRECTORY_SEPARATOR);
// Version du vCard
define("VCARD_VERSION",4.0);

/**
 * Classe représentation le contenu d'un fichier VCard et permettant son exportation çà partir
 * d'une fiche détaillé d'un Contact
 * Class VCard
 */
class VCard {


    /**
     * @var
     * Toutes les propriétés utiles au vCard
     */
    var $nom;
    var $prenom;
    var $dateNaissance;
    var $societe;
    var $commentaire;
    var $numVoie;
    var $nomVoie;
    var $ville;
    var $cp;
    var $cptAdresse;
    var $pays;
    var $telFixe;
    var $telPortable;
    var $telFaxe;
    var $telPersonnel;
    var $dir;
    var $dirDefault = "default";
    private $content;
    private $nomFichier;
    private $rev;

    /**
     * VCard constructor. Créer le répertoire et la structure du fichier VCard
     * @param string $repDownload
     * @param string $lang
     */
    function __construct($repDownload= ''){
        // si le chemin entré est correct on le choisi sinon on en choisi un autre par défaut;
        $this->dir = strlen(trim($repDownload)) > 0 ? $repDownload : $this->dirDefault;
        $this->rev = (string) date('YmdTHi00Z',time());
        if ($this->createDownloadDir() == false){
            die("ERREUR : création répertoire");
        }
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @param mixed $dateNaissance
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
    }

    /**
     * @param mixed $societe
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;
    }

    /**
     * @param mixed $commentaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    }


    /**
     * @param $numVoie
     * @param $nomVoie
     * @param $cptAdresse
     * @param $ville
     * @param $cp
     * @param $pays
     */
    public function setAdresse($numVoie,$nomVoie,$cptAdresse,$ville,$cp,$pays)
    {
        $this->nomVoie = $numVoie;
        $this->nomVoie = $nomVoie;
        $this->cptAdresse = $cptAdresse;
        $this->ville = $ville;
        $this->cp = $cp;
    }

    /**
     * @param mixed $telFixe
     */
    public function setTelFixe($telFixe)
    {
        $this->telFixe = $telFixe;
    }

    /**
     * @param mixed $telPortable
     */
    public function setTelPortable($telPortable)
    {
        $this->telPortable = $telPortable;
    }

    /**
     * @param mixed $telFaxe
     */
    public function setTelFaxe($telFaxe)
    {
        $this->telFaxe = $telFaxe;
    }

    /**
     * @param mixed $telPersonnel
     */
    public function setTelPersonnel($telPersonnel)
    {
        $this->telPersonnel = $telPersonnel;
    }

    /**
     * Définition du chemin où sera créé le vCard
     * @param $rep
     */
    public function setRepDowload($rep){
        $this->dir = $rep;
    }


    /**
     * Fonctions permettant la génération du contenu du VCard et de la génération du fichier en lui-même
     */

    /**
     * Créer le répertoire local si il n'existe pas déjà où les fichiers vCard vont être sauvegardés
     * @return bool
     */
    function createDownloadDir()
    {
        if (!is_dir($this->dir))
        {
            if (!mkdir($this->dir, 0700))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Affichage du bouton d'export de la fiche détail d'un contact
     */
    public static function displayButtonExport(){
        echo '<input class="boutonFormulaire" type="submit" value="Exporter VCard" id="boutonValider" name="Exporter" class="bouton" />';
    }

    /**
     * Création du contenu du vCard à partir des attributs chargés avant au préalable.
     */
    public function writeContentVCard(){
        $this->content = (String) "BEGIN:VCARD\r\n";
        $this->content .= (String) "VERSION:" . VCARD_VERSION . "\r\n";
        $this->content .= (String) "N;ENCODING=QUOTED-PRINTABLE:$this->nom;$this->prenom\r\n";
        $this->content .= (String) "FN;ENCODING=QUOTED-PRINTABLE:$this->nom;$this->prenom\r\n";
        $this->content .= (String) "ORG;ENCODING=QUOTED-PRINTABLE:$this->societe\r\n";
        if (strlen(trim($this->telFixe)) > 0)
            $this->content .= (String) "TEL;TYPE=HOME,voice;VALUE=uri:tel:$this->telFixe\r\n";
        if (strlen(trim($this->telFaxe)) > 0)
            $this->content .= (String) "TEL;TYPE=HOME;FAX,voice;VALUE=uri:tel:$this->telFaxe\r\n";
        if (strlen(trim($this->telPortable)) > 0)
            $this->content .= (String) "TEL;TYPE=CELL,voice;VALUE=uri:tel:$this->telPortable\r\n";
        if (strlen(trim($this->telPersonnel)) > 0)
            $this->content .= (String) "TEL;TYPE=CAR,voice;VALUE=uri:tel:$this->telPersonnel\r\n";
        $this->content .= (String) "ADR;POSTAL;ENCODING=QUOTED-PRINTABLE:'$this->numVoie;$this->nomVoie;$this->cptAdresse;$this->cp;$this->ville\r\n";
        $this->content .= (String) "BDAY:$this->dateNaissance\r\n";
        $this->content .= (String) "END:VCARD\r\n";
    }

    /**
     * Créer le fichier vCard
     */
    public function writeVCardFile(){
        if (!empty($this->nom) && (!empty($this->prenom))) {
            $this->nomFichier = (String)$this->nom . "_" . $this->prenom . '.vcf';
        }
        else{
            $this->nomFichier = (String) time() . '.vcf';
        }

        if (!isset($this->content)){
            $this->writeContentVCard();
        }
        $handle = fopen(getcwd() . '\\' . $this->dir . '\\' . $this->nomFichier, 'w');
        if ($handle === false) {
            echo "opening '$this->nomFichier' failed";
            exit;
        }
        fputs($handle, $this->content);
        fclose($handle);
        if (isset($handle)) { unset($handle); }
    }

    /*
     * Retourne le chemin absolue de l'endroit du fichier
     * Le chemin peut être utilisé pour pouvoir être téléchargé
     */
    function getCardFilePath()
    {
        $path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
        $port = (string) (($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '' );
        return (string) 'http://' . $_SERVER['SERVER_NAME'] . $port . $path_parts["dirname"] . '/' . $this->dir . '/' . $this->nomFichier;
    }

}