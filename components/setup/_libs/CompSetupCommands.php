<?php

class CompSetupCommands{

	

	
	public static function basic(){

		$basicOptions = new stdClass;
		$basicOptions->urlmode = create_function('$args', '

			$mode = isset($args[0])?$args[0]:"rewrite";
			if($mode != "rewrite" && $mode != "query"){
				$text = "~{\"width\":5} ".$mode."~{\"bold\":true} - mode invalid";
			}else{
				Suite_libs::run("Options/base",
					array(
						"urlmode"=>$mode
					)
				);

				$text = "~{\"width\":5} - urlmode changed to ".$mode."~{\"bold\":true} with success.";
			}		
			
			return $text;
		');

		$basicOptions->debug = create_function('$args', '
			$mode = isset($args[0])?$args[0]:false;
			if($mode !== "true" && $mode !== "false"){
				$text = "~{\"width\":5} ".$mode."~{\"bold\":true} - mode invalid";
			}else{
				Suite_libs::run("Options/base",
					array(
						"debug"=>$mode
					)
				);
				$text = "~{\"width\":5} - debug changed to ".$mode."~{\"bold\":true} with success.";
			}		
			
			return $text;					
		');

		$basicOptions->charset = create_function('$args', '
			$mode = isset($args[0])?$args[0]:false;
			
				Suite_libs::run("Options/base",
					array(
						"charset"=>$mode
					)
				);
				$text = "~{\"width\":5} - charset changed to ".$mode."~{\"bold\":true} with success.";
					
			
			return $text;					
		');


		$basicOptions->app = create_function('$args', '
			$mode = isset($args[0])?$args[0]:false;
			
				Suite_libs::run("Options/base",
					array(
						"app"=>$mode
					)
				);
				$text = "~{\"width\":5} - app changed to ".$mode."~{\"bold\":true} with success.";
					
			
			return $text;					
		');


		$basicOptions->access = create_function('$args', '
			
			$username = isset($args[0])?$args[0]:null;
			$password = isset($args[1])?$args[1]:null;
			
			if($username == null || $password == null){
				$text = "Insert correcty your username and passord.";
			}else{
				Suite_libs::run("Options/base",
					array(
						"access"=>array(
							"username"=> $username,
							"password"=> md5($password),
						)
					)
				);
				$text = "~{\"width\":5} - access changed to Username: ".$username."~{\"bold\":true} and Password: ".$password."~{\"bold\":true} (".md5($password).")";
						
			}

			return $text;					
		');



		$basicOptions->showoptions = create_function('$args', '			
			$resultOptions = Suite_libs::run("Options/base");
			$text = "". str_replace(" ","[:space:]",print_r($resultOptions,true))."~{\"color\":\"yellow\"}";
								
			return array(
				"type"=>"text",
				"output"=>$text
			);					

		');
		/*$basicOptions->cryptOptions = create_function('$args', '			
				$resultOptions = Suite_libs::run("Options/base");
				
				Suite_libs::run("Options/base",base64_encode(json_encode($resultOptions)));

				$resultOptionsPos = Suite_libs::run("Options/base");

				$text = print_r($resultOptionsPos,true);
								
			return $text;					
		');*/
	

		// gera o arquivo json base no diretório raiz
		$basicOptions->generateoptions = create_function('$args', '	

				if($args){



							$code = (\'

						 		Suite_libs::run("Options/base",
									array(
										"urlmode"=>"{urlmode}",
										"debug"=>{debug},
										"charset"=>"{charset}",
										"app"=>"{app}",
										"access"=>array(
											"username"=> "{username}",
											"password"=> md5("{password}"),
										)
									)
								);

								$resultOptions = Suite_libs::run("Options/base");
								

									// return $resultOptions;

						\');
	

					foreach ($args as $key => $value) {						
						$code = str_replace(\'{\'.$key.\'}\', $value, $code);
					}

					
					$codeBase64 = base64_encode($code);

					return array(
											
						"code" => $codeBase64
					);	

				}

				$appDir	= Suite_globals::get("base/dir")."app/";
				$appDirList = Suite_libs::run("Files/Scan/onlyDir",$appDir);				
				$defaultFirst = $appDirList[0];
				
				$inputs["urlmode"] = array(
					"type"=>"text",
					"title"=>"Insert urlmode (rewrite/query) [rewrite]:~{\"bold\":true}",
					"value"=>"rewrite",
					"validate"=>"rewrite|query"
				);

				$inputs["debug"] = array(
					"type"=>"text",
					"title"=>"Insert debug mode (true/false) [true]:~{\"bold\":true}",
					"value"=>"true",
					"validate"=>"true|false"
				);

				$inputs["charset"] = array(
					"type"=>"text",
					"title"=>"Insert charset encode (utf-8/iso-8859-1) [utf-8]:~{\"bold\":true}",
					"value"=>"utf-8"
				);

				$inputs["app"] = array(
					"type"=>"text",
					"title"=>"Insert app name [".$defaultFirst."]:~{\"bold\":true}",
					"value"=>$defaultFirst
				);

				$inputs["username"] = array(
					"type"=>"text",
					"title"=>"Insert username [admin]:~{\"bold\":true}",
					"value"=>"admin"
				);

				$inputs["password"] = array(
					"type"=>"text",
					"title"=>"Insert password [suite]:~{\"bold\":true}",
					"value"=>"suite"
				);


			 	

			

			return array(
				
				"inputs" => $inputs				
			);						
		');


		$basicOptions->generatehtaccess = create_function('$args', '	

			$currentDir = Suite_globals::get("current/dir");

			$otimization = isset($args[0])?$args[0]:null;

			if(!$otimization){
				$htaccessSourceFile = $currentDir."_files/htaccess";
				$text = "    For generate with optimization use -o as arguments.";
			}else{

				

				if($otimization == "-o"){
					$htaccessSourceFile = $currentDir."_files/htaccess-optimized";
					$text = "~{\"width\":5} - htaccess optimized~{\"bold\":true,\"color\":\"yellow\"} generate with success.";
				}else{
					$htaccessSourceFile = $currentDir."_files/htaccess";
					$text = "~{\"width\":5} - htaccess generate with success.";
				}

						
			}


			$baseDir = Suite_globals::get("base/dir");
				$optionBaseFile = $baseDir.".htaccess";

			

				$data = file_get_contents($htaccessSourceFile);

				$dataFinal = "# Generate by Suite at ".Date("Y/m/d h:i:s")." ===============================\n\n" . $data;

				
				file_put_contents($optionBaseFile, $dataFinal);


						
			return array(
				"type"=>"text",
				"output"=>$text
			);					

		');
		

		$basicOptions->webconfig = create_function('$args', '	

			$currentDir = Suite_globals::get("current/dir");

			
				$htaccessSourceFile = $currentDir."_files/web.config";
				$text = "    For generate with optimization use -o as arguments.";
			


			$baseDir = Suite_globals::get("base/dir");
				$optionBaseFile = $baseDir."web.config";

			

				$data = file_get_contents($htaccessSourceFile);

				$dataFinal = $data . "\n<!-- Generate by Suite at ".Date("Y/m/d h:i:s")." =============================== -->" ;
				

				
				file_put_contents($optionBaseFile, $dataFinal);


						
			return array(
				"type"=>"text",
				"output"=>$text
			);					

		');




		// $basicOptions->urlmode = new CompSetupCommands;

		/*$basicOptions = (object) array(
			 "urlmode"=>function($args){
				return "123-".$args;
			}
		);*/

		// print_r($basicOptions);

		return $basicOptions;

	}



	public static function author(){
		$text = 'The Suite\sFramework~{"bold":true} has developed by Wallace Rio <wallrio@gmail.com> @ 2012-'.Date('Y');		
		$array = array(						
			'type'=>'text',									
			'output'=>$text									
		);
		return ($array);		
	}

	public static function core_path(){				
		$array = array(						
			'type'=>'text',									
			'output'=>suite_path									
		);
		return ($array);	
	}


	public static function version(){				
		$array = array(	
			'type'=>'text',						
			'output'=>suite_version								
		);
		return ($array);
	}


	public static function permission(){	
		$command = base64_encode('
			// use: $args e $globals

			$handle = popen("find . -type d -exec chmod 755 {} \;", "r");
			pclose($handle);
			$handle = popen("find . -type f -exec chmod 655 {} \;", "r");
			pclose($handle);

			return array(
				"type"=>"text",
				"output"=>"   Permission[:space:]changed!~{\"color\":\"green\"}"
			);

		');
			
		$array = array(			
			'type'=>'command',	
			'output'=> $command			
			
		);
		return ($array);
	}
	
	public static function copyConsole(){	

		$command = base64_encode('

			$currentDir = Suite_globals::get("current/dir");

			$otimization = isset($args[0])?$args[0]:null;

			
			$text = "    For generate with optimization use -o as arguments.";
			


			$baseDir = Suite_globals::get("base/dir");
			$optionBaseFile = $baseDir."console2";
			

			$dataFinal = \'#!/usr/bin/env php
<?php
	
/*
 * Suite Framework
 * ================
 * suite.wallrio.com
 *
 * This file is part of the Suite Core.
 *
 * Wallace Rio <wallacerio@wallrio.com>
 * 
 */

	
	if (version_compare(phpversion(), "5.3.0", "<""))
		require dirname(__FILE__)."/libs/wallrio/suite/Suite.php";
	else
		require "vendor/autoload.php";

	if(!Suite::component("setup","checkexist")){
		echo "\n\n"."Component \"setup\" not found"."\n\n";
		exit;
	}

	$result = Suite::component("setup",array(
		"exclude" => "plugins,modules"
	))->console("");

	echo $result;

\';
				


		

				file_put_contents($optionBaseFile, $dataFinal);


						
			return array(
				"type"=>"text",
				"output"=>$text
			);					

		');


		/*$command = base64_encode('
			// use: $args e $globals

			$handle = popen("find . -type d -exec chmod 755 {} \;", "r");
			pclose($handle);
			$handle = popen("find . -type f -exec chmod 655 {} \;", "r");
			pclose($handle);

			return array(
				"type"=>"text",
				"output"=>"   Permission[:space:]changed!~{\"color\":\"green\"}"
			);

		');*/
			
		$array = array(			
			'type'=>'command',	
			'output'=> $command			
			
		);
		return ($array);
	}





	/**
	 * habilita/desabilita um componente
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public static function componentsListEnabledDisabled($args = null){
		if($args== null) return '';
		$return = '';
		$noSecondPar = false;
		$componentFound = false;
		$list = Suite_globals::get('components/list');
		$nameComponent = isset($args[0])?$args[0]:null;
		$optionForComponent = isset($args[1])?$args[1]:null;



		if($optionForComponent == '-on'){
			
			foreach ($list as $key => $value) {
				$name = isset($value['name'])?$value['name']:null;
				$dir = isset($value['dir'])?$value['dir']:null;
				$options = isset($value['options'])?$value['options']:null;
				
				if($key == $nameComponent){
					$nameCurrent = $key;
					$options['enabled'] = true;
					$optionsFile = $dir.'options.json';					
					
					file_put_contents($optionsFile, json_encode($options));					
				}
			}		


		}else if($optionForComponent == '-off'){
				
				foreach ($list as $key => $value) {
					$name = isset($value['name'])?$value['name']:null;
					$dir = isset($value['dir'])?$value['dir']:null;
					$options = isset($value['options'])?$value['options']:null;
					if($key == $nameComponent){
						$nameCurrent = $key;
						$options['enabled'] = false;
						$optionsFile = $dir.'options.json';
						file_put_contents($optionsFile, json_encode($options));					
					}
				}		
			
		
		}

		Suite_components::getComponentsList();

		return $return;
	}






	/**
	 * lista os componentes instalados, mostrando se estão habilitados ou não
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public static function componentsList($args = null){
		
		$arraylist = Array();	
						
		$returnActionString = self::componentsListEnabledDisabled($args);
		$list = Suite_globals::get('components/list');		
		unset($list['setup']);
		
		if(count($list)>0){
						
			foreach ($list as $key => $value) {				
				$enabled = isset($value['options']['enabled'])?$value['options']['enabled']:false;				
				$enabledString = ($enabled == true)?'on ':'off';

				if($enabled == true){					
					$arraylist[] = array(						
						'type'=>'button',
						'text'=>''.$key.'~{"color":"white","width":20}  on~{"color":"green","bold":true} ',
						'action'=>$key.' -off 1',						
					);
				}else{					
					$arraylist[] = array(
						'type'=>'button',
						'text'=>''.$key.'~{"color":"white","width":20}  off~{"color":"red","bold":true} ',
						'action'=>$key.' -on ',						
					);			
				}							
			}
		}
		
		$array = array(
			'type'=>'list',
			'output'=>$arraylist
		);
		return ($array);
	}




	
	
}