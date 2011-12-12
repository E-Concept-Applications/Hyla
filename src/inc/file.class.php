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

class file
{
	/*	Copie un dossier dans un autre
		@param	string	$dir			Le r�pertoire
		@param	string	$dest			La cible
		@param	octal	$dir_chmod		Le mode de cr�ation des r�pertoires
		@return	Renvoie le nombre de fichiers et r�pertoires cr��s
	 */
	function copyDir($folder_origin, $folder_dest, $dir_chmod = 0765) {

		static $var = true;
		$ret = 0;

		// R�cup�ration et cr�ation du premier r�pertoire de destination
		if ($var) {
			$folder_dest = $folder_dest.(($folder_dest{strlen($folder_dest) - 1} == '/') ? null : '/').file::getLastDir($folder_origin);
			$ret += mkdir($folder_dest, $dir_chmod);
			$var = false;
		}

		$hdl = dir($folder_origin);
		if ($hdl) {
			while (($_occ = $hdl->read()) !== false) {
				if ($_occ == '.' || $_occ == '..')
					continue;

				if (is_dir($folder_origin.'/'.$_occ)) {
					if (mkdir($folder_dest.'/'.$_occ, $dir_chmod))
						$ret++;
					$ret += file::copyDir($folder_origin.'/'.$_occ, $folder_dest.'/'.$_occ, $dir_chmod);
				} else if (is_file($folder_origin.'/'.$_occ)) {
					if (copy($folder_origin.'/'.$_occ, $folder_dest.'/'.$_occ))
						$ret++;
				}
		 	}
		}

		return $ret;
	}

	/*	Renvoie le nom du dernier dossier
		@param	string	$path	Le chemin � scanner
	 */
	function getLastDir($path) {
		$size = strlen($path);
		if ($path{$size - 1} == '/')
			$path{$size - 1} = null;
		$path = trim($path);
		$last_dir = strrchr($path, '/');
		$last_dir = substr($last_dir, 1);
		return $last_dir;
	}

	/*	Cr�er des r�pertoires (php.net)
		@param	string	$dir	Le chemin complet contenant les r�pertoires � cr�er
		@param	octal	$mode	Les droits
	 */
	function mkDirs($dir, $mode = 0755) {
		if (is_null($dir) || $dir == '')
			return false;
		if (is_dir($dir) || $dir == '/')
			return true;
		if (file::mkdirs(dirname($dir), $mode))
			return mkdir($dir, $mode);
		return false;
	}

	/*	Supprime un r�pertoire et tout son contenu
		@param string $dir
	 */
	function rmDirs($dir) {
		$hdl = dir($dir);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {
				if ($_occ == '.' || $_occ == '..')
					continue;
				if (is_dir($dir.'/'.$_occ))
					file::rmDirs($dir.'/'.$_occ);
				else if (is_file($dir.'/'.$_occ))
					unlink($dir.'/'.$_occ);
			}
			$hdl->close();
		}
		return rmdir($dir);
	}

	/*	Si le fichier existe d�j� dans la racine, retourne le nom du fichier pr�fix� d'une chaine le rendant unique
		@param	string	$name	Le nom du fichier
		@param	string	$root	La racine
	 */
	function getUniqueName($name, $root) {
		$prefix = '0';
		if (file_exists($root.'/'.$name)) {
			while (1) {
				if (file_exists($root.'/'.$prefix.'_'.$name)) {
					$prefix++;
					continue;
				}
				$name = $prefix.'_'.$name;
				break;
			}
		}
		return $name;
	}

	/*	Retourne un tableau contenant l'arborescence du repertoire sp�cifi�
		@param	string	$_folder_root	Le r�pertoire racine
		@param	string	$_folder		Sous r�pertoire
		@param	bool	$hidden			Renvoie ou non les objets cach�s
	 */
	function scanDir($_folder_root, $hidden = true, $_folder = null) {
		static $tab_dir = null;
		static $cmpt = 0;
		$cmpt++;

		$hdl = dir($_folder_root.$_folder);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {
				if ($_occ != '.' && $_occ != '..' && is_dir($_folder_root.$_folder.'/'.$_occ)) {
					if ($_occ{0} != '.' || $hidden) {
						$tab_dir[] = $_folder.'/'.$_occ.'/';
						file::scanDir($_folder_root, $hidden, $_folder.'/'.$_occ);
					}
				}
			}
			$hdl->close();
		}

		if ($cmpt == 1) {
			$tab = $tab_dir;
			$tab_dir = null;
		}
		$cmpt--;
		return $tab;
	}

	/*	Les fichiers correspondant l'expression r�guli�re sont stock�s dans un tableau
		@param	string	$folder		R�pertoire pour le scan (finissant par un '/')
		@param	string	$exp		L'expression r�guli�re
		@param	boolean	$recurs		Faire �a r�cursivement
		@param	string	$base		R�pertoire de base
		@param	boolean	$scandir	Scanner aussi le nom des r�pertoire
		@param	bool	$hidden		Renvoie ou non les objets cach�s
	 */
	function searchFile($folder, $exp, $recurs = true, $base = null, $scandir = false, $hidden = false) {
		static $tab_file = null;
		static $cmpt = 0;
		$cmpt++;

		if (!$tab_file && $folder == '/')
			$folder = null;
		
		$hdl = dir($base.$folder);
		if ($hdl) {
			while (false !== ($_occ = $hdl->read())) {
				if ($_occ{0} == '.' && !$hidden) {
					continue;
				}
				if (is_dir($base.$folder.'/'.$_occ) && $_occ != '.' && $_occ != '..') {
					 if ($scandir && fnmatch(strtolower($exp), strtolower($_occ))) {
						$tab_file[] = $folder.'/'.$_occ.'/';
					}

					if ($recurs)
						file::searchFile($folder.'/'.$_occ, $exp, $recurs, $base, $scandir, false);
				} else if (is_file($base.$folder.'/'.$_occ) && $_occ != '.' && $_occ != '..') {					 if (fnmatch(strtolower($exp), strtolower($_occ))) {
						$tab_file[] = $folder.'/'.$_occ;
					}
				}
			}
			$hdl->close();
		}


		if ($cmpt == 1) {
			$tab = $tab_file;
			$tab_file = null;
		}
		$cmpt--;
		return $tab;
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
		@param	string	$folder Le r�pertoire avec un nom de fichier ou non
		@param	string	$base La racine qui comporte $folder
		@return Le chemin r�el
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
		return file::formatPath($ret);
	}
	
	/*	Retourne le nom du fichier
		ATTENTION : Diff�rent de basename car v�rifie si le fichier existe vraiment
		@param	string	$file Le nom du fichier
		@param	string	$base La racine comportant le fichier
	 */
	function getRealFile($file, $base = null) {
		$ret = null;
		if (is_file($base.$file)) {
			if (file::_isLegalPath($base, $file, $_)) {
				$ret = basename($file);
			}
		}
		return file::formatPath($ret);
	}

	/*	Envoie un fichier avec le bon type mime
		@param string $file Le fichier
	 */
	function sendFile($file) {
		$ext = null;		// Extension
		$ctype = null;		// Content type

		$ext = file::getExtension($file);
	
		switch ($ext) {
			case 'mpeg':
			case 'mpg':
			case 'mpe':	$ctype = 'video/mpeg';			break;	// Vid�os Mpg
			case 'avi':		$ctype = 'video/avi';			break;	// Vid�os Microsoft Windows
			case 'doc':		$ctype = 'application/word';	break;
			case 'zip':		$ctype = 'application/zip';		break;
			case 'pdf':		$ctype = 'application/pdf';		break;
			case 'png':		$ctype = 'image/png';			break;
			case 'gif':		$ctype = 'image/gif';			break;
			case 'jpeg':
			case 'jpg':		$ctype = 'image/jpeg';			break;
			case 'mp3':	$ctype = 'audio/mpeg3';			break;
			case 'htm':
			case 'html':	$ctype = 'text/html';			break;
			
			case 'asm':
			case 'inc':
			case 'c':
			case 'cpp':
			case 'txt':		$ctype = 'text/plain';			break;
			default:		$ctype = 'octet/stream';		break;
		}

		header('Content-Disposition: inline; filename="'.basename($file).'"');
		header('Content-Type: '.$ctype);
		header('Content-Length: '.filesize($file));

		readfile($file);
		// Surtout pas de exit ici
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

		$tab = explode('/', trim($path));
		$path = '/';
		$size = sizeof($tab);
		$size--;
		for ($i = 0; $i < $size; $i++) {
			$n = $tab[$i];
			if ($n)
				$path .= $n.'/';
		}
		return $path;
	}

	/*	Certain h�bergeur d�sactive realpath pour des raisons de s�curit�, pourtant la fonction existe et ne renvoie rien (merci Free)...
		@param	string	$path	Le chemin � tester
		@param	bool	$exist	Test si le fichier ou r�pertoire existe vraiment (avec ce param�tre � true, cette fonction simule parfaitement realpath)
	 */
	function realpath($path, $exist = false) {

		$ret = null;
		$path = file::formatPath($path);

		if ($exist && $path == '.')
			return dirname($_SERVER['PATH_TRANSLATED']);

		$dirs = explode('/', $path);
		$dirs_out = array();

		$i = 0;
		foreach ($dirs as $k => $v) {
			if ($v == null || $v == '.')
				continue;
			if ($v == '..') {
				$dirs_out[--$i] = null;
			} else {
				$dirs_out[$i++] = $v;
			}
		}

		$p = ($exist && substr(PHP_OS, 0, 3) == 'WIN') ? null : '/';
		foreach($dirs_out as $k => $v) {
			if (isset($v))
				$p .= $v.'/';
		}
		
		$p = substr($p, 0, strlen($p) - 1);

		if ($exist) {
			if (is_dir($p))
				$ret = $p;
			else if (is_file($p))
				$ret = $p;
			else
				$ret = false;
		} else
			$ret = $p;

		$ret = file::unFormatPath($ret);

		return $ret;
	}

	/*	Renvoie true si le chemin canonique absolu ne descend pas en dessous de la base
		@param string $base La base r�f�rence
		@param string $path Le chemin symbolique ou autre
		@param string $realpath Retourne le vrai chemin avec la base
		@return boolean
		@access private
	 */
	function _isLegalPath($base, $path, &$realpath) {
		$ret = false;

		$rpath = file::realpath($path);
		$rpath = file::realpath($base.$rpath, true);

		if ($rpath) {
			$realpath = $rpath;
			$ret = true;
		}

		return $ret;
	}
	
	/*	Renvoie une chains propre pour windows
	 */
	function formatPath($path) {
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$path = str_replace('\\', '/', $path);
		}
		return $path;
	}

	/*	Renvoie une chains propre pour windows
	 */
	function unFormatPath($path) {
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$path = str_replace('/', '\\', $path);
		}
		return $path;
	}
}

?>
