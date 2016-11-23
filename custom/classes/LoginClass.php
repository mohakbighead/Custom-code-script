<?php
class LoginClass{
	
	private $db;
	
	/**
     * Constructor to create instance of DB object
     *
	 */
	public function __construct(){
		$this -> db = DbClass::getInstance();
		$this -> db -> getsettingsData();
	}
	
	/**
     * Client Login
     *
	 * @param int - user id
	 *
	 */
	public function clientLogin($username, $password, $remember=false){
				
		$username = $this -> db -> cleanData($username);
		$password = $this -> db -> cleanData($password);
		
		$passObj = new PassHashClass();
		
		$encrypted_pass = $passObj -> hash($password);
		
		$user = $this -> db -> row("SELECT * FROM `user` WHERE username = :u AND password = :ep ",array('u' => $username, 'ep' => $encrypted_pass));
		
		if(count($user) && $user['user_id'] > 0){
			$_SESSION['user_id'] = $user['user_id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['user_email'] = $user['user_email'];
			$_SESSION['lastlogin'] = time();
			$red_url = SITEURL.'dashboard';
			$output = json_encode(array('type'=>'success', 'text' => 'Please wait logging to dashboard...','urlpass'=>$red_url));
		}
		else{
			$output = json_encode(array('type'=>'error', 'text' => 'Invalid Username or Password'));
		}
		return $output;
	}
	
	/**
     * Client Logout
	 *
	 */
	public function clientLogout(){		
    	session_destroy();
		$passObj = new PassHashClass();
		header("location: ".SITEURL);		
	}
}
?>