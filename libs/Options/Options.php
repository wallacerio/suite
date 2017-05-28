<?php

class Options__Options{

	public static function base($options = null){

		$baseDir = Suite_globals::get('base/dir');
		$optionBaseFile = $baseDir.'options.json';

		if(file_exists($optionBaseFile)){
			$optionsBaseJson = file_get_contents($optionBaseFile);			
			$optionsBase = json_decode($optionsBaseJson,true);						
			
		}

		if($options == null){
						
			if(file_exists($optionBaseFile)){
				return $optionsBase;
			}

			return false;
		
		}else{			
			if(isset($optionsBase) && $options != null){				
				
				if(is_array($options)){
					$optionsBase = array_merge($optionsBase,$options);
					file_put_contents($optionBaseFile, json_encode($optionsBase));			
				}else{
					file_put_contents($optionBaseFile, $options);			
				}
			}else{
				if(is_array($options))
					file_put_contents($optionBaseFile, json_encode($options));
				else
					file_put_contents($optionBaseFile, $options);	
			}
		}

	}

}