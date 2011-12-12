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

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

require 'src/inc/graphic.class.php';

// Template...
$tpl = new Template(DIR_TEMPLATE);
$tpl->set_file(array(
		'index'	 	=>	'index.tpl',
		'edit'		=>	'edit.tpl',
		'misc'		=>	'misc.tpl',
		'comment'	=>	'comment.tpl',
		'obj'		=>	'obj.tpl',
		'toolbar'	=>	'toolbar.tpl'
		));

$tpl->set_block('edit', array(
		'plugin'			=>	'Hdlplugin',
		'edit_plugins'		=>	'Hdledit_plugins',
		'edit_description'	=>	'Hdledit_description'
		));

$tpl->set_block('misc', array(
		'error'				=>	'Hdlerror',
		'status'			=>	'Hdlstatus',
		'action_rename'	=>	'Hdlaction_rename',
		'action_mkdir'		=>	'Hdlaction_mkdir',
		'action_del'		=>	'Hdlaction_del',
		'action_addfile'	=>	'Hdlaction_addfile',
		'action_edit'		=>	'Hdlaction_edit',
		'action_comment'	=>	'Hdlaction_comment',
		'action_move'		=>	'Hdlaction_move',
		'action_copy'		=>	'Hdlaction_copy',
		'aff_login'	 		=>	'Hdlaff_login',
		'aff_logout'		=>	'Hdlaff_logout',
		'aff_admin'		=>	'Hdlaff_admin',
		'toolbar'			=>	'Hdltoolbar',
		));

$tpl->set_block('comment', array(
		'comment_line'		=>	'Hdlcomment_line',
		'current_comment'	=>	'Hdlcurrent_comment',
		'last_comment_line' =>	'Hdllast_comment_line',
		'last_comment'		=>	'Hdllast_comment'
		));

$tpl->set_block('obj', array(
		'dir_previous_page'	=>	'Hdldir_previous_page',
		'dir_next_page'		=>	'Hdldir_next_page',
		'dir_pagination'	=>	'Hdldir_pagination',
		'previous_page'		=>	'Hdlprevious_page',
		'next_page'			=>	'Hdlnext_page',
		'pagination'		=>	'Hdlpagination'
		));


$var_tpl = null;
$start = null;

/*	Renvoie la directory
 */
function get_directory() {
	global $cobj;
	cache::getFilePath($cobj->file, $file);
	return ($cobj->type == TYPE_ARCHIVE) ? DIR_ROOT.$file.'/'.$cobj->target : $cobj->realpath;
}


$aff = (@$curl->aff[0] == 'obj') ? @$curl->aff[1] : @$curl->aff[0];

if ($aff != 'login')
	$_SESSION['sess_url'] = null;

switch ($aff) {

	#	Cas sp�cial : Ne n�cessite pas l'affichage de l'objet courant et de ces actions courante
	case 'page':

		switch (@$curl->aff[1]) {

			#	Affichage des derniers commentaires
			case 'lastcomment':
				$tab = $obj->getLastComment();
				$size = sizeof($tab);
				for ($i = 0; $i < $size; $i++) {
					$tpl->set_var(array(
							'PATH_FORMAT'	=>	format($tab[$i]->object, false),
							'FILE_ICON'		=>	get_icon(file::getExtension($tab[$i]->object)),
							'PATH_INFO'		=>	url::getObj($tab[$i]->object),
							'COMMENT'		=>	$tab[$i]->content,
							'AUTHOR'		=>	$tab[$i]->author,
							'MAIL'			=>	(empty($tab[$i]->mail) ? (empty($tab[$i]->url) ? '#' : $tab[$i]->url) : 'mailto:'.$tab[$i]->mail),
							'URL'			=>	(empty($tab[$i]->mail) ? null : $tab[$i]->url),
							'DATE'			=>	format_date($tab[$i]->date, 1),
							));
					$tpl->parse('Hdllast_comment_line', 'last_comment_line', true);
				}

				$tpl->set_var('MSG', (!$size) ? __('There are no comments !') : null);

				$tpl->parse('Hdllast_comment', 'last_comment', true);
				$var_tpl .= $tpl->parse('OutPut', 'comment');
				break;

			#	Administration
			case 'admin':
				test_perm(ADMIN);
				include('src/admin.php');
				break;

			default:
				system::end('Fatal error !');
		}

		break;

	#	Login !
	case 'login':
		include('src/login.php');
		break;

	#	Copier / D�placer
	case 'copy':
		test_perm(ADD_FILE);
		include('src/action.php');
		break;

	#	Renommer
	case 'rename':
		test_perm(($cobj->type == TYPE_DIR ? (CREATE_DIR | DEL_DIR) : (ADD_FILE | DEL_FILE)));
		include('src/action.php');
		break;

	#	D�placer
	case 'move':
		test_perm(($cobj->type == TYPE_DIR ? (CREATE_DIR | DEL_DIR) : (ADD_FILE | DEL_FILE)));
		include('src/action.php');
		break;

	#	Cr�er un r�pertoire
	case 'mkdir':
		test_perm(CREATE_DIR);
		include('src/action.php');
		break;

	#	Upload
	case 'upload':
		include('src/upload.php');
		break;

	#	La page de recherche
	case 'search':
		include('src/search.php');
		break;

	#	T�l�chargement
	case 'download':

		$status = false;
		switch ($cobj->type) {

			#	On envoie simplement le fichier
			case TYPE_FILE:
				file::sendFile($cobj->realpath);
				$status = true;
				break;

			#	On "zip" le r�pertoire et on l'envoie
			case TYPE_DIR:
				$file = null;
				if (!cache::getArchivePath($cobj->file, $file)) {
					$out = archive::createFromDir(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'.zip', $cobj->realpath);
					if ($out) {
						file::sendFile(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'.zip');
						$status = true;
					} else
						redirect(__('Error'), file::downPath(url::getObj($cobj->path)), __('Dir is probably empty or not readable !'));
				}
				break;

			#	On extrait le fichier et on l'envoie
			case TYPE_ARCHIVE:
				$file = null;
				if (!cache::getFilePath($cobj->file, $file)) {
					$zip = new PclZip($cobj->realpath);
					$out = $zip->extract($file);
				}

				// Si le fichier n'est pas trouv� dans l'archive : Erreur !
				if (!file_exists(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target)) {
					header('HTTP/1.x 404 Not Found');
					redirect(__('Error'), url::getObj($cobj->file), __('Object not found !'));
					system::end();
				}

				file::sendFile(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target);
				$status = true;
				break;
		}

		if ($conf['download_counter'] && $status) {
			$obj->addDownload();
		}

		system::end();
		break;

	#	Affichage d'une image en miniature
	case 'mini':
		if ($cobj->type == TYPE_ARCHIVE) {
			$file = null;
			if (!cache::getFilePath($cobj->file, $file)) {
				$zip = new PclZip(FOLDER_ROOT.$cobj->file);
				$out = $zip->extract($file.'/'.basename($cobj->file));
			}
		}

		graphic::image_resize(get_directory(), (!@empty($curl->aff[2]) ? $curl->aff[2] : THUMB_SIZE_X), 0);
		system::end();
		break;

	#	Edition
	case 'edit':
		test_perm(EDIT_FILE);
		if ($cobj->type == TYPE_DIR) {
			$tab = plugins::getDirPlugins();

			$tpl->set_var('PLUGIN_DEFAULT_CHECKED', (!$cobj->info->plugin) ? 'value="default" checked="checked"' : 'value="default"');		

			foreach($tab as $occ) {
				$name = strtolower($occ['name']);
				$tpl->set_var('PLUGIN_CHECKED', ($cobj->info->plugin == $name) ? 'value="'.$name.'" checked="checked"' : 'value="'.$name.'"');		
				$tpl->set_var(array(
						'PLUGIN_NAME'			=>	$occ['name'],
						'PLUGIN_DESCRIPTION'	=>	htmlentities($occ['description'], ENT_QUOTES),
						));
				$tpl->parse('Hdlplugin', 'plugin', true);
			}
			$tpl->parse('Hdledit_plugins', 'edit_plugins', true);
		}

		$tpl->set_var(array(
				'FORM_EDIT_SETDESCRIPTION'	=>	url::getCurrentObj('edit', 'setdescription'),
				'FORM_EDIT_SETPLUGIN'		=>	url::getCurrentObj('edit', 'setplugin'),
				'DEFAULT_PLUGIN'			=>	$conf['dir_default_plugin'],
				'OBJECT'					=>	$cobj->file,
				'DESCRIPTION'				=>	isset($cobj->info->description) ? string::unFormat($cobj->info->description) : null));
		$tpl->parse('Hdledit_description', 'edit_description', true);
		$var_tpl .= $tpl->parse('OutPut', 'edit');
		break;

	#	Affichage de l'objet
	case 'start':
		$start = @$curl->aff[2];
		$_SESSION['sess_start'] = $start;
	default:
	case 'obj':

		$curl->aff[0] = 'obj';

		if (!is_readable($cobj->realpath))
			$msg_error = __('Object not readable !');
		else {
			// Chargement d'un plugin...
			$plugins = new plugins();

			if ($cobj->type == TYPE_FILE) {
				if ($cobj->extension)
					$plugins->search();
			} else if ($cobj->type == TYPE_DIR) {
				$plugins->info['name'] = ($cobj->info->plugin) ? $cobj->info->plugin : $conf['dir_default_plugin'];					// TODO: mettre tout ce code dans la classe plugins
				$plugins->info['dir'] = ($cobj->info->plugin) ? $cobj->info->plugin : strtolower($conf['dir_default_plugin']);
			} else if ($cobj->type == TYPE_ARCHIVE) {

				if (!cache::getFilePath($cobj->file, $file)) {
					$zip = new PclZip($cobj->realpath);
					$out = $zip->extract($file);
				}

				// Si le fichier n'est pas trouv� dans l'archive : Erreur !
				if (!file_exists(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target)) {
					header('HTTP/1.x 404 Not Found');
					redirect(__('Error'), url::getObj($cobj->file), __('Object not found !'));
					system::end();
				}

				// Si on tente d'ouvrir de nouveau une archive, on stop !
				if ($cobj->extension == 'zip') {
					redirect($cobj->file, url::getObj($cobj->file), __('Not implemented !'));
					system::end();
				}

		/*	Oui, il serait possible de faire du r�cursif et ainsi pouvoir se balader dans un zip contenu dans un zip ...
			... mais bon, y'a d'autre priorit� pour le moment
		 */
	/*
		$tab_a = explode('!', $cobj->target);
		if ($tab_a) {
	
			foreach ($tab_a as $y) {
				$zip = new PclZip(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$y);
				$file = cache::getFilePath($obj->file.'/'.$y);
				$out = $zip->extract($file);
			}
		}
	*/

				$plugins->search();
			}

			$var_tpl .= $plugins->load();

			//	Les commentaires sont uniquement pour les fichiers
			if ($cobj->type == TYPE_FILE && ($cuser->perm & ADD_COMMENT)) {
				if ($cobj->info->nbr_comment) {
					for ($i = 0; $i < $cobj->info->nbr_comment; $i++) {
						$tpl->set_var(array(
								'COMMENT'	=>	$cobj->info->comment[$i]->content,
								'AUTHOR'	=>	$cobj->info->comment[$i]->author,
								'MAIL'		=>	(empty($cobj->info->comment[$i]->mail) ? (empty($cobj->info->comment[$i]->url) ? '#' : $cobj->info->comment[$i]->url) : 'mailto:'.$cobj->info->comment[$i]->mail),
								'URL'		=>	(empty($cobj->info->comment[$i]->mail) ? null : $cobj->info->comment[$i]->url),
								'DATE'		=>	format_date($cobj->info->comment[$i]->date, 1)));
						$tpl->parse('Hdlcomment_line', 'comment_line', true);
					}

					// Pour �viter que �a apparaisse dans les champs du formulaire d'envoie
					$tpl->set_var(array(
							'COMMENT'	=>	null,
							'AUTHOR'	=>	$cuser->id != ANONYMOUS_ID ? $cuser->name : (string::format(@$_POST['cm_author'], false)),
							'MAIL'		=>	null,
							'URL'		=>	null,
							'DATE'		=>	null));
				}


				$tpl->parse('Hdlcurrent_comment', 'current_comment', true);

				$tpl->set_var(array(
						'FORM_ADD_COMMENT'	=>	url::getCurrentObj('', 'addcomment'),
						'AUTHOR'			=>	$cuser->id != ANONYMOUS_ID ? $cuser->name : @$_POST['cm_author'],
						'EMAIL'				=>	stripslashes(strip_tags(@$_POST['cm_mail'], ENT_QUOTES)),
						'SITE'				=>	stripslashes(strip_tags(@$_POST['cm_site'] ? $_POST['cm_site'] : 'http://', ENT_QUOTES)),
						'CONTENT'			=>	stripslashes(htmlentities(@$_POST['cm_content'], ENT_QUOTES)),
						'ERROR'				=>	view_error($msg_error),
						'OBJECT'			=>	$cobj->file,
						'COMMENT_NBR'		=>	$cobj->info->nbr_comment,
						));
				$var_tpl .= $tpl->parse('OutPut', 'comment');
			}
		}

		break;
}

$tpl->set_var(array(
		'DOWNLOAD_COUNT'	=>	$cobj->info->dcount ? $cobj->info->dcount.__(' download') : null,
		'DESCRIPTION'		=>	($aff != 'lastcomment') ? (isset($cobj->info->description) ? $cobj->info->description : __('No description !')) : __('Last comments'),
		'FILE_ICON'			=>	$cobj->icon,
		'OBJECT'			=>	$cobj->file,
		'PATH'				=>	$cobj->path,
		'ERROR'				=>	view_error($msg_error),
		));

// La pagination
if ($cobj->type == TYPE_FILE || $cobj->type == TYPE_ARCHIVE) {

	if ($cobj->prev)
		$tpl->parse('Hdlprevious_page', 'previous_page', true);

	if ($cobj->next)
		$tpl->parse('Hdlnext_page', 'next_page', true);

	$tpl->set_var(array(
			'OBJ_PREV'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->prev->target : $cobj->prev->name,
			'OBJ_NEXT'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->next->target : $cobj->next->name,
			'PREV_PATH'			=>	url::getObj(($cobj->type == TYPE_ARCHIVE) ? $cobj->prev->file.'!'.$cobj->prev->target : $cobj->prev->file),
			'NEXT_PATH'			=>	url::getObj(($cobj->type == TYPE_ARCHIVE) ? $cobj->next->file.'!'.$cobj->next->target : $cobj->next->file),
			'PREV_FILE_ICON'	=>	$cobj->prev->icon,
			'NEXT_FILE_ICON'	=>	$cobj->next->icon));
	$tpl->parse('Hdlpagination', 'pagination', true);

} else if ($cobj->type == TYPE_DIR && $aff != 'search') {

	$nbr_obj = $obj->getNbrObject();
	if ($conf['nbr_obj'] > 0 && $nbr_obj > $conf['nbr_obj']) {

		if ($start)
			$start = ($start >= $nbr_obj) ? (($nbr_obj - $conf['nbr_obj'] < 0) ? 0 : $nbr_obj - $conf['nbr_obj']) : $start;

		if ($start > 0) {
			$tpl->parse('Hdldir_previous_page', 'dir_previous_page', true);
			$page = ($start <= $conf['nbr_obj']) ? 0 : $start - $conf['nbr_obj'];
			$tpl->set_var('PREV_PATH' , url::getObj($cobj->path, array('start', $page)));
		}

		if ($start < ($nbr_obj - 10))
			$tpl->parse('Hdldir_next_page', 'dir_next_page', true);
			$tpl->set_var('NEXT_PATH' , url::getObj($cobj->path, array('start', $start + $conf['nbr_obj'])));
	}

	$tpl->parse('Hdldir_pagination', 'dir_pagination', true);
}



if (@$curl->aff[0] == 'obj') {
	$tpl->set_var('CONTENT', $var_tpl);
	$var_tpl = $tpl->parse('Hdlobj', 'obj', true);
}

$tpl->set_var(array(
		'URL_EDIT'		=>	url::getObj($cobj->file, 'edit'),
		'URL_UPLOAD'	=>	url::getObj($cobj->file, 'upload'),
//		'URL_COPY'		=>	url::getObj($cobj->file, 'copy'),
		'URL_MOVE'		=>	url::getCurrentObj('move'),
		'URL_RENAME'	=>	url::getObj($cobj->file, 'rename'),
		'URL_DEL'		=>	url::getCurrentObj('', 'del'),
		'URL_SEARCH'	=>	url::getObj($cobj->path, 'search'),
		'URL_LOGIN'		=>	url::getCurrentObj('login'),
		'URL_MKDIR'		=>	url::getObj($cobj->file, 'mkdir'),
		'URL_LOGOUT'	=>	url::getObj($cobj->file, '', 'logout'),
		'URL_ADMIN'		=>	url::getPage('admin'),
		'URL_COMMENT'	=>	url::getPage('lastcomment'),
		));


/*	G�n�ration de la barre d'outils
 */
if ($cuser->id == ANONYMOUS_ID)
	$tpl->parse('Hdlaff_login', 'aff_login', true);
if (@$curl->aff[0] != 'page') {
	if ($conf['view_toolbar'] || $conf['anonymous_add_file'] || $cuser->perm & ADD_FILE)
		$tpl->parse('Hdlaction_addfile', 'action_addfile', true);
	if ($conf['view_toolbar'] || $cuser->perm & EDIT_FILE)
		$tpl->parse('Hdlaction_edit', 'action_edit', true);
	if ($conf['view_toolbar'] || $cuser->perm & CREATE_DIR)
		$tpl->parse('Hdlaction_mkdir', 'action_mkdir', true);
//	if ($conf['view_toolbar'] || $cuser->perm & ADD_FILE)
//		$tpl->parse('Hdlaction_copy', 'action_copy', true);
	// On ne peut pas d�placer ou supprimer la racine !
	if ($cobj->file != '/') {
		if ($conf['view_toolbar'] || ($cuser->perm & ($cobj->type == TYPE_DIR ? CREATE_DIR | DEL_DIR : ADD_FILE | DEL_FILE)))
			$tpl->parse('Hdlaction_rename', 'action_rename', true);
		if ($conf['view_toolbar'] || ($cuser->perm & ($cobj->type == TYPE_DIR ? CREATE_DIR | DEL_DIR : ADD_FILE | DEL_FILE)))
			$tpl->parse('Hdlaction_move', 'action_move', true);
		if ($conf['view_toolbar'] || $cuser->perm & ($cobj->type == TYPE_DIR ? DEL_DIR : DEL_FILE))
			$tpl->parse('Hdlaction_del', 'action_del', true);
	}
}
if ($cuser->id != ANONYMOUS_ID)
	$tpl->parse('Hdlaff_logout', 'aff_logout', true);

if ($cuser->perm & ADMIN)
	$tpl->parse('Hdlaff_admin', 'aff_admin', true);


$tpl->parse('Hdltoolbar', 'toolbar', true);
$var_tpl_toolbar = $tpl->parse('Hdlmisc', 'misc', true);


$endtime = system::chrono();
$totaltime = ($endtime - $starttime);

$tpl->set_var(array(
		'OBJECT_URL'		=>	format($cobj->file),
		'TOOLBAR'			=>	$var_tpl_toolbar,
		'OBJECT'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->file.'!'.$cobj->target : $cobj->file,
		'OBJ'				=>	$var_tpl,
		'TITLE'				=>	$conf['title'],
		'HYLA_VERSION'		=>	HYLA_VERSION,
		'DEBUG'				=>	__('%s query executed in %s seconds', $bdd->getNbrQuery(), round($totaltime, 4)),
		));

$tpl->pparse('OutPut', 'index');

?>
