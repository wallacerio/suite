<?php

class Control extends Model{
	public function load(){
		$action = Suite_globals::get('http/action');		
		$langCurrent = isset($_COOKIE["lang-current"])?$_COOKIE["lang-current"]:"en";

		$browserLanguage = $this->getBrowserLanguages();
		
		
		// redefine o prefix com a linguagem atual
		// if($browserLanguage != $langCurrent)
			Suite_globals::set('http/prefix',$langCurrent);		

		$action = str_replace($langCurrent, '', $action);

		if(substr($action, 0,1)=='/')
			$action = substr($action, 1);

		// if($browserLanguage != $langCurrent)
		Suite_globals::set('http/action',$action);	


		


		$currentUrl = Suite_globals::get('current/url');
		$translationJs = $currentUrl . 'assets/js/translation.js';

		

		$returnTags = $this->insertTag();	
		$register = $returnTags;
		$register[] = array('script'=>array('parameters'=>array('src'=>$translationJs,'type'=>'text/javascript'),'into'=>'first-head'));
		
		return array(
			'register' => $register 
		);
	}

	public function widget($options){	
		$html = '';

		

		$component_name = isset($options['component_name'])?$options['component_name']:null;
		$widget_name = isset($options['widget_name'])?$options['widget_name']:null;
		$widget_attr = isset($options['widget_attr'])?$options['widget_attr']:null;
		$widget_content = isset($options['widget_content'])?$options['widget_content']:null;

		$currentDir = Suite_globals::get('current/dir');
		$currentUrl = Suite_globals::get('current/url');
		$widgetDir = $currentDir .'widgets/';
		$widgetUrl = $currentUrl .'widgets/';
		

		
		$currentWidgetDir = $widgetDir.$widget_name.'/';		
		$currentWidgetUrl = $widgetUrl.$widget_name.'/';	

			
		if(file_exists($currentWidgetDir)){
			$html = file_get_contents($currentWidgetDir.'view.html');
			$return = include $currentWidgetDir.'Control.php';
			$return['html'] = $html;
			
				
			return $return;
		}		
		
		
	}


	
	
	public function render($view){	
		
		$prefix = Suite_globals::get('http/prefix');
		
		$prefixArray = explode('/', $prefix);
		
		$browserLanguage = $this->getBrowserLanguages();

		$listLanguages = $this->listLanguages();
		if(count($listLanguages)>0)
		foreach ($listLanguages as $key => $value) {
			
			if(in_array($key,$prefixArray))
				$browserLanguage = $key;
		}

		
	
		$target = $browserLanguage;	
			
		

		$view = $this->reference($view,$target);	
		
		$view = $this->pathTranlate($view,$target);	
		

		return $view;
	}
}