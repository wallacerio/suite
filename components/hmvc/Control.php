<?php

class Control {

	private $hmvcNameFolder = '_hmvc';

	function __construct(){
		CompHmvcConsole::opDefault();

		$compModulesRefact = new CompModulesRefact();
		$compModulesRefact->makeMenu($this->hmvcNameFolder);

	}

	public function render($html = ''){
		
		$compModulesRefact = new CompModulesRefact();
		

		$html = $compModulesRefact->render($html);
		 
		$HMVCTags = new HMVCTags();
		$html = $HMVCTags->render($html);
	
		return $html;
	}



	public function checkPrefixPosfix(){

		$appDir = Suite_globals::get('app/dir');
		$modulesDir = $appDir . $this->hmvcNameFolder.DIRECTORY_SEPARATOR;

		$action = Suite_globals::get('http/action');
		$actionArray = explode('/', $action);

		$prefix = '';
		foreach ($actionArray as $key => $value) {			
			if(file_exists($modulesDir.$value)){				
				break;
			}else{
				$prefix .= $value.'/';
			}
		}

		if(substr($prefix, strlen($prefix)-1,strlen($prefix))=='/')
			$prefix = substr($prefix, 0,strlen($prefix)-1);

		$actionNew = $action;
		$actionNew = str_replace($prefix, '', $actionNew);


		$actionNewArray = explode('/', $actionNew);
		$actionNewArray = array_filter($actionNewArray);

		
		$join = '';
		$actionReal = '';
		foreach ($actionNewArray as $key => $value) {
				
			$join .= $value.'/';

			if(file_exists($modulesDir.$join)){				
				$actionReal .= $value.'/';
				
			}
		}
		

		
		if(substr($actionReal, strlen($actionReal)-1,strlen($actionReal))=='/')
			$actionReal = substr($actionReal, 0,strlen($actionReal)-1);

		if(substr($actionReal, 0,1)=='/')
			$actionReal = substr($actionReal, 1);

		$posfix = str_replace($actionReal, '', $actionNew);

		if($actionReal == '')
			$actionReal = 'home';

		if(substr($posfix, strlen($posfix)-1,strlen($posfix))=='/')
			$posfix = substr($posfix, 0,strlen($posfix)-1);

		$posfix = str_replace('//', '/', $posfix);

		if(substr($posfix, 0,1)=='/')
			$posfix = substr($posfix, 1);

		
		if($prefix)
		Suite_globals::set('http/prefix',$prefix);
		if($posfix)
		Suite_globals::set('http/posfix',$posfix);
		if($actionReal)
		Suite_globals::set('http/action',$actionReal);

	}


	public function loadController($url = null){
		$appDir = Suite_globals::get('app/dir');
		$modulesDir = $appDir . $this->hmvcNameFolder.DIRECTORY_SEPARATOR;

		
		$this->checkPrefixPosfix();

		$prefix = Suite_globals::get('http/prefix');
		$posfix = Suite_globals::get('http/posfix');
		
		$action = Suite_globals::get('http/action');
		$target = Suite_globals::get('http/target');

		$targetArray = explode('/', $target);
		$targetLast = end($targetArray);

		if($url != null)
			$action = $url;

		// $actionArray = explode('/', $target);
		
		$resultArray = Suite_components::storage('menu/hmvc');
				
		$rec = create_function('$rec,$resultArray,&$found','
			
			
			$target = Suite_globals::get("http/target");
			$targetArray = explode("/", $target);	
			
			$newArray = array();
			$testTarget = "";
			foreach ($resultArray as $key => $value) {
				
				if(substr($key, 0,1)=="@")
					continue;

				$keyArray = explode("/", $key);
				
				foreach ($targetArray as $keyT => $valueT) {
						

					if( in_array($valueT, $keyArray)  || $testTarget != ""){							
						$testTarget .= (($testTarget=="")?"":"/").$valueT;						

						
						if($key == $testTarget){													
							$targetCurrent = substr($target, strpos($target, $key));							
							if(isset($resultArray[$targetCurrent])){
								$found = $resultArray[$targetCurrent];
								$found["@status"] = "success";
								$found["@path"] = $targetCurrent;
								return $found;															
							}
							
						}
					}								

					
				}

			
				if(is_array($value)){					
					$newArray[$key] = $rec($rec,$value,$found);
				}else{
					$newArray[$key] = $value;
				}
			}

			return $newArray;
		');

		$array = $rec($rec,$resultArray,$found);


		if($found){
			$action = $found['@path'];

		}else{

			$html = Suite_view::content();				
			if($html === false)
			Suite_view::content(false);	
			
			// return null;
		}




		$argsModule = array(
			'action'=>$action
		);
			
		$actionArray = explode('/', $action);

		$objectMaster = Suite_class::loadClass($modulesDir,'modules',array('control'=>'_Control','model'=>'_Model'));		
		$controlMaster = isset($objectMaster['control'])?$objectMaster['control']:null;
		$modelMaster = isset($objectMaster['model'])?$objectMaster['model']:null;
		$viewMaster = isset($objectMaster['view'])?$objectMaster['view']:null;

		if(method_exists($objectMaster['control'], 'control')){	
			$resultControlMaster = $controlMaster->control($argsModule);					
			

			if(class_exists('CompAssets')){				
				CompAssets::actionRegister($resultControlMaster,'_master');
			}
		}

		if(!$found){
			// return null;
		}

		$join = '';
		foreach ($actionArray as $key => $value) {
			

			$join .= $value ;
			if(substr($join, strlen($join)-1,strlen($join))!='/')
				$join .= '/' ;

			$path = $modulesDir .$join;
			$path = preg_replace('/\/\//i', '/', $path);

			$newArray[] = $path;
		}
		$newArray = array_reverse($newArray);
			
		
			
		// print_r($newArray);

		$moduleFound = false;
		foreach ($newArray as $key => $value) {
			
			$object = Suite_class::loadClass($value,'modules');
				
			if(method_exists($object['control'], 'indexAction')){						
				$moduleFound = true;				
				break;
			}

			if(method_exists($object['control'], $posfix.'Action')){						
				$moduleFound = true;				
				break;
			}
			
		}
		
		if($moduleFound == true){
			$control = isset($object['control'])?$object['control']:null;
			$model = isset($object['model'])?$object['model']:null;
			$view = isset($object['view'])?$object['view']:null;

			if(method_exists($control, $posfix.'Action')){						
				$actionName = $posfix.'Action';
				$resultAction = $control->$actionName($argsModule);						
				if(class_exists('CompAssets'))
				CompAssets::actionRegister($resultAction,'hmvc');
			}

			if(method_exists($control, 'indexAction')){						
				$resultAction = $control->indexAction($argsModule);						
				if(class_exists('CompAssets'))
				CompAssets::actionRegister($resultAction,'hmvc');
			}


		}else{
			$view = null;
			$resultAction = array('http'=>array('response'=>'404'));									
			if(class_exists('CompAssets'))
			CompAssets::actionRegister($resultAction,'error');
		}	

		// get options master
		$optionsMasterFile = $modulesDir . 'options.json';
		if(file_exists($optionsMasterFile)){
			$optionsMasterContentJson = file_get_contents($optionsMasterFile);
			$optionsMasterContent = json_decode($optionsMasterContentJson,true);
			$options_meta = isset($optionsMasterContent['meta'])?$optionsMasterContent['meta']:array();
			Suite_globals::set('app/meta',$options_meta);		
		}

		// get options of action current 
		$optionsActionFile = $modulesDir .$action. '/options.json';
	
		if(file_exists($optionsActionFile)){
			$optionsActionContentJson = file_get_contents($optionsActionFile);
			$optionsActionContent = json_decode($optionsActionContentJson,true);
			$options_action_meta = isset($optionsActionContent['meta'])?$optionsActionContent['meta']:array();
			Suite_globals::set('app/action/meta',$options_action_meta);
		}
		

		Suite_view::content($view);
		
		return $view;
	}

	public function load(){

		

		$return = $this->loadController();
			
		
		return null;
	}
}