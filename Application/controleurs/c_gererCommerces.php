<?php
include("vues/v_sommaire.php");
$idEmploye = $_SESSION['idEmploye'];

$action = $_REQUEST['action'];

	
switch($action){
	case 'gererCommerces':{
		$lesCommerces = $pdo->getLesCommerces();
		include("vues/v_listeCommerces.php");
		include("vues/v_ajoutCommerce.php");
		break;
	}
	case 'supprimerCommerce':{
		$idCommerce= $_REQUEST['id'];
		$pdo->supprimerCommerce($idCommerce);
		$lesCommerces = $pdo->getLesCommerces();
		include("vues/v_listeCommerces.php");
		include("vues/v_ajoutCommerce.php");
	  break;
	}
	case 'ajouterUnCommerce':{
		$nom = $_REQUEST['nom'];
		$rue = $_REQUEST['rue'];
		$cp = $_REQUEST['cp'];
		$ville = $_REQUEST['ville'];
		$pdo->ajouterUnCommerce($nom, $rue, $cp, $ville);
		$lesCommerces = $pdo->getLesCommerces();
		include("vues/v_listeCommerces.php");
		include("vues/v_ajoutCommerce.php");
		break;
	}

}


?>