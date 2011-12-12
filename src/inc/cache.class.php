<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reserved

	Hyla is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	Hyla is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


class cache {

	/*	Supprime les infos en cache d'un fichier
		@param	string	$file	Le fichier concern�
	 */
	function del($file) {

		// Suppression du cache d'un r�pertoire
		if (is_dir(FOLDER_ROOT.$file)) {
			cache::getArchivePath($file, $out);
			if (is_file(DIR_ROOT.$out))
				unlink(DIR_ROOT.$out);
		} else {
			cache::getFilePath($file, $out);

			if (is_dir($out)) {
				$file =  array();

				// R�cup�ration des miniatures pour suppression
				$hdl = dir($out);
				if ($hdl) {
					while (false !== ($_occ = $hdl->read())) {
						if ($_occ == '.' || $_occ == '..')
							continue;
						$fmd5 = md5(dirname(DIR_ROOT.$out.'/'.$_occ));
						$file[] = '#'.preg_quote($fmd5).'.[0-9]*x[0-9]*.'.preg_quote($_occ).'#s';
						$dir = dirname($fmd5{0}.'/'.$fmd5);
					}
				}
				$hdl->close();

				if (is_dir(DIR_ROOT.DIR_CACHE.$dir)) {
					$list = array();
					$hdl = dir(DIR_ROOT.DIR_CACHE.$dir);
					if ($hdl) {
						while (false !== ($_occ = $hdl->read())) {
							if ($_occ == '.' || $_occ == '..')
								continue;
							$list[]	= $_occ;				
						}
						$hdl->close();

						$cmpt = 0;
						foreach ($file as $k) {
							$ret = null;
							if ($ret = preg_grep($k, $list)) {
								$ret = array_values($ret);
								foreach ($ret as $r)
									unlink(DIR_ROOT.DIR_CACHE.$dir.'/'.$r);
							}
						}
					}
				}

				if (is_dir(DIR_ROOT.$out))
					file::rmDirs(DIR_ROOT.$out);
			}
		}
	}

	/*	Renvoie le chemin et le nom vers l'image "cach�e" en tenant compte de la taille de l'image

		On obtient un r�sultat proche de celui ci :
		- cache/6/6676cd76f96956469e7be39d750cc7d8.320x240.name.jpg

		@param	string	$file	Le fichier concern�
		@param	int		$sizex	La taille x
		@param	int		$sizey	La taille y
	 */
	function getImagePath($file, $sizex, $sizey, &$out) {

		$fmd5 = md5(dirname($file));

		if (!is_dir(DIR_CACHE.$fmd5{0}))
			mkdir(DIR_CACHE.$fmd5{0});

		$out = DIR_CACHE.$fmd5{0}.'/'.$fmd5.'.'.$sizex.'x'.$sizey.'.'.basename($file);

		return (bool)file_exists($out);
	}

	/*	Renvoie le chemin et le nom du fichier � cacher

		On obtient un r�sultat proche de celui ci :
		- cache/6/6676cd76f96956469e7be39d750cc7d8.jpg

		@param	string	$file	Le fichier concern�
		@param	&string	$out	Le buffer o� �crire le r�sultat
		@return Renvoie true si le fichier existe d�j� dans le cache
	 */
	function getFilePath($file, &$out) {

		$fmd5 = md5(dirname($file));

		if (!is_dir(DIR_CACHE.$fmd5{0}))
			mkdir(DIR_CACHE.$fmd5{0});

		$out = DIR_CACHE.$fmd5{0}.'/'.$fmd5.'.'.basename($file);

		return (bool)file_exists($out);
	}

	/*	Renvoie le chemin et le nom de l'archive � cacher

		On obtient un r�sultat proche de celui ci :
		- IN	:	/gal/LICENSE.txt.zip
		- OUT	:	cache/1/17be6e1c87b44864d301d499d68eec5d/LICENSE.txt.zip

		@param	string	$file	Le fichier concern�
		@param	&string	$out	Le buffer o� �crire le r�sultat
		@return Renvoie true si le fichier existe d�j� dans le cache
	 */
	function getArchivePath($file, &$out) {

		$fmd5 = md5(dirname($file));

		if (!is_dir(DIR_CACHE.$fmd5{0}))
			mkdir(DIR_CACHE.$fmd5{0});

		if (!is_dir(DIR_CACHE.$fmd5{0}.'/'.$fmd5))
			mkdir(DIR_CACHE.$fmd5{0}.'/'.$fmd5);

		if ($file == '/')
			$file = 'root';

		$out = DIR_CACHE.$fmd5{0}.'/'.$fmd5.'/'.basename($file);

		return (bool)file_exists($out);
	}

}

?>
