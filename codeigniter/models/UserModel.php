<?php
class UserModel extends CI_Model {

 function __construct(){
 	parent::__construct();
 }

 function processLogin($userName='',$password){
	// grab user input		
 	$username = $this->security->xss_clean($userName);
    $password = $this->security->xss_clean($password);
	// Load hash class library
	$this->load->library('passhashclass');
	$password = $this->passhashclass->hash($password);
	
	// Prep the query
    $this->db->where('username', $username);
    $this->db->where('password', $password);
	
	// Run the query
    $query = $this->db->get('user');
  	return $query;
 }

}
?>