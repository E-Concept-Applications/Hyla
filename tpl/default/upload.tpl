
{MSG}

<p>
Taille maximale d'un fichier : <strong>{MAX_FILESIZE}</strong>
</p>
<select name="sort" onchange="location.href='{URL_UPLOAD}&amp;file=' + this.options[this.selectedIndex].value">
	<option selected="selected">Nombre de fichiers � ajouter...</option>
	<option value="1">&nbsp;1</option>
	<option value="2">&nbsp;2</option>
	<option value="3">&nbsp;3</option>
	<option value="4">&nbsp;4</option>
</select>

<form enctype="multipart/form-data" method="post" name="form_upload" action="{URL_UPLOAD}">
{STATUS}
<!-- BEGIN form_upload -->
<fieldset>
	<legend><a href="#" onclick="swap_couche('upload_{NUM}');"><img src="{DIR_TEMPLATE}/img/Floppy.png" align="middle" width="32" height="32" alt="Disquette" /> Ajout de fichier <strong>({NUM})</strong></a></legend>

	{ERROR}

	<div id="Layerupload_{NUM}" style="display: none;">
	<p>
		<input type="radio" name="ul_file_method[{NUM}]" value="local" id="ul_url_method_{NUM}" {LOCAL_CHECKED} />
		<label for="ul_url_method_{NUM}">
			<img src="{DIR_TEMPLATE}/img/Disks.png" align="middle" width="32" height="32" alt="Disque d�r" /> Fichier local : 
		</label>
		<input type="file" name="ul_file_local[]" size="40" onclick="eval(getID('ul_url_method_{NUM}') + '.checked = true;');" />
	</p>
	<!-- BEGIN from_url -->
	<p>
		<input type="radio" name="ul_file_method[{NUM}]" value="fromurl" id="ul_file_method_{NUM}" {FROM_URL_CHECKED} />
		<label for="ul_file_method_{NUM}">
			<img src="{DIR_TEMPLATE}/img/WWW.png" align="middle" width="32" height="32" alt="Disque d�r" /> Fichier distant : 
		</label>
		<input type="text" name="ul_file_fromurl[]" size="40" onclick="eval(getID('ul_file_method_{NUM}') + '.checked = true;');" value="{FROM_URL}" />
	</p>
	<!-- END from_url -->
	<p>
		<label for="ul_description_{NUM}">Description :</label>
		<textarea name="ul_description[]" id="ul_description_{NUM}" cols="50" rows="5">{FILE_DESCRIPTION}</textarea>
	</p>
	<p>
		<label for="ul_name_{NUM}">Nom :</label>
		<input type="text" name="ul_new_name[]" id="ul_name_{NUM}" size="20" value="{NEW_NAME}" /> (laissez vide pour garder le nom original)
	</p>
	</div>
</fieldset>
<br />
<!-- END form_upload -->
	<input type="submit" name="Submit" value="Envoyer" />
</form>
