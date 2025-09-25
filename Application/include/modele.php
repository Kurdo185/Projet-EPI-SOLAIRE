<?php
/** 
 * Classe d'accès aux données. 
 * Utilise les services de la classe PDO pour l'application GECET
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monModele qui contiendra l'unique instance de la classe
 * @package default
 * @author Cheri Bibi
 * @version 1.0
 */

class Modele{   		
    private static $serveur = 'mysql:host=localhost';
    private static $bdd     = 'dbname=getcet';	
    private static $user    = 'root';    		
    private static $mdp     = '';	
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
    public static function getModele(){
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
     * (avec alias EXACTS attendus par la vue)
     */
    public function getLesAcheteurs(){
        $req = "SELECT 
                    a.id                      AS id,                 -- clé de la table acheteur
                    h.nom                     AS nom,
                    h.prenom                  AS prenom,
                    h.telephonePortable       AS telephonePortable,  -- adapte si nécessaire
                    h.mail                    AS mail,
                    h.dateNaiss               AS dateNaiss,          -- adapte si nécessaire
                    a.justificatif_identite   AS justificatif_identite,
                    a.justificatif_domicile   AS justificatif_domicile,
                    a.statut                  AS statut
                FROM acheteur a
                JOIN habitant h 
                  ON h.idHabitant = a.idHabitant
                 AND h.idFoyer    = a.idFoyer";
        $res = Modele::$monPdo->query($req);
        return $res->fetchAll();
    }
	
    /**
     * Supprimer l'acheteur dont l'id est passé en argument
     * @param $idAcheteur 
     */

public function supprimerAcheteur($idAcheteur){
    $idAcheteur = (int)$idAcheteur;

    // Récupérer les id des commandes de cet acheteur
    $cmdIds = [];
    $st = Modele::$monPdo->query("SELECT id FROM commande WHERE idAcheteur = $idAcheteur");
    if ($st) {
        $cmdIds = $st->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    Modele::$monPdo->beginTransaction();
    try {
        if (!empty($cmdIds)) {
            $ids = implode(',', array_map('intval', $cmdIds));

            // 1) Supprimer les LIGNES / DÉTAILS de commande si de telles tables existent
            // Essaye plusieurs noms courants ; ignore si la table n'existe pas
            foreach ([
                "DELETE FROM ligne_commande WHERE idCommande IN ($ids)",
                "DELETE FROM detailcommande WHERE idCommande IN ($ids)",
                "DELETE FROM contenir WHERE idCommande IN ($ids)",
            ] as $sqlTry) {
                try { Modele::$monPdo->exec($sqlTry); } catch (Exception $e) { /* ignore */ }
            }

            // 2) Supprimer les commandes
            Modele::$monPdo->exec("DELETE FROM commande WHERE id IN ($ids)");
        }

        // 3) Supprimer les autres tables enfant liées à l’acheteur
        Modele::$monPdo->exec("DELETE FROM acheter WHERE idAcheteur = $idAcheteur");

        // 4) Supprimer l’acheteur
        Modele::$monPdo->exec("DELETE FROM acheteur WHERE id = $idAcheteur");

        Modele::$monPdo->commit();
    } catch (Exception $e) {
        Modele::$monPdo->rollBack();
        throw $e;
    }
}


    
    /**
     * Ajouter un acheteur dont les infos sont passées en arguments
     * (téléphone/mail sont dans HABITANT → on ne les insère pas ici)
     * @param $idHabitant
     * @param $idFoyer
     * @param $tel (ignoré)
     * @param $mail (ignoré)
     * @param $justifIdentite
     * @param $justifDomicile
     */
    public function ajouterAcheteur($idHabitant, $idFoyer, $tel, $mail, $justifIdentite, $justifDomicile){
        $statut = ($justifIdentite == 1 && $justifDomicile == 1) ? 'valide' : 'en_attente';

        $req = "INSERT INTO acheteur (idHabitant, idFoyer, justificatif_identite, justificatif_domicile, statut)
                VALUES ($idHabitant, $idFoyer, $justifIdentite, $justifDomicile, " . Modele::$monPdo->quote($statut) . ")";
        Modele::$monPdo->exec($req);
    }

    /**
     * Ajouter le commerce dont les infos sont passées en arguments
     * @param $nom
     * @param $rue
     * @param $cp
     * @param $ville
     */
    public function ajouterUnCommerce($nom, $rue, $cp, $ville){
        $req = "Insert into commerce (nom, rue, codePostal, ville) values ('$nom', '$rue', '$cp', '$ville'); ";
        Modele::$monPdo->exec($req);
    }	

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de Commerces
     */
    public function getLesCommerces(){
        $req = "select * from commerce";	
        $res = Modele::$monPdo->query($req);
        return $res->fetchAll();
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
