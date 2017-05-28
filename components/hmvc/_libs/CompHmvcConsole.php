<?php

class CompHmvcConsole{

	public static $control_content = "[?php \n\n// - generate by console\n\nclass Control{\n\n\tpublic function indexAction(){\n\t\treturn array(); \n\t} \n}";
	public static $model_content = "[?php \n\n// - generate by console\n\nclass Model{\n\n\t \n}";
	public static $view_content = "<!-- generate by console -->";

	public static function listOptions(){
		$basicOptions = new stdClass;
		$basicOptions->generateStruct = create_function('$args', '
				
				$nameApp = isset($args[0])?$args[0]:null;
				$pathController = isset($args[1])?$args[1]:null;

				if($nameApp == null)
					return;
			
				$baseDir = Suite_globals::get("base/dir");
				$appDir = $baseDir ."app/" ."$nameApp"."/";
		
				
						
						$structure["_assets/"]="";
						$structure["_data/"]="";						
						$structure["_error/"]="";
						$structure["_html/"]="";
						$structure["_hmvc/"]="";


						if($pathController != "")
						$structure["_hmvc/"] =array(										
										"$pathController/"=>array(
											"Control.php"=>"'.CompHmvcConsole::$control_content.'",
											"Model.php"=>"'.CompHmvcConsole::$model_content.'",
											"view.php"=>"'.CompHmvcConsole::$view_content.'"
										)																	
								);

						Suite_libs::run("Files/Scan/mkstructure",
							array(
								"dir"=>$appDir,
								"structure"=>$structure
							)
						);

				$text = "\tApplication created ".$nameApp."~{\"bold\":true} ";
				if($pathController)
					$text .= "and Controller ".$pathController."~{\"bold\":true} ";
				
				return array(
					"type"=>"text",
					"output"=>$text
				);		
							
		');

		return $basicOptions;
	}

	public static function opDefault(){

		$func_option_urlmode ='
			$text = "";
			if(!$args){
				$text = "\t Insert the application name and optionally controller name"
						."\n\n"
						."\t Example: ./console hmvc/generate-struct APPLICATION_NAME CONTROLLER_NAME"
						."\n";
				
			}else{
				$basic = CompHmvcConsole::listOptions();				
				$method = $basic->generateStruct;
				$methodArray = $method($args);
				$text = $methodArray["output"];
		
				
			}

			$array = array(						
				"type"=>"text",
				"output"=>$text
				
					
				
			);
			return ($array);					
		';



		$array = array(			
			'@type'=>'category',	
			'generate-struct'=>array(
				'@type'=>'command',
				// '@content'=>'test',
				"@preinput"=>"Insert the application name",
				'@description'=>'Generate the struct of directories on your application',
				'@function' => base64_encode($func_option_urlmode)
			),				
		);

		if(class_exists('SuiteSetup'))
		SuiteSetup::addOption('hmvc',$array);

		
	}
}