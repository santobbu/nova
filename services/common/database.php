<?php 
ini_set("memory_limit", "2048M");

class Database {
	private static $instance;

	private $_rows_affected;
	private $_conn;

	public static function getInstance() {
	    if(!self::$instance) {
			self::$instance = new self();
	    }
	    return self::$instance;
	}
	 
  	function __construct() {
		$this->_conn = $this->open_connection();
  	}
  	
	function open_connection()
	{
		global $config;

		if( !$this->_conn ){
			$this->_conn = mysql_connect( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD ) or error( 'Database Connection error', 'Error', mysql_error() );
			mysql_query("SET NAMES UTF8");
		}

		return $this->_conn;
	}
	
	function close_connection()
	{
		mysql_close( $this->_conn );
	}
	
	function select_db( $database_name = '' )
	{
		global $config;
	
		if( !$database_name )
			$database_name = DB_NAME;
		if( !$this->_conn ){
			$this->open_connection();
		}

		mysql_select_db( $database_name, $this->_conn ) or die( "Select database error" );
	}

	function query( $command )
	{
		global $config;
		
		$this->_rows_affected	 = 0;
	
		$this->select_db( );

		$result	= mysql_query( $command ) or die('MYSQL_QUERY_ERROR: ' . $command);

		$this->_rows_affected	= mysql_affected_rows() ;
		 
		if( $this->_rows_affected == 0 )
	    {
	        return FALSE;
	    }
	    else if( !$result && ($this->_rows_affected > 0 ))
	    {
	        return $this->_rows_affected;
	    }
		return $result;
	}
	
	function num_rows( $query_result )
	{
		if( $query_result ){
			return mysql_num_rows( $query_result );
		}
		return 0;
	}
	
	function is_query_error()
	{
		if( mysql_error() == '' ) return false;
		return true;
	}
	
	function fetch_assoc( $query_result )
	{
		if( $query_result )
			return mysql_fetch_assoc( $query_result );
		return null;
	}
	
	function fetch_all_assoc( $query_result )
	{
		if( $query_result )
		{
			$n = $this->num_rows( $query_result );
			for( $i = 0; $i < $n; $i++ )
				$assoc[$i]	= mysql_fetch_assoc( $query_result );
			return $assoc;
		}
		return null;
	}
}
?>