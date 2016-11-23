<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
	
	public function __construct()
	{
	  parent::__construct();
	  //Load the user model
	  $this->load->model('UserModel');
	  //Load the Library 'form_validation'
	  $this->load->library(array('form_validation','session'));
	  $this->load->helper(array('url','html','form'));
	 }

	/**
	 * Index Page for this controller.
	 *	
	 */
	public function index()
	{
		$this->login();
	}
	
	public function login(){
		//Load login view.
		$this->load->view('login');
	}
	
	public function dashboard(){
		//Load dashboard view.
  		$this->load->view('dashboard');
 	}
	
	function logout(){
  		$this->session->sess_destroy();
  		redirect(base_url());
 	}
	
	function signin(){

  		$userName= trim($this->input->post('userName'));
  		$password= trim($this->input->post('password'));
		//Call processLogin function 
  		$query = $this->UserModel->processLogin($userName,$password);

		
  		$this->form_validation->set_rules('userName', 'Username', 'required|callback_validateUser[' . $query->num_rows() . ']');
  		$this->form_validation->set_rules('password', 'Password', 'required');

  		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		//Set custom error message
  		$this->form_validation->set_message('required', 'Enter %s');

  		if ($this->form_validation->run() == false) {
			//Load login view
   			$this->load->view('login');
  		}else{
   			if($query){
    			$query = $query->result();
    			$user = array(
     				'user_id' => $query[0]->user_id,
     				'username' => $query[0]->username,
     				'user_email' => $query[0]->user_email
    			);
				//set value to the session
    			$this->session->set_userdata($user);
    			redirect('dashboard');
   			}
  		}
 	}
	
	/** Custom Validation Method*/
 	public function validateUser($userName,$recordCount)
	{
  		if ($recordCount != 0){
   			return true;
  		}else{
   			$this->form_validation->set_message('validateUser', 'Invalid %s or Password');
   			return false;
  		}
 	}
}
