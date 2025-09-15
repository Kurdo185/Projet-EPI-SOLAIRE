
<table class="listeLegere">
  	   <caption>Liste des acheteurs enregistrés </caption>
             <tr>
                <th>Nom</th>
				<th>Prenom</th>  
                <th>Téléphone</th>  
                <th>Mail</th> 
				<th>Date de naissance</th>
                <th class="action">Supprimer</th>   				
             </tr>
          
    <?php    
	    foreach($lesAcheteurs as $unAcheteur) 
		{
		
			$idAcheteur = $unAcheteur['id'];
			$nom = $unAcheteur['nom'];
			$prenom = $unAcheteur['prenom'];
			$tel=$unAcheteur['telephonePortable'];
			$mail= $unAcheteur['mail'];
			$dn= $unAcheteur['dateNaiss'];
	?>		
            <tr>
                <td><?php echo $nom ?></td>
                <td><?php echo $prenom ?></td>
                <td><?php echo $tel ?></td>
				<td><?php echo $mail ?></td>
				<td><?php echo $dn ?></td>
				<td><a href="index.php?uc=listeAcheteurs&action=supprimerAcheteur&idAcheteur=<?php echo $idAcheteur?>" 
				onclick="return confirm('Voulez-vous vraiment supprimer cet acheteur?');">Supprimer cet acheteur</a></td>
             </tr>
	<?php		 
          
          }
	?>	  
                                          
    

  </table>
  

