<?php

class CompSetupAccess{
	
	
	public static function session($args = null){
		// return false;
		$domain = Suite_globals::get('http/domain/host');
		if(substr($domain, strlen($domain)-1,strlen($domain))=='/')
			$domain = substr($domain, 0,strlen($domain)-1);
		
		$domainFit = str_replace('/', '-', $domain);
		$identifier = 'suiteCompSetup-'.$domainFit;
		// logout
		if($args === false){
			session_name($identifier);
			session_start();		
			foreach ($_SESSION as $key => $value) {
				$_SESSION[$key] = null;
				unset($_SESSION[$key]);
				if(isset($_SESSION[$key])) unset($_SESSION[$key]);
			}
			
								
			return false;
		}


		// get data session
		if($args == null){
			if(session_id() == '') { 
					session_name($identifier);                      
					session_start();
	        }
	        $array = isset($_SESSION)?$_SESSION:null;				
	        

	        if(count($array)>0)
	        	return $array;
	        else
    			return null;
		}

		//set data session
		if(session_id() == '') {  
				session_name($identifier);                     
				session_start();
	    }	   
	    foreach ($args as $key => $value) {
	     	$_SESSION[$key] = $value;	
	    }

		return $_SESSION;
	}


	public static function register($args){
		
		$hashRequest = isset($args['hash'])?$args['hash']:null;

		$ifbase = self::ifaccessexist();

		if( isset($ifbase['access']['username']) &&
			isset($ifbase['access']['password']) ){

			$hash_username = isset($ifbase['access']['username'])?$ifbase['access']['username']:null;
			$hash_password = isset($ifbase['access']['password'])?$ifbase['access']['password']:null;
			$hash = $hash_username.$hash_password;

			if( $hash != $hashRequest)
				return false;

		}

		

		$username = isset($args['username'])?$args['username']:null;
		$password = isset($args['password'])?$args['password']:null;
		
		if($username == null || $password == null )
			return false;

		$return = Suite_libs::run('Options/base',
			array(
				"access"=>array(
					'username' => $username,
					'password' => $password
				)
			)
		);

		return true;
	}

	public static function ifaccessexist(){
		return Suite_libs::run('Options/base');
	}

}