<?php
/*
	This file is part of iFile
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reserved

	iFile is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	iFile is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with iFile; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


/*
function dir_size($dir)
{
   $handle = opendir($dir);
  $mas = null;
   while ($file = readdir($handle)) {
       if ($file != '..' && $file != '.' && @!is_dir($dir.'/'.$file)) {
           $mas += @filesize($dir.'/'.$file);
           } else if (@is_dir($dir.'/'.$file) && $file != '..' && $file != '.') {
           $mas += dir_size($dir.'/'.$file);
       }
   }
   return $mas;
}
*/


class file
{
	/*	Efface TOUT le contenu d'un dossier
	 	@param string $_folder Le r�pertoire
	 	@param boolean $_recurs On d�gage aussi les r�pertoires r�cursivement !
	 */
	function delFolder($_folder, $_recurs = true) {
		$hdl = @dir($_folder);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {
				if ($_recurs == true && is_dir($_folder.'/'.$_occ) && $_occ != '.' && $_occ != '..')
					file::delFolder($_folder.'/'.$_occ);
				if (is_file($_folder.'/'.$_occ))
					unlink($_folder.'/'.$_occ);
			}
			$hdl->close();
		}
		return @rmdir($_folder);
	}
	
	/*	Copie les fichier XML vide dans le r�pertoire TMP du user
		@param string $_folder_string R�pertoire d'origine
		@param string $_folder_dest R�pertoire de destination
	 */
	function copyAllFile($_folder_origin, $_folder_dest) {
		$hdl = dir($_folder_origin);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {
				if (is_file($_folder_origin.'/'.$_occ))
		 			copy($_folder_origin.'/'.$_occ, $_folder_dest.'/'.$_occ);
		 	}
		}
	}

	/*	Retourne un tableau contenant l'arborescence du repertoire sp�cifi�
		@param string $_folder
	 */
	function scanDir($_folder_root, $_folder = null) {
		static $tab_dir;
		echo $_folder_root;
		$hdl = dir($_folder_root.$_folder);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {
				if (is_dir($_folder_root.$_folder.'/'.$_occ) && $_occ != '.' && $_occ != '..') {
					$tab_dir[] = $_folder.'/'.$_occ;
					file::scanDir($_folder_root, $_folder.'/'.$_occ);
				}
			}
			$hdl->close();
		}
		return $tab_dir;
	}

	/*	Les fichiers matchant l'expression r�guli�re sont stock�s dans un tableau
		@param string $folder R�pertoire root pour le scan
		@param string $exp L'expression r�guli�re
		@param boolean $recurs Faire �a r�cursivement
		@param string $base R�pertoire de base
		@param boolean $scandir Scanner aussi le nom des r�pertoire
	 */
	function searchFile($folder, $exp, $recurs = true, $base = null, $scandir = false) {
		static $tab_file;
		
		if (!$tab_file && $folder == '/')
				$folder = null;
		
		$hdl = dir($base.$folder);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {			
				if (is_dir($base.$folder.'/'.$_occ) && $_occ != '.' && $_occ != '..' && $recurs) {
//					 if ($scandir && @preg_match("/".$exp."/i", $_occ)) {
					 if ($scandir && @fnmatch(strtolower($exp), strtolower($_occ))) {
						$tab_file[] = $folder.'/'.$_occ.'/';
					}
					file::searchFile($folder.'/'.$_occ, $exp, $recurs, $base);
				} else if (is_file($base.$folder.'/'.$_occ) && $_occ != '.' && $_occ != '..') {
//					 if (@preg_match("/".$exp."/i", $_occ)) {
					 if (@fnmatch(strtolower($exp), strtolower($_occ))) {
						$tab_file[] = $folder.'/'.$_occ;
					}
				}
			}
			$hdl->close();
		}
		return $tab_file;
	}
	
	/*	Retourne l'extension d'un fichier
		@param string $file Le nom du fichier concern�
	 */
	function getExtension($file) {
		$ext = null;
		$pos = strrpos($file, '.');

		// Attention car si on envoie toto/super.txt/cool, �a va retourner txt/cool
		if ($pos)
			$ext = substr(strtolower($file), $pos + 1, strlen($file));

		return $ext;
	}

	/*	Retourne le r�pertoire
		ATTENTION :	Diff�rent de dirname car v�rifie si le r�pertoire existe vraiment et si
					$folder est un chemin avec un fichier, il extrait le chemin si il existe
		@param string $folder Le r�pertoire avec un nom de fichier ou non
		@param string $base La racine qui comporte $folder
		@return string Le chemin r�el
	 */
	function getRealDir($folder, $base = null) {
		$ret = null;
		if (is_file($base.$folder)) {
			if (file::_isLegalPath($base, $folder, $rpath))	
				$ret = substr($rpath, strlen($base), (strlen($rpath) - strlen(file::getRealFile($folder, $base))) - strlen($base));
		} else if (is_dir($base.$folder)) {
			if (file::_isLegalPath($base, $folder, $rpath)) {
				$ret = substr($rpath, strlen($base), (strlen($rpath) - strlen(file::getRealFile($folder, $base))) - strlen($base));
				if (!$ret)
					$ret = '/';
			}
		}
		return file::_formatPath($ret);
	}
	
	/*	Retourne le nom du fichier
		ATTENTION : Diff�rent de basename car v�rifie si le fichier existe vraiment
		@param string $file Le nom du fichier
		@param string $base La racine comportant le fichier
	 */
	function getRealFile($file, $base = null) {
		$ret = null;
		if (is_file($base.$file)) {
			if (file::_isLegalPath($base, $file, $_)) {
				$ret = basename($file);
			}
		}
		return file::_formatPath($ret);
	}

	/*	Envoie un fichier avec le bon type mime
		@param string $file Le fichier
		@param boolean $force Forcer le t�l�chargement
	 */
	function sendFile($file) {	//, $force = false) {
		$ext = null;		// Extension
		$ctype = null;		// Content type
		
//		if ($path{strlen($path) - 1} != '/')
//			$path{strlen($path)} = '/';
		
		$ext = file::getExtension($file);
	
		switch ($ext) {
			case 'mpeg':
			case 'mpg':
			case 'mpe':		$ctype = 'video/mpeg';			break;	// Vid�os Mpg
			case 'avi':		$ctype = 'video/avi';			break;	// Vid�os Microsoft Windows
			case 'doc':		$ctype = 'application/word';	break;
			case 'zip':		$ctype = 'application/zip';		break;
			case 'pdf':		$ctype = 'application/pdf';		break;
			case 'png':		$ctype = 'image/png';			break;
			case 'gif':		$ctype = 'image/gif';			break;
			case 'jpeg':
			case 'jpg':		$ctype = 'image/jpeg';			break;
			case 'mp3':		$ctype = 'audio/mpeg3';			break;
			case 'htm':
			case 'html':	$ctype = 'text/html';			break;
			
			case 'asm':
			case 'inc':
			case 'c':
			case 'cpp':
			case 'txt':		$ctype = 'text/plain';			break;
			default:		$ctype = 'octet/stream';		break;
		}
		
		// Force le t�l�chargement
		/*if ($force || $ctype == 'octet/stream') {
			header('Content-Type: '.$ctype);
			header('Content-Length: '.filesize($path.$file));
			header('Content-Disposition: attachment; filename='.$file);
		} else {
			header('Content-Type: '.$ctype.'; filename='.$file);
			header('Content-Length: '.filesize($path.$file));
		}*/

		header('Content-Disposition: inline; filename="'.basename($file).'"');
		header('Content-Type: '.$ctype);
		header('Content-Length: '.filesize($file));

		readfile($file);
		exit;
	}

	/*	Renvoie le contenu d'un fichier
	 	@param string $file Le fichier
	 */
	function getContent($file) {
		$ret = null;
		if (function_exists('file_get_contents'))
			$ret = file_get_contents($file);
		else {
			$fp = fopen($file, 'rb');
			if ($fp) {
				while ($str = fread($fp, 4096))
					$ret .= $str;
			}
			fclose($fp);
		}
		return $ret;
	}
	
	/*	Ecrit dans un fichier
		@param string $file Le nom du fichier
		@param string $str Le contenu
	 */
	function putContent($file, $str) {
		$ret = false;
		if (function_exists('file_put_contents'))
			$ret = file_put_contents($file, $str);
		else {
			$fp = fopen($file, 'ab');
			if ($fp) {
				if (fwrite($fp, $str)) {
					fclose($fp);
					$ret = true;
				}
			}
		}
		return $ret;
	}
	
	/*	Descend d'une arborescence (ATTENTION, uniquement des chemins *NIX
		Ex:	* /usr/local/apache -> /usr/local
			* /home/toto/doc.htm -> /home/toto
		@param string $path Le chemin
	 */
	function downPath($path) {
		if ($path{strlen($path) - 1} == '/')
			$path{strlen($path) - 1} = null;
		return substr($path, 0, strlen($path) - strlen(strrchr($path, '/')));
	}

	/*	Renvoie true si le chemin canonique absolu ne descend pas en dessous de la base
		@param string $base La base r�f�rence
		@param string $path Le chemin symbolique ou autre
		@param string $realpath Retourne le vrai chemin avec la base
		@return boolean
		@access private
	 */
	function _isLegalPath($base, $path, &$realpath) {
		$base = realpath($base);
		$rpath = realpath($base.$path);
		$size = strlen($base);		
		$ret = ($base == substr($rpath, 0, $size)) ? true : false;
		if ($ret)
			$realpath = $rpath;
		return $ret;
	}
	
	/*
		@access private
	 */
	function _formatPath($path) {
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$path = str_replace('\\', '/', $path);
		}
		return $path;
	}
}

?>
