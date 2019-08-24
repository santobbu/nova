<?php
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );

class Common {

	public $db;
  
	private $_rows_affected;
	private $_connection;
	public $table = '';
	public $keyField = '';
	public  $currentuser;
	public $defaultacctor;
  	public $currentuserid;

  	function __construct() {
  		$this->db = Database::getInstance();
  		if(isset($_SESSION['CurrentAccount'])){
  			$this->currentuser = unserialize( $_SESSION['CurrentAccount']);
    		$this->currentuserid = $this->currentuser->getUserId();
  		}else{
  			$this->currentuserid = 9999999;
  		}
  		
  		$this->defaultacctor = 9999999;
  	}
	
	function get_by_key( $table, $keyid, $value )
	{
		$cmd = 'SELECT * FROM '. $table .' WHERE `'. $keyid . '` = \'' . $value . '\'';
		//echo $cmd;
		$result = $this->db->fetch_assoc( $this->db->query($cmd) );
		return $result;
	}

	function get_by_multiple_key( $table, $datainfo )
	{
		$fields = '';
		foreach( $datainfo as $key => $val )
		{
			if( !$val ) { $val = ''; }
			$fields .= '`'. $key . '`=\''. mysql_real_escape_string($val).'\' AND ';
		}
	
		$fields = trim( $fields, 'AND ' );

		$cmd = 'SELECT * FROM ' .$table. ' WHERE '. $fields;
		
		$result = $this->db->fetch_assoc( $this->db->query($cmd) );
		return $result;
	}

	/**
	Get All assessment by key
	**/
	function get_all_by_key( $table, $keyid, $value, $order_field = null, $order = 'ASC' )
	{
		$cmd = 'SELECT * FROM '. $table .' WHERE `'. $keyid . '` = \'' . $value . '\' ';
		if( isset($order_field) )
			$cmd .= 'ORDER BY `'. $order_field. '` '. $order;
		else
			$cmd .= 'ORDER BY `'. $keyid. '` '. $order;
		//echo $cmd;
		$result = $this->db->fetch_all_assoc( $this->db->query($cmd) );
		return $result;
	}

	/**
	get all by multiple key
	**/
	function get_all_by_multiple_key( $table, $datainfo )
	{
		$fields = '';
		foreach( $datainfo as $key => $val )
		{
			if( !$val ) { $val = ''; }
			$fields .= '`'. $key . '`=\''. mysql_real_escape_string($val).'\' AND ';
		}
	
		$fields = trim( $fields, 'AND ' );

		$cmd = 'SELECT * FROM ' .$table. ' WHERE '. $fields;
		//echo $cmd;
		$result = $this->db->fetch_all_assoc( $this->db->query($cmd) );
		return $result;
	}

	/**
	Insert new item
	**/
	function insert_item( $table, $keyid, $datainfo )
	{
		unset($datainfo[$keyid]);
		$fields = '';
		$values = '';
		
		if( !isset($datainfo['createddate']) ) {
			$datainfo['createddate'] = get_system_date();
		}

		if( $this->currentuser ){
			$datainfo['createdby'] = $this->currentuser->getUserId();		
		}else{
			$datainfo['createdby'] = $this->defaultacctor;
		}
		
		foreach( $datainfo as $key => $val )
		{
			if( !$val ) $val = '';
			$fields .= '`'. $key . '`,';
			$values .= ' \''. mysql_real_escape_string($val) .'\',';
		}
	
		$fields = trim( $fields, ',' );
		$values= trim( $values, ',' );
		$cmd = 'INSERT INTO '. $table .'('.$fields.') VALUES('.$values.')';

		//echo $cmd;
		return $this->db->query( $cmd );
	}


	/**
	Insert
	**/
	function insert( $data )
	{
		$this->insert_item( $this->table, $this->keyField, $data );
		
		if(!$data[ $this->keyField ]){
			$uid = $this->get_last_insert_id( $this->table );
		}else{
			$uid = $data[ $this->keyField ];
		}
		
		return $uid;
	}

	
	/**
	update or add new item
	**/
	function update_item( $table, $keyid, $datainfo )
	{
		if(!isset($datainfo[$keyid]) || !$datainfo[$keyid]){
			return $this->insert_item( $table, $keyid, $datainfo );	
		}

		$datainfo['updateddate'] = get_system_date();
		if( $this->currentuser ){
			$datainfo['updatedby'] = $this->currentuser->getUserId();
		}else{
			$datainfo['updatedby'] = $this->defaultacctor;
		}
		
		$fields = '';
		foreach( $datainfo as $key => $val )
		{
			if( !$val ) $val = '';
			$fields .= '`'. $key . '`=\''. mysql_real_escape_string($val).'\',';
		}
	
		$fields = trim( $fields, ',' );
		if( $keyid == 'ID' ){
			$cmd = 'UPDATE ' .$table. ' SET '.$fields.' WHERE '. $keyid . ' = '. $datainfo[$keyid] .' ';
		}else{
			$cmd = 'UPDATE ' .$table. ' SET '.$fields.' WHERE '. $keyid .' = \''. $datainfo[$keyid].'\' ';
		}

		//echo $cmd;
		return $this->db->query( $cmd );
	}

	/**
	update
	**/
	function update( $data )
	{
		$this->update_item( $this->table, $this->keyField, $data );
		
		if(!$data[ $this->keyField ]){
			$uid = $this->get_last_insert_id( $this->table );
		}else{
			$uid = $data[ $this->keyField ];
		}
		
		return $uid;
	}
	
	/**
	update or add new item by multiple keys
	**/
	function update_item_multiple_key( $table, $datainfo, $keyinfo )
	{
		$datainfo['updateddate'] = get_system_date();
		if( $this->currentuser ){
			$datainfo['updatedby'] = $this->currentuser->getUserId();
		}else{
			$datainfo['updatedby'] = $this->defaultacctor;
		}
		
		$fields = '';
		foreach( $datainfo as $key => $val )
		{
			if( !$val ) $val = '';
			$fields .= '`'. $key . '`=\''. mysql_real_escape_string($val).'\',';
		}
		$fields = trim( $fields, ',' );

		//key list
		$keys = '';
		foreach( $keyinfo as $key => $val )
		{
			if( !$val ) $val = '';
			$keys .= 'AND `'. $key . '`=\''. mysql_real_escape_string($val).'\' ';
		}

		$cmd = 'UPDATE ' .$table. ' SET '.$fields.' WHERE 1=1 '. $keys;

		//echo $cmd;
		return $this->db->query( $cmd );
	}

	/**
	delete item by id
	**/
	function delete_item_by_key( $table, $keyid, $value )
	{
		$cmd = 'DELETE FROM ' .$table. ' WHERE `'. $keyid. '` = \''. $value. '\'' ;
		
		$keys = array ();
		$keys[ $keyid ] = $value;
		$this->log_deleting_rows( $table, $keys );

		$this->db->query( $cmd );
	}


	/**
	delete item by key list
	**/
	function delete_item_by_key_list( $table, $datainfo )
	{	
		$fields = '';
		foreach( $datainfo as $key => $val )
		{
			if( !$val ) { $val = ''; }
			$fields .= '`'. $key . '`=\''. mysql_real_escape_string($val).'\' AND ';
		}
	
		$fields = trim( $fields, 'AND ' );
		$cmd = 'DELETE FROM ' .$table. ' WHERE '. $fields;

		$this->log_deleting_rows( $table, $datainfo );

		//echo $cmd;
		return $this->db->query( $cmd );
	}

	/**
	delete item by id
	**/
	function mark_as_deleted_by_key( $table, $keyid, $value )
	{
		$cmd = 'UPDATE ' . $table . ' SET isdeleted = 1 WHERE '. $keyid .' = \''. $value .'\' ';
		
		$this->db->query( $cmd );
	}

	/**
	get all item
	**/
	function get_all( $table, $order_field = 'id', $order = 'ASC' )
	{
		$cmd = 'SELECT * FROM ' .$table. ' ';
		$cmd .= 'ORDER BY `'. $order_field. '` '. $order;
	
		$result = $this->db->fetch_all_assoc( $this->db->query( $cmd ) );
		return $result;
	}

	/**
	get last inserted table id
	**/
	function get_last_insert_id( $table )
	{
		$query = 'SELECT LAST_INSERT_ID() AS id FROM '. $table . ' LIMIT 1';
		$row = $this->db->fetch_assoc( $this->db->query($query) );

		return $row['id'];
	}

	/**
	check project name to prevent duplication
	**/
	function is_name_duplicated( $keyid, $name, $description ) {
		$cmd = 'SELECT * FROM '. $this->table .' ';
		$cmd .= 'WHERE `'. $this->keyField .'` NOT IN (\'' . $keyid . '\') ';
		$cmd .= 'AND name = \'' . $name . '\' ';
		if( isset($description) ){
			$cmd .= 'AND description = \'' . $description . '\' ';
		}
		$cmd .= 'AND isdeleted = 0 ';
	//echo $cmd;
		$result = $this->db->fetch_assoc( $this->db->query( $cmd ) );
	
		return (!empty( $result ));
	}

	/**
	update or add new item by multiple keys
	**/
	function log_deleting_rows (  $table, $keys  ) {
		//echo 'adding log';
		$deletingRows = $this->get_all_by_multiple_key( $table, $keys  );
		if( isset($deletingRows) && !empty($deletingRows) ) {
			//echo 'adding log 2 ';
			foreach ( $deletingRows as $row ) {
				//echo 'adding log 3 ';
				savelog( $table, json_encode($row) );
			}
		}
	}
}
?>