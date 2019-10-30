<?php
	include 'header.php';
	permission_check("", "", "", "", "You do not have permission to view items!");
?>

<form method='get' action='searchResults.php' enctype="multipart/form-data">
	<fieldset>
	<legend>Search for Items</legend>
	<p><label>Keywords</label>
	<textarea name='keywords' class='span5' rows='5' cols='5' maxlength='100' wrap='hard' placeholder="Please enter keywords seperated by a space and a semi-colon ' ;'"></textarea></p>
	<p style='line-height: 30px'><label>Type</label>
	<input type="radio" id="type1" name="type" value="theatricalCostume" />
		<label for="type1">Theatrical Costume</label>
	<input type="radio" id="type2" name="type" value="theatricalProp" />
		<label for="type2">Theatrical Prop</label>
	<input type="radio" id="type3" name="type" value="theatricalSet" />
		<label for="type3">Theatrical Set</label>
	<input type="radio" id="type4" name="type" value="drapes" />
		<label for="type4">Drapes</label><br />
	<input type="radio" id="type5" name="type" value="music" />
		<label for="type5">Music</label>
	<input type="radio" id="type6" name="type" value="dance" />
		<label for="type6">Dance</label>
	<input type="radio" id="type7" name="type" value="misc" />
		<label for="type7">Miscellaneous</label></p><br/>
	<p><input type="submit" value="Search"/></p>
	</fieldset>
</form>

<?php
	include 'footer.php';
?>