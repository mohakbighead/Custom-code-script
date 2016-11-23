<?php

define('FACEBOOK_SDK_V4_SRC_DIR','../Vendor/fb/src/Facebook/');
require_once("../Vendor/fb/autoload.php");
/*use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;*/
/**
 * This UsersController class will have functions that handles user registeration,
 * login, forget password and other functionalities
 * @author muni
 * @copyright www.smarttutorials.net
 */
Class UserController extends AppController
{
	/**
	 * This beforeFilter will excuted before excting other
	 * functions, this will some function to excute before get
	 * logged in
	 * (non-PHPdoc)
	 * @see Controller::beforeFilter()
	 */
	public function beforeFilter()
	{
		$this->Auth->allow('fblogin', 'fb_login', 'google_login', 'googlelogin', 'register', 'forget_password', 'check_email', 'check_email_exists', 'check_password' );
		parent::beforeFilter();
	}
	
	/**
	 * Main index page
	 */
	public function index()
	{
		$this->layout = 'main';
	}
	
	/**
	 * This function will handle user login
	 */
	public function login()
	{
		$this->layout = 'login';
		
		$id = $this->Auth->user('id');
		if (!empty($id)){
			$this->redirect(BASE_PATH);
		}
		
		
		if ( !empty( $this->request->data )) {
			$this->Auth->login();
			$id = $this->Auth->user('id');
			if (!empty($id)) {
				$this->Session->setFlash(LOGIN_SUCCESS, 'default', array( 'class' => 'message success'), 'success' );
				$this->redirect(BASE_PATH);
			} else {
				$this->Session->setFlash(LOGIN_ERROR, 'default', array( 'class' => 'message error'), 'error' );
				$this->redirect(BASE_PATH.'login');
			}
		}
	}
	/**
	 * This function will handle user registration functionality
	 */
	public function register()
	{
		$this->layout = 'login';
		if( !empty( $this->request->data ) ) {
			$this->request->data['User']['password'] = $this->Auth->password( $this->request->data['User']['password'] );
			$this->request->data['User']['uuid'] = String::uuid ();
			if( $this->User->save( $this->request->data ) ){
				$this->Session->setFlash(REGISTRATION_SUCCESS, 'default', array( 'class' => 'message error'), 'success' );
				$this->redirect(BASE_PATH.'login');
			}else{
				$this->Session->setFlash(REGISTRATION_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
				$this->redirect(BASE_PATH.'login');
			}
			
		}
	}
	/**
	 * This function will handle forget password functionality
	 */
	public function forget_password()
	{
		$this->layout = 'login';
		if( !empty( $this->request->data ) ){
			$email =  $this->request->data['User']['email'];
			$password = $this->randomPassword();
			$password1 = $this->Auth->password( $password );
			$this->User->query("UPDATE users SET password = '$password1' WHERE email = '$email'");
			$to = $email;
			$subject = "New Password Request";
			$txt = "Your New Password ".$password;
			$headers = "From: admin@smarttutorials.net" . "\r\n" .
					"CC: admin@smarttutorials.net";
			
			if( mail($to,$subject,$txt,$headers) ){
				$this->Session->setFlash(FORGET_PASSWORD_SUCCESS, 'default', array( 'class' => 'message error'), 'success' );
				$this->redirect(BASE_PATH.'login');
			}else{
				$this->Session->setFlash(FORGET_PASSWORD_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
				$this->redirect(BASE_PATH.'forget_password');
			}
		}
	}
	/**
	 * This function will handle user logout functionality
	 */
	public function logout()
	{
		$this->autoRender = false;
		$this->Auth->logout();
		$this->Session->setFlash(LOGOUT_SUCCESS, 'default', array( 'class' => 'message error'), 'success' );
		$this->redirect(BASE_PATH);
	}
	
	/**
	 * This is my account page
	 */
	public function account() {
		$this->layout = 'main';
		if(!empty($this->request->data)){
			$this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['password']);
			$this->User->save( $this->request->data);
			
			$this->request->data = $this->Auth->user();
			$this->Session->setFlash(PASSWORD_CHANGE_SUCCESS, 'default', array( 'class' => 'message error'), 'success' );
			$this->redirect(BASE_PATH);
		}
		$this->request->data = $this->Auth->user();
	}	
	/**
	 * This function checks unique email for user registration
	 */
	public function check_email()
	{
		$this->autoRender = false;
		if($this->request->is('ajax')){
			$email = $this->request->data['User']['email'];
			if(!empty($email)){
				$result = $this->User->findByEmail( $email);
				if(count( $result ) == 0 )echo 'true';
				else echo "false";
			}else{
				echo "false";
			}
		}
	}
	/**
	 * This function checks email for forget password
	 */
	public function check_email_exists()
	{
		$this->autoRender = false;
		if($this->request->is('ajax')){
			$email = $this->request->data['email'];
			if(!empty($email)){
				$result = $this->User->findByEmail( $email);
				if(count( $result ) == 0 )echo 'false';
				else echo "true";
			}else{
				echo "false";
			}
		}
		exit;
	}
	
	/**
	 * This function checks current password matches user entered password
	 * while changing password
	 */
	public function check_password()
	{
		$this->autoRender = false;
		if($this->request->is('ajax')){
			$password = $this->request->data['password'];
			$email = $this->request->data['email'];
			if(!empty($email) && !empty( $password)){
				$password = $this->Auth->password($password);
				$result = $this->User->check_password( $email, $password);
				if(count( $result ) == 1 )echo 'true';
				else echo "false";
			}else{
				echo "false";
			}
		}
		exit;
	}
	
	/**
	 * This function generates random password 
	 * @return string
	 */
	private function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	} 
	/**
	 * This is farmer donkey page
	 */
	public function farmer_donkey()
	{
		$this->layout = 'main';
	}
	
	/**
	 * This is never quit page
	 */
	public function never_quit()
	{
		$this->layout = 'main';
	}	

	public function googlelogin()
	{
		$this->autoRender = false;
		require_once '../Config/google_login.php';
		$client = new Google_Client();
		$client->setScopes(array('https://www.googleapis.com/auth/plus.login','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me'));
		$client->setApprovalPrompt('auto');
		$url = $client->createAuthUrl();
		$this->redirect($url);
	}
	
	
	public function google_login()
	{
		$this->autoRender = false;
		require_once '../Config/google_login.php';
		$client = new Google_Client();
		$client->setScopes(array('https://www.googleapis.com/auth/plus.login','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me'));
		$client->setApprovalPrompt('auto');
		
		$plus       = new Google_PlusService($client);
		$oauth2     = new Google_Oauth2Service($client);
		if(isset($_GET['code'])) {
			$client->authenticate(); // Authenticate
			$_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
		}
		
		if(isset($_SESSION['access_token'])) {
			$client->setAccessToken($_SESSION['access_token']);
		}
		
		if ($client->getAccessToken()) {
			$_SESSION['access_token'] = $client->getAccessToken();
			$user         = $oauth2->userinfo->get();
			try {
				if(!empty($user)){
					$result = $this->User->findByEmail( $user['email'] );
					if(!empty( $result )){
						if($this->Auth->login($result['User'])){
							$this->Session->setFlash(GOOGLE_LOGIN_SUCCESS, 'default', array( 'class' => 'message success'), 'success' );
							$this->redirect(BASE_PATH);
						}else{
							$this->Session->setFlash(GOOGLE_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
							$this->redirect(BASE_PATH.'login');
						}
							
					}else{
						$data = array();
						$data['email'] = $user['email'];
						$data['first_name'] = $user['given_name'];
						$data['last_name'] = $user['family_name'];
						$data['social_id'] = $user['id'];
						$data['picture'] = $user['picture'];
						$data['gender'] = $user['gender'] == 'male' ? 'm':'f';
						$data['user_level_id'] = 1;
						$data['uuid'] = String::uuid();
						$this->User->save( $data );
						if($this->User->save( $data )){
							$data['id'] = $this->User->getLastInsertID();
							if($this->Auth->login($data)){
								$this->Session->setFlash(GOOGLE_LOGIN_SUCCESS, 'default', array( 'class' => 'message success'), 'success' );
								$this->redirect(BASE_PATH);
							}else{
								$this->Session->setFlash(GOOGLE_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
								$this->redirect(BASE_PATH.'login');
							}
					
						}else{
							$this->Session->setFlash(GOOGLE_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
							$this->redirect(BASE_PATH.'login');
						}
					}
					
				}else{
					$this->Session->setFlash(GOOGLE_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
					$this->redirect(BASE_PATH.'login');
				}
			}catch (Exception $e) {
				$this->Session->setFlash(GOOGLE_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
				$this->redirect(BASE_PATH.'login');
			}
		}
		
		exit;
	}
	
	/**
	 * Facebook Login
	 */
	
	public function fblogin()
	{
		$this->autoRender = false;
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);
		$helper = new FacebookRedirectLoginHelper(FACEBOOK_REDIRECT_URI);
		$url = $helper->getLoginUrl(array('email'));
		$this->redirect($url);
	}
	
	public function fb_login()
	{
		$this->layout = 'ajax'; 
		FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);
		$helper = new FacebookRedirectLoginHelper(FACEBOOK_REDIRECT_URI);
		$session = $helper->getSessionFromRedirect();
	
		if(isset($_SESSION['token'])){
			$session = new FacebookSession($_SESSION['token']);
			try{
				$session->validate(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);
			}catch(FacebookAuthorizationException $e){
				echo $e->getMessage();
			}
		}
	
		$data = array();
		$fb_data = array();
	
		if(isset($session)){
			$_SESSION['token'] = $session->getToken();
			$request = new FacebookRequest($session, 'GET', '/me');
			$response = $request->execute();
			$graph = $response->getGraphObject(GraphUser::className());
	
			$fb_data = $graph->asArray();
			$id = $graph->getId();
			$image = "https://graph.facebook.com/".$id."/picture?width=100";
				
			if( !empty( $fb_data )){
				$result = $this->User->findByEmail( $fb_data['email'] );
				if(!empty( $result )){
					if($this->Auth->login($result['User'])){
						$this->Session->setFlash(FACEBOOK_LOGIN_SUCCESS, 'default', array( 'class' => 'message success'), 'success' );
						$this->redirect(BASE_PATH);
					}else{
						$this->Session->setFlash(FACEBOOK_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
						$this->redirect(BASE_PATH.'login');
					}
						
				}else{
					$data['email'] = $fb_data['email'];
					$data['name'] = $fb_data['first_name'];
					$data['social_id'] = $fb_data['id'];
					$data['picture'] = $image;
					$data['uuid'] = String::uuid ();
					$this->User->save( $data );
					if($this->User->save( $data )){
						$data['id'] = $this->User->getLastInsertID();
						if($this->Auth->login($data)){
							$this->Session->setFlash(FACEBOOK_LOGIN_SUCCESS, 'default', array( 'class' => 'message success'), 'success' );
							$this->redirect(BASE_PATH);
						}else{
							$this->Session->setFlash(FACEBOOK_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
							$this->redirect(BASE_PATH.'index');
						}
	
					}else{
						$this->Session->setFlash(FACEBOOK_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
						$this->redirect(BASE_PATH.'index');
					}
				}
	
	
	
	
			}else{
				$this->Session->setFlash(FACEBOOK_LOGIN_FAILURE, 'default', array( 'class' => 'message error'), 'error' );
				$this->redirect(BASE_PATH.'index');
			}
				
				
		}
	}
}