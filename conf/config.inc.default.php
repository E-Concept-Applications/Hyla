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


/*	+----------------------------------------------------+
 	| R�pertoire contenant vos fichiers � lister         |
 	| /!\ ATTENTION, pas de slash ou anti slash de fin ! |
 	+----------------------------------------------------+
 */
define('FOLDER_ROOT', '');


/*	L'emplacement de ifile apr�s le nom de domaine (sans slash de fin !)
	Ex: http://ifile.free.fr/				-> mettez ''
	Ex: http://ifile.free.fr/ifile/			-> mettez '/ifile'
	Ex: http://ifile.free.fr/data/ifile		-> mettez '/data/ifile'
 */
define('ROOT_URL', '');


/*	+---------------------------------+
	| Connection � la base de donn�es |
	+---------------------------------+
 */
define('SQL_HOST',	'localhost');
define('SQL_USER',	'');
define('SQL_PASS',	'');
define('SQL_BASE',	'');


/*	+----------------------------------------------------+
	| Pour pouvoir ajouter des fichiers, �diter...etc... |
	+----------------------------------------------------+
 */
define('LOGIN', 'admin');
define('PASSWORD', 'admin');

?>
