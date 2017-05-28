<?php

class Model{

	

	/**
	 * responde a uma requisição, enviando um json
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function consoleAction($args){	
		$g = Suite_globals::get('http/argv');
			
		if(!$g){
			$token = isset($args['token'])?$args['token']:null;
			if($token == null){
				$response = array('status'=>'access-negate','msg'=>'missing token');

				return array(
					'response'=>json_encode($response),
					'type'=>'application/json'
				);
			}else{
				if(!CompAnalize::checkToken($token)){
					$response = array('status'=>'token-invalid','msg'=>'token not validate');

					return array(
						'response'=>json_encode($response),
						'type'=>'application/json'
					);
				}
			}
		}

		$parameters = isset($args['parameters'])?$args['parameters']:null;
		if(!is_array($parameters)){
			$parametersArray = explode(' ', $parameters);
			$args['parameters'] = $parametersArray;
		}
		
		
		$response = CompConsole::request($args);

		return array(
			'response'=>json_encode($response),
			'type'=>'application/json'
		);
	}





	
	/**
	 * Envia uma requisição para obter o json do console
	 * @param  [type] $argv [description]
	 * @return [type]       [description]
	 */
	public function console($argv = null){	
		$out = '';

		

		if($argv == null)
		$argv = Suite_globals::get('http/argv');
		$domainUrl = Suite_globals::get('http/domain/url');		
		$domainDir = Suite_globals::get('http/domain/dir');		


		

		$inputsArray = $argv;
		$runner = isset($inputsArray[0])?$inputsArray[0]:null;
		unset($inputsArray[0]);
		$inputsArray = array_values($inputsArray);		
		$inputsArrayPos['command'] = isset($inputsArray[0])?$inputsArray[0]:null;
		unset($inputsArray[0]);
		$inputsArray = array_values($inputsArray);
	
		$inputsArrayPos['parameters'] = $inputsArray;		
		$inputsArray = $inputsArrayPos;

		$command = isset($inputsArrayPos['command'])?$inputsArrayPos['command']:null;
		


		$result = @Suite_libs::run('Http/Request/dir',array(
			'dir'=>'_component/setup/console',				
			'data'=>$inputsArray
		));


		
		

		$result = CompConsole::analize($result,$inputsArray,$showLegend);
		
		$out = CompSetupPrint::out($result,true,$showLegend);

		// caso seja recebido uma lista de inputs
		if(CompAnalize::$inputs!=null){
			$arrayResultInputs = CompSetupPrint::inputs(CompAnalize::$inputs);
			CompAnalize::$inputs = null;
			
			$returnSignal = '-return';
			$returnCode = json_encode($arrayResultInputs);
		
			$this->console(array(
				$runner,
				$command,
				$returnSignal,			
				$returnCode			
			));		
		}	

		$out .= "\n\n";
		
		return $out;
	}


}
