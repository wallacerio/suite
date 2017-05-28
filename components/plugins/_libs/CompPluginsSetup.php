<?php

class CompPluginsSetup{

	public static function pluginsAct($args = null){

		if($args== null) return '';
		$return = '';
		$noSecondPar = false;
		$componentFound = false;		
		$nameComponent = isset($args[0])?$args[0]:null;
		$optionForComponent = isset($args[1])?$args[1]:null;

		

		$list = CompPlugins::getPlugins();
		$list = json_encode($list);
		$list = json_decode($list,true);
			

		// executa caso a opção do componente especifico for omitida,
		// então retorna se o componente existe ou não
		if( $optionForComponent== null){
			foreach ($list as $key => $value) {
				$name = isset($value['name'])?$value['name']:null;
				$dir = isset($value['dir'])?$value['dir']:null;
				$options = isset($value['options'])?$value['options']:null;

				

				if($name == $nameComponent){
					$componentFound = true;
				}
			}
			$return .= "";
			$return .= CompSetup::output("\t component [$nameComponent] ",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>true),true);
			if($componentFound == true){
				$return .= CompSetup::output("Found",array('breackline'=>false,'forecolor'=>'green','backcolor'=>'','bold'=>true),true);
				$return .= "\n";
			}else{				
				$return .= CompSetup::output("Not Found",array('breackline'=>false,'forecolor'=>'red','backcolor'=>'','bold'=>true),true);
				$return .= "\n";
			}
			return $return;
		}



		if($optionForComponent == '-on'){
			
			foreach ($list as $key => $value) {
				$name = isset($value['name'])?$value['name']:null;
				$dir = isset($value['dir'])?$value['dir']:null;
				$options = isset($value['options'])?$value['options']:null;
				if($name == $nameComponent){
					$nameCurrent = $name;
					$options['enabled'] = true;
					$optionsFile = $dir.'options.json';
					file_put_contents($optionsFile, json_encode($options));					
				}
			}		
			if(isset($nameCurrent)){
				$return .= CompSetup::output("\t component [".$nameCurrent."] ",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>true),true);
				$return .= CompSetup::output("enabled",array('breackline'=>false,'forecolor'=>'green','backcolor'=>'','bold'=>true),true);
				$return .= "\n";
			}else{
				$return .= CompSetup::output("\t component [".$nameComponent."] ",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>true),true);				
				$return .= CompSetup::output("Not Found",array('breackline'=>false,'forecolor'=>'red','backcolor'=>'','bold'=>true),true);
				
			}

		}else if($optionForComponent == '-off'){
				
				foreach ($list as $key => $value) {
					$name = isset($value['name'])?$value['name']:null;
					$dir = isset($value['dir'])?$value['dir']:null;
					$options = isset($value['options'])?$value['options']:null;
					if($name == $nameComponent){
						$nameCurrent = $name;
						$options['enabled'] = false;
						$optionsFile = $dir.'options.json';						
						file_put_contents($optionsFile, json_encode($options));					
					}
				}		
			

			$return .= CompSetup::output("\t component [".$nameCurrent."] ",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>true),true);
			$return .= CompSetup::output("disabled",array('breackline'=>false,'forecolor'=>'red','backcolor'=>'','bold'=>true),true);
			$return .= "\n";
		}

		Suite_components::getComponentsList();

		return $return;
	}
	
	public static function lists($args,$globals){

		if($args != null)
		$args = explode(' ', $args);

		$out = '';
		$appDir = $globals['app']['dir'];
		$appUrl = $globals['app']['url'];

		$out .= CompSetup::output("\t"."List of Plugins Installed",array('forecolor'=>'white','backcolor'=>'','bold'=>true));
		$out .= "\n\n";

		$returnActionString = self::pluginsAct($args);

		$list = CompPlugins::getPlugins();
		$list = json_encode($list);
		$list = json_decode($list,true);

		if(count($list)>0){
			
			$out .= "\t"."Showing ".count($list)." components \n\n";
		
			foreach ($list as $key => $value) {
					
					
				$name = isset($value['name'])?$value['name']:$key;
				$enabled = isset($value['options']['enabled'])?$value['options']['enabled']:false;
				
				$enabledString = ($enabled == true)?'on ':'off';

				if($enabled == true){
					$out .= CompSetup::output("\t"."ON  ",array('breackline'=>false,'forecolor'=>'green','backcolor'=>'','bold'=>true),true);
					$out .= CompSetup::output(" - ".$name,array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>false),true);

				}else{
					$out .= CompSetup::output("\t"."OFF ",array('breackline'=>false,'forecolor'=>'red','backcolor'=>'','bold'=>true),true);
					$out .= CompSetup::output(" - ".$name,array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>false),true);

				}
	
				$out .= "\n";								
			}
		}

		$out .= CompSetup::output("\n\t- Use options with plugin name:",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>true),true);
		$out .= CompSetup::output("\n\t\t -on = Enable the plugin",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>false),true);
		$out .= CompSetup::output("\n\t\t -off = Disable the plugin",array('breackline'=>false,'forecolor'=>'white','backcolor'=>'','bold'=>false),true);

		$out .= "\n";	
		if($returnActionString != '')
		$out = $returnActionString;


		return $out;
	}
}