<?php

class Control {
	function __construct(){
		
	}

	public function renderTemplate($view = ''){	
		$appDir = Suite_globals::get('app/dir');
		$_htmlDir = $appDir . '_html/';

		if(!file_exists($appDir))
			return false;


		if(file_exists($_htmlDir.'index.html')){
			$html_index = file_get_contents($_htmlDir.'index.html');		
		}else{
			$coreDir = Suite_globals::get('core/dir');			
			$viewHtmlDir = $coreDir . 'components/view/html/' ;
			$html_index = file_get_contents($viewHtmlDir.'200.html');			
		}
		return $html_index;
	}


	public function renderize($view = '',$args = null){		
		$appDir = Suite_globals::get('app/dir');

		$return = $this->renderTemplate($view);

		if($return){
			Header("HTTP/1.1 200 Ok Suite");	
			return $return;
		}else{
			Header("HTTP/1.1 200 Ok Suite");	
			return null;			
		}		
		
	}

	public function render301($view = '',$args = null){			
		$location = isset($args['http']['location'])?$args['http']['location']:null;		
		Header("HTTP/1.1 301 Moved Permanently");
		Header("Location: ".$location);
		return null;
	}

	public function render307($view = '',$args = null){			
		$location = isset($args['http']['location'])?$args['http']['location']:null;		
		Header("HTTP/1.1 307 Moved Temporary");
		Header("Location: ".$location);
		return null;
	}

	public function render404($view = ''){			
		
		header("HTTP/1.0 404 Not Found");		
		
		$appDir = Suite_globals::get('app/dir');
		$_htmlDir = $appDir . '_error/404/';

		if(file_exists($_htmlDir.'view.html')){

			$resultLoad = Suite_class::load($_htmlDir,'error');
			$control = $resultLoad['control'];
			if(method_exists($control, 'indexAction')){
				$resultAction = $control->indexAction();
				Suite_globals::set('module/action',$resultAction);
			}					
			$html_index = file_get_contents($_htmlDir.'view.html');
		}else{
			$coreDir = Suite_globals::get('core/dir');			
			$viewHtmlDir = $coreDir . 'components/view/html/' ;
			$html_index = file_get_contents($viewHtmlDir.'404.html');
		}

		return $html_index;
	}

	public function load(){	
		
		Suite_view::template(new $this);
		return null;
	}

}