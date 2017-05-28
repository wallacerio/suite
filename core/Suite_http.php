<?php
/*
 * Suite Framework
 * ================
 * suite.wallrio.com
 *
 * This file is part of the Suite Core.
 *
 * Wallace Rio <wallrio@gmail.com> 
 * 
 */

class Suite_http{
	
	function __construct(){
		$this->load();
	}

	/**
	 * [set description]
	 * @param [type] $options [description]
	 */
	public static function set($options = null){		
		if($options == null) return false;
		$url = isset($options['url'])?$options['url']:null;
		$get = isset($options['get'])?$options['get']:null;
		$post = isset($options['post'])?$options['post']:null;
		if($get!= null ){
			
			$_GET = array_merge($_GET, $get);
			$_REQUEST = array_merge($_REQUEST, $_GET);
			
		}
		if($post!= null ){
			$_POST = array_merge($_POST, $post);
			$_REQUEST = array_merge($_REQUEST, $_POST);
		}

	

		if($url){
			$settings = Suite_globals::get('settings');
			$querytarget = $settings['querytarget'];

			
			Suite_globals::set('http/action',$url);
			Suite_globals::set('http/target',$url);
		}
	}

	/**
	 * [load description]
	 * @return [type] [description]
	 */
	public function load(){		

		$root = isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:null;
		if($root=='console'){
			$root = $_SERVER['PWD'];		
		}else{
			$root = dirname($root);		
		}
		
		$host = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:null;
		$agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
		$language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:null;
		$method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
		$protocol = isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:null;
		$cookie = isset($_SERVER['HTTP_COOKIE'])?$_SERVER['HTTP_COOKIE']:null;		
		
		
		$argv = isset($_SERVER['argv'])?$_SERVER['argv']:null;		

		$httpType = (strpos(strtolower($protocol), 'http')!==false)?'http':'https';
			
		
		

		if($_SERVER['PHP_SELF'] == 'console')
			$domainName = ($_SERVER['PWD']);
		else
			$domainName = dirname($_SERVER['PHP_SELF']);


		$requestURI = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;		
		
		$queryReal = str_replace($domainName.'/', '', $requestURI);
		$operatorInterrogation = strpos($queryReal, '?');
		if($operatorInterrogation !== false){
			$target = substr($queryReal, 0,$operatorInterrogation);
			$queryPar = substr($queryReal, $operatorInterrogation+1);
		}else{			
			$target = $queryReal;
			$queryPar = null;
		}
		
		$domain = $host.''.$domainName.'';
		if(substr($domain, strlen($domain)-1)=='/')
			$domain = substr($domain, 0,strlen($domain)-1);
		
		if(substr($domainName, 0,1)=='~' || substr($domainName, 0,2)=='/~'){
			$domainName = '';
		}

		$root = $root.DIRECTORY_SEPARATOR;

		if($_SERVER['PHP_SELF'] == 'console'){
			$domainURL = null;		
		}else{
			$domainURL = $httpType.'://'.$domain.'/';		
		}
		
		$domainPath = $root;
		$domainPath = str_replace('//', '/', $domainPath);
		
		$variableArray = array();

		
		$parsequery = explode("&",$queryPar);

			
			
			$action = '/';
			if($queryReal != null){			
					// modifica as chaves do array criado com as variaveis get da requisição;
					
					foreach ($parsequery as $key => $values) {
						$valueArray = explode('=', $values);
						$variableArray[$valueArray[0]]=isset($valueArray[1])?$valueArray[1]:null;
						unset($variableArray[$key]);
					}		

					if($action == '') $action = '/';
			}
			

		$action = $target;
		$actionArray = explode('/', $action);

		$actionFirst = isset($actionArray[0])?$actionArray[0]:'';

		if(substr($actionFirst, 0,1)==':'){			
			$action = substr($action, strlen($actionFirst)+1);
			$newApp = substr($actionFirst, 1);
			
		}else{
			$newApp = null;
		}

		

		if($action == '/' || $action == '')
			$action = 'home';
		
		if(substr($action, strlen($action)-1,strlen($action))=='/')
			$action = substr($action, 0,strlen($action)-1);

	
		$target = $action;

		$httpRequest = array(
			'query'=>$queryReal,			
			'host'=>$host,
			'agent'=>$agent,
			'language'=>$language,
			'method'=>$method,
			'protocol'=>$protocol,
			'cookie'=>$cookie,
			'root'=>$root,
			'domain'=>array(
				'host'=>$domain.DIRECTORY_SEPARATOR,
				'url'=>$domainURL,
				'dir'=>$domainPath
				),

			'prefix'=>'',
			'posfix'=>'',
			'action'=>$action,
			'target'=>$target,
			'queries'=>$variableArray,
			
			
			
		);	

		if($newApp != null)
			$httpRequest['app'] = $newApp;

		

		if($argv != null)
			$httpRequest['argv'] = $argv;

		

		Suite_globals::set('http',$httpRequest);
		Suite_globals::set('domain',array(
			'host'=>$domain.DIRECTORY_SEPARATOR,
				'url'=>$domainURL,
				'dir'=>$domainPath
		));

		$this->defineHeader();
	}

	/**
	 * [defineHeader description]
	 * @return [type] [description]
	 */
	public function defineHeader(){
		header("Suite:".suite_version);
	}
}