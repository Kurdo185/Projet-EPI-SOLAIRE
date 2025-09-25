<?php
include("vues/v_sommaire.php");
$idEmploye = $_SESSION['idEmploye'];

// Si aucune action n'est fournie, on affiche la liste
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'gererAcheteurs';
}
$action = $_REQUEST['action'];

/* ---- Traitement de l'ajout AVANT l'affichage ----
   On détecte le submit du formulaire (bouton name="ajoutAcheteur")
   et on insère en base via le modèle. */
if (isset($_POST['ajoutAcheteur'])) {
    // Récup champs du formulaire (mêmes names que dans la vue)
    $idHabitant     = isset($_POST['idHabitant']) ? (int)$_POST['idHabitant'] : 0;
    $idFoyer        = isset($_POST['idFoyer']) ? (int)$_POST['idFoyer'] : 0;
    $tel            = isset($_POST['tel']) ? $_POST['tel'] : '';
    $mail           = isset($_POST['mail']) ? $_POST['mail'] : '';
    $justifIdentite = isset($_POST['justifIdentite']) ? 1 : 0;
    $justifDomicile = isset($_POST['justifDomicile']) ? 1 : 0;

    // (Validation basique, optionnelle)
    if ($idHabitant > 0 && $idFoyer > 0 && $tel !== '' && $mail !== '') {
        $pdo->ajouterAcheteur($idHabitant, $idFoyer, $tel, $mail, $justifIdentite, $justifDomicile);
    } else {
        // Si tu utilises déjà ajouterErreur / v_erreurs.php :
        // ajouterErreur("Tous les champs obligatoires doivent être renseignés.");
        // include("vues/v_erreurs.php");
    }

    // Après insertion, on retombe sur l'affichage de la liste
    $action = 'gererAcheteurs';
}

/* ---- Affichages et autres actions ---- */
switch ($action) {
    case 'gererAcheteurs': {
        $lesAcheteurs = $pdo->getLesAcheteurs();
        include("vues/v_listeAcheteurs.php");
        break;
    }

    case 'supprimerAcheteur': {
        $idAcheteur = $_REQUEST['idAcheteur'];
        $pdo->supprimerAcheteur($idAcheteur);
        $lesAcheteurs = $pdo->getLesAcheteurs();
        include("vues/v_listeAcheteurs.php");
        break;
    }

    default: {
        // Par défaut on affiche la liste
        $lesAcheteurs = $pdo->getLesAcheteurs();
        include("vues/v_listeAcheteurs.php");
        break;
    }
}
?>
