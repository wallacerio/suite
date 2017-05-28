<?php

	
class HMVCoptions{

	private static $hmvcNameFolder = '_hmvc';

	public static function set($parameters){		
		return $this->get($parameters,true);
	}

	public static function get($parameters,$modeSave = false){

		$mode = "echo";

	
	
		$app = isset($_POST['app'])?$_POST['app']:'current';
		$target = isset($_POST['target'])?$_POST['target']:null;
		$content = isset($_POST['content'])?$_POST['content']:null;
		

		if($target == null){
			$mode = "return";			
			$app = isset($parameters['app'])?$parameters['app']:'current';
			$target = isset($parameters['target'])?$parameters['target']:null;
			$content = isset($parameters['content'])?$parameters['content']:null;
			
		}

		$domainDir = Suite_globals::get('http/domain/dir');
		
		if($app == 'current'){
			$appDir = Suite_globals::get('app/dir');			
		}else{			
			$appDir = $domainDir.'app/'.$app.'/';
		}

		

		$actionCurrent = Suite_globals::get('http/action');
		
		if($actionCurrent == '')
			$actionCurrent = 'home';

		
		if($target == 'current'){
			$dirTarget = $appDir.self::$hmvcNameFolder.DIRECTORY_SEPARATOR.$actionCurrent.DIRECTORY_SEPARATOR;			
		}else if($target == '/'){
			$dirTarget = $appDir.self::$hmvcNameFolder.DIRECTORY_SEPARATOR;
		}else if($target == '.'){
			$dirTarget = $domainDir;	
		}else{

			
			if(substr($target, strlen($target)-1)=='/')
				$target = substr($target, 0,strlen($target)-1);
	

			$dirTarget = $appDir.self::$hmvcNameFolder.DIRECTORY_SEPARATOR.$target.DIRECTORY_SEPARATOR;
		}

		


			$fileOptions = $dirTarget.'options.json';

			if($modeSave == true){
				file_put_contents($fileOptions, $content);
				return true;
			}
			
			

			if(file_exists($fileOptions)){

				$contentOptionsJSON = file_get_contents($fileOptions);
				$contentOptionsObj = json_decode($contentOptionsJSON,true);

				return $contentOptionsObj;
			}else{
				return false;
			}
		
		
	}
}