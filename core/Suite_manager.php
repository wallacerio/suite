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



class Suite_manager{

	public static function status(){
		
		$result = Suite_session::get('suitemanager');
		if($result)
			$logged = true;
		else
			$logged = false;

	
		$result['logged'] = $logged;

		return (object) $result;
	}

	public static function login($data = null){

		$username = isset($data['username'])?$data['username']:null;
		$password = isset($data['password'])?$data['password']:null;
		$dataSave = isset($data['data'])?$data['data']:null;

		$optionsBase = self::getOptionsBase();

		

		if($optionsBase['access']['username'] == $username){
			if($optionsBase['access']['password'] == md5($password)){

				$sessionData = array(
					'username' => $username,
					'role' => 'root'
				);

				if($dataSave != null) $sessionData['data'] = $dataSave;
				
				Suite_session::set('suitemanager',$sessionData);
				return true;				
			}
		}


		return false;
			
	}

	public static function logout(){
		Suite_session::destroy('suitemanager');
		return true;
	}

	public static function getOptionsBase(){
		return Suite_libs::run('Options/base');
	}
}