<?php
/*
    This file is part of Hyla
    Copyright (c) 2004-2007 Charles Rincheval.
    All rights reserved

    Hyla is free software; you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License,
    or (at your option) any later version.

    Hyla is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warcranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
    header('location: ../index.php');

acl_test(AC_ADD_FILE);

if ($cuser->id == ANONYMOUS_ID && $conf['anon_file_send'] == 1) {
    $anonymous_mode = true;
    $destination_root = DIR_ROOT.DIR_ANON;
} else {
    $anonymous_mode = false;
    $destination_root = FOLDER_ROOT.$cobj->path;
}

/*  Send email, valid
    @param  string  $root   Root dir
    @param  string  $name   File name
 */
function upload_act($root, $name) {
    global $conf, $obj, $cuser;
    if ($cuser->id == ANONYMOUS_ID) {
        switch ($conf['anon_file_send']) {
            case 0:
                break;
            case 1:
            case 2:
                if ($conf['webmaster_mail']) {
                    system::mail($conf['webmaster_mail'], __('Hyla - An anonymous file was sent !'), __('mail_content', $name), $conf['webmaster_mail']);
                }
        }
    }
}

$tpl->set_file('upload', 'upload.tpl');

$tpl->set_block('upload', array(
        'from_url'          =>  'Hdlfrom_url',
        'form_upload'       =>  'Hdlform_upload',
        ));

$tpl->l10n->setFile('upload.php');


if (ini_get('allow_url_fopen'))
    $tpl->parse('Hdlfrom_url', 'from_url', true);

// Un envoie à été fait...
if (isset($_POST['Submit'])) {

    $ierror = 0;
    $msg_error = null;
    $size = sizeof($_FILES['ul_file_local']['name']);
    for ($i = 0; $i < $size; $i++) {

        if ($_POST['ul_file_method'][$i] == 'local') {

            $up_name = $_POST['ul_new_name'][$i] ? $_POST['ul_new_name'][$i] : $_FILES['ul_file_local']['name'][$i];
            $up_name = strip_tags($up_name);
            switch ($_FILES['ul_file_local']['error'][$i]) {
                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_INI_SIZE:       $msg_error = __('The file is too large !'); break;
                case UPLOAD_ERR_PARTIAL:        $msg_error = __('An error occured while downloading the file !');       break;
                case UPLOAD_ERR_NO_FILE:        $msg_error = __('No specified file  !');        break;
                case UPLOAD_ERR_OK:

                    // Il ne doit pas y avoir de caractère interdit !
                    $up_name = stripslashes($up_name);
                    if (string::test($up_name, UNAUTHORIZED_CHAR)) {
                        $msg_error = __('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR);
                        break;
                    }

                    // En mode anonyme, on ne doit pas dire que le fichier existe déjà, on change le nom
                    $up_name = ($anonymous_mode) ? file::getUniqueName($up_name, $destination_root.'/') : $up_name;

                    $destinationname = $destination_root.'/'.$up_name;

                    // Si le fichier existe déjà...
                    if (file_exists($destinationname)) {
                        $msg_error = __('The file already exists !');
                        break;
                    }

                    // On déplace le fichier ou il faut, on met la description si elle existe et on quitte !
                    if (is_writable($destination_root)) {

                        if (move_uploaded_file($_FILES['ul_file_local']['tmp_name'][$i], $destinationname)) {

                            @chmod($destinationname, $conf['file_chmod']);

                            $object = $cobj->path.$up_name;

                            // Mode anonyme ?
                            if ($anonymous_mode) {
                                $obj->addAnonFile($object, string::format($_POST['ul_description'][$i]));
                            } else {
                                if (!empty($_POST['ul_description'][$i])) {
                                    $obj->setDescription(string::format($_POST['ul_description'][$i]), $object);
                                }
                            }

                            // Action
                            upload_act($cobj->path, $up_name);

                            // On fait ça pour ne pas donner le nom du fichier copié anonymement
                            $tab_ok[$i]['name'] = ($anonymous_mode) ? $_POST['ul_new_name'][$i] ? $_POST['ul_new_name'][$i] : $_FILES['ul_file_local']['name'][$i] : $up_name;
                        }

                    } else {
                        $msg_error = __('Not writable !');
                        break;
                    }

                    // On teste enfin si le fichier à bien été créé
                    if (!file_exists($destinationname)) {
                        $msg_error = __('An unknown error occured during upload !');
                    }
            }

        } else {

            if (!ini_get('allow_url_fopen')) {
                redirect($cobj->file, $url->linkToCurrentObj('upload'), __('Error while accessing remote file, "allow_url_fopen" parameter is off'));
                system::end();
            } else {

                $up_name = $_POST['ul_new_name'][$i] ? $_POST['ul_new_name'][$i] : basename($_POST['ul_file_fromurl'][$i]);

                // On supprime les paramètres contenus dans l'url si il y en a...
                $pos = strpos($up_name, '&');
                if ($pos) {
                    $tmp_name = substr($up_name, 0, $pos);
                    if ($tmp_name) {
                        $up_name = $tmp_name;
                    }
                }

                $up_name = strip_tags($up_name);

                // Il ne doit pas y avoir de caractère interdit !
                $up_name = stripslashes($up_name);
                if (string::test($up_name, UNAUTHORIZED_CHAR)) {
                    $msg_error = __('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR);
                } else if (empty($up_name)) {
                    $msg_error = __('Empty filename !');
                } else {

                    // En mode anonyme, on ne doit pas dire que le fichier existe déjà, on change le nom
                    $up_name = ($anonymous_mode && $conf['anon_file_send'] == 1) ? file::getUniqueName($up_name, $destination_root.'/') : $up_name;

                    $destinationname = $destination_root.'/'.$up_name;

                    // Si le fichier existe déjà...
                    if (!preg_match('#^(http|ftp)+(s)?:(//)#i', $_POST['ul_file_fromurl'][$i])) {
                        $msg_error = __('An unknown error occured during upload !');
                    } else if (file_exists($destination_root.'/'.$up_name)) {
                        $msg_error = __('The file already exists !');
                    } else if ($var = file::getContent($_POST['ul_file_fromurl'][$i])) {

                        file::putContent($destinationname, $var);

                        @chmod($destinationname, $conf['file_chmod']);

                        $object = $cobj->path.$up_name;

                        // Mode anonyme ?
                        if ($anonymous_mode) {
                            $obj->addAnonFile($object, string::format($_POST['ul_description'][$i]));
                        } else {
                            if (!empty($_POST['ul_description'][$i])) {
                                $obj->setDescription(string::format($_POST['ul_description'][$i]), $object);
                            }
                        }

                        // Action
                        upload_act($cobj->path, $up_name);

                        $tab_ok[$i]['name'] = ($anonymous_mode) ? $_POST['ul_new_name'][$i] ? $_POST['ul_new_name'][$i] : basename($_POST['ul_file_fromurl'][$i]) : $up_name;
                    } else
                        $msg_error = __('An unknown error occured during upload !');
                }
            }
        }

        if ($msg_error) {
            $tab_error[$ierror]['error'] = $msg_error;
            $tab_error[$ierror]['name'] = $up_name;
            $tab_error[$ierror]['description'] = $_POST['ul_description'][$i];
            $tab_error[$ierror]['new_name'] = $_POST['ul_new_name'][$i] ? $up_name : null;
            $tab_error[$ierror]['from_url'] = $_POST['ul_file_method'][$i] != 'local' ? $_POST['ul_file_fromurl'][$i] : null;
            $msg_error = null;
            $ierror++;
        }
    }

    $rapport = null;
    if (isset($tab_ok)) {
        foreach ($tab_ok as $occ) {
            $rapport .= $occ['name'].' ';
        }
    }

    // Pas d'erreur, tout niquel, on redirige !
    if (!isset($tab_error) && isset($tab_ok)) {
        redirect($cobj->file, $url->linkToCurrentObj(), __('Upload correctly finished !').view_status(__('File correctly uploaded : %s', $rapport)));
        system::end();
    }
}

$nbr_form = (!isset($_GET['file'])) ? ((isset($_POST['Submit']) && isset($tab_error)) ? sizeof($tab_error) : 1) : $_GET['file'];
if (!$nbr_form || $nbr_form < 0)
    $nbr_form = 1;

for ($i = 0; $i < $nbr_form; $i++) {
    $tpl->set_var(array(
            'STATUS'            =>  isset($rapport) ? view_status(__('File correctly uploaded : %s', $rapport)) : null,
            'ERROR'             =>  isset($tab_error) ? view_error(($tab_error[$i]['name'] ? $tab_error[$i]['name'].' : ' : null).$tab_error[$i]['error']) : null,
            'NUM'               =>  $i,
            'NUM_HUMAN'         =>  $i + 1,
            'FILE_DESCRIPTION'  =>  isset($tab_error[$i]['error']) ? stripslashes(htmlentities($tab_error[$i]['description'], ENT_QUOTES)) : null,
            'NEW_NAME'          =>  isset($tab_error[$i]['error']) ? stripslashes(htmlentities($tab_error[$i]['new_name'])) : null,
            'FROM_URL'          =>  isset($tab_error[$i]['error']) ? $tab_error[$i]['from_url'] : null,
            'FROM_URL_CHECKED'  =>  isset($tab_error[$i]['from_url']) ? ' checked="checked"' : null,
            'LOCAL_CHECKED'     =>  !isset($tab_error[$i]['from_url']) ? ' checked="checked"' : null,
            ));

    $tpl->parse('Hdlform_upload', 'form_upload', true);
}

$tpl->set_var(array(
        'MSG'           =>  ($anonymous_mode) ? view_status(__('You are in anonymous mode, an administrator will have to validate the sent file !')) : null,
        'URL_UPLOAD'    =>  $url->linkToCurrentObj('upload'),
        'OBJECT'        =>  $cobj->file,
        'MAX_FILESIZE'  =>  ini_get('upload_max_filesize')));

$var_tpl = $tpl->parse('OutPut', 'upload');

?>
