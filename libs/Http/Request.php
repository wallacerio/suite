<?php

	
class Http__Request{	
	
	public function includes($dir = null){
		ob_start();
		include $dir;
		$content = ob_get_contents();
		ob_end_clean();		
		return $content;
	}


	/**
	 * Requisição local
	 * utilizado para acessar um caminho semelhante a uma url, porem diretamente via arquivo.
	 * 
	 * @param  [array] $options [array contendo como parametros dir e data]
	 * @return [type]          [retorna o conteúdo da requisição]
	 *
	 * parametro dir: caminho do recurso da requisição
	 * parametro data: valor em array para anexar na requisição semelhante ao get/post
	 *
	 * Exemplo:
	 *
	 * $result = Suite_libs::run('Http/Request/dir',array(
	 *		'dir'=>'_component/setup/console',
	 *		'method'=>'GET',
	 *		'data'=>array('color'=>'blue')
	 *	));
	 *	
	 */
	public function dir($options){

		$dir = isset($options['dir'])?$options['dir']:null;
		$data = isset($options['data'])?$options['data']:null;
		$method = isset($options['method'])?$options['method']:'get';
		
		

		$dataFinal = array(
				'return'=>true,
				'url'=>$dir
			);

		if($method == 'get'){
			$dataFinal['get'] = $data;
		}else{
			$dataFinal['post'] = $data;
		}
	
		$result = Suite::load($dataFinal);
		
		return $result;
	}

	public function url($options){

		$url = isset($options['url'])?$options['url']:null;
		$method = isset($options['method'])?$options['method']:'get';
		$data = isset($options['data'])?$options['data']:null;
		$operatorquery = isset($options['operatorquery'])?$options['operatorquery']:'?';
		

		if(is_array($data)){
			foreach ($data as $key => &$value) {
				if(is_array($value))
					$value = json_encode($value);
			}
		}

		$curl = curl_init();
		if($method == 'post'){

			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url,		
			    CURLOPT_POST => count($data),
			    CURLOPT_POSTFIELDS => $data
			));

		}else if($method == 'get'){		
			
			if($data != null){
				$dataQuery = http_build_query($data);
			}else{
				$dataQuery = '';
				$operatorquery = '';
			}

		
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url.$operatorquery.$dataQuery,					   
			));
		}

		

		$resp = curl_exec($curl);		
		curl_close($curl);
		return $resp;	
	}

	public function component($options){
		$domainUrl = Globals::get('path/app/link');

		$component = isset($options['component'])?$options['component']:null;
		$action = isset($options['action'])?$options['action']:'';
		$requestGet = isset($options['requestGet'])?$options['requestGet']:null;

		$requestGetString = '';

		if($component == null)
			return false;

		if($requestGet != null){
			$requestGetString = '&'.urldecode(http_build_query($requestGet));
		}
		

		return Functions::getFileBySuite($domainUrl.'!/_component/'.$component.'/'.$action.''.$requestGetString);
	}
}