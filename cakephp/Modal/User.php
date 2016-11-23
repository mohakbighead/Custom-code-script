<?php
class User extends AppModel
{
	function check_password( $email = '', $password = '')
	{
		return $this->find('first', array('fields' => array('id'),'conditions' => array('email' => $email, 'password' => $password)));
	}
}