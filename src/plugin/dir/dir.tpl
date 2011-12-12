<div class="table">
	<table width="100%">
	<!-- BEGIN line -->
	<!-- BEGIN line_header -->
		<tr>
			<td colspan="5">
				<div class="header">{HEADER_VALUE}
					<span class="header-info">{HEADER_INFO_VALUE}</span>
				</div>
			</td>
		</tr>
	<!-- END line_header -->
	<!-- BEGIN line_content -->
		<tr class="line">
			<td width="2%"><a href="{PATH_INFO}"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a></td>
			<td width="30%">
				<a href="{PATH_INFO}">{FILE_NAME}</a>
				<!-- BEGIN line_comment -->
				<a href="{PATH_INFO}#comment"><img src="{DIR_TEMPLATE}/img/comment.png" width="32" height="32" border="0" align="middle" alt="Commentaires" /><!--<sub>{NBR_COMMENT}</sub>--></a>
				<!-- END line_comment -->
			</td>
			<td width="56%" align="right" class="description">{FILE_DESCRIPTION}</td>
			<td width="10%" align="right">{FILE_SIZE}</td>
			<td width="2%" align="right"><a href="{PATH_DOWNLOAD}" title="T�l�charger"><img src="{DIR_TEMPLATE}/img/gnome-fs-bookmark.png" width="32" height="32" border="0" align="middle" alt="T�l�charger" /></a></td>
		</tr>
	<!-- END line_content -->
	<!-- END line -->
	</table>
	<br />
	<div>
	<form method="post" name="dir_form" action="{OBJECT}&amp;paff[]=grp:1">
	<select name="paff[]" onchange="submit();">
		<option value="sort:-1" selected="selected">Trier...</option>
		<option value="sort:0">&nbsp;Ordre par d�faut</option>
		<option value="sort:1">&nbsp;Alphab�tique A-Z (R�pertoires en premier)</option>
		<option value="sort:2">&nbsp;Alphab�tique Z-A (R�pertoires en premier)</option>
		<option value="sort:3">&nbsp;Extensions   A-Z (R�pertoires en premier)</option>
		<option value="sort:4">&nbsp;Extensions   Z-A (R�pertoires en premier)</option>
		<option value="sort:5">&nbsp;Alphab�tique A-Z</option>
		<option value="sort:6">&nbsp;Alphab�tique Z-A</option>
		<option value="sort:7">&nbsp;Extensions   A-Z</option>
		<option value="sort:8">&nbsp;Extensions   Z-A</option>
	</select>
	<label for="grp">
		<input type="checkbox" name="paff[]" id="grp" value="grp:ok" {CHECKED} />
		Grouper par crit�re de tri
	</label>
	<input type="submit" name="" value="Envoyer !" />
	</form>
	</div>

</div>
