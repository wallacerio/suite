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



class Suite_components{

	private static $componentDirName = 'components';
	private $componentsLoaded = Array();

	function __construct(){
		
	}


	public static function storage($path = null,$value = null){		
		if($value == null)
			return Suite_globals::get('storage/component/'.$path);

		Suite_globals::set('storage/component/'.$path,$value);
	}

	/**
	 * inializa uma classe diretamente pelo caminho
	 * @param  [type] $pathMethod [caminho relativo como base o diretório components]
	 * @return [type]             [description]
	 */
	public static function callDirectClass($pathMethod = null){		

		$coreDir = Suite_globals::get('core/dir');
		$coreUrl = Suite_globals::get('core/url');

		$path = 'components/'.$pathMethod;
		$pathArray = explode('/', $path);
		$method = end($pathArray);		
		$class = $pathArray[count($pathArray)-1];
		
		$pathClass = $coreDir. implode('/', $pathArray) . '.php';
		
		$pathDir = $coreDir . dirname(implode('/', $pathArray)).'/';
		$pathUrl = $coreUrl . dirname(implode('/', $pathArray)).'/';
	
		if(!class_exists($class))
		require $pathClass;
		
		$object = new $class();
		return $object;
	
	}

	/**
	 * [load description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function load($options = null){		
		$return = $this->searchComponents($options);		

		$resultComponent = self::directAction();
		if($resultComponent != null){					
			
			$resultOut = Suite_view::out($resultComponent);			

			
			return $resultOut;	
		}


		return $return;
	}

	/**
	 * [getComponentsList description]
	 * @return [type] [description]
	 */
	public static function getComponentsList(){

		$compsAll = array();

		$resultListAll = Array();
		$html = '';

		$coreDir = Suite_globals::get('core/dir');
		$coreUrl = Suite_globals::get('core/url');

		$dir = $coreDir . self::$componentDirName .DIRECTORY_SEPARATOR;		
		$url = $coreUrl . self::$componentDirName .'/';		

		$result = Suite_libs::run('Files/Scan/onlyDir',$dir);		
		$indexAll = 0;
		if(count($result)>0)
		foreach ($result as $key => $value) {

			if(substr($value, 0,1)=="_")
				continue;

			$priority = 100;
			$enabled = false;
			$optionsObj = null;

			$optionsDir = $dir.$value.DIRECTORY_SEPARATOR.'options.json';		
		
			if(file_exists($optionsDir)){
				$optionsJson = file_get_contents($optionsDir);
				$optionsObj = json_decode($optionsJson,true);				
				$priority = isset($optionsObj['priority'])?$optionsObj['priority']:100;
				$enabled = isset($optionsObj['enabled'])?$optionsObj['enabled']:true;
				$name = isset($optionsObj['name'])?$optionsObj['name']:$value;
			}


			$compsAll[$value]['name'] = isset($name)?$name:$key;
			if($optionsObj != null)
			$compsAll[$value]['options'] = $optionsObj;
			$compsAll[$value]['dir'] = $dir.$value.DIRECTORY_SEPARATOR;
			$compsAll[$value]['url'] = $url.$value.DIRECTORY_SEPARATOR;

			$indexAll++;

		}
		if(count($compsAll)>0)
		foreach ($compsAll as $key => $value) {
        	$name = $value['name'];
			$dir = $value['dir'];
			$url = $value['url'];
			$options = isset($value['options'])?$value['options']:null;	
			$resultListAll[$key] = $value;
        }
        

        Suite_globals::set('components/list',$resultListAll);

	}

	/**
	 * [searchComponents description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public function searchComponents($options = null){

		$excludeComponents = isset($options['exclude'])?$options['exclude']:null;
		$excludeComponentsArray = explode(',', $excludeComponents);
	
			
		$args = $_REQUEST;

		$resultLoadAll = Array();
		$resultListAll = Array();
		$html = '';

		$coreDir = Suite_globals::get('core/dir');
		$coreUrl = Suite_globals::get('core/url');

		$dir = $coreDir . self::$componentDirName .DIRECTORY_SEPARATOR;		
		$url = $coreUrl . self::$componentDirName .'/';		

		$result = Suite_libs::run('Files/Scan/onlyDir',$dir);
		$index = 0;
		$indexAll = 0;
		if(count($result)>0)
		foreach ($result as $key => $value) {

			if(substr($value, 0,1)=="_")
				continue;
			
			

			$priority = 100;
			$enabled = false;
			$optionsObj = null;

			$optionsDir = $dir.$value.DIRECTORY_SEPARATOR.'options.json';		
		
			if(file_exists($optionsDir)){
				$optionsJson = file_get_contents($optionsDir);
				$optionsObj = json_decode($optionsJson,true);				
				$priority = isset($optionsObj['priority'])?$optionsObj['priority']:100;
				$enabled = isset($optionsObj['enabled'])?$optionsObj['enabled']:true;
			}

			$compsAll[$indexAll]['name'] = $value;
			if($optionsObj != null)
			$compsAll[$indexAll]['options'] = $optionsObj;
			$compsAll[$indexAll]['dir'] = $dir.$value.DIRECTORY_SEPARATOR;
			$compsAll[$indexAll]['url'] = $url.$value.DIRECTORY_SEPARATOR;

			$indexAll++;

			if($enabled == false)
				continue;


			$comps[$index]['name'] = $value;
			if($optionsObj != null)
			$comps[$index]['options'] = $optionsObj;
			$comps[$index]['dir'] = $dir.$value.DIRECTORY_SEPARATOR;
			$comps[$index]['url'] = $url.$value.DIRECTORY_SEPARATOR;


			$index++;
		}

		// ordena por prioridade no options
		if( isset($comps) && count($comps)>0){
	        $price = array();
	        foreach ($comps as $key => $row){        
	            $price[$key] = isset($row['options']['priority'])?$row['options']['priority']:100;
	        }
	        array_multisort($price, SORT_ASC, $comps);
	    }


        self::getComponentsList();


        $index = 0;
        if( isset($comps) && count($comps)>0)
		foreach ($comps as $key => $value) {
			$name = $value['name'];

			

			$dir = $value['dir'];
			$url = $value['url'];
			$options = isset($value['options'])?$value['options']:null;

			$componentControl = $this->loadComponents($name);
			$this->componentsLoaded[$index]['objectClass'] = $componentControl;
			$this->componentsLoaded[$index]['options'] = $options;
			$this->componentsLoaded[$index]['dir'] = $dir;
			$this->componentsLoaded[$index]['url'] = $url;

			
			// set informations current errors			
			if(class_exists('Suite_error'))
			Suite_error::setContext(array(
				"components"=>array(
					"name"=>$name,
					"path"=>$dir,
				)
			));
			
			if(in_array($name,$excludeComponentsArray)) continue;

			if(method_exists($componentControl, 'load')){
				
				Suite_globals::set('current/dir',$dir);
				Suite_globals::set('current/url',$url);

				
				Suite_globals::set('components/action/'.$name,$componentControl);


				$returnAction = $componentControl->load($args);

				
				if(count($returnAction)>0){

					$resultLoadAll[] = $returnAction;

					
					foreach ($returnAction as $key => $value) {		
						if($key === 'response'){
							$htmlPre = isset($value)?$value:$html;						
							if($htmlPre != "" && $htmlPre != null)
							$html = $htmlPre;
						}

						if(is_array($value) && count($value)){
							foreach ($value as $key2 => $value2) {			
								
								if($key2 === 'response'){

									$htmlPre = isset($value2)?$value2:$html;						

									if($htmlPre != "" && $htmlPre != null)
									$html = $htmlPre;
								}
							}
						}
					}	

					
				}
			}

			$index++;
		}

		Suite_globals::set('components/load',$resultLoadAll);

		
		return $html;
	}

	/**
	 * [loadComponents description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function loadComponents($name = null){
		$coreDir = Suite_globals::get('core/dir');
		$coreUrl = Suite_globals::get('core/url');

		$objectComponent = Suite_class::load($coreDir.self::$componentDirName.DIRECTORY_SEPARATOR.$name);
		
		$control = $objectComponent['control'];		
		return $control;
	}

	
	/**
	 * [render description]
	 * @param  [type] $html [description]
	 * @return [type]       [description]
	 */
	public function render($html = null){	

		$args = $_REQUEST;

		$lastControl = Array();
	
		foreach ($this->componentsLoaded as $key => $value) {

			$objectClass = $value['objectClass'];
			$options = isset($value['options'])?$value['options']:null;


			$lastRun = false;
			if(method_exists($objectClass, 'render')){

				
				if(isset($options['last']) &&  $options['last'] == true){
					$lastControl[] = $objectClass;	
					continue;
				}
			
				$htmlPre = $objectClass->render($html,$args);
				
				if($htmlPre == null) continue;

				

				if(is_array($htmlPre)){
					$lastRun = isset($htmlPre['last'])?$htmlPre['last']:$lastRun;
					if($lastRun == true){

						$lastControl[] = $objectClass;					
					}else{
						$html = isset($htmlPre['html'])?$htmlPre['html']:$html;	
					}
				}else{
					$html = $htmlPre;
				}


				
			}

		}

		
		if(isset($lastControl) ){			
			foreach ($lastControl as $key => $value) {

				
				$htmlPre = $value->render($html);
				
				
				if($htmlPre == null) continue;
				$html = isset($htmlPre['html'])?$htmlPre['html']:$html;

				

			}
		
		}
		

		return $html;
	}









	// public static function directAction($dir = null,$argsForce = null){
	/**
	 * [directAction description]
	 * @return [type] [description]
	 */
	public static function directAction(){
		$coreDir = Suite_globals::get('core/dir');
		$componentDir = $coreDir . 'components/';

		

		
		$action = Suite_globals::get('http/action');
		$prefix = Suite_globals::get('http/prefix');
		$posfix = Suite_globals::get('http/posfix');
		$target = Suite_globals::get('http/target');

		
		$args = $_REQUEST;

		$prefixArray = explode('/', $target);
		$prefixArray = array_filter($prefixArray);
		$prefixArray = array_values($prefixArray);
		
	
		$firstPrefix = isset($prefixArray[0])?$prefixArray[0]:null;
	
		$lastPrefix = end($prefixArray);

		if($firstPrefix == null) return null;
		
		

		$lastPrefix = $prefixArray[count($prefixArray)-1];
		unset($prefixArray[0]);
		$prefixArray = array_filter($prefixArray);
		unset($prefixArray[count($prefixArray)]);

		$componentActions = implode('/', $prefixArray);


		
			 

		if($firstPrefix == '_component'){

			// acesso 1
			$componentActionsDir = $componentDir .$componentActions . DIRECTORY_SEPARATOR;								
			$resultAction = Suite_class::load($componentActionsDir,'component','component');			
			$control = $resultAction['control'];
			$actionName = $lastPrefix.'Action';
		
			if(method_exists($control, $actionName)){				
				$resultComponent = $control->$actionName($args);
				return $resultComponent;				
			}else{				
				$indexName = 'indexAction';
				if(method_exists($control, $indexName)){
					return $control->$indexName($args);
				}else{

					
					// return null;
				}
			}


			// acesso 2
			$componentActionsDir = $componentDir .$componentActions . DIRECTORY_SEPARATOR . $lastPrefix . DIRECTORY_SEPARATOR ;			
			$resultAction = Suite_class::load($componentActionsDir,'component','component');			
			$control = $resultAction['control'];
			$actionName = $lastPrefix.'Action';
		
			if(method_exists($control, $actionName)){				
				$resultComponent = $control->$actionName($args);
				return $resultComponent;				
			}else{			
				$indexName = 'indexAction';
				if(method_exists($control, $indexName)){

					return $control->$indexName($args);
				}else{
					return null;
				}
			}


		}
	}


	


}