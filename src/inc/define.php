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


/*	Le d�calage pour l'heure
 */
define('TIME_OFFSET', 0);


/*	R�pertoires et fichiers de travail...
 */
define('DIR_PLUGINS',	'src/plugin/');

define('DIR_TPL',		'tpl/');

define('DIR_CACHE', 	'sys/cache/');
define('DIR_ANON', 		'sys/anon');

define('DIR_CONF',		'conf/');
define('FILE_INI',		DIR_CONF.'hyla.ini');
define('FILE_ICON',		DIR_CONF.'icon.ini');


/*	La taille des images 'vignettes'
	THUMB_SIZE_Y � 0 pour garder la proportionnalit�
 */
define('THUMB_SIZE_X', 260);
define('THUMB_SIZE_Y', 0);


define('PREFIX_TABLE',	'hyla_');

define('TABLE_LIST',	PREFIX_TABLE.'object');
define('TABLE_COMMENT',	PREFIX_TABLE.'comment');
define('TABLE_USERS',	PREFIX_TABLE.'users');


/*	/!\ Grain de sel pour le cryptage des mot de passe
	Attention, si vous changez cette valeur apr�s l'installation, il vous faudra changez tous les mots de passe
 */
define('CRYPT_SALT',	123456789);

/*	+-------------------------------------------------------------------+
	| ATTENTION, les valeurs en dessous ne doivent pas �tre chang� !!!  |
	| Il s'agit des constantes indispensable pour le bon fonctionnement |
	| du moteur...                                                      |
	+-------------------------------------------------------------------+
 */

define('UNAUTHORIZED_CHAR', '\/:*?"<>|!');

define('URL_TEST_VERSION',	'http://www.digitalspirit.org/hyla/last_version.php');

define('PREFIX_ANON',		'[ANON]');

define('DIR_ICON',			'icon.png');


define('TYPE_UNKNOW',		0);		// Inconnu
define('TYPE_DIR',			1);		// R�pertoire
define('TYPE_FILE',			2);		// Fichier standard
define('TYPE_ARCHIVE',		3);		// Il s'agit d'un fichier contenu dans une archive


/*	Les tri
 */
define('SORT_DEFAULT',	 	0);		// Normal
define('SORT_ALPHA',	 	1);		// A -> Z
define('SORT_ALPHA_R',	 	2);		// Z -> A
define('SORT_ALPHA_EXT', 	4);		// xxx.A -> xxx.Z
define('SORT_ALPHA_EXT_R',	8);		// xxx.Z -> xxx.A
define('SORT_ALPHA_CAT',	16);	// Doc, Image
define('SORT_ALPHA_CAT_R',	32);	// Image, Doc
define('SORT_SIZE',			64);	// Taille - / +
define('SORT_SIZE_R',		128);	// Taille + / -
define('SORT_FOLDER_FIRST',	256);	// R�pertoire en premier


/*	Les permissions
 */
define('ANONYMOUS_ID',	1);


define('ADD_COMMENT', 	1);
define('EDIT_FILE',		2);
define('ADD_FILE',		4);
define('DEL_FILE',		8);
define('CREATE_DIR',	16);
define('DEL_DIR',		32);
define('ADMIN',			64);

?>
