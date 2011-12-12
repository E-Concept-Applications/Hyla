
<div class="action">

<!-- BEGIN mkdir -->
	<form method="post" name="form_mkdir" action="{FORM_MKDIR}">
		<fieldset>
			<legend><img src="{DIR_TEMPLATE}/img/Folder-Yellow_Tigert.png" align="middle" width="32" height="32" alt="Cr�ation" /> Cr�ation d'un r�pertoire </legend>
			{ERROR}
			<p>
				Le r�pertoire sera cr�� dans &laquo; {OBJECT} &raquo; 
			</p>
			<p>
				<label for="mk_name">
					Nom : 
				</label>
				<input type="text" name="mk_name" id="mk_name" size="20" />
			</p>
			<p>
				<label for="mk_redirect">
					�tre redirig� vers le nouveau r�pertoire :
				</label>
				<input type="checkbox" name="mk_redirect" id="mk_redirect" value="1" checked="checked" />
			</p>
			<input type="submit" name="Submit" value="Cr�er" />
		</fieldset>
	</form>
<!-- END mkdir -->

<!-- BEGIN rename -->
	<form method="post" name="form_rename" action="{FORM_RENAME}">
		<fieldset>
			<legend><img src="{DIR_TEMPLATE}/img/PenWrite.png" align="middle" width="32" height="32" alt="Renommage" /> Renommer </legend>
			{ERROR}
			<p>
				<label for="rn_newname">
					Nouveau nom : 
				</label>
				<input type="text" name="rn_newname" id="rn_newname" size="20" />
			</p>
			<p>
				<label for="rn_redirect">
					�tre redirig� vers l'objet :
				</label>
				<input type="checkbox" name="rn_redirect" id="rn_redirect" value="1" checked="checked" />
			</p>
			<input type="submit" name="Submit" value="Renommer" />
		</fieldset>
	</form>
<!-- END rename -->

<!-- BEGIN move -->
	<form method="post" name="form_move" action="{FORM_MOVE}">
		<fieldset>
			<legend><img src="{DIR_TEMPLATE}/img/gnome-dev-symlink.png" align="middle" width="32" height="32" alt="D�placer" /> D�placer </legend>
			{ERROR}
			<p>
				<label for="mv_destination">
					Choisissez le r�pertoire de destination :
				</label>
				<select name="mv_destination" id="mv_destination">
					<option value="/">/</option>
					<!-- BEGIN dir_move_occ --><option value="{DIR_NAME}">{DIR_NAME}</option><!-- END dir_move_occ -->
				</select>
			</p>
			<p>
				<label for="mv_redirect">
					�tre redirig� vers l'objet :
				</label>
				<input type="checkbox" name="mv_redirect" id="mv_redirect" value="1" checked="checked" />
			</p>
			<input type="submit" name="Submit" value="D�placer" />
		</fieldset>
	</form>
<!-- END move -->

</div>
