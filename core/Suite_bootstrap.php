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

	
require_once suite_path.'/core/Suite_error.php';	
require_once suite_path.'/core/Suite_libs.php';	
require_once suite_path.'/core/Suite_globals.php';		
require_once suite_path.'/core/Suite_settings.php';	
require_once suite_path.'/core/Suite_server.php';	
require_once suite_path.'/core/Suite_http.php';	
require_once suite_path.'/core/Suite_path.php';	
require_once suite_path.'/core/Suite_components.php';	
require_once suite_path.'/core/Suite_view.php';	
require_once suite_path.'/core/Suite_class.php';
require_once suite_path.'/core/Suite_session.php';
require_once suite_path.'/core/Suite_manager.php';



class Suite_bootstrap{
	
	function __construct(){
		
		$this->options();
		
		// new Suite_error();
		new Suite_http();
		new Suite_path();		
		new Suite_server();
		new Suite_settings();				
	}


	/**
	 * [component description]
	 * @param  [type] $name       [description]
	 * @param  [type] $parameters [description]
	 * @return [type]             [description]
	 */
	public function component($name = null,$parameters = null){	

		// $components = new Suite_components();	
		// $components->load($parameters);		

		$componentDir = suite_path.'/components/'.$name;
		
		Suite_components::getComponentsList();
		$componentListArray = Suite_globals::get('components/list');
		

		
		if($parameters == 'checkexist'){
			return isset($componentListArray[$name]);
		}

		foreach ($componentListArray as $key => $value) {
			$nameComponent = isset($value['name'])?$value['name']:null;

			if($nameComponent == $name){				
				$options = isset($value['options'])?$value['options']:null;
				$optionsEnabled = isset($options['enabled'])?$options['enabled']:false;				
				if($optionsEnabled == false){					
					return (object) array();
				}
			}
		}
		

		$resultLoad = Suite_class::load($componentDir);		
		return $resultLoad['control'];
	}


	/**
	 * [html description]
	 * @param  string $html    [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function html($html = '',$options = null){	
		$options['return'] = true;
		Suite_http::set($options);
		$components = new Suite_components();	
		$components->load();								
		$html = Suite_view::out($html);		
		$html = $components->render($html);						
		return Suite_view::render($html,$options);
	}

	/**
	 * [load description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function load($options = null){		
	
		Suite_http::set($options);		
		
	
		$components = new Suite_components();	
		$html = $components->load();

		
		
		// caso houver um response no action
		if(is_array($html) && isset($html['break']) && $html['break']  == true ){							
			return $html['out'];
		}





		$html = Suite_view::out($html);		

		
		// caso houver um response no action
		if(is_array($html) && isset($html['break']) && $html['break'] == true ){

			return $html['out'];
		}

		// $html = $resultOut;

		$html = $components->render($html);	
		$html = $components->render($html);	
						
		return Suite_view::render($html,$options);
		
	}

	/**
	 * [options description]
	 * @return [type] [description]
	 */
	public function options(){	
		$version = 'unknown';
		$optionsFile = suite_path.'/options.json';
		if(file_exists($optionsFile)){
			$optionsJson = file_get_contents($optionsFile);
			$options = json_decode($optionsJson,true);
			
			if(count($options)>0)
			foreach ($options as $key => $value) {
				if(!defined('suite_'.$key)){
					define('suite_'.$key, $value);			
					Suite_globals::set('env/'.$key,$value);
				}
			}
			
		}
	}
	
}