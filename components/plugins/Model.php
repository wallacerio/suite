<?php

class Model{

	/**
	 * faz o login no manager dos plugins
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function managerLoginAction($args){	

		// access from url to manager
		$pluginsManager = new CompPluginsManager();
		$result = $pluginsManager->login($args);

		if($result == true){
			$status = 'success';
		}else{
			$status = 'error';
		}

		return array(
			'response'=>json_encode(array('status'=>$status)),
			'type'=>'application/json'
		);
	}

	/**
	 * faz o logout no manager dos plugins
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function managerLogoutAction($args){	

		// access from url to manager
		$pluginsManager = new CompPluginsManager();
		$result = $pluginsManager->logout();

		if($result == true){
			$status = 'success';
		}else{
			$status = 'error';
		}

		return array(
			'response'=>json_encode(array('status'=>$status)),
			'type'=>'application/json'
		);
	}


}