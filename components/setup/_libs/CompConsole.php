<?php

class CompConsole{
	
	/**
	 * captura as opções definidas para listagem e ações
	 * @param  [type] $argv [description]
	 * @return [type]       [description]
	 */
	public static function request($argv = null){		
		$out = array();				

		$listComponentsConsoleArray = Suite_globals::get('setup');
		$result = self::listCommand($listComponentsConsoleArray,$argv);
		if($result)
			$out = $result;

		return $result;
	}


	/**
	 * mostra as opções baseado na requisição ou executa a opção
	 * @param  [type] $listComponentsConsoleArray [description]
	 * @param  [type] $inputsArray                [description]
	 * @return [type]                             [description]
	 */
	public static function listCommand($listComponentsConsoleArray, $inputsArray = null){

		
		$command = isset($inputsArray['command'])?$inputsArray['command']:null;
		$commandArray = explode('/', $command);
		$commandArray = array_filter($commandArray);
		$join = '';		
		$joinString = '';
		$countCommand = 0;

		$joinStringPos = $command;


		if(  count($commandArray)>0){
			
			
			foreach ($commandArray as $key => $value) {
				$join .= '["'.$value.'"]';
				$joinString .= $value.'/';
				
				eval('if(isset($listComponentsConsoleArray'.$join.')){$resultArray = $listComponentsConsoleArray'.$join.';$joinStringPos = $joinString;$countCommand++;}');
				
				if(isset($resultArray) && ($resultArray['@type']=='remote' || $resultArray['@type']=='remote-command')){
					$countCommand = count($commandArray);
					break;
				}
			}

			// check if command is a default.console command
			if($countCommand != count($commandArray)){
				$join = '["setup.default"]';
				foreach ($commandArray as $key => $value) {
					$join .= '["'.$value.'"]';
					$joinString .= $value.'/';
					
					
					eval('if(isset($listComponentsConsoleArray'.$join.')){$resultArray = $listComponentsConsoleArray'.$join.';$joinStringPos = $joinString;$countCommand++;}');
					
					if(isset($resultArray) && ($resultArray['@type']=='remote' || $resultArray['@type']=='remote-command')){
						$countCommand = count($commandArray);
						break;
					}
				}	
			}
			

		}else{				
			$resultArray = $listComponentsConsoleArray;
		}

		if(substr($joinStringPos, strlen($joinStringPos)-1,strlen($joinStringPos))=='/')
			$joinStringPos = substr($joinStringPos, 0,strlen($joinStringPos)-1);

		
		$commandEfetive = isset($inputsArray['command-efetive'])?$inputsArray['command-efetive']:'';

		$inputsArray['command-efetive'] = $joinStringPos;
		$inputsArray['command-path'] = $commandEfetive.'/'. $joinStringPos;

				

		if($countCommand == count($commandArray)){
			$dataJson = self::runCommand($resultArray,$inputsArray);					
			return $dataJson;	
		}else{
			$out = array('status'=>'not-found');		
			return $out;					
		}

	}


	/**
	 * analisa a resposta da requisição
	 * @param  [type]  $dataJson    [description]
	 * @param  [type]  $inputsArray [description]
	 * @param  boolean &$showLegend [description]
	 * @return [type]               [description]
	 */
	public static function analize($dataJson,$inputsArray,&$showLegend = true){
		$out = array();
		$dataArray = json_decode($dataJson,true);
			
		$status = isset($dataArray['status'])?$dataArray['status']:null;
		$data = isset($dataArray['data'])?$dataArray['data']:null;
		$msg = isset($dataArray['msg'])?$dataArray['msg']:'';
		if($status == 'success'){						
			$out = CompAnalize::request($data,$inputsArray,$type);		
			if($type != 'list-commands')
				$showLegend = false;
			else
				$showLegend = true;
		}else if($status == 'not-found'){						
			$out  = "    Command[:space:]unknown~{\"bold\":true,\"color\":\"red\"} ";			
			$showLegend = false;
		}else{									
			$msg = str_replace(' ', '\s', $msg);
			$out  = "    ".$msg."~{\"bold\":true,\"color\":\"red\"} ";			
			$showLegend = false;
		}

		

		return $out;
	}


	/**
	 * executa um comando local, baseado se ele é uma opção ou categoria
	 * @param  [type] $resultArray [description]
	 * @param  [type] $inputsArray [description]
	 * @return [type]              [description]
	 */	
	public static function runCommand($resultArray,$inputsArray){

		$globals = Suite_globals::get();

		$function = isset($resultArray['@function'])?$resultArray['@function']:null;		
		
		$parameters = isset($inputsArray['parameters'])?$inputsArray['parameters']:null;


		// caso seja retornado como parametro @function
		if($function != null){
			$ifRunFunc = true;				
			$function = base64_decode($function);
			$FuncExec = create_function('$args,$globals', $function);				
			$endOut = $FuncExec($parameters,$globals);		
			return array('status'=>'success','data'=>$endOut);			
		}
	

		// executa caso não seja enviado nenhum comando nem parametro		
		return array('status'=>'success','data'=>array('type'=>'list-commands','output'=>$resultArray));			


	}


}