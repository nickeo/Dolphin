<?php
/*********************************************************************************************
*
*	Description: class handling database activity for Dolphin
*
*	Author: Niklas Odén
*
***********************************************************************************************/
	
	require_once(TP_SQLPATH . 'config.php');
	require_once(TP_SQLPATH . 'CSQL.php');
	
class CDatabaseController {

	//-----------------------------------------------------------------------------------------
	//
	//	member variables
	//
	
	protected $iMysqli;
	protected $iLoadSQL;
	
	//-----------------------------------------------------------------------------------------
	//
	//	constructor and destructor
	//
	
	function __construct() {
	
		$this->iMysqli = false;
		
	}
	
	function __destruct() {
	
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	connecting to database
	//
	
	public function connectToDataBase() {
	
		$this->iMysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		if (mysqli_connect_error()) {
  		 	echo "Connect failed: ".mysqli_connect_error()."<br>";
   			exit();
		}
		return $this->iMysqli;
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	get and store results from multiquery
	//
	
	public function getAndStoreResults(&$aResult) {
	
		$mysqli = $this->iMysqli;
		$i = 0;
		do {
			$aResult[$i++] = $mysqli->store_result();
		} while($mysqli->next_result());
	
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	performing query when query is sent directly from page
	//
	
	public function performQuery($aQuery) {
		
		$query = $this->loadQuery($aQuery);
		
		$res = $this->iMysqli->query($query) 
            or die("Could not query database, query =<br/><pre>{$query}</pre><br/>{$this->iMysqli->error}");

        return $res;
		
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	performing multi_query
	//
	
	public function performMultiQuery($aQuery) {
		
		$query = $this->loadQuery($aQuery);
		
		$res = $this->iMysqli->multi_query($query) 
            or die("Could not query database, query =<br/><pre>{$query}</pre><br/>{$this->iMysqli->error}");

        return $res;
		
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	loading query
	//
	
	public function loadQuery($aQuery) {
		
		$loadSQL = new CSQL();
		
		switch($aQuery) {
		
			case 'install' : $loadQuery = $loadSQL->installDB(); break;
			case 'articleTable' : $loadQuery = $loadSQL->installArticleTable(); break;
			case 'installProcedures' : $loadQuery = $loadSQL->installProcedures(); break;
			case 'tempInstall' : $loadQuery = $loadSQL->installTemp(); break;
			case 'tunatalk' : $loadQuery = $loadSQL->installTunaTalk(); break;
			case 'login' : {
					global $username, $password;
					$username     = $this->iMysqli->real_escape_string($username);
					$password     = $this->iMysqli->real_escape_string($password);
					$loadQuery = $loadSQL->login();
			} break;
			case 'filesArchive' : $loadQuery = $loadSQL->InstallFile(); break;
			default: $loadQuery = '';
		
		}
		
		return $loadQuery;
		
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	performing query when query is sent directly from page
	//
	
	public function performDirectQuery($aQuery) {
		
		$res = $this->iMysqli->query($aQuery) 
            or die("Could not query database, query =<br/><pre>{$aQuery}</pre><br/>{$this->iMysqli->error}");

        return $res;
		
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	performing multi_query when query is sent directly from page
	//
	
	public function performDirectMultiQuery($aQuery) {
		
		$res = $this->iMysqli->multi_query($aQuery) 
            or die("Could not query database, query =<br/><pre>{$aQuery}</pre><br/>{$this->iMysqli->error}");

        return $res;
		
	}
	
	// ------------------------------------------------------------------------------------
	//
	// Execute multiquery, retrieve and store the resultset in an array.
	// Return the resultset.
	//
	
	public function performMultiQueryAndStore($aQuery) {

		$res = $this->iMysqli->multi_query($aQuery)
		or die("Could not query database, query =<br/><pre>{$aQuery}</pre><br/>{$this->iMysqli->error}");

		$results = Array();
		do {
		$results[] = $this->iMysqli->store_result();
		} while($this->iMysqli->next_result());

		// Check if there is a database error
		!$this->iMysqli->errno
		or die($this->iMysqli->errno);

		return $results;
	}
	
	
	} // end of class
?>