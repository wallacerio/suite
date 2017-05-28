<?php

class Control extends Model{


	public function indexAction($args){

		$CompConsoleGui = Suite_components::callDirectClass('setup/gui/_libs/CompConsoleGui');
		return $CompConsoleGui->gui($args);	
		
	}


	public function optionsAction(){	
		return array(
			'response'=>json_encode($this->options())
		);
	}


	public function getfunctionAction($argsRequest){

		$parameters = isset($argsRequest['parameters'])?$argsRequest['parameters']:null;
		$function = isset($argsRequest['function'])?$argsRequest['function']:null;
		$functionDecoded = base64_decode($function);

		$globals = Suite_globals::get();
		
		
		$Func = create_function('$args,$globals', $functionDecoded);
		$result = $Func($parameters,$globals);

		

		return array(
			'response' => json_encode($result),
			'type'=>"application/json"
		);
	}

	public function getAction($args){	

		$domainUrl = Suite_globals::get('http/domain/url');

		
		$command = isset($args['command'])?$args['command']:'';
		$parameters = isset($args['parameters'])?$args['parameters']:'';

		$url = $domainUrl . '_component/setup/console';

		$result = Suite_libs::run('Http/Request/url',array(
				'url' => $url,
				'data'=> array(
					'token'=>'admin.123',
					'command'=>$command,
					'parameters'=>$parameters

				)
			));

		return array(
			'response'=>$result,
			'type'=>"application/json"
		);
	}

}