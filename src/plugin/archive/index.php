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

class Plugin_archive extends plugin {

	var $zip;

	var $_act;
	var $_act_result_ok;
	var $_act_result_error;

	function Plugin_archive() {
		parent::plugin();

		$this->_act = null;
		$this->_act_result_ok = null;
		$this->_act_result_error = null;

		$this->tpl->set_root(FOLDER_PLUGINS.'archive');
		$this->tpl->set_file(array(
				'archive'	=>	'archive.tpl'));
		$this->tpl->set_block('archive', array(
				'zipfile'	=>	'Hdlzipfile'));
		
		$this->zip = new PclZip($this->cobj->realpath);
	}
	
	function act($act = null) {

		// Extrait dans le dossier parent
		if ($act == 'extract') {

			test_right(ADD_FILE);

			$this->zip = new PclZip($this->cobj->realpath);
			$out = $this->zip->extract(FOLDER_ROOT.$this->cobj->path);
			$this->_act = $act;
			$this->_act_result = 0;

			foreach ($out as $occ) {
				if ($occ['status'] == 'ok') {
					$this->_act_result_ok++;
					if (system::getOS() == 'Linux') {
						chmod($occ['filename'], FILE_CHMOD);
					}
				} else
					$this->_act_result_error++;
			}
		}	
	}
	
	function aff($paff) {

		if (($list = $this->zip->listContent()) == 0) {
			exit("Error : ".$this->zip->errorInfo(true));
		}
			
		for ($size = 0, $i = 0; $i < sizeof($list); $i++) {
			$size += $list[$i]['size'];
			if (!$list[$i]['folder']) {
				$this->tpl->set_var(array(
						'FILE_ICON'			=>	get_icone_from_ext(file::getExtension(basename($list[$i]['filename']))),
						'FILE_NAME'			=>	$list[$i]['filename'],
						'FILE_SIZE'			=>	get_intelli_size($list[$i]['size']),
						'PATH_DOWNLOAD'		=>	obj::getUrl($this->cobj->file.'!'.$list[$i]['filename'], AFF_DOWNLOAD)
						));
		  		$this->tpl->parse('Hdlzipfile', 'zipfile', true);
		  	}
		}
		
		$prop = $this->zip->properties();

  		$this->tpl->set_var(array(
				'RAPPORT'			=>	(($this->_act_result_ok) ? view_status($this->_act_result_ok.traduct('extractfile')) : null).(($this->_act_result_error) ? view_error($this->_act_result_error.traduct('extractfileerror')) : null),
  				'COMMENT'			=>	$prop['comment'],
  				'NBR_FILE'			=>	$prop['nb'],
  				'STATUS'			=>	$prop['status'],
  				'COMPRESSED_SIZE'	=>	get_intelli_size(filesize($this->cobj->realpath)),
  				'REAL_SIZE'			=>	get_intelli_size($size),
  				'OBJECT'			=>	obj::getCurrentUrl(AFF_INFO)
  				));

		unset($this->zip);
		
		return $this->tpl->parse('OutPut', 'archive');
	}
}

?>