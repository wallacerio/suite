<?php

class CompAssets{
	
	public static $actionCount = 0;
	
	public function render($html = null){		
		
		$html = $this->register($html);
		$html = $this->replace($html);
		
		return array('html'=>$html,'last'=>true);
	}


	public static function actionRegister($action = null,$id = null){	

		// print_r($action);
		
		if($id == null) $id = self::$actionCount;
		Suite_globals::set('actions/'.$id,$action);

		self::$actionCount = self::$actionCount + 1;
	}


	public function replace($html = null){
		
		$actionArray = Suite_globals::get('actions');		


		if(count($actionArray)>0)
		foreach ($actionArray as $keyUnit => $valueUnit) {
			if(count($valueUnit)>0)
			foreach ($valueUnit as $key2 => $value2) {
				if(isset($value2['replace'])){
					$replace = $value2['replace'];	
					foreach ($replace as $key => $value) {					
						$html = str_replace($key, $value, $html);		
					}				
				}	
			}

			if(isset($valueUnit['replace'])){
				$replace = $valueUnit['replace'];	
				foreach ($replace as $key => $value) {					
					$html = str_replace($key, $value, $html);		
				}				
			}
		}

		// gerado pelo load dos components
		$actionArray = Suite_globals::get('components/load');
		if(count($actionArray)>0)
		foreach ($actionArray as $keyUnit => $valueUnit) {
			if(isset($valueUnit['replace'])){
				$replace = $valueUnit['replace'];	
				foreach ($replace as $key => $value) {					
					$html = str_replace($key, $value, $html);		
				}	
			}
		}

		
		
		return  $html;
	}



	public function register($html = null){
			

		// gerado de forma personalizada, com origem desconhecida
		// dentro dos actions tem varios retornos
		$actionArray = Suite_globals::get('actions');		

		// print_r($actionArray);

		if(count($actionArray)>0)
		foreach ($actionArray as $keyUnit => $valueUnit) {
			if(count($valueUnit)>0)
			foreach ($valueUnit as $key2 => $value2) {
				if(isset($value2['register-overwrite']) == true){
					CompRegister::clean();
				}
				if(isset($value2['register'])){				
					$register = $value2['register'];				
					CompRegister::join($register);		
				}
			}

			if(isset($valueUnit['register-overwrite']) == true){
					CompRegister::clean();
				}
			if(isset($valueUnit['register'])){								
				$register = $valueUnit['register'];				
				CompRegister::join($register);		
			}
		}
		

		// gerado pelo load dos components
		$actionArray = Suite_globals::get('components/load');
		if(count($actionArray)>0)
		foreach ($actionArray as $keyUnit => $valueUnit) {
			if(isset($valueUnit['register'])){
				if(isset($valueUnit['register-overwrite']) == true){
					CompRegister::clean();
				}

				$register = $valueUnit['register'];
				CompRegister::join($register);		
			}
		}

		
		// captura os registros gerados
		$register = CompRegister::getHtml();
		// print_r($register);
		$head = $register['head'];
		$footer = $register['footer'];
		
		// inclui os scripts no documento
		$html = str_replace('</head>', $head.'</head>', $html);
		$html = str_replace('</body>', $footer.'</body>', $html);

		$html = str_replace('[register:head]', $head, $html);
		$html = str_replace('[register:footer]', $footer, $html);
		

		return  $html;
	}


}	