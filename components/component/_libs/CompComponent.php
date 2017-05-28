<?php

class CompComponent{
	
public $CountWidget = 0;




public function render($html = null){

		$pluginsList = $this->getPlugins();

		
				
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








/**
 * captura conteúdo widget baseado na tag widget
 * @param  [type] $string [tag widget]
 * @return [type]         [string html]
 */
public function getContent($string,$loadMasterReturn,$valueAction){
	$matchesOri[0] = $string;


	$result = $this->html2array($string,$m);
	$tagGet = 'component';
	$html = '';
		
	

		if($result[0]['tag'] == $tagGet){
				
				$html = $string;
								
				$componentName = isset($result[0]['attr']['name'])?$result[0]['attr']['name']:null;
				$nameWidget = isset($result[0]['attr']['widget'])?$result[0]['attr']['widget']:null;				
										

				$this->CountWidget++;

				if(is_array($result[0]['val'])){				
					
					$string = preg_replace('/\['.$tagGet.'.*?\](.*?)\[\/'.$tagGet.'\]?/is','$1',$string);

					$contentWidget = $string;
				
				}else{	
					$contentWidget = isset($result[0]['val'])?$result[0]['val']:'';
				}
				
				
				

				$shortcode = $string;

				$parOpt["component_name"] = $componentName;
				$parOpt["widget_name"] = $nameWidget;
				// $parOpt["widget_id"] = $idPartialW;
				$parOpt["widget_attr"] = $result[0]['attr'];
				unset($parOpt["widget_attr"]['id']);
				unset($parOpt["widget_attr"]['plugin']);
				unset($parOpt["widget_attr"]['name']);
				$parOpt["widget_content"] = $contentWidget;
				

				$listComponents = Suite_globals::get('components/list');
				$actionComponents = Suite_globals::get('components/action');
				
				
			
				if(isset($listComponents)){

					foreach ($listComponents as $key => $value) {

						if($value['name'] == $componentName){			
							$componentDir = $value['dir'];
							$componentUrl = $value['url'];
							
							$options = isset($value['options'])?$value['options']:null;

							$enabled = isset($options['enabled'])?$options['enabled']:false;
							if($enabled != true)
								continue;

							$resultComponent = $actionComponents[$componentName];//Suite_class::load($componentDir);
							
						
							
									
							if(method_exists($resultComponent, "widget")){							
								
								Suite_globals::set('current/dir',$componentDir);
								Suite_globals::set('current/url',$componentUrl);

								$resultWidgets = $resultComponent->widget($parOpt);
									


								$this->resultWidgetsAll[] = $resultWidgets;



								if($resultWidgets == null || $resultWidgets == false)
									return $matchesOri[0]; 
								
								 $html = isset($resultWidgets["html"])?$resultWidgets["html"]:'';
						
								
							}
						}
						
					}
				}
				
				if(class_exists('CompAssets'))
						CompAssets::actionRegister($this->resultWidgetsAll,'componentWidget_'.$componentName);

				return $html;
				
		}

		
		return false;
		
}



public function tagToWidgets($html = null, $loadMasterReturn = null,$valueAction = null){

		
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

					

					if($m2 != null)	{
						foreach ($m2 as $key3 => $value3) {	
								
								$content = $this->getContent($value3,$loadMasterReturn,$valueAction);
								if($content !== false)
									 $string = str_replace($value3, $content, $string);	
							


						 }

						
						 
					}
					
						
					$foundWidget = true;
					$views[$key] = $string;						
					
				 }	



			
				
			if($foundWidget == true){
				
				return array('html'=>$views['html'],'script'=>array(
						// 'head'=>$registerHead,
						// 'footer'=>$registerFooter,
					));
			}else{
				return '';
			}
		


				
	}


}