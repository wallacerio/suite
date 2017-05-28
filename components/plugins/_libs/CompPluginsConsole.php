<?php

class CompPluginsConsole{

	public static function pluginsList($args,$globals){

		$appDir = Suite_globals::get('app/dir');
		$appPluginsDir = $appDir . '_plugins/';
		$dataDir = $appDir . '_data/';
		$dataPluginDir = $dataDir . 'plugins/';
		$configPlugin = $dataPluginDir . 'config.json';


		$plugin = isset($args[0])?$args[0]:null;
		$parameter = isset($args[1])?$args[1]:null;
		
		if(file_exists($configPlugin)){			
				$configPluginContentJson = file_get_contents($configPlugin);
				$configPluginContent = json_decode($configPluginContentJson,true);			
			}
			
		// exibe o plugin especifico e/ou altera seu estado
		if($plugin != null){

			
			if($parameter != null){
				// altera o estado plugin
				if(isset($configPluginContent)){
					if($configPluginContent[$plugin]){
						$configPluginContent[$plugin]['enabled'] = ($parameter=='-on')?true:false;
						$configPluginContentJson = file_put_contents($configPlugin, json_encode($configPluginContent));
					}
				}
			}else{
				// exibe informação do plugin
				if($configPluginContent[$plugin]){
					if($configPluginContent[$plugin]['enabled'] == true)
						$status = 'on~{"bold":true,"color":"green"}';
					else
						$status = 'off~{"bold":true,"color":"red"}';

					$output = '~{"width":3} '.$plugin.'~{"bold":true} - '.$status.' ';
				}
				else{
					$output = '~{"width":3} '.$plugin.'~{"bold":true} not[:space:]found~{"color":"red"}';
				}


				return array(
					'type'=>'text',
					'output'=>$output
				);
			}
		}
		


		


		
		// lista todos os plugins
		$listPluginsArray = Suite_libs::run('Files/Scan/dir',$appPluginsDir);
		$arraylist = array();
		foreach ($listPluginsArray as $key => $value) {
			
			$state = '';
			$description = $key;
			$title = $key;
			$enabled = false;


			if(isset($configPluginContent)){				
				$enabled = $configPluginContent[$key]['enabled'];
			}

			$optionFile = $appPluginsDir . $key . '/options.json';
			if(file_exists($optionFile)){
				$optionsJson = file_get_contents($optionFile);
				$optionsContent = json_decode($optionsJson,true);				
				$description = $optionsContent['description'];
				
			}	

			if($enabled == true){
				$status = 'on~{"bold":true,"color":"green"}';
			}else{
				$status = 'off~{"bold":true,"color":"red"}';
			}

			$arraylist[] = array(						
				'type'=>'button',
				'text'=>$title . '~{"bold":true} - '.$status,
				'action'=>$key.' -off',						
			);
			
		}


		return array(
			'type'=>'list',
			'output'=>$arraylist
		);
	}

	public function pluginsInstall($args = null){

		$envServer = Suite_globals::get('env/server');	
		$serverResource = $envServer;

		if(is_array($args)){
			$argsString = implode("%20", $args);
			// $argsString = implode(" ", $args);
		}
		

		$result = Suite_libs::run("Http/Request/url",array(															
								"url"=> $serverResource."plugins/install",	
								"method"=>"post",
								"data"=>array('args'=>$args)
							));

		/*$result = Suite_libs::run("Http/Request/url",array(										
			"url"=> $serverResource."plugins/install?parameters=$argsString",	
			"method"=>"post"
			
		));*/

		$resultArray = json_decode($result,true);
	
		return array(
			'type'=>$resultArray['type'],
			'output'=>$resultArray['output']
		);
	}


	public static function optionConsole(){
		$array = array(			
			'@type'=>'category',	
			'list'=>array(
				'@type'=>'command',
				// '@content'=>'test',				
				'@description'=>'List the plugins installed',
				'@function' => base64_encode('
					return CompPluginsConsole::pluginsList($args,$globals);
				')
			),				
			'install'=>array(
				'@type'=>'remote-command',
				// '@content'=>'test',				
				'@description'=>'Install new plugins',
				'@function' => base64_encode('
					return CompPluginsConsole::pluginsInstall($args,$globals);
				')
			),				
		);

		if(class_exists('SuiteSetup'))
		SuiteSetup::addOption('plugins',$array);
	}
}