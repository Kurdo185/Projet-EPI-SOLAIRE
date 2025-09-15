<?php
session_start();
require_once("include/fonctions.php");
require_once ("include/modele.php");

include("vues/v_entete.php") ;



$pdo = Modele::getModele();
$estConnecte = estConnecte();

if(!isset($_REQUEST['uc']) || !$estConnecte){
     $_REQUEST['uc'] = 'connexion';
}	
 
$uc = $_REQUEST['uc'];
switch($uc){
	case 'connexion':{
		include("controleurs/c_connexion.php");
		break;
	}
	case 'profilEmploye' :{
		include("controleurs/c_employe.php");
		break; 
	}
	case 'listeCommerces' :{
		include("controleurs/c_gererCommerces.php");
		break;
	}
	case 'listeAcheteurs' :{
		include("controleurs/c_gererAcheteurs.php");
		break;
	}
}
include("vues/v_pied.php") ;
?>