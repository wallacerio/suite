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

class Suite_libs{

	/**
	 * [run description]
	 * @param  [type] $libTarget  [description]
	 * @param  [type] $parameters [description]
	 * @return [type]             [description]
	 */
	public static function run($libTarget = null,$parameters = null,$condition = null){		
		return self::runEfetive($libTarget,$parameters,$condition);		
	}

	/**
	 * [runEfetive description]
	 * @param  [type] $libTarget  [description]
	 * @param  [type] $parameters [description]
	 * @return [type]             [description]
	 */
	public static function runEfetive($libTarget = null,$parameters = null,$condition = null){
		if($libTarget == null)return false;
		

		

		$libTargetArray = explode('/',$libTarget);
		
		$coreDir = Suite_globals::get('core/dir');
		$coreUrl = Suite_globals::get('core/url');
		
		$foundLib = false;

		$libAction = null;
		$joinClass = '';
		$joinDir = '';
		$firstDir = '';
		foreach ($libTargetArray as $key => $value) {
			$joinDir .= ucfirst($value).'/';
			$joinClass .= $value.'__';
			if($firstDir == '')
				$firstDir = ucfirst($value);

			$joinDirTest = substr($joinDir, 0,strlen($joinDir)-1);
			

			$libDir = $coreDir.'libs/'.$joinDirTest.'.php';
			
			$libDirContruct = $coreDir.'libs/'.$firstDir.'/'.$joinDirTest.'.php';
			
			

			if(file_exists($libDirContruct)){
				$joinClass = $joinClass . $firstDir ;
				$libDir = $libDirContruct;
				$foundLib = true;
				break;
			}

			if(file_exists($libDir)){
				$foundLib = true;
				$libAction = $value;

				break;
				
			}
			
		}


		if($foundLib == false){
			throw new Exception('Lib not found: '.$libTarget);					
		}

				

		$libAction = end($libTargetArray);



		if($libAction == null)
			return false;

		if(substr($joinClass, strlen($joinClass)-2,strlen($joinClass)) == '__')
			$joinClass = substr($joinClass, 0,strlen($joinClass)-2);

			require_once $libDir;
		
		

		$libObj = new $joinClass();



		if(method_exists($libObj, $libAction)){			
			return $libObj->$libAction($parameters,$condition);		
		}
	}
}