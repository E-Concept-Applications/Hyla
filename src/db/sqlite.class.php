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



class db
{
	/*	Connection serveur
	 */
	var $_db_host;		// Serveur MySQL
	var $_db_user;		// Login de la base
	var $_db_pass;		// Mot de Passe de la base
	var $_db_base;		// Base de donn�es

	/*	Attribut divers
	 */
	var $_id_bdd;		// ID de la connexion � la base
	var $_id_query;		// ID de la derni�re requ�te
	var $_nbr_query;	// Comptage des requ�tes
	var $_last_query;	// La derni�re requ�te


	/*	Le constructeur...
	 */
	function db()
	{
		$this->_db_host = null;
		$this->_db_user = null;
		$this->_db_pass = null;
		$this->_db_base = null;
	
		$this->_id_bdd = null;
		$this->_id_query = null;
		
		$this->_nbr_query = 0;
		$this->_last_query = null;
	}

	/*	Connection au serveur...
		@param string $_db_host Serveur SQL
		@param string $_db_user User
		@param string $_db_pass Password
		@param string $_db_base Base de donn�es
	 */
	function connect($_db_host, $_db_base, $_db_user, $_db_pass)
	{
		$this->_db_host = $_db_host;
		$this->_db_user = $_db_user;
		$this->_db_pass = $_db_pass;
		$this->_db_base = $_db_base;
		
		//extension_loaded('mysql');
//		dl('php_sqlite.dll');
		
		// Connexion � la base de donn�es
		if (!$this->_id_bdd = sqlite_open($this->_db_host, 0666, $error)) {
			error::log(__FILE__, __LINE__, MSG_ERROR_SQL_CONNECT, $error);
			exit;
		}
		else
			define('ID_BDD', $this->_id_bdd);

		return $this->_id_bdd;
	}

	/*	Fermeture de la base de donn�es
	 */
	function close($_id_bdd = null)
	{
		if ($_id_bdd == null)
			$_id_bdd = $this->_id_bdd;
		if (!$ret = @sqlite_close($this->_id_bdd))
			error::log(__FILE__, __LINE__, MSG_ERROR_SQL_CLOSE);
		return $ret;
	}

	/*	Ex�cution d'une requ�te
	 */
	function execQuery($qry, $_id_bdd = null)
	{
		$this->_nbr_query++;

		if (!$_id_bdd)
			$_id_bdd = $this->_id_bdd;
		$this->_last_query = $qry;
		$this->_id_query = @sqlite_query($qry, $_id_bdd);
		
		// Affichage des requ�tes pour le debug...
		if (ERROR_REPORT == 5)
			error::log(null, null, null, '<i>'.$this->_nbr_query.':</i> '.$qry);
		
		return $this->_id_query;
	}

	/*	Renvoie le nombre de requ�tes ex�cut� en tout !
	 */
	function getNbrQuery() {
		return $this->_nbr_query;
	}

	/*	Retourne le tuple suivant !
	 */
	function nextTuple($_id_query = null) {
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return $this->sqlite_next($_id_query);
	}

	/* Reset tuple
	 */
	function reset() {
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return @sqlite_rewind($_id_query);
	}

	/*	Retourne une ligne de r�sultat sous la forme d'un tableau associatif
	 */
	function fetchArray($_id_query = null)
	{
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return @sqlite_fetch_array($_id_query);
	}

	/*	Retourne une ligne de r�sultat sous la forme d'un tableau associatif
	 */
	function fetchAssoc($_id_query = null) {
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return @sqlite_fetch_array($_id_query, SQLITE_ASSOC);
	}

	/*	Retourne le nombre de ligne d'un r�sultat
	 */
	function getNumRows($_id_query = null)
	{
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return @sqlite_num_rows($_id_query);
	}

	/*	Efface le r�sultat de la m�moire
	 */
	function freeResult($_id_query)
	{
		//$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		//return @mysql_free_result($_id_query);
		echo 'ERROR';
	}

	/*	Retourne l'identifiant g�n�r� par la derni�re requ�te INSERT
	 */
	function getInsertID($_id_bdd = null) {
		if (!$_id_bdd)
			$_id_bdd = $this->_id_bdd;
		return @sqlite_last_insert_rowid($_id_bdd);
	}

	/*	Retourne le num�ro d'erreur et l'erreur
	 */
	function getError()
	{
		$error_num = @sqlite_last_error($this->_id_bdd);
		$error['message'] = @sqlite_error_string($error_num);
		$error['code'] = $error_num;
		$error['query'] = $this->_last_query;
		return $error;
	}
}



?>
