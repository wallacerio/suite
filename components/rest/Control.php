<?php

class Control{

	public $dataInput = null;

	function __construct(){

		
		// echo file_get_contents("php://input")."---<br>";	
				
	}

	public function test(){
		return "test ok";
	}

	static function get_headers_from_curl_response($headerContent)
	{

	    $headers = array();

	    // Split the string on every "double" new line.
	    $arrRequests = explode("\r\n\r\n", $headerContent);

	    // Loop of response headers. The "count() -1" is to 
	    //avoid an empty row for the extra line break before the body of the response.
	    for ($index = 0; $index < count($arrRequests) -1; $index++) {

	        foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
	        {
	            if ($i === 0){
	            	$lineÀrray = explode(' ', $line);
	            	$protocolArray = explode('/', $lineÀrray[0]);
	                $headers[$index]['http'] = array(	                	
	                	'protocol'=>$protocolArray[0],
	                	'version'=>$protocolArray[1],
	                	'code'=>$lineÀrray[1],
	                	'message'=>$lineÀrray[2]
	                );
	            }
	            else
	            {
	                list ($key, $value) = explode(': ', $line);
	                if($key == 'Set-Cookie'){
		               	if(isset($headers[$index]['Set-Cookie']) && !is_array($headers[$index]['Set-Cookie']))
		               	$headers[$index]['Set-Cookie'] = Array();		            
		            	$headers[$index][$key][] = $value;		            	
		            }else{
		            	$headers[$index][$key] = $value;		            
		            }
	            }

	            // $headers[$index]['method'] = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
				// $headers[$index]['agent'] = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;

	        }
	    }

	    

	    return $headers;
	}

	public function request($options){

		$url = isset($options['url'])?$options['url']:null;
		$method = isset($options['method'])?$options['method']:'get';
		$data = isset($options['data'])?$options['data']:null;
		$operatorquery = isset($options['operatorquery'])?$options['operatorquery']:'?';
		
		$method = strtolower($method);

		if(is_array($data)){
			foreach ($data as $key => &$value) {
				if(is_array($value))
					$value = json_encode($value);
			}
		}

		$curl = curl_init();
	

		 if($method == 'delete' || $method == 'put' || $method == 'post' || $method == 'get'){	

			$data = json_encode($data);
			
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url,					    
			    CURLOPT_POSTFIELDS => ($data),
			    CURLOPT_CUSTOMREQUEST => strtoupper($method),			    
			    CURLOPT_HTTPHEADER => array('Accept: application/json'),
				CURLOPT_HEADER=>true

			));

			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		    $info = curl_getinfo($curl);
		}

		$resp = curl_exec($curl);		
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$speed_download = curl_getinfo($curl, CURLINFO_SPEED_DOWNLOAD);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$CONTENT_TYPE = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
		
		$headerGet = substr($resp, 0, $header_size);

		$body = substr($resp, $header_size);
		curl_close($curl);

		$header = $this->get_headers_from_curl_response($headerGet);
		$header = $header[count($header)-1];

		/*$header = array(
			'content-type'=>$CONTENT_TYPE,
			'header-size'=>$header_size,
			'speed-download'=>$header,
			// 'speed-download'=>,
			// 'http'=>array('code'=>$http_code)
		);*/
		
		return array('body'=>$body,'header'=>$header);	
	}



	public function route($path,$func){

		$target = Suite_globals::get('http/target');
		if($target == 'home') $target = '/';
		
		

		if(substr($path, 0,1)=='/')
			$target = '/'.$target;

		$target = str_replace('//', '/', $target);

		$targetArray = explode('/', $target);
		$targetArray = array_filter($targetArray);
		$targetArray = array_values($targetArray);

		$pathArray = explode('/', $path);
		$pathArray = array_filter($pathArray);
		$pathArray = array_values($pathArray);



		if( strpos($path, '*') === -1 &&  strpos($path, '{') === -1 && $path != $target){			
			return false;
		}
		
		// echo $path .'==='. $target."<br>";

		$http_header = array(
			'type' => isset($_SERVER['CONTENT_TYPE'])?$_SERVER['CONTENT_TYPE']:'',
			'protocol' => isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'',
			'method' => isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'',
			'length' => isset($_SERVER['CONTENT_LENGTH'])?$_SERVER['CONTENT_LENGTH']:'',
			'query_string' => isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'',
			'host' => isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'',
			'request_ip' => isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'',
			'request_port' => isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:'',
			'request_agent' => isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:''
		);

		if(!isset($GLOBALS['SuiteComponent_rest_input'])){
			$this->dataInput = file_get_contents("php://input");
			$GLOBALS['SuiteComponent_rest_input'] = $this->dataInput;			
		}else{			
			$this->dataInput = $GLOBALS['SuiteComponent_rest_input'];
		}

		// $dataInput = file_get_contents("php://input");		
		$dataInput = $this->dataInput;		
		
		
		
		$data = array();

		if(isset($_POST) && count($_POST)>0) $data['post'] = $_POST;
		if(isset($_GET) && count($_GET)>0) $data['get'] = $_GET;
		if(isset($dataInput) && $dataInput != null) $data['input'] = $dataInput;
		
		

		$pathArrayNew = array();
		
		foreach ($pathArray as $key => $value) {	
			
			if(!isset($targetArray[$key])){
				if($value ==='*'){
					break;
				}
				return false;
			}


			if(strpos($value, '{') === false  && $value != $targetArray[$key]  ){
				if($value ==='*'){
					break;
				}
				// echo $value .'=='. $targetArray[$key]."<br>";
				return false;
			}

		}


		foreach ($targetArray as $key => $value) {	

			if($pathArray[$key]==='*'){
					break;
				}
			if(!isset($pathArray[$key])){				
				return false;			
			}
		}


		// echo $pathArray[0] .'=='. $targetArray[0];

		/*if(strpos($pathArray[0], '{') === false  && $pathArray[0] != $targetArray[0]){
			return false;
		}*/

		
		// if( count($pathArray)>0 && ( strpos($pathArray, '{')!= -1 && (count($targetArray) == count($pathArray)))){
		//if( count($pathArray)>0 && (  (count($targetArray) == count($pathArray)))){
		
		if(count($pathArray)<1)
			$pathArray = array('root');

			$args = '';
			foreach ($targetArray as $key => $value) {	
				
				if(isset($pathArray[$key]))
					$val = $pathArray[$key];


				// if(!isset($val))
					// continue;

				preg_match_all("|\{(.*)\}|m",$val,$out, PREG_SET_ORDER);
				$tag = isset($out[0][1])?$out[0][1]:'';		


				if(isset($targetArray[$key]))
					$valueBefore = str_replace('{'.$tag.'}', $targetArray[$key], $val);

				
				$valueNew = $val;

				

				if(strpos($valueNew, '{') === false  && $valueNew != $value  && $valueNew !=='*'){
					return false;
				}


				// $valueNew = str_replace('{', '', $valueNew);
				// $valueNew = str_replace('}', '', $valueNew);

				if(isset($pathArrayNew[$valueNew]))
					$valueNew = $key;
				// echo $valueNew."--<br>";

				$pathArrayNew[$valueNew] = $value;

				$args .= ",'".$valueBefore."'";						
			}
			if(substr($args, 0,1)==',')
				$args = substr($args, 1);


			// $request =$_REQUEST;

			// print_r($pathArrayNew);
			// echo $args;

			// eval('$func($_REQUEST,'.$args.');');			
			eval('$result = $func($pathArrayNew,$http_header,$data);');			

			$this->analizer($result);

			return true;
		//}

		// echo 4;
			//$result = $func($request,$pathArray,$data);

			//$this->analizer($result);

	}

	public function analizer($result = null){

		$body = isset($result['body'])?$result['body']:null;
		$header = isset($result['header'])?$result['header']:null;
		// $response = isset($result['response'])?$result['http']:null;
		
		if($body != null){
			header("HTTP/1.1 200 Ok");			
		}else{
			header("HTTP/1.1 404 Not Found");	
		}

		
		$httpMake = create_function('$http', '
			$protocol = isset($http["protocol"])?$http["protocol"]:"HTTP";
			$version = isset($http["version"])?$http["version"]:"1.1";
			$code = isset($http["code"])?$http["code"]:"200";
			$message = isset($http["message"])?$http["message"]:"Ok";
			return ($protocol."/".$version." ".$code." ".$message);
		');

		

		// print_r($_SERVER);
		if($header!= null)
		foreach ($header as $key => $value) {	
			if($key=='http'){
				header($httpMake($value));
			}else{
				header($key.": ".$value);		
			}
		}
		
		

		if($body != null){			
			echo $body;
		}
		exit;
	}

}