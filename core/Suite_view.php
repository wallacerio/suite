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

class Suite_view{

	private static $html;
	private static $template = null;
	private static $currentAction = null;
	private static $modeRender = 'renderize';
	private static $resultAction = null;
	private static $templateList = Array();
	private static $contentMethod = null;

	function __construct(){}
	
	/**
	 * [contentMethod description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public static function contentMethod($class = null){
		if($class != null)
		self::$contentMethod = $class;
	}


	

	/**
	 * utilizado para manipular um conteúdo
	 * @param  [type] $html    [description]
	 * @return [type]          [description]
	 */
	public static function content($html = null){
				
		if($html === false){			
			self::$modeRender = 'render404';
			return false;
		}

		if($html != null && $html !== false){	
			self::$modeRender = 'renderize';
			Suite_globals::set('render/content',$html);
			self::$html = $html; 
		}else{

			$renderArray = Suite_globals::get('render');
			if(!isset($renderArray))
				return false;

			$html = Suite_globals::get('render/content');

		}

		return $html;
	}

	/**
	 * [view description]
	 * @param  [type] $html [description]
	 * @return [type]       [description]
	 */
	public static function view($html = null){
		if($html != null){
			// Suite_globals::set('render/view',$html);
			self::$html = $html; 
		}
		return self::$html;
	}

	/**
	 * [action description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	public static function action($object = null){if($object != null) self::$currentAction = $object; return self::$currentAction; }
	/**
	 * [html description]
	 * @param  [type] $html [description]
	 * @return [type]       [description]
	 */
	public static function html($html = null){self::$html = $html;}

	/**
	 * [render description]
	 * @param  [type]  $html    [description]
	 * @param  boolean $options [description]
	 * @return [type]           [description]
	 */
	public static function render($html = null,$options = false){
		$return = isset($options['return'])?$options['return']:false;
		
		if( $return == false){
			
			

			if( ($html == null ) ){
			// if($html == null && self::$template == null){
				echo "<!DOCTYPE html>"
						. "<html>"
						. "<head>"
						. "<title>Basic for initialization - Suite framework</title>"
						. "</head>"
						. "<body>"
						
						. "<h1>Suite framework ".suite_version."</h1>"
						. "<hr>"
						. "<p>Suite framework are running normally, please, install the components and make your application.</p>"
						. "</body>"
						. "</html>";				
				
			}else{
				echo $html;
			}
		}else{		
			return $html;
		}
	}
	
	/**
	 * [out description]
	 * @param  [type] $html [description]
	 * @return [type]       [description]
	 */
	public static function out($html = null){
		
		


		$target = Suite_globals::get('http/target');
		$queries = Suite_globals::get('http/queries');
		
		// self::$html = self::content($target,$queries);

		/*$renderView = Suite_globals::get('render/view');
		if($renderView)
		self::$html = $renderView;*/

		/*$renderContent = Suite_globals::get('render/content');
		if($renderContent)
		self::$html = $renderContent;
		*/
		$action = Suite_globals::get('module/action');


		

		self::$resultAction = $action;

		if(isset($html['response'])){			
			$action = $html;
		}

		

		$analizeResult = self::analizeAction($action,self::$html);
		// caso houver um response no action
		if(is_array($analizeResult) && $analizeResult['break'] == true ){	
	
			return $analizeResult;
		}
		
		
		if($html != null) return $html;
		$modeRender = self::$modeRender;

					
		
		

			
		if(self::$template == null){
			return self::$html;
		}else{		
			if(method_exists(self::$template, $modeRender)){	

				$result = self::$template->$modeRender(self::$html,self::$resultAction);				
				return $result;
			}else{
				
				foreach (self::$templateList as $key => $value) {
					if(method_exists($value, $modeRender))
						self::$template = $value;
				}				


				return self::$template->$modeRender(self::$html,self::$resultAction);
			}
			
		}
	}
	
	/**
	 * [template description]
	 * @param  [type] $template [description]
	 * @param  [type] &$action  [description]
	 * @return [type]           [description]
	 */
	public static function template($template = null,&$action = null){
		if($template == null && $action == null)
			return self::$template;

		self::$templateList[] = $template;

		

		if(self::$template == null){
			self::$template = $template;			
		}

		return $action;
	}

	/**
	 * [analizeAction description]
	 * @param  [type] $resultAction [description]
	 * @param  [type] &$view        [description]
	 * @return [type]               [description]
	 */
	public static function analizeAction($resultAction = null,&$view){
		
		
		
		if(isset($resultAction['response'])){			
			self::$template = null;
			$view = $resultAction['response'];	
			$type = isset($resultAction['type'])?$resultAction['type']:'application/json';
			$arrayHeader = array();
			$arrayHeader['type'] = $type;

			self::header($arrayHeader);			
			
			// echo $view;

			return array('break'=>true,'out'=>$view);
		}

		if(isset($resultAction['http'])){
			$http = $resultAction['http'];			
			$response = $http['response'];						
			self::$modeRender = 'render'.$response;
		}
		return false;
	}

	/**
	 * [header description]
	 * @param  [type] $type [description]
	 * @return [type]       [description]
	 */
	public static function header($arrayHeader = null){
		$type = isset($arrayHeader['type'])?$arrayHeader['type']:null;
		if($type != null){			
			header('Content-type: '.$type);
		}
	}



}