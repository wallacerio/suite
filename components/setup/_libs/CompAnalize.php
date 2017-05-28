<?php

class CompAnalize{

	public static $inputs = null;

	/**
	 * Token utilizado se a requisção for feita via web
	 * atualmente o token é o username[ponto]password
	 * exemplo: admin.123
	 * 
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
	public static function checkToken($token = null){
		$result = Suite_libs::run('Options/base');
		$access = isset($result['access'])?$result['access']:null;

		$tokenArray = explode('.', $token);
		$username = isset($tokenArray[0])?$tokenArray[0]:null;
		$password = isset($tokenArray[1])?$tokenArray[1]:null;

		$tokenRemote = $username.md5($password);
		$tokenLocal = $access['username'].$access['password'];

		if( $tokenLocal == $tokenRemote){
			return true;
		}
		return false;
	}

	/**
	 * analisa se o tipo de resposta da requisição
	 * @param  [type] $dataArray   [description]
	 * @param  [type] $inputsArray [description]
	 * @return [type]              [description]
	 */	
	public static function request($dataArray,$inputsArray,&$type = null){
		$type = isset($dataArray['type'])?$dataArray['type']:'text';
		$output = isset($dataArray['output'])?$dataArray['output']:null;
		$out = '';

		

		if($type == 'text')
			$out = CompSetupPrint::formatStrings($output);
		
		if($type == 'list-commands')
			$out = CompSetupPrint::listCommands($output,$inputsArray);
		
		if($type == 'list')
			$out = CompSetupPrint::lists($output,$inputsArray);
		
		if($type == 'inputs'){
			self::$inputs = $output;						
		}

		if($type == 'code'){
			$out = CompAnalize::runCommand($output,$inputsArray);				
		}

		if($type == 'code-text'){
			$output = base64_encode($output);
			$out = CompAnalize::runCommand($output,$inputsArray);		
		}
		
		if($type == 'command'){
			$out = CompAnalize::runCommand($output,$inputsArray);	
		}

		return $out;
	}



	/**
	 * executa um commando enviado por uma requisição
	 * @param  [type] $commandEncoded [description]
	 * @param  [type] $inputsArray    [description]
	 * @return [type]                 [description]
	 */
	public static function runCommand($commandEncoded,$inputsArray){
		$globals = Suite_globals::get();
		$commandDecoded = base64_decode($commandEncoded);
				
		$command = create_function('$args,$globals', $commandDecoded);
	
		$resultCommand = $command($inputsArray,$globals);

		if(is_array($resultCommand)){			
			$resultCommand = CompAnalize::request($resultCommand,null);
		}
		

		return $resultCommand;
	}

}