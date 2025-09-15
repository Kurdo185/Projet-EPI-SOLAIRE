<?php
include("vues/v_sommaire.php");
$action = $_REQUEST['action'];
$idEmploye = $_SESSION['idEmploye'];

switch($action){
	case 'profil':{
		$leProfil = $pdo->getInfosEmployeById($idEmploye);
		include("vues/v_profil.php");
		break;
	}
	case 'autre':{

	}
}
?>