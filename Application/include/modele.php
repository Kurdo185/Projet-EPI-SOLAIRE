<?php
/** 
 * Classe d'accès aux données. 
 * Utilise les services de la classe PDO
 * pour l'application GECET
 * $monPdo de type PDO 
 * $monModele qui contiendra l'unique instance de la classe
 *
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 */

class Modele{   		
    private static $serveur = 'mysql:host=172.16.203.112';
    private static $bdd    = 'dbname=getcet';   		
    private static $user   = 'sio' ;    		
    private static $mdp    = 'slam' ;	
    private static $monPdo;
    private static $monModele = null;

    /**
     * Constructeur privé
     */				
    private function __construct(){
        Modele::$monPdo = new PDO(Modele::$serveur.';'.Modele::$bdd, Modele::$user, Modele::$mdp); 
        Modele::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct(){
        Modele::$monPdo = null;
    }

    /**
     * Retourne l'unique instance de la classe
     */
    public static function getModele(){
        if(Modele::$monModele == null){
            Modele::$monModele = new Modele();
        }
        return Modele::$monModele;  
    }

    /**
     * Teste l'existence d'un employe (comparaison avec sha1)
     * @param string $login
     * @param string $mdp (en clair)
     * @return int 1 si ok, 0 sinon
     */
    public function testConnexionEmploye($login, $mdp){
        // Si ta colonne en BD s'appelle MDP (MAJ), remplace 'mdp' par 'MDP' dans la requête
        $req = "SELECT mdp FROM Employe WHERE Login = :login";
        $stmt = Modele::$monPdo->prepare($req);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ligne) {
            return 0;
        }
        $hashStocke = $ligne['mdp'];
        $hashSaisi = sha1($mdp);
        return ($hashSaisi === $hashStocke) ? 1 : 0;
    }

    /**
     * Retourne les informations d'un employe si mdp ok (sha1)
     * @param string $login
     * @param string $mdp (en clair)
     * @return array|false
     */
    public function getInfosEmploye($login, $mdp){
        // Si ta colonne en BD s'appelle MDP (MAJ), remplace 'mdp' par 'MDP' dans la requête
        $req = "SELECT Employe.idEmploye as id, Employe.Nom as nom, Employe.Prenom as prenom, mdp FROM Employe WHERE Login = :login";
        $stmt = Modele::$monPdo->prepare($req);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ligne) {
            return false;
        }
        $hashStocke = $ligne['mdp'];
        $hashSaisi = sha1($mdp);
        if ($hashSaisi === $hashStocke) {
            unset($ligne['mdp']);
            return $ligne;
        }
        return false;
    }

    /**
     * Retourne les informations d'un employe à partir de son id
     * @param int $id
     * @return array
     */
    public function getInfosEmployeById($id){
        $req = "select Employe.nom as nom, Employe.prenom as prenom, login, dateEmbauche from Employe 
        where Employe.idEmploye=$id";
        $rs = Modele::$monPdo->query($req);
        $ligne = $rs->fetch();
        if ($ligne && isset($ligne['dateEmbauche'])) {
            if (function_exists('dateAnglaisVersFrancais')) {
                $ligne['dateEmbauche'] = dateAnglaisVersFrancais($ligne['dateEmbauche']);
            }
        }
        return $ligne;
    }

    /**
     * Ajouter un employé en hachant le mdp avec sha1
     * @param string $nom
     * @param string $prenom
     * @param string $login
     * @param string $mdp (en clair)
     * @param string|null $dateEmbauche (YYYY-MM-DD)
     * @return int|false idEmploye ou false
     */
    public function ajouterUnEmploye($nom, $prenom, $login, $mdp, $dateEmbauche = null){
        // 1) Vérifier doublon login
        $req = "SELECT COUNT(*) AS nb FROM Employe WHERE Login = :login";
        $stmt = Modele::$monPdo->prepare($req);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($ligne && $ligne['nb'] > 0) {
            return false; // login déjà utilisé
        }

        // 2) Hacher (sha1 - demandé)
        $hash = sha1($mdp);

        // 3) Insert
        if ($dateEmbauche === null) {
            $req = "Insert into Employe (Nom, Prenom, Login, mdp) values (:nom, :prenom, :login, :mdp)";
            $stmt = Modele::$monPdo->prepare($req);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':mdp', $hash, PDO::PARAM_STR);
            $params = null;
        } else {
            $req = "Insert into Employe (Nom, Prenom, Login, mdp, dateEmbauche) values (:nom, :prenom, :login, :mdp, :dateEmbauche)";
            $stmt = Modele::$monPdo->prepare($req);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':mdp', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':dateEmbauche', $dateEmbauche, PDO::PARAM_STR);
            $params = null;
        }

        $ok = $stmt->execute();
        if ($ok) {
            $id = Modele::$monPdo->lastInsertId();
            return $id === null ? false : (int)$id;
        }
        return false;
    }

    /**
     * Retourne toutes les lignes de Acheteurs
     * @return array
     */
    public function getLesAcheteurs(){
        $req = "select * from acheteur, habitant where habitant.idHabitant = acheteur.idHabitant and habitant.idFoyer = acheteur.idFoyer;";	
        $res = Modele::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes; 
    }

    /**
     * Supprimer l'acheteur dont l'id est passé en argument
     */
    public function supprimerAcheteur($idAcheteur){
        $req = "Delete from Acheteur where Acheteur.ID = $idAcheteur ";
        Modele::$monPdo->exec($req);
    }	

    /**
     * Ajouter le commerce
     */
    public function ajouterUnCommerce($nom, $rue, $cp, $ville){
        $req = "Insert into commerce (nom, rue, codePostal, ville) values ('$nom', '$rue', '$cp', '$ville'); ";
        Modele::$monPdo->exec($req);
    }	

    /**
     * Retourne toutes les lignes de Commerces
     */
    public function getLesCommerces(){
        $req = "select * from commerce";	
        $res = Modele::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes; 
    }

    /**
     * Supprimer le commerce dont l'id est passé en argument
     */
    public function supprimerCommerce($id){
        $req = "Delete from commerce where commerce.id = $id ";
        Modele::$monPdo->exec($req);
    }	
}
?>
