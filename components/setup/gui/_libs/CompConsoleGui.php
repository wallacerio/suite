<?php

class CompConsoleGui{


	
	
	
	public static function gui($args){	

		// $currentDir = Suite_globals::get('current/dir');
		// $currentUrl = Suite_globals::get('current/url');		
		

		$coreDir = Suite_globals::get('core/dir');
		$coreUrl = Suite_globals::get('core/url');

		// $currentDir .= 'gui/';
		// $currentUrl .= 'gui/';

		$componentsDir = $coreDir.'components/';
		$componentsUrl = $coreUrl.'components/';
		$compGuiDir = $componentsDir.'setup/gui/';
		$compGuiUrl = $componentsUrl.'setup/gui/';
		
		$currentDir = $compGuiDir;
		$currentUrl = $compGuiUrl;
		// $currentUrl = 'gui/';

		$baseCss = $currentUrl.'assets/css/base.css';
		$styleCss = $currentUrl.'assets/css/style.css';
		$responsibleCss = $currentUrl.'assets/css/responsible.css';
		$baseJs = $currentUrl.'assets/js/base.js';

		$html = self::getHtml('setup-login');
		/*if(CompSetupAccess::ifaccessexist() == true){
			$session = $this->session();

			if($session){
				
				$html = self::getHtml('setup-login');
			}
			else{
				$html = self::getHtml('setup-logon');
			}
		}else{*/
			// $html = self::getHtml('setup-register');
		// }

		$html = str_replace('{current:url}', $currentUrl, $html);
		$html = str_replace('{title}', 'Suite', $html);
		$html = str_replace('{suite:version}', suite_version, $html);

		

		// return array('response'=>'123');

		return array(
			// 'html'=>$html,
			'response'=>$html,
			'type'=>'text/html',

			'register-overwrite'=>true,
			/*'replace'=>array(
				'{title}'=>'Suite'
			),*/
			'register'=>array(						
				// array('link'=>array('appManager','parameters'=>array('href'=>$responsibleCss,'rel'=>'stylesheet'),'into'=>'head')),								
				// array('link'=>array('appManager','parameters'=>array('href'=>$baseCss,'rel'=>'stylesheet'),'into'=>'head')),								
				// array('script'=>array('parameters'=>array('type'=>'text/javascript'),'content'=>'alert(2);','into'=>'head')),												
			)	
		);	

		return array('response'=>'123');
	}

	






	public static function getHtml($tpl = null){

		// $currentDir = Suite_globals::get('current/dir');
		// $currentUrl = Suite_globals::get('current/url');	
		$coreDir = Suite_globals::get('core/dir');

		// $currentDir .= 'gui/';
		// $currentUrl .= 'gui/';

		$componentsDir = $coreDir.'components/';
		$compGuiDir = $componentsDir.'setup/gui/';

		$first_htmlFile = $compGuiDir.'assets/html/'.$tpl.'.html';
		
		if(file_exists($first_htmlFile))
			return file_get_contents($first_htmlFile);

		return false;
	}

}