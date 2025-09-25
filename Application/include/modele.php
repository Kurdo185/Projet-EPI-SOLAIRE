<?php
/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application GECET
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monModele qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 */

class Modele{   		
      	private static $serveur = 'mysql:host=localhost';
      	private static $bdd = 'dbname=getcet';   		
      	private static $user = 'root' ;    		
      	private static $mdp = '' ;	
		private static $monPdo;
		private static $monModele = null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct(){
    	Modele::$monPdo = new PDO(Modele::$serveur.';'.Modele::$bdd, Modele::$user, Modele::$mdp); 
		Modele::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		Modele::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 * Appel : $instanceModele = Modele::getModele();
 * @return l'unique objet de la classe Modele
 */
	public  static function getModele(){
		if(Modele::$monModele == null){
			Modele::$monModele = new Modele();
		}
		return Modele::$monModele;  
	}
	
/**
 * Teste l'existence d'un employe
 
 * @param $login 
 * @param $mdp
 * @return $verif
*/
	public function testConnexionEmploye($login, $mdp){
		$req = "select count(*) as nb from Employe where Employe.Login='$login' and Employe.mdp='$mdp'";
		$rs = Modele::$monPdo->query($req);
		$ligne = $rs->fetch();
		$res = $ligne['nb'];
		return $res;
	}
	
/**
 * Retourne les informations d'un employe
 
 * @param $login 
 * @param $mdp
 * @return l'idEmploye, le nom et le prénom sous la forme d'un tableau associatif 
*/
	public function getInfosEmploye($login, $mdp){
		$req = "select Employe.idEmploye as id, Employe.Nom as nom, Employe.Prenom as prenom from Employe 
		where Employe.login='$login' and Employe.MDP='$mdp'";
		$rs = Modele::$monPdo->query($req);
		$ligne = $rs->fetch();
		return $ligne;
	}

/**
 * Retourne les informations d'un employe à partir de son id
 
 * @param $id 
 * @return les infos sous la forme d'un tableau associatif 
*/
	public function getInfosEmployeById($id){
		$req = "select Employe.nom as nom, Employe.prenom as prenom, login, dateEmbauche from Employe 
		where Employe.idEmploye=$id";
		$rs = Modele::$monPdo->query($req);
		$ligne = $rs->fetch();
		$ligne['dateEmbauche'] = dateAnglaisVersFrancais($ligne['dateEmbauche']);
		return $ligne;
	}
	
	
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de Acheteurs
 * @return tous les champs sous la forme d'un tableau associatif 
*/
	public function getLesAcheteurs(){
	    $req = "select * from acheteur, habitant where habitant.idHabitant = acheteur.idHabitant and habitant.idFoyer = acheteur.idFoyer;";	
		$res = Modele::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
	
/**
 * Supprimer l'acheteur dont l'id est passé en argument
 
 * @param $idAcheteur 
*/
	public function supprimerAcheteur($idAcheteur){
		$req = "Delete from Acheteur where Acheteur.ID = $idAcheteur ";
		Modele::$monPdo->exec($req);
	}	
	

/**
 * Ajouter le commerce dont les infos sont passées en arguments
 
 * @param $nom
 * @param $rue
 * @param $cp
 * @param $ville
*/

/**
 * Ajouter un acheteur dont les infos sont passées en arguments
 * @param $idHabitant
 * @param $idFoyer
 * @param $tel
 * @param $mail
 * @param $justifIdentite
 * @param $justifDomicile
 */
public function ajouterAcheteur($idHabitant, $idFoyer, $tel, $mail, $justifIdentite, $justifDomicile){
    $statut = ($justifIdentite == 1 && $justifDomicile == 1) ? 'valide' : 'en_attente';

    $req = "INSERT INTO acheteur (idHabitant, idFoyer, telephone, mail, justificatif_identite, justificatif_domicile, statut)
            VALUES ($idHabitant, $idFoyer,
                    " . Modele::$monPdo->quote($tel) . ",
                    " . Modele::$monPdo->quote($mail) . ",
                    $justifIdentite, $justifDomicile,
                    " . Modele::$monPdo->quote($statut) . ")";
    Modele::$monPdo->exec($req);
}




	public function ajouterUnCommerce($nom, $rue, $cp, $ville){
		$req = "Insert into commerce (nom, rue, codePostal, ville) values ('$nom', '$rue', '$cp', '$ville'); ";
		Modele::$monPdo->exec($req);
	}	

/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de Commerces
 * @return tous les champs sous la forme d'un tableau associatif 
*/
	public function getLesCommerces(){
	    $req = "select * from commerce";	
		$res = Modele::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
	
/**
 * Supprimer le commerce dont l'id est passé en argument
 * @param $id 
*/
	public function supprimerCommerce($id){
		$req = "Delete from commerce where commerce.id = $id ";
		Modele::$monPdo->exec($req);
	}	


}
?>