<?php

class CompConsoleOptionsBasics{


	/**
	 * opções introdutórias
	 * @return [type] [description]
	 */
	public static function opDefault(){
		$array = array(			
			'@type'=>'category',	
			'author'=>array(
				'@type'=>'command',
				// '@content'=>'test',
				'@description'=>'Show information about author of Suite Framework',
				'@function' => base64_encode('return CompSetupCommands::author();')
			),
			'core_path'=>array(
				'@type'=>'command',
				// '@content'=>'test',
				'@description'=>'Show path of Suite core directory',
				'@function' => base64_encode('return CompSetupCommands::core_path();')
			),
			'version'=>array(
				'@type'=>'command',				
				'@description'=>'Show version of Suite',
				'@function' => base64_encode('return CompSetupCommands::version();')
			),
			'permission'=>array(
				'@type'=>'command',				
				'@description'=>'Set correct permissions',
				'@function' => base64_encode('return CompSetupCommands::permission();')
			),
			'copy-console'=>array(
				'@type'=>'command',				
				'@description'=>'Generate console on directory project',
				'@function' => base64_encode('return CompSetupCommands::copyConsole();')
			)			
		);

		SuiteSetup::addOption(null,$array);

		
	}




	/**
	 * opções relacionadas a componentes
	 * @return [type] [description]
	 */
	public static function opComponents(){

		$envServer = Suite_globals::get('env/server');	
		$serverResource = $envServer;
		
		$func ='
				$componentsInstalled = Suite_globals::get("components/list");
				return json_encode(array("components-installed"=>json_encode($componentsInstalled)));
			';
		
				// echo $serverResource;
		$array = array(
			'@type'=>'category',
			// '@description'=>'Manager of components',
			'install'=>array(
				'@type'=>'remote-command',
				'@description'=>'Install new components',
				'@function' =>base64_encode('			
						$argsString = "";
						if(is_array($args)){
							// $argsString = implode("%20", $args);
							$argsString = implode(" ", $args);
						}
					
						$result = Suite_libs::run("Http/Request/url",array(							
							// "url"=>"http://localhost/lp/gl/suite/skeleton/_component/setup/console?command=components/list&parameters=$argsString",														
							"url"=>"'.$serverResource.'components/install?parameters=$argsString",	
							"method"=>"post"
							
						));

					
						$resultArray = json_decode($result,true);						
						$reRequest = isset($resultArray["request"])?$resultArray["request"]:null;
						$backparameters = isset($resultArray["backparameters"])?$resultArray["backparameters"]:null;
						$functionEncoded = isset($resultArray["function"])?$resultArray["function"]:null;
						
					
						// caso seja solicitado uma outra requisição
						if($reRequest!=null){

							$reData = Array();

							if($backparameters != null)
								$reData = $backparameters;							
							
							if($functionEncoded != null){
								$functionDecoded = base64_decode($functionEncoded);							
								$Function = create_function(\'$args,$globals\', $functionDecoded);
								$globals = json_encode(Suite_globals::get());
								$resultFuncReq = $Function($args,$globals);
								$reData = array_merge($reData,$resultFuncReq);
							}
							
							
							$reData["args"] = $args;
								


							$result = Suite_libs::run("Http/Request/url",array(															
								"url"=>$reRequest,	
								"method"=>"post",
								"data"=>$reData
							));
						
						
							return json_decode($result);

							
						}
				
						

						$resultObj = json_decode($result,"true");
						if($resultObj["status"]=="success"){
							$data = $resultObj["data"];						
							return $data;
						}

						return null;						
				'),				
				
			),
			'list'=>array(
				'@type'=>'command',
				'@description'=>'List components local',				
				'@function' =>base64_encode('								
						$result = CompSetupCommands::componentsList($args);
						return $result;						
				'),
				
			),
			
		);

		SuiteSetup::addOption('components',$array);
	}



	/**
	 * opções básicas, relacionadas ao option base
	 * @return [type] [description]
	 */
	public static function opBasics(){

		$envServer = Suite_globals::get('env/server');	
		$serverResource = $envServer;
		
		$func_option_urlmode ='
			$text = "";
			if(!$args){
				$text = "    Pass the arguments: rewrite~{\"bold\":true} or query~{\"bold\":true}";
			}else{				
				$basic = CompSetupCommands::basic();				
				$method = $basic->urlmode;
				$text = $method($args);
			}

			
			$inputs["urlmode"] = array(
					"type"=>"text",
					"title"=>"Insert urlmode (rewrite/query)",
					"value"=>"",
					"validate"=>"rewrite|query"
				);

			$array = array(						
				"type"=>"text",									
				"output"=>$text,
				"inputs"=>$inputs							
			);
			return ($array);	

		';

		$func_option_debug ='
			$text = "";
			if(!$args){
				$text = "    Pass the arguments: true~{\"bold\":true} or false~{\"bold\":true}";
			}else{
				$basic = CompSetupCommands::basic();				
				$method = $basic->debug;
				$text = $method($args);
			}

			$inputs["debug"] = array(
					"type"=>"text",
					"title"=>"Insert debug mode (true/false)",
					"value"=>"",
					"validate"=>"true|false"
				);

			$array = array(						
				"type"=>"text",											
				"output"=>$text,
				"inputs"=>$inputs
			);

			return ($array);	
		';

		$func_option_charset ='
			$text = "";
			if(!$args){
				$text = "   Pass the arguments the encode character.<br><br>";
				$text .= "\tExample 1: utf-8~{\"bold\":true} <br>";
				$text .= "\tExample 2: iso-8859-1~{\"bold\":true}";
			}else{
				$basic = CompSetupCommands::basic();				
				$method = $basic->charset;
				$text = $method($args);
			}

			$inputs["charset"] = array(
					"type"=>"text",
					"title"=>"Insert charset encode (utf-8/iso-8859-1)",
					"value"=>""
				);


			$array = array(						
				"type"=>"text",
				"output"=>$text,
				"inputs"=>$inputs
			);
			return ($array);	
		';

		$func_option_app ='
			$text = "";
			if(!$args){
				$text = "    Insert the name of application to define active.";		
			}else{
				$basic = CompSetupCommands::basic();				
				$method = $basic->app;
				$text = $method($args);
			}

			$inputs["app"] = array(
					"type"=>"text",
					"title"=>"Insert app name",
					"value"=>""
				);


			$array = array(						
				"type"=>"text",
				"output"=>$text,
				"inputs"=>$inputs
			);

			return ($array);	
		';

		$func_option_access ='
			$text = "";
			if(!$args){
				$text = "    Insert the username and password.<br><br>";
				$text .= "\tExample 1: php console basic/access [username]~{\"bold\":true} [password]~{\"bold\":true} <br>";
				$text .= "\tExample 2: php console basic/access admin~{\"bold\":true} a9d5m3i1n~{\"bold\":true} <br>";
				
			}else{
				$basic = CompSetupCommands::basic();				
				$method = $basic->access;
				$text = $method($args);
			}
			
			$inputs["username"] = array(
					"type"=>"text",
					"title"=>"Insert username",
					"value"=>"admin"
				);
			$inputs["password"] = array(
					"type"=>"text",
					"title"=>"Insert password",
					"value"=>"suite"
				);

			$array = array(						
				"type"=>"text",									
				"output"=>$text,
				"inputs"=>$inputs							
			);
			return ($array);	
		';

		$func_option_showoptions ='
			$text = "";

			$basic = CompSetupCommands::basic();				
				$method = $basic->showoptions;
				$array = $method($args);
			
			return ($array);	
		';

		/*$func_option_crypt ='
			if(!$args){
				$text = "Encryption the options file.<br>";
				$text = "Insert as parameters a key to encryption.<br><br>";
				$text .= "\tExample 1: php console basic/crypt-options mykey::{\"bold\":true} <br>";
				$text .= "\tExample 2: php console basic/crypt-options abc123::{\"bold\":true} <br>";
				$text .= "\tExample 3: php console basic/crypt-options @35dAfgg::{\"bold\":true} <br>";
				
				
				
				
			}else{
				$basic = CompSetupCommands::basic();				
				$method = $basic->cryptOptions;
				$text = $method($args);
			}
			$array[] = array(						
				"string"=>$text									
			);
			return json_encode($array);	
		';
*/

		$func_option_generateoptions ='
			$text = "";
			if(!$args){
				$basic = CompSetupCommands::basic();				
				$method = $basic->generateoptions;
				$methodArray = $method($args);	
				
				$inputs = isset($methodArray["inputs"])?$methodArray["inputs"]:null;
			
				$array = array(						
					"type"=>"inputs",									
					"output"=>$inputs		
				);
			}else{		

				 if($args[0]=="-return"){					
				 	$argsVal = isset($args[1])?$args[1]:null;
				 		
					$argsArray = json_decode($argsVal,true);					
					
					$basic = CompSetupCommands::basic();				
					$method = $basic->generateoptions;
					$methodArray = $method($argsArray);	

					$code = isset($methodArray["code"])?$methodArray["code"]:null;


					$array = array(						
						"type"=>"code",									
						"output"=>$code		
					);

					CompAnalize::request($array,$args);

					return array(						
						"type"=>"text",									
						"output"=>" - SUCCESS:~{\"bold\":true,\"color\":\"green\"} Code runned with success"		
					);
				}else{

					$array = array(						
						"type"=>"code",									
						"output"=>base64_encode($args),					
					);
				}
			}
				
				

			
						

			return ($array);	
		';


		$func_generate_htaccess ='
			$text = "";

			$basic = CompSetupCommands::basic();				
				$method = $basic->generatehtaccess;
				$array = $method($args);
			
			return ($array);	
		';

		$func_generate_webconfig ='
			$text = "";

			$basic = CompSetupCommands::basic();				
				$method = $basic->webconfig;
				$array = $method($args);
			
			return ($array);	
		';
		
		
		$array = array(
			'@type'=>'category',
			// '@description'=>'Manager of components',						
				"urlmode"=>array(
					'@type'=>'command',
					'@description'=>'Define mode to access to URL',					
					'@function' => base64_encode($func_option_urlmode)
				),
				
				"debug"=>array(
					'@type'=>'command',
					'@description'=>'Enable/Disable the mode for debug',					
					'@function' => base64_encode($func_option_debug)
				),
				"charset"=>array(
					'@type'=>'command',
					'@description'=>'Choose the mode to encode character',					
					'@function' => base64_encode($func_option_charset)
				),
				"app"=>array(
					'@type'=>'command',
					'@description'=>'Define the application active',					
					'@function' => base64_encode($func_option_app)
				),
				"access"=>array(
					'@type'=>'command',
					'@description'=>'Define the access root to framework setup',					
					'@function' => base64_encode($func_option_access)
				),
				"show-options"=>array(
					'@type'=>'command',
					'@description'=>'Show options basic file',					
					'@function' => base64_encode($func_option_showoptions)
				),
				"generate-htaccess"=>array(
					'@type'=>'command',
					'@description'=>'Generate htaccess file',					
					'@function' => base64_encode($func_generate_htaccess)
				),
				"generate-webconfig"=>array(
					'@type'=>'command',
					'@description'=>'Generate webconfig file',					
					'@function' => base64_encode($func_generate_webconfig)
				),
								
			
		);

		$resultOptions = Suite_libs::run("Options/base");
		

		if(!$resultOptions)
		$array = array(
			'@type'=>'category',
			"generate-options"=>array(
					'@type'=>'command',
					'@description'=>'Generate the options config file',					
					'@function' => base64_encode($func_option_generateoptions)					
				)
		);

		SuiteSetup::addOption('basic',$array);
	}


	
}