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

class Suite_settings{

	function __construct(){
		$this->load();
	}

	/**
	 * [load description]
	 * @return [type] [description]
	 */
	public function load(){

		$http = Suite_globals::get('http');

		
		$baseDir = Suite_globals::get('base/dir');
		$baseUrl = Suite_globals::get('base/url');

		$baseOptionsFile = $baseDir . 'options.json';
		if(file_exists($baseOptionsFile)){
			$baseOptionsJson = file_get_contents($baseOptionsFile);
			$options = json_decode($baseOptionsJson,true);
		}


		$settings = array(			
			'prefix' => isset($options['prefix'])?$options['prefix']:'',
			'querytarget' => isset($options['querytarget'])?$options['querytarget']:'url',
			'urlmode' => isset($options['urlmode'])?$options['urlmode']:'rewrite',
			'app' => isset($options['app'])?$options['app']:'default',
			'debug' => isset($options['debug'])?$options['debug']:true,		
			'charset' => isset($options['charset'])?$options['charset']:'utf-8'				
		);

		
		$appName = $settings['app'];

		$http = Suite_globals::get('http');

		$appName = isset($http['app'])?$http['app']:$appName;
		

		$appDir = $baseDir .'app'.DIRECTORY_SEPARATOR. $appName.DIRECTORY_SEPARATOR;
		if($baseUrl == null)
			$appUrl = null;
		else
			$appUrl = $baseUrl .'app/'. $appName.'/';



		Suite_globals::set('app',array(
			'name'=>$appName,
			'dir'=>$appDir,
			'url'=>$appUrl,
		));


		
		
		Suite_globals::set('settings',$settings);

		Suite_settings::setCookieSuite();

		


		$this->execute($settings);

		return $settings;
	}

	/**
	 * [delCookieSuite description]
	 * @return [type] [description]
	 */
	public static function delCookieSuite(){	
		// $domain = Suite_globals::get('http/domain/host');	
		// setcookie('suite',null,0,$domain," ");
	}


	/**
	 * [setCookieSuite description]
	 */
	public static function setCookieSuite(){		
		// define cookie do suite para ser utilizado com javascript
		$domain = Suite_globals::get('http/domain/host');
	
		
		$action = Suite_globals::get('http/action');
		$prefix = Suite_globals::get('http/prefix');
		$posfix = Suite_globals::get('http/posfix');
		$target = Suite_globals::get('http/target');
		
		
		
		if(isset($_COOKIE['suite'])){			
			unset($_COOKIE['suite']);						
		}

		if(!isset($_COOKIE['suite'])){

			if(!Suite_globals::get('http/argv')){
				
				// ini_set('session.cookie_domain', $domain);
				// echo $action;

				$suite_header_json = json_encode(array(
					'name' => Suite_globals::get('app/name'),
	    			'domain'=>Suite_globals::get('http/domain/host'),
	    			'url'=>Suite_globals::get('http/domain/url'),			    				    			
	    			'http'=>array(
	    				'action'=>$action,   			
	    				'prefix'=>$prefix,   			
	    				'posfix'=>$posfix,   			
	    				'target'=>$target 			
	    			),
	    			'app'=>array(
	    				'url'=>Suite_globals::get('app/url'),
	    				'dir'=>Suite_globals::get('app/dir'),
	    			)	    			
				));
				// echo $domain;
				

				// echo $suite_header_json;
				// $suite_header_json = urlencode($suite_header_json);

				// 
				// session_start();
				// $_COOKIE['suite'] = $suite_header_json;
				// header("Set-Cookie: suite=".$suite_header_json."; path=/".$domain); 
				// setcookie('suite');
				setcookie('suite',$suite_header_json,0,'/'," ");
				// echo $domain;
				// setcookie("suite", $suite_header_json, 0, "/", $domain,  0);
				// setcookie("suite", $suite_header_json, 0, $domain, " ");
				;
				// $_COOKIE['suite'] = $suite_header_json;


			}
		}


	}



	/**
	 * [execute description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function execute($settings){		

		$listSettings = array(
			'debug' => create_function('$value', '
				if($value == true){				
					error_reporting(E_ALL);
					ini_set("display_errors", true);
					ini_set("display_startup_erros",true);					
				}
			'),
			'charset' => create_function('$value', '											
				header("Content-Type: text/html; charset=".$value);
			')		
		);




		foreach ($settings as $key => $value) {			
			$func = isset($listSettings[$key])?$listSettings[$key]:null;		
			if($func != null && ($key == 'charset' && !Suite_globals::get('http/argv')) )
			$func($value);
		}
		return $listSettings;
	}
	


}