<?php
if(!isset($_REQUEST['action'])){
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch($action){
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':{
		$login = $_REQUEST['login'];
		$mdp = $_REQUEST['mdp'];
		$verif = $pdo->testConnexionEmploye($login,$mdp);
		if($verif == 0){
			ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		}
		else{
			$employe = $pdo->getInfosEmploye($login,$mdp);
			$id = $employe['id'];
			$nom =  $employe['nom'];
			$prenom = $employe['prenom'];
			connecter($id,$nom,$prenom);
			include("vues/v_sommaire.php");
		}
		break;
	}

	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>