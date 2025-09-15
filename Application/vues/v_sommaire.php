    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    
    
      </div>  
        <ul id="menuList">
			<li >
				  Employé : <?php echo $_SESSION['prenom']."  ".$_SESSION['nom']  ?><br/ >
			</li>
			<li>------------------------------------------</li>
			<li class="smenu">
              <a href="index.php?uc=profilEmploye&action=profil" title="Mon Profil">Mon Profil</a>
           </li>
           <li class="smenu">
              <a href="index.php?uc=listeCommerces&action=gererCommerces" title="Gestion des commerces">Gestion des commerces</a>
           </li>
           <li class="smenu">
              <a href="index.php?uc=listeAcheteurs&action=gererAcheteurs" title="Gestion des acheteurs">Gestion des acheteurs</a>
           </li>
			<li class="smenu">
              <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Se déconnecter</a>
           </li>
         </ul>
        
    </div>
    