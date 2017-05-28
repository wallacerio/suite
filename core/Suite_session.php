<?php
/*
 * Suite Framework
 * ================
 * suite.wallrio.com
 *
 * This file is part of the Suite Core.
 *
 * Wallace Rio <wallrio@gmail.com> 
 * 
 */



class Suite_session{

	private static $role = null;
	private static $session_id = null;

	public static function sessionId($session_id = null){
		if($session_id == null)
			return self::$session_id;
		self::$session_id = $session_id;
	}
	/**
	 * verifica o papel do usuario
	 * @param  [type] $roleCompareArray [description]
	 * @param  string $session_id       [description]
	 * @return [type]                   [description]
	 */
	public static function checkRole($roleCompareArray = null,$session_id=null){				

		if($session_id == null)return false;	
		

		// if($session_id == null)
			// $session_id = self::$session_id;

			

		$data = self::get($session_id);	


		

		if($roleCompareArray == null) return false;
		$role = isset($data['role'])?$data['role']:array('subscriber');
		


		if($role != null && is_array($role))					
		foreach ($roleCompareArray as $key => $value) {	
			if(in_array($value, $role))
				return true;		
		}

		return false;
	}

	// captura ou define o papel do usuario
	public static function role($role = null){		
		
		if($role == null)
			return self::$role;

		self::$role = $role;


	}


	public static function get($session_id = ''){		
		if($session_id == null)return false;	
		session_id($session_id);	
		@session_start();

		self::$session_id = $session_id;

		// session_id($session_id);	
		// session_start($session_id);
		 // if(session_id($session_id) == '') {   
				// @session_start();
         // }
        
        $array = isset($_SESSION)?$_SESSION:null;		      

        if(count($array)>0){        	
        	return $array;
        }else{
    		return null;
    	}
	}



	public static function destroy($session_id = null){
		if($session_id == null) return false;
		
		session_id($session_id);	
		session_start();

		self::$session_id = $session_id;
		
		foreach ($_SESSION as $key => $value) {
			$_SESSION[$key] = null;
			unset($_SESSION[$key]);
			if(isset($_SESSION[$key])) unset($_SESSION[$key]);
		}
		
		@session_destroy($session_id);

		return true;
	}


	public static function set($session_id,$data){

		if($session_id == null)return false;
		/*
		if(is_object($access)){
			$access = json_encode($access);	
			$access = json_decode($access,true);
			
		}*/
		


		 if(session_id($session_id) == '') {                       
				
				self::$session_id = $session_id;

				session_id($session_id);	
				session_start();
         }

         /*if( is_object($access) && !isset($access['session'])){         	
         	//$access['session'] = session_id();
         }*/


         foreach ($data as $key => $value) {
         	$_SESSION[$key] = $value;	
         }

         // print_r($data['role']);
         if(isset($data['role'])){
        	 self::role($data['role']);
     	}

        /* echo '<pre>';
         print_r($_SESSION);
         echo '</pre>';*/

         /*if(count($access)>0){
			$_SESSION['dashboard_data'] = json_encode($access);			
			return true;
		}*/
	}


}