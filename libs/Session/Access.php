<?php

	
class Session__Access{
	// private $roles = array('root','admin','editor','author','subscriber');
	public function checkPermission($options){

		$allowed = isset($options['allowed'])?$options['allowed']:null;
		$role = isset($options['role'])?$options['role']:null;

		$allowedArray = explode(',', $allowed);


		if($role != null && in_array($role,$allowedArray))
			return true;
		else
			return false;
	}

}