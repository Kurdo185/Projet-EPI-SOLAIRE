
<table class="listeLegere">
  	   <caption>Liste des commerces enregistrés </caption>
             <tr>
			    <th>Id</th>
                <th>Nom</th>
				<th>Rue</th>  
                <th>Code Postal</th>  
                <th>Ville</th> 
                <th class="action">Supprimer</th>   				
             </tr>
          
    <?php    
	    foreach($lesCommerces as $unCommerce) 
		{
			$id = $unCommerce['id'];
			$nom = $unCommerce['nom'];
			$rue = $unCommerce['rue'];
			$cp =$unCommerce['codePostal'];
			$ville = $unCommerce['ville'];
	?>		
            <tr>
			    <td> <?php echo $id ?></td>
                <td> <?php echo $nom ?></td>
                <td><?php echo $rue ?></td>
                <td><?php echo $cp ?></td>
				<td><?php echo $ville ?></td>
                <td><a href="index.php?uc=listeCommerces&action=supprimerCommerce&id=<?php echo $id?>" 
				onclick="return confirm('Voulez-vous vraiment supprimer ce commerce?');">Supprimer ce commerce</a></td>
             </tr>
	<?php		 
          
          }
	?>	  
                                          
    

  </table>
  

