<?php

class Control{
	
	function __construct(){

		

		
	}

	public function load(){
		

		// verifica se existe o controller/action existe fisicamente,
		// se houver então não continua
		$modulesMenuArray = array();
		$menuArray = Suite_components::storage('menu');  	

		

	  	if(count($menuArray)>0)
	  	foreach ($menuArray as $key => $value) {
	  		foreach ($value as $key2 => $value2) {  

	  			$modulesMenuArray[$key2] = Suite_components::storage('menu/'.$key."/".$key2);
	  		}  		
	  	}

	  	// print_r($modulesMenuArray);

	  	$rec0 = create_function('$rec0,$modulesMenuArray,$path = ""','

	  		$prefix = Suite_globals::get("http/prefix");
			$target = Suite_globals::get("http/target");
			$targetArray = explode("/", $target);	
			if($targetArray[0] == $prefix) unset($targetArray[0]);		
			$target = implode("/", $targetArray);

			

	  		foreach ($modulesMenuArray as $key => $value) {
	  			
	  			if(substr($key, 0,1)=="@")
	  				continue;
	  			
				$pathNew = $path ."/". $key;			

	  			if($pathNew == "/".$target){	  						  				
	  				return $target;  					
  					break;
  				}

	  			if(is_array($value)){
	  				return $rec0($rec0,$value,$pathNew);
	  			}else{

	  			}

	  		}

	  		return false;
	  		');

	  	$checkActionReal = $rec0($rec0,$modulesMenuArray);

	  	

	  	if($checkActionReal)
	  		return;
		



	  	// menu dinamico
		$resultArray = CompMenu::getMenuDyn();
		
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

		// $found = Suite_http::getArrayByTarget($resultArray);
    	
    	// print_r($found);

		if($found){
			$meta = array();
			$rec2 = function($rec2,$found){
				$meta = array();

				foreach ($found as $key => $value) {
					if(substr($key, 0,1)=='@')
						$keyNew = substr($key, 1);
					else
						$keyNew = $key;

					if(is_array($value)){
						// $meta[$keyNew] = $rec2($rec2,$value);
					}else{
						$meta[$keyNew] = $value;
					}
				}
				return $meta;
			};
			
			$meta = $rec2($rec2,$found);

			Suite_globals::set('app/action/meta',$meta);

			$content = isset($found['@content'])?$found['@content']:null;
			
			if($content != null){								
				Suite_view::content($content);
			}else{								
				// Suite_view::content($content);
			}



			
		}else{
			
			$html = Suite_view::content();				
			if($html === false){				
				Suite_view::content(false);	
			}
			
		}


	
	}

	public function render($html){

		
		$compMenu = new CompMenu;
		$html = $compMenu->render($html);
		
		

		return array('html'=>$html);
	}
}