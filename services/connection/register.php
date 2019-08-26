<?php
defined( '_VALID_SETTING' ) or die( 'Direct Access to this location is not allowed.' );

class Register extends common {
	
	public $table = 'tb_customer';
	public $keyField = 'customerid';

	/**
	* get all product with detail
	**/
	function get_imei( $imei ) {
		$cmd  = 'SELECT cus.* ';
		$cmd .= 'FROM tb_customer cus ';
		$cmd .= 'WHERE 1 = 1 ';
		$cmd .= 'AND cus.imei = \'' . $imei . '\' ';	

		//echo $cmd;
		$result = $this->db->fetch_assoc( $this->db->query( $cmd ) );
		return $result;
	}

	/**
	* get customer by firstname and lastname
	**/
	function get_customer( $firstname, $lastname ) {
		$cmd  = 'SELECT cus.* ';
		$cmd .= 'FROM tb_customer cus ';
		$cmd .= 'WHERE 1 = 1 ';
		$cmd .= 'AND cus.firstname = \'' . $firstname . '\' ';	
		$cmd .= 'AND cus.lastname = \'' . $lastname . '\' ';

		//echo $cmd;
		$result = $this->db->fetch_all_assoc( $this->db->query( $cmd ) );
		return $result;
	}

	function get_customer_detail( $customerid ) {
		$cmd  = 'SELECT cus.* ';
		$cmd .= 'FROM tb_customer cus ';
		$cmd .= 'WHERE 1 = 1 ';
		$cmd .= 'AND cus.customerid = ' . $customerid. ' ';	

		//echo $cmd;
		$result = $this->db->fetch_assoc( $this->db->query( $cmd ) );
		return $result;
	}

	function register_member( $data ) {
		return $this->insert( $data );
	}

	function get_report( $startdate, $enddate ) {
		$cmd  = "SELECT cus.* ";
		$cmd  .= ", CASE WHEN cus.sex = 1 THEN 'นาย' WHEN cus.sex = 2 THEN 'นาง' WHEN cus.sex = 3 THEN 'นางสาว' END AS prefix ";
		$cmd  .= ", cor.name AS colorname ";
		$cmd  .= "FROM tb_customer cus  ";
		$cmd  .= "LEFT JOIN tb_color cor  ";
		$cmd  .= "	ON cus.color = cor.colorid  ";
		$cmd  .= "WHERE 1 = 1 ";
		if (!empty($startdate)) {
			$cmd  .= "AND cus.createddate >= '". $startdate ."' ";
		}
		if (!empty($enddate)) {
			$cmd  .= "AND cus.createddate < '". $enddate ."' ";
		}
		$cmd  .= "ORDER by cus.createddate ";

		//echo $cmd;
		$result = $this->db->fetch_all_assoc( $this->db->query( $cmd ) );
		return $result;
	}

}
?>