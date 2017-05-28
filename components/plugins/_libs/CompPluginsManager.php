<?php

class CompPluginsManager{

	/**
	 * [authenticate description]
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function login($args){
		$username = isset($args['username'])?$args['username']:null;
		$password = isset($args['password'])?$args['password']:null;

		$result = Suite_manager::login(array(
				'username' => $username,
				'password' => $password,	
				'data'=>array(
					'date-logon'=>Date("Y/m/d H:i:s")
				)
			));
		return $result;
	}

	/**
	 * [authenticate description]
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function logout(){
		$result = Suite_manager::logout();
		return $result;
	}


	/**
	 * [managerAccess description]
	 * @return [type] [description]
	 */
	public function managerUrlAccess(){
		
		$currentDir = Suite_globals::get('current/dir');
		$currentUrl = Suite_globals::get('current/url');

		

		$appDir = Suite_globals::get('app/dir');
		$pluginDir = $appDir . '_plugins/';

		$action = Suite_globals::get('http/action');
		$prefix = Suite_globals::get('http/prefix');
		$posfix = Suite_globals::get('http/posfix');

		$act = $prefix.'/'.$action.'/'.$posfix;
		

		$prefixArray = explode('/', $act);
		$prefixArray = array_filter($prefixArray);
		$prefixArray = array_values($prefixArray);
		
	
		$firstPrefix = isset($prefixArray[0])?$prefixArray[0]:null;
	
		if($firstPrefix == null) return null;
		

		$lastPrefix = $prefixArray[count($prefixArray)-1];		
		unset($prefixArray[0]);
		$prefixArray = array_filter($prefixArray);	
		$pluginActions = implode('/', $prefixArray);

		

		if($firstPrefix == '_plugins-manager'){
			$pluginActionsDir = $pluginDir .$pluginActions;			
			
			$resultAction = Suite_class::load($pluginActionsDir);			
			$control = $resultAction['control'];
			$actionName = $lastPrefix.'Action';
			
			$resultStatus = Suite_manager::status();

			if($resultStatus->logged == true)
				$managerReturn = $this->managerPage($currentDir,$currentUrl,$pluginActions,true);
			else
				$managerReturn = $this->managerPage($currentDir,$currentUrl,$pluginActions);

			return $managerReturn;	
			
		}

	}

	/**
	 * monta a página de gerenciamento do plugin
	 * @param  [type]  $currentDir    [description]
	 * @param  [type]  $currentUrl    [description]
	 * @param  [type]  $pluginActions [description]
	 * @param  boolean $logged        [description]
	 * @return [type]                 [description]
	 */
	public function managerPage($currentDir,$currentUrl,$pluginName,$logged = false){
		
		$resultStatus = Suite_manager::status();

		if($logged == true)
			$managerHtmlFile = $currentDir . 'assets/html/' . 'manager-online.html';
		else
			$managerHtmlFile = $currentDir . 'assets/html/' . 'manager-offline.html';

		if($pluginName == null && $logged == true)
			$managerHtmlFile = $currentDir . 'assets/html/' . 'manager-nopluginsel.html';

		if(file_exists($managerHtmlFile))
			$managerHtml = file_get_contents($managerHtmlFile);
		else
			$managerHtml = '';

		$pluginVersion = null;
		$pluginManagerContent = '';
		
		if($pluginName){
			$PluginManager = $this->getPluginManager($pluginName);
			$returnPluginManager = $PluginManager['control']->setup($_REQUEST);
			$pluginManagerContent = isset($returnPluginManager['response'])?$returnPluginManager['response']:'';

			$resultPlugin = $this->getPlugin($pluginName);
			
			$options = $resultPlugin['options'];
			/*echo '<pre>';
			print_r($options['options']);
			echo '</pre>';*/

			// $options = json_decode($resultPlugin['options'],true);
			$pluginVersion = isset($options['version'])?$options['version']:null;
		



			$pluginManagerArea = $this->pluginManagerArea($pluginName,$returnPluginManager);

			$managerHtml = str_replace('{plugin:name}',$pluginName , $managerHtml);
			$managerHtml = str_replace('{plugin:version}',$pluginVersion , $managerHtml);
			$managerHtml = str_replace('{plugin:content}',$pluginManagerContent , $managerHtml);
			$managerHtml = str_replace('{plugin:managerarea}',$pluginManagerArea , $managerHtml);
			
			
		}

		
		$returnPluginManager['response'] = $managerHtml;
			$returnPluginManager['type'] = 'text/html';
		
		$designCss = $currentUrl . 'assets/plugins/design/' . 'design.css';
		$designElementsCss = $currentUrl . 'assets/plugins/design/' . 'design-elements.css';
		$designMenuCss = $currentUrl . 'assets/plugins/design/' . 'design-menu-side.css';
		$designResponsiveCss = $currentUrl . 'assets/plugins/design/' . 'design-responsive.css';
		$styleCss = $currentUrl . 'assets/css/' . 'style.css';
		$pluginsManagerJs = $currentUrl . 'assets/js/' . 'plugins-manager.js';
		$designJs = $currentUrl . 'assets/plugins/design/' . 'design.js';
		$simplifyFormJs = $currentUrl . 'assets/plugins/simplifyForm/' . 'simplifyForm.js';
		
		$itabsCss = $currentUrl . 'assets/plugins/itabs/' . 'itabs.css';
		$itabsJs = $currentUrl . 'assets/plugins/itabs/' . 'itabs.js';
		
		$suitePluginManagerJs = $currentUrl . 'assets/js/' . 'suitePluginManager.js';

		$register = array(			
			array('script'=>array('parameters'=>array('src'=>$suitePluginManagerJs,'type'=>'text/javascript'),'into'=>'head')),

			array('script'=>array('name'=>'simplifyForm','parameters'=>array('src'=>$simplifyFormJs,'type'=>'text/javascript'),'into'=>'footer','version'=>'1.2')),
			
			// design
			array('link'=>array('parameters'=>array('href'=>$designCss,'rel'=>'stylesheet'),'into'=>'footer')),			
			array('link'=>array('parameters'=>array('href'=>$designElementsCss,'rel'=>'stylesheet'),'into'=>'footer')),			
			array('link'=>array('parameters'=>array('href'=>$designMenuCss,'rel'=>'stylesheet'),'into'=>'footer')),			
			array('link'=>array('parameters'=>array('href'=>$designResponsiveCss,'rel'=>'stylesheet'),'into'=>'footer')),						
			array('script'=>array('name'=>'simplifyForm','parameters'=>array('src'=>$simplifyFormJs,'type'=>'text/javascript'),'into'=>'footer')),
			array('script'=>array('parameters'=>array('src'=>$designJs,'type'=>'text/javascript'),'into'=>'footer')),
			
			array('link'=>array('parameters'=>array('href'=>$styleCss,'rel'=>'stylesheet'),'into'=>'footer')),			
			array('script'=>array('parameters'=>array('src'=>$pluginsManagerJs,'type'=>'text/javascript'),'into'=>'footer')),
			

			array('link'=>array('name'=>'itabs','parameters'=>array('href'=>$itabsCss,'rel'=>'stylesheet'),'into'=>'footer')),			
			array('script'=>array('name'=>'itabs','parameters'=>array('src'=>$itabsJs,'type'=>'text/javascript'),'into'=>'footer')),
			
		);

		if(!isset($returnPluginManager['register']) || !is_array($returnPluginManager['register']))
			$returnPluginManager['register'] = array();

		foreach ($register as $key => $value) {
			$returnPluginManager['register'][] = $value;
		}

		
		// $returnPluginManager['register-overwrite'] = true;

		return $returnPluginManager;
	}

	public function pluginManagerArea($pluginName,$return){
		$html = '';
		$elements = isset($return['plugin-manager'])?$return['plugin-manager']:null;

		foreach ($elements as $key => $value) {
			$name = $key;
			$type = isset($value['type'])?$value['type']:'button';
			$title = isset($value['title'])?$value['title']:null;			
			$list = isset($value['list'])?$value['list']:null;			
			$class = isset($value['class'])?$value['class']:null;			
			$href = isset($value['href'])?$value['href']:null;
			if($href) $href = ' href="'.$href.'" ';
			if($class) $class = ' class="'.$class.'" ';

			

			if($type == 'button')
				$html .= '<li name="plugin-manager-li-'.$name.'" '.$class.'  ><a name="plugin-manager-'.$name.'" '.$href.' >'.$title.'</a></li>'."\n";

			if($type == 'list'){
				$optionsLi = '';

				$html .= '<li name="plugin-manager-li-'.$name.'" '.$class.' ><select name="plugin-manager-'.$name.'" >';

				$html .= '<option value="" selected disabled>'.$title.'</option>';

				if(count($list)>0)
				foreach ($list as $key => $value) {
					$optionsLi .= '<option value="'.$key.'">'.$value.'</option>';
				}				
				$html .= $optionsLi.'</select></li>'."\n";

				// $html .= '<li name="plugin-manager-li-'.$name.'" '.$class.' ><select name="plugin-manager-'.$name.'" >'.$optionsLi.'</select></li>'."\n";
				// $html .= '<li name="plugin-manager-li-'.$name.'" '.$class.' ><label>'.$title.'</label><select name="plugin-manager-'.$name.'" >'.$optionsLi.'</select></li>'."\n";
			}
		}
		return $html;
	}


	/**
	 * captura o retorno do metodo manager do plugin
	 * @param  [string] $pluginName [nome do plugin]
	 * @return [type]                [description]
	 */	
	public function getPluginManager($pluginName){
		$listPlugins = CompPlugins::getPlugins($pluginName);

		$pluginDir = $listPlugins[0]['dir'];
		$pluginDir = $pluginDir . '_manager/';		
		$resultPlugin = Suite_class::load($pluginDir);		
		return $resultPlugin;		
	}

	/**
	 * captura o retorno do metodo manager do plugin
	 * @param  [string] $pluginName [nome do plugin]
	 * @return [type]                [description]
	 */
	public function getPlugin($pluginName){
		$listPlugins = CompPlugins::getPlugins($pluginName);

		$pluginDir = $listPlugins[0]['dir'];
		$pluginDir = $pluginDir ;		
		$resultPlugin = Suite_class::load($pluginDir);	
		return $resultPlugin;		
	}
}