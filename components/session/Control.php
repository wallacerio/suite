<?php

class Control{

	
	public static function get($session_id){		
		if(session_id() == '') {                       
				@session_start($session_id);
        }
        $array = isset($_SESSION)?$_SESSION:null;				
        if(count($array)>0)
        	return $array;
    	else
    		return null;
	}



	public static function out($session_id){
		
		foreach ($_SESSION as $key => $value) {
			$_SESSION[$key] = null;
			unset($_SESSION[$key]);
			if(isset($_SESSION[$key])) unset($_SESSION[$key]);
		}
		
		@session_destroy($session_id);

		return false;
	}


	public static function in($session_id,$access){

		
		
		if(is_object($access)){
			$access = json_encode($access);	
			$access = json_decode($access,true);
			
		}
		


		 if(session_id() == '') {                       
				session_start('suite_dashboard');
         }

         /*if( is_object($access) && !isset($access['session'])){         	
         	//$access['session'] = session_id();
         }*/


         foreach ($access as $key => $value) {
         	$_SESSION[$key] = $value;	
         }

         /*if(count($access)>0){
			$_SESSION['dashboard_data'] = json_encode($access);			
			return true;
		}*/
	}

}