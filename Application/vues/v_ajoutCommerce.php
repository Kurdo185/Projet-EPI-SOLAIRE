
<h3>Ajouter un nouvau commerce : </h3>

<form method="POST" action="index.php?uc=listeCommerces&action=ajouterUnCommerce">
	<table  class="listeLegere">
		<tr>
			<td>Nom : </td>
			<td>
				<input  type='text' name = nom  size='50' maxlength='50'>
			</td>
		</tr>
		<tr>
			<td>Rue</td>
			<td>
				<input  type='text' name = rue  size='50' maxlength='50'>
			</td>
		</tr>
		<tr>
			<td>Code Postal</td>
			<td>
				<input  type='text' name = cp  size='5' maxlength='10'>
			</td>
		</tr>
		<tr>
			<td>Ville</td>
			<td>
		
				<input type='text' name = ville size='50' maxlength='50'> 
			</td>
	</tr>

	</table>
	<br />
	
	<input type='submit' value='Enregister' name='valider'>
	<input type='reset' value='Annuler' name='annuler'>

	
</form>
<p>
