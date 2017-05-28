<?php 
class CompPlugins {

	public $widgetsParametersJoin = array();
	public $resultWidgetsAll = array();
	public $CountWidget;

	public static function getData($options = null){
		
		if($options == null) return null;

		$pluginData = null;
		$pluginName = isset($options['plugin'])?$options['plugin']:null;
		$fileName = isset($options['file'])?$options['file']:'options.json';
		$fileType = isset($options['type'])?$options['type']:'json';

		if($pluginName == null) return null;

		$appDir = Suite_globals::get('app/dir');
		$pluginsFileData = $appDir . '_data/plugins/'.$pluginName.'/'.$fileName;

		if(file_exists($pluginsFileData)){
			$pluginDataContent = file_get_contents($pluginsFileData);
			if($fileType == 'json')
				$pluginData = json_decode($pluginDataContent,true);
			else
				$pluginData = $pluginDataContent;
		}

		return $pluginData;
	}


	public static function setData($options = null){
		if($options == null) return null;

		$pluginData = null;
		$pluginName = isset($options['plugin'])?$options['plugin']:null;
		$fileName = isset($options['file'])?$options['file']:'options.json';
		$fileType = isset($options['type'])?$options['type']:'json';
		$pluginDataContent = isset($options['content'])?$options['content']:'';

		if($pluginName == null) return null;

		$appDir = Suite_globals::get('app/dir');
		$pluginsFileDataDir = $appDir . '_data/plugins/'.$pluginName.'/';
		if(!file_exists($pluginsFileDataDir))
			mkdir($pluginsFileDataDir,0777,true);

		$pluginsFileData = $pluginsFileDataDir.$fileName;		

		if($fileType == 'json' && is_array($pluginDataContent)){
			$pluginDataContent = json_encode($pluginDataContent);
		}

		file_put_contents($pluginsFileData,$pluginDataContent);					

	}



	public static function delData($options = null){
		if($options == null) return null;

		$pluginData = null;
		$pluginName = isset($options['plugin'])?$options['plugin']:null;
		$fileName = isset($options['file'])?$options['file']:'options.json';
		$fileType = isset($options['type'])?$options['type']:'json';
		$pluginDataContent = isset($options['content'])?$options['content']:'';

		if($pluginName == null) return null;

		$appDir = Suite_globals::get('app/dir');
		$pluginsFileDataDir = $appDir . '_data/plugins/'.$pluginName.'/';

		Suite_libs::run('Files/Scan/rmdir',$pluginsFileDataDir);
	

	}


	public static function execute($action,$args = null){
		$resultAction = null;
		
		$controllerArray = explode('/', $action);
		$actionName = $controllerArray[count($controllerArray)-1];
		unset($controllerArray[count($controllerArray)-1]);
		$controller = implode('/', $controllerArray);


		$appDir = Suite_globals::get('app/dir');
		$pluginDir = $appDir . '_plugins/'.$controller;
		$resultAction = Suite_class::load($pluginDir);

		$control = $resultAction['control'];

		if(method_exists($control, $actionName))
			$resultAction = $control->$actionName($args);
		
		return $resultAction;

	}

	public static function directAction(){
		$appDir = Suite_globals::get('app/dir');
		$appUrl = Suite_globals::get('app/url');
		$domainUrl = Suite_globals::get('http/domain/url');
		$pluginDir = $appDir . '_plugins/';
		$pluginUrl = $domainUrl . '_plugins/';

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
		// unset($prefixArray[count($prefixArray)]);

		$pluginActions = implode('/', $prefixArray);

		

		if($firstPrefix == '_plugins'){
			$pluginActionsDir = $pluginDir .$pluginActions;			
			$pluginActionsUrl = $pluginUrl .$pluginActions;			
			
			
			if(file_exists($pluginActionsDir)){
				$resultAction = Suite_class::load($pluginActionsDir);			
				$control = $resultAction['control'];
				$actionName = $lastPrefix.'Action';
			}
				
			if(isset($control) && method_exists($control, $actionName)){
				$args = $_REQUEST;
				return $control->$actionName($args,null);
			}else{
				


				$pluginActionsDirArray = explode('/',$pluginActionsDir);				

				$join = '';
				$afteraction = '';
				$joinArray = array();
				foreach ($pluginActionsDirArray as $key => $value) {					
					$join .= $value.'/';					
					$action = isset($pluginActionsDirArray[$key+1])?$pluginActionsDirArray[$key+1]:null;
					
					$joinArray[] = array('path'=>$join,'action'=>$action);
				}
				$joinArray = array_reverse($joinArray);

				// print_r($joinArray);

				foreach ($joinArray as $key => $value) {
					$dirTry = $value['path'];
					$action = isset($value['action'])?$value['action']:null;
					if($action != null ){
						$afteraction = $action.'/' .$afteraction;
						$afteraction = str_replace('//', '/', $afteraction);
					}
					// echo $afteraction."\n";

					if(file_exists($dirTry)){
						$resultAction = Suite_class::load($dirTry);			
						$control = $resultAction['control'];
						$actionName = $action.'Action';
						
						if(isset($control) && method_exists($control, $actionName)){							
							return $control->$actionName($_REQUEST,$afteraction);
						}
					}else{
						
						
					}
				}
				

				return null;
			}
		}
	}


	


	public static function config(){
		$appDir = Suite_globals::get('app/dir');		
		$pluginsDir = $appDir . '_data/plugins/config.json';

		if(file_exists($pluginsDir)){
			$pluginsConfig = file_get_contents($pluginsDir);
			$pluginsObj = json_decode($pluginsConfig,true);
			return $pluginsObj;		
		}
		return null;
	}


	public function render($html = null){

		$pluginsList = self::getPlugins();

		
				
		if(isset($pluginsList)){

			foreach ($pluginsList as $key => $value) {

				$name = $value['name'];
				$dir = $value['dir'];
				$config = $value['config'];

				$enabled = isset($config['enabled'])?$config['enabled']:false;
				if($enabled == true){
					$resultPlugin = Suite_class::load($dir,'plugin');

					if(method_exists($resultPlugin['control'], "render")){														
						$html = $resultPlugin['control']->render($html);						
					}
				}

				
	
				
			}
		}


		return $html;
	}


	public function prepareError($args){

		$name = $args['name'];
		$dir = $args['dir'].DIRECTORY_SEPARATOR;
		$url = $args['url'].'/';
		
		if(class_exists('Suite_error'))
		Suite_error::setContext(array(
			'plugin'=>array(
				'name'=>$name,
				'path'=>$dir
			)
		));
		/*		

		$contentErrorsLastAction = $_COOKIE['suite_error'];
		$contentErrorsLastActionObj = json_decode($contentErrorsLastAction,true);	
		$newArray = array(
			'plugin'=>array(
				'name'=>$name,
				'path'=>$dir
			)
		);		
		$contentErrorsLastActionObj = array_merge($contentErrorsLastActionObj,$newArray);			
		setcookie('suite_error',json_encode($contentErrorsLastActionObj),0,'/'," ");*/
	}

	public function load(){
		$pluginsList = self::getPlugins();
		$resultLoadAll = Array();
		
				
		if(isset($pluginsList)){

			foreach ($pluginsList as $key => $value) {

				$name = $value['name'];
				$dir = $value['dir'];
				$url = $value['url'];
				$config = $value['config'];



				$this->prepareError($value);


				$enabled = isset($config['enabled'])?$config['enabled']:false;
				if($enabled == true){
					
					$resultPlugin = Suite_class::load($dir,'plugin');

					if(method_exists($resultPlugin['control'], "load")){	

						Suite_globals::set('current/dir',$dir);
						Suite_globals::set('current/url',$url);
					
						$resultAction = $resultPlugin['control']->load();
						$resultLoadAll[] = $resultAction;

						

						if(class_exists('CompAssets')) 
							CompAssets::actionRegister($resultAction,'plugin_'.$name);
					}
				}

				
	
				
			}

			

			// Suite_globals::set('plugins/load',$resultLoadAll);
		}

		return $resultLoadAll;
	}

public static function getPlugins($pluginName = null){
	
	$data = null;

	$appDir = Suite_globals::get('app/dir');
	$appUrl = Suite_globals::get('app/url');
	$pluginsDir = $appDir . '_plugins/';
	$pluginsUrl = $appUrl . '_plugins/';

	$configPlugins = self::config();


	if($pluginName == null){
		$result = Suite_libs::run('Files/Scan/onlyDir',$pluginsDir);
	}else{
		
		$optionsFile = $pluginsDir.$pluginName."/options.json";
		if(file_exists($optionsFile)){
			$optionsJson = file_get_contents($optionsFile);
			$optionsObj = json_decode($optionsJson,true);

			$name = isset($optionsObj['name'])?$optionsObj['name']:null;

			$result[0] = array(
				'name' => $pluginName,
				'dir' => $pluginsDir.$pluginName.DIRECTORY_SEPARATOR,
				'url' => $pluginsUrl.$pluginName."/",
				'options' => $optionsObj,
				// 'config' => $configPlugins
			);
		}

		return $result;	
	}
	
	
	$index = 0;
	if(count($result)>0)
	foreach ($result as $key => $value) {
		$name = $value;
		$data[$index]['name'] = $name;
		$data[$index]['dir'] = $pluginsDir.$name.DIRECTORY_SEPARATOR;
		$data[$index]['url'] = $pluginsUrl.$name."/";

		$options = null;
		$optionsDir = $pluginsDir.$value.'/options.json';
		if(file_exists($optionsDir)){
			$optionsJson = file_get_contents($optionsDir);
			$options = json_decode($optionsJson,true);
		}

		$data[$index]['options'] = $options;
		

		$data[$index]['config'] = isset($configPlugins[$name])?$configPlugins[$name]:null;
		$index++;
	}

	return $data;
}


/**
 * converte string html para array
 * @param  [type] &$html [string]
 * @param  [type] &$join [description]
 * @param  [type] $index [description]
 * @return [type]        [array]
 */
public function html2array ( &$html = 'null',&$join = array(),$index = null) {  
  $pattern = '@\[\s*?(\w+)((?:\b(?:\'[^\']*\'|"[^"]*"|[^\]])*)?)\]((?:(?>[^\[]*)|(?R))*)(\[\/\s*?\\1(?:\b[^\]]*)?\])?|\[\s*(\w+)(\b(?:\'[^\']*\'|"[^"]*"|[^\]])*)?\/?\]@uxis'; 

  if ( !preg_match_all( $pattern, $html = trim($html), $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) )
    return $html; 


  $i = 0;
  $ret = array();
  foreach ($m as $set) {

    

    if ( strlen( $val = trim( substr($html, $i, $set[0][1] - $i) ) ) )
      $ret[] = $val;

    if($index == null)$index = 0;

    $join[] = ($set[0][0]);
   
     
    $index++;

    $val = $set[1][1] < 0 ? array( 'tag' => ($set[4][0]) ) : array( 'tag' => strtolower($set[1][0]), 'val' => $this->html2array($set[3][0],$join,$index) );
    if ( preg_match_all( '/(\w+)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|(\w+)))?/usix', isset($set[5]) && $set[2][1] < 0 ? $set[5][0] : $set[2][0],$attrs, PREG_SET_ORDER ) ) {
      foreach ($attrs as $a) {
        $val['attr'][$a[1]]=$a[count($a)-1];
      }
    }
    $ret[] = $val;
    $i = $set[0][1]+strlen( $set[0][0] );
   
  }

  $l = strlen($html);
  if ( $i < $l )
    if ( strlen( $val = trim( substr( $html, $i, $l - $i ) ) ) )
      $ret[] = $val;

  
   return $ret;
}


	public function tagToWidgets($html = null, $loadMasterReturn = null,$valueAction = null){

		// View::$widgetsParameters = null;
		// $views = Globals::get('app/html');
		
		

		// View::$parametersHttpRequest = $parameters;
		// $views = array('html'=>$html);
		
		$views = array('html'=>$html);

		$mc = array();

				 $foundWidget = false;
				foreach ($views as $key => &$value) {		
				$m = null;		
					$string = $views[$key];	


					$result = $this->html2array($string,$m);

					
					
					if($m == null)continue;
				
					$m2 = $m;

				
					
					foreach ($m as $key2 => $value2) {	
						
						
						$content = $this->getContent($value2,$loadMasterReturn,$valueAction);

						if($content !== false)
							$string = str_replace($value2, $content, $string);
						$m2 = null;		
						$result = $this->html2array($string,$m2);


					}

					

					/*if($m2 != null)	{
						foreach ($m2 as $key3 => $value3) {	
								
								$content = $this->getContent($value3,$loadMasterReturn,$valueAction);
								if($content !== false)
									 $string = str_replace($value3, $content, $string);	
							


						 }

						
						 
					}*/
					
						
					$foundWidget = true;
					$views[$key] = $string;						
					
				 }	



			
			/*$register = CompRegister::getHtml();	
			
			$registerHead = $register['head'];
			$registerFooter = $register['footer'];	*/

			

			// Suite_globals::set('plugins/widgets',$this->resultWidgetsAll);

				
			if($foundWidget == true){
				
				return array('html'=>$views['html'],'script'=>array(
						// 'head'=>$registerHead,
						// 'footer'=>$registerFooter,
					));
			}else{
				return '';
			}
		


				
	}






	/**
 * captura conteúdo widget baseado na tag widget
 * @param  [type] $string [tag widget]
 * @return [type]         [string html]
 */
public function getContent($string,$loadMasterReturn,$valueAction){
	$matchesOri[0] = $string;


	$result = $this->html2array($string,$m);
	$tagGet = 'widget';
	$html = '';
		
	

		if($result[0]['tag'] == $tagGet){
				
				$html = $string;
				
				$pluginName = isset($result[0]['attr']['plugin'])?$result[0]['attr']['plugin']:null;
				$nameW = isset($result[0]['attr']['name'])?$result[0]['attr']['name']:null;
				$idPartialW = isset($result[0]['attr']['id'])?$result[0]['attr']['id']:'widget_'.$this->CountWidget;
				
						

				$this->CountWidget++;

				if(is_array($result[0]['val'])){				
					
					$string = preg_replace('/\['.$tagGet.'.*?\](.*?)\[\/'.$tagGet.'\]?/is','$1',$string);

					$contentWidget = $string;
				
				}else{	
					$contentWidget = isset($result[0]['val'])?$result[0]['val']:'';
				}
				
				
				

				$shortcode = $string;

				$parOpt["plugin_name"] = $pluginName;
				$parOpt["widget_name"] = $nameW;
				$parOpt["widget_id"] = $idPartialW;
				$parOpt["widget_attr"] = $result[0]['attr'];
				unset($parOpt["widget_attr"]['id']);
				unset($parOpt["widget_attr"]['plugin']);
				unset($parOpt["widget_attr"]['name']);
				$parOpt["widget_content"] = $contentWidget;
				

			
				
				$pluginsList = self::getPlugins();

				/*echo '<pre>';
				print_r($pluginsList);
				echo '</pre>';*/
				
				if(isset($pluginsList)){

					foreach ($pluginsList as $key => $value) {

						if($value['name'] == $pluginName){			
							$pluginDir = $value['dir'];
							
							$config = $value['config'];

							$enabled = isset($config['enabled'])?$config['enabled']:false;
							if($enabled != true)
								continue;

							$resultPlugin = Suite_class::load($pluginDir);
							


							
									
							if(method_exists($resultPlugin['control'], "widget")){							
									
								$resultWidgets = $resultPlugin['control']->widget($parOpt);
								
								$this->resultWidgetsAll[] = $resultWidgets;


								if(class_exists('CompAssets'))
								CompAssets::actionRegister($resultWidgets,'pluginWidget_'.$pluginName);

								if($resultWidgets == null || $resultWidgets == false)
									return $matchesOri[0]; 
								
								 $html = isset($resultWidgets["html"])?$resultWidgets["html"]:'';
						
								
								Suite_globals::set('plugins/view',$html);
								
							}
						}
						
					}
				}
				
			
				return $html;
				
		}

		
		return false;
		
}




}