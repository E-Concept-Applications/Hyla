<div class="plugin">

	<a href="{OBJECT_DOWNLOAD}"><img src="{OBJECT_MINI}" border="0" align="middle" alt="Image" /></a>

	<p>
	<!-- BEGIN image_size -->
		Diff�rentes tailles possibles :
		<a href="{AFF_SIZE_1_4}">1/4</a> |
		<a href="{AFF_SIZE_1_3}">1/3</a> |
		<a href="{AFF_SIZE_1_2}">1/2</a> |
		<a href="{AFF_SIZE_1_1}">1/1</a>
	<!-- END image_size -->
	</p>



	<div class="info" style="text-align: left;">
		Largeur r�elle de l'image : {IMAGE_X} px<br />
		Hauteur r�elle de l'image : {IMAGE_Y} px

		<!-- BEGIN exif_data -->
		<p>
			<a name="comment"><a href="#comment" onclick="swap_couche('1');"> Donn�es EXIF de l'image</a></a>
			<table id="Layer1" style="display: none;">
				<tr>
					<td>Marque</td>
					<td>{EXIF_MAKE}</td>
				</tr>
				<tr>
					<td>Mod�le</td>
					<td>{EXIF_MODEL}</td>
				</tr>
				<tr>
					<td>Orientation de l'image</td>
					<td>{EXIF_ORIENTATION}</td>
				</tr>
				<tr>
					<td>R�solution</td>
					<td>{EXIF_XRESOLUTION}</td>
				</tr>
				<tr>
					<td>Y R�solution :</td>
					<td>{EXIF_YRESOLUTION}</td>
				</tr>
				<tr>
					<td>Unit� de r�solution</td>
					<td>{EXIF_RESOLUTIONUNIT}</td>
				</tr>
			</table>
<!--
				{EXIF_DATETIME}
				{EXIF_YCBCRPOSITIONING}
				{EXIF_EXIFIFDPOINTER}

				{EXIF_EXPOSURETIME}
				{EXIF_FNUMBER}
				{EXIF_EXPOSUREPROGRAM}
				{EXIF_ISOSPEEDRATINGS}
				{EXIF_EXIFVERSION}
				{EXIF_DATETIMEORIGINAL}
				{EXIF_DATETIMEDIGITIZED}
				{EXIF_COMPONENTSCONFIGURATION}
-->
		</p>
		<!-- END exif_data -->

	</div>
</div>
