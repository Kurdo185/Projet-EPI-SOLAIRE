
  	   <h2>Mon profil</h2>

<table class="listeLegere">

             <tr>
                <th>Nom</th>
				<th>Prenom</th>  
                <th>Login</th>  
                <th class="date">Date embauche</th> 
                <th class="action">Modifier</th>   				
             </tr>
          
    <?php    

			$nom = $leProfil['nom'];
			$prenom = $leProfil['prenom'];
			$login = $leProfil['login'];
			$dateEmbauche = $leProfil['dateEmbauche'];
	?>		
            <tr>
                <td> <?php echo $nom ?></td>
                <td><?php echo $prenom ?></td>
                <td><?php echo $login ?></td>
				<td><?php echo $dateEmbauche ?></td>
                <td><a href="index.php?uc=profilEmploye&action=profil=<?php echo $_SESSION['idEmploye'] ?>" 
				onclick="return confirm('Voulez-vous vraiment modifier votre mot de passe ?');">Modifier mon mot de passe</a></td>
             </tr>
  
                                          
    

  </table>
  

