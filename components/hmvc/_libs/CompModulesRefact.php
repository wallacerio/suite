<?php

class CompModulesRefact{
	
	public function makeMenu($hmvcNameFolder){		
		$appDir = Suite_globals::get('app/dir');
		$modulesDir = $appDir . $hmvcNameFolder.DIRECTORY_SEPARATOR;

		$condition = create_function('$value','
			if(substr($value, 0,1)=="_")
				return false;
			else
				return true;
		');

		$dirListArray = Suite_libs::run('Files/Scan/onlyDirRecursive',$modulesDir,$condition);
		
		
		// adiciona no array opções do controller
		$rec = create_function('$rec,$hmvcNameFolder,$dirListArray,$path = ""','
			$appDir = Suite_globals::get("app/dir");
  			
  			

			$newArray = array();
			if(count($dirListArray)>0)
			foreach ($dirListArray as $key => $value) {
				$menuArray2 = array();
				
				$newKey = $path.DIRECTORY_SEPARATOR.$key;
				$modulesDir = $appDir .$hmvcNameFolder.DIRECTORY_SEPARATOR;
				$ControllerDir = $modulesDir . $newKey.DIRECTORY_SEPARATOR;
				$modulesDir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $modulesDir);
				$optionsFilename = $ControllerDir."options.json";
				

				if(substr($newKey, 0,1)=="/") $newKey = substr($newKey, 1);
								
				if(file_exists($optionsFilename)){

		  			$optionsJson = file_get_contents($optionsFilename);
		  			$options = json_decode($optionsJson,true);
		  			$priority = isset($options["priority"])?$options["priority"]:100;
		  			$menuArray = isset($options["menu"])?$options["menu"]:null;
		  			if($menuArray != null)
		  			foreach ($menuArray as $keyM => $valueM) {
		  				$menuArray2["@".$keyM] = $valueM;
		  			}

		  		}

				if(is_array($value)){				
					$newArray[$newKey] = $rec($rec,$hmvcNameFolder,$value,$newKey);
					$newArray[$newKey] = array_merge($newArray[$newKey],$menuArray2);
				}else{
					$newArray[$newKey] = $menuArray2;
				}
			}
			return $newArray;
		');




		$array = $rec($rec,$hmvcNameFolder,$dirListArray);
		

	
		Suite_components::storage('menu/hmvc',$array);
		
		
	}


	public function render($html = null){		
		
		$html = $this->view($html);
		
		return $html;		
	}




	public function view($html = null){
		$html = str_replace('[suite:view]', Suite_view::view(), $html);
		return $html;
	}

	
}