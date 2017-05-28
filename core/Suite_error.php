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

class Suite_error{
		
	public static $coreDir;
	public static $ErrorsDir;
	public static $ErrorsLastActionDir;

	private static $newMethod;

	public function __construct(){
		Suite_error::setSuiteCoreDir(suite_path);
		Suite_error::setHandler();
		self::sessionErrorClean();
	}

	
	/**
	 * [newMethod description]
	 * @param  [type] $method [description]
	 * @return [type]         [description]
	 */
	public static function newMethod($method = null){
		if($method == null){
			return null;
		}else{
			Suite_error::$newMethod = $method;
			return $method;
		}

	}

	/**
	 * [sessionErrorClean description]
	 * @return [type] [description]
	 */
	public static function sessionErrorClean(){
		
		// $session_id = 'suite_error';
		unset($_COOKIE['suite_error']);
		/*foreach ($_COOKIE as $key => $value) {
			$_COOKIE[$key] = null;
			unset($_COOKIE[$key]);
			if(isset($_SESSION[$key])) unset($_SESSION[$key]);
		}
		
		@session_destroy($session_id);*/

		return false;
	}

	/**
	 * [setSuiteCoreDir description]
	 * @param [type] $path [description]
	 */
	public static function setSuiteCoreDir($path = null){
		self::$coreDir = $path.'core/';
		self::$ErrorsDir = $path.'components/exceptions/';	
	}

	
	/**
	 * [setHandler description]
	 */
	public static function setHandler(){	

		/*function exception_error_handler($errno, $errstr, $errfile, $errline ) {
		    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		}
		set_error_handler("exception_error_handler");
		*/

		register_shutdown_function(array('Suite_error', 'shutdown'));
		set_error_handler(array('Suite_error', 'handler'));
		// set_exception_handler(array('Suite_error', 'handler'));
		// exit;
			
	}

	/**
	 * [shutdown description]
	 * @return [type] [description]
	 */
	public static function shutdown() {

		$modeView = 'html';

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {		
			$modeView = 'string';			
		}else if(isset($_SERVER['SHELL']) ){
			$modeView = 'string';			
		}

		
		if(self::$newMethod != null){
			Suite_error::$newMethod->shutdown();
			return true;
		}

	    $error = error_get_last();
	    $error_code = $error['type'];
		$error_line = $error['line'];
		$error_file = $error['file'];

	    if ($error['type'] === E_ERROR) {
	        // fatal error has occured
	    }

	    if($error_code){
		    	
		    if($modeView=='html'){
		    	echo '<script>document.body.innerHTML = "";</script>';	   
		    	echo self::viewHtml(array('error_code'=>$error_code,'error_file'=>$error_file,'error_line'=>$error_line));		    
		    }else if($modeView=='string'){
		    	echo self::viewString(array('error_code'=>$error_code,'error_file'=>$error_file,'error_line'=>$error_line));		    
		    }
		}

	   
	   exit;
	}

	/**
	 * [handler description]
	 * @param  [type] $num     [description]
	 * @param  [type] $str     [description]
	 * @param  [type] $file    [description]
	 * @param  [type] $line    [description]
	 * @param  [type] $context [description]
	 * @return [type]          [description]
	 */
	public static function handler($num, $str, $file, $line, $context = null){

		$modeView = 'html';

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {		
			$modeView = 'string';			
		}else if(isset($_SERVER['SHELL']) ){
			$modeView = 'string';			
		}


		if(self::$newMethod != null){
			Suite_error::$newMethod->handler($num, $str, $file, $line, $context);
			return true;
		}			

		if($modeView=='html'){	    	
	    	echo self::viewHtml(array('error_code'=>$num,'error_file'=>$file,'error_line'=>$line));		    	
	    }else if($modeView=='string'){
	    	echo self::viewString(array('error_code'=>$num,'error_file'=>$file,'error_line'=>$line));		    
	    }

		 exit;
	    return true;
	}

	/**
	 * [setContext description]
	 * @param [type] $newArray [description]
	 */
	public static function setContext($newArray = null){

		$contentErrorsLastAction = isset($_COOKIE['suite_error'])?$_COOKIE['suite_error']:null;

		if($contentErrorsLastAction == 'null' || $contentErrorsLastAction == null){			
			$contentErrorsLastActionObj = $newArray;
		}else{		
			$contentErrorsLastActionObj = json_decode($contentErrorsLastAction,true);			
			$contentErrorsLastActionObj = array_merge($contentErrorsLastActionObj,$newArray);				
		}
		
		
		if(!Suite_globals::get('http/argv'))
		setcookie('suite_error',json_encode($contentErrorsLastActionObj),0,'/'," ");
	}


	/**
	 * [getContext description]
	 * @param  string $viewMode [description]
	 * @return [type]           [description]
	 */
	public static function getContext($viewMode = 'string'){
		$contentErrorsLastAction = isset($_COOKIE['suite_error'])?$_COOKIE['suite_error']:null;	
		if($contentErrorsLastAction == null) return '';
		$contentErrorsLastActionObj = json_decode($contentErrorsLastAction,true);

		

		$contextHtml = '';
		if(count($contentErrorsLastActionObj)>0){
			foreach ($contentErrorsLastActionObj as $key => $value) {

				if($viewMode == 'string'){
					
					$contextHtml .= $key."\n";
					$contextHtml .= 'Name:'.$value['name']."\n";
					$contextHtml .= 'Path:'.$value['path']."\n";
					$contextHtml .= "\n\n";

				}else if($viewMode == 'html'){
					$contextHtml .= '<ul>';
					$contextHtml .= '<li>';
					$contextHtml .= '<label><strong>'.$key.'</strong></label>';
						$contextHtml .= '<ul>';
						$contextHtml .= '<li><label><strong>Name:</strong></label> '.$value['name'].'</li>';
						$contextHtml .= '<li><label><strong>Path:</strong></label> '.$value['path'].'</li>';
						$contextHtml .= '</ul>';
					$contextHtml .= '</li>';
					$contextHtml .= '</ul>';
				}
			}
		}
		
		return $contextHtml;
	}

	/**
	 * [viewHtml description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public static function viewHtml($array = null){
		if($array == null) return '';

		$error_code = isset($array['error_code'])?$array['error_code']:'';
		$error_line = isset($array['error_line'])?$array['error_line']:'';
		$error_file = isset($array['error_file'])?$array['error_file']:'';
		$error_context = self::getContext('html');

		$html = '';
	    $html .= '<html>';
	    $html .= '<h1>Error</h1>';
	    $html .= 'Type: '.$error_code.'<br>';
	    $html .= 'File: '.$error_file.'<br>';
	    $html .= 'Line: '.$error_line.'<br>';		    
	    if($error_context != '')
	    $html .= 'Context: '.$error_context.'<br>';		    
	    $html .= '</html>';

	    return $html;
	}


	/**
	 * [viewString description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public static function viewString($array = null){
		if($array == null) return '';

		$error_code = isset($array['error_code'])?$array['error_code']:'';
		$error_line = isset($array['error_line'])?$array['error_line']:'';
		$error_file = isset($array['error_file'])?$array['error_file']:'';

		$error_context = self::getContext('string');

		$html = "\n\n";
		$html .= 'Error'."\n\n";
		$html .= 'Type:'."\t".$error_code."\n";
		$html .= 'File:'."\t".$error_file."\n";
		$html .= 'Line:'."\t".$error_line."\n";
		$html .= "\n\n";

		if($error_context != '')
		$html .= 'Context:'."\t".$error_context."\n";


	    return $html;
	}

}