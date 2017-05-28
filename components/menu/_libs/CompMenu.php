<?php

class CompMenu{
	
	public static $add = Array();

	private static $controlerDir = '_hmvc/';

	public  function render($html){
		$html = $this->shortcode($html);
		// $html = $this->app($html);
	
		return $html;
	}

	


	public  function app($html){

		// $html = str_replace('[app.*:title]', 'replace', $html);

		return $html;
	}


	public  function shortcode($html){

    // procura pela tag [menu] e substitui pelo menu identificado
      $menuFunction = create_function('$matches','   

        $value0 = isset($matches[0])?$matches[0]:null;      
        $value1 = isset($matches[1])?$matches[1]:null;      
        $value2 = isset($matches[2])?$matches[2]:null;      
        $value3 = isset($matches[3])?$matches[3]:null;              
        $value4 = isset($matches[4])?$matches[4]:null;              
        $value5 = isset($matches[5])?$matches[5]:null;              
        
        $value1 = substr($value1, 1);
        $value2 = substr($value2, 1);
        $value3 = substr($value3, 1);     
        $value4 = substr($value4, 1);     
        $value5 = substr($value5, 1);     

        

        $result = "";     
       	
       	if($value2){       		

        	$result = CompMenu::getMenuFromDyn($value2,$value3);

       	}else{
       		
       		if($value3){       			
       			$result = CompMenu::getMenuData($value1,$value3);       			
       		}else{
        		$menuArray = CompMenu::getMenuFromModule($value1);
        		$result = CompMenu::makeMenu($menuArray);
       		}
        }

        return $result;
      ');
      $html = preg_replace_callback('/\[menu(\/.*?)?(\..*?)?(:.*?)?(\=.*?)?(\@.*?)?\]/is',$menuFunction,$html);
      
      return $html;
  }



  public static function getMenuDyn($baseMenu = null){
	$appDir = Suite_globals::get('app/dir');  	
  	$configDir = $appDir . '_data/menu/';  	  	
  	$menuDynFile = $configDir . 'config.json';

  	if(file_exists($menuDynFile)){
  		$menuDynJSon = file_get_contents($menuDynFile);
  		$menuDyn = json_decode($menuDynJSon,true);
  		
  		if($baseMenu != null)
  			$menuDynCurrent = isset($menuDyn[$baseMenu])?$menuDyn[$baseMenu]:null;
  		else
  			$menuDynCurrent = $menuDyn;
  		return $menuDynCurrent;
  	}

  	return array();
  }

  public static function getMenuFromModule($val){

  	

  	$html = '';

  	$appDir = Suite_globals::get('app/dir');
  	$modulesDir = $appDir . self::$controlerDir.DIRECTORY_SEPARATOR;

  
  	$modulesMenuArray = array();
  	$menuArray = Suite_components::storage('menu');
  	
  	
		
  	foreach ($menuArray as $key => $value) {
  		foreach ($value as $key2 => $value2) {  
  			$modulesMenuArray[$key2] = Suite_components::storage('menu/'.$key."/".$key2);
  		}  		
  	}
  	
  	$menuArray = $modulesMenuArray;

	$dynMenu = self::getMenuDyn('modules');
  	
  	if($dynMenu)
  		$menuArray = array_merge_recursive($menuArray,$dynMenu);
  	



	// ordena menu
	$funcOrder = create_function('$funcOrder,$array', '
		$data = Array();

			// ordena por prioridade no options
		
			if(count($array) >0){
			    $price = array();
			    foreach ($array as $key => $row){    
			        $price[$key] = isset($row["@priority"])?$row["@priority"]:100;
			    }
			    array_multisort($price, SORT_ASC, $array);
			}
		
		

		    if(count($array) >0)
		foreach ($array as $key => $value) {
			
			// ordena por prioridade no options
			$price = array();
			if(is_array($value) && count($value)>0){
			    foreach ($value as $key2 => $row2){    
			        $price[$key2] = isset($row2["@priority"])?$row2["@priority"]:100;
			    }
			    array_multisort($price, SORT_ASC, $value);
			}


			if(substr($key, 0,1)=="@")continue;

			if(is_array($value)){	  	
				$array = $funcOrder($funcOrder,$value);				
				$data[$key] = $value;
			}else{
				$data[$key] = $value;
			}
			

		}	

		return $data;
	');


	$menuArray = $funcOrder($funcOrder,$menuArray);


  	$resultDyn = self::getMenuDyn('modules');
  	$menuArray = self::array_merge_recursive_ex($menuArray,$resultDyn);
  	

  	$menuArray = self::array_merge_recursive_ex($menuArray,self::$add);
	
  
	

  	return $menuArray;
  }





  	public static  function add($target,$array){
  		$targetArray = explode('/', $target);

 		$m = Array();

  		$join = '';
		$joinBase = '';
		if(count($targetArray) >0)
		foreach ($targetArray as $key => $value) {
			$joinBase .= $value.'/';		
			$join .= '["'.$joinBase.'"]';
			$join = str_replace('/"]', '"]', $join);
		}
  		
  		eval('if(!isset(self::$add'.$join.'))self::$add'.$join.' = $array;');
	  		
		
	}
 


  public static function replacePrefix($prefix){

  	$menuArray = self::getMenuDyn();

  	$rec = create_function('$rec,$prefix,$menuArray','
  		$val = false;
  		foreach ($menuArray as $key => $value) {
  			if(substr($key, 0,1)=="@")
  				continue;

  			if($key == $prefix){
  				$val = $key;
  				return $val;  				
  			}
  			

  			if(is_array($value)){
  				$val = $rec($rec,$prefix,$value);
  				if($val) return $val;
  			}
  		}



  		return $val;
  	');

  	$found = $rec($rec,$prefix,$menuArray);
  	

  	if($found) 
  		return "";

  	return $prefix;
  }

  public static function makeMenu($menuArray){
  

  	// desenha menu
  	$func = create_function('$func,$array,$nivelMenu = 0', '
  		$domain = Suite_globals::get("http/domain/url");
  		


  		$action = Suite_globals::get("http/action");
  		$prefix = Suite_globals::get("http/prefix");
  		$posfix = Suite_globals::get("http/posfix");
  		$target = Suite_globals::get("http/target");
  		$appForce = Suite_globals::get("http/app");
  		
  		
  		$targetNew = "";

  		

  		$prefix = CompMenu::replacePrefix($prefix);

  		$langCurrent = "";  		
  		$targetArray = explode("/", $target);
  		if(in_array($prefix, $targetArray)){
  			$langCurrent = $prefix;
  		}

  		if($targetArray[0]==$prefix){
  			$targetNew = $targetArray;
  			unset($targetNew[0]);
  			$targetNew = implode("/", $targetNew);
  		}


  		

  		

  		if(!$targetNew)
  			$targetNew = $target;
  		
  		

  		if($appForce)
  		$appForce = ":".$appForce."/";

  		$html = "<ul data-nivel=\"".$nivelMenu."\">";
  		if(count($array) >0)
	  	foreach ($array as $key => $value) {

	  		if(substr($key, 0,1)=="@")continue;

	  		
			$url = $key;	  		
			if(substr($url, 0,1)=="/")
				$url = substr($url, 1);
			

			$urlPrefix = $appForce.$langCurrent."/".$url;			
			$urlPrefix = str_replace("//", "/", $urlPrefix);

			if(substr($urlPrefix, 0,1)=="/")
				$urlPrefix = substr($urlPrefix, 1);

	  		$link = $domain.$urlPrefix;

	  		

	  		$statusCurrent = "";
	  		$targetHtml = "";

	  		
		
	  		
	  	  		
	  		
	  		if($targetNew)
	  			$actionAdjust = $targetNew."/".$posfix;
	  		else
	  			$actionAdjust = $action."/".$posfix;

	  		if(substr($actionAdjust, strlen($actionAdjust)-1,strlen($actionAdjust))=="/")
	  			$actionAdjust = substr($actionAdjust, 0,strlen($actionAdjust)-1);


	  		$actionAdjustArray = explode("/", $actionAdjust);
	  		$join = "";
	  		foreach ($actionAdjustArray as $keyAJ => $valueAJ) {
	  			$join .= $valueAJ."/";
	  			$join = str_replace("//", "/", $join);
	  			
	  			if(substr($join, 0,1)=="/")
	  			$join = substr($join, 1);

	  			if($join == $url."/")
	  				$statusCurrent = " data-status=\"active\" ";
	  		}

	  		if(substr($actionAdjust, 0,1)=="/")
	  			$actionAdjust = substr($actionAdjust, 1);

	  		if(substr($actionAdjust, strlen($actionAdjust)-1,strlen($actionAdjust))=="/")
	  			$actionAdjust = substr($actionAdjust, 0,strlen($actionAdjust)-1);


	  		
				

	  		if( $actionAdjust  == $url )
	  			$statusCurrent = " data-status=\"current\" ";


	  		

	  		if(is_array($value)){	  		
	  			$preTitleArray = explode("/", $key);	  			
	  			$preTitle = end($preTitleArray);

	  			if(substr($preTitle, 0,1)=="_")
	  				continue;

	  			$title = isset($value["@title"])?$value["@title"]:$preTitle;
	  			$urlTarget = isset($value["@url"])?$value["@url"]:null;
	  			$target = isset($value["@target"])?$value["@target"]:null;
	  			$visible = isset($value["@visible"])?$value["@visible"]:true;
	  			$content = isset($value["@content"])?$value["@content"]:null;
	  			$enabled = isset($value["@enabled"])?$value["@enabled"]:true;

	  			if($content != null){	  				
	  				// Suite_globals::set("render/view",$content);
	  				// Suite_view::view($content);
	  			}
	  			

	  			if($visible == false) continue;


	  			if($urlTarget != null) $link = $urlTarget;
	  			if($target != null) $targetHtml = " target=\"".$target."\" ";


	  			// echo $link . "<br>";

	  			$ifChild = CompMenu::checkChild($value);
	  			$dropdown = "";

	  			if($ifChild == true)
	  				$dropdown = "data-dropdown";

	  			$href = " ";

	  			if($enabled === true || $enabled === "true")
	  				$href = "href = \'".$link."\'";

	  			$html .= "<li ".$statusCurrent." ".$dropdown." >";
	  			$html .= "<a  ".$href." ".$targetHtml." ".$statusCurrent." ".$dropdown." >";
		  		$html .= $title;	
		  		$html .= "</a>";
		  		
		  		if($ifChild == true)
	  				$html .= $func($func,$value,$nivelMenu+1);	  			

		  		$html .= "</li>";
	  		}else{

	  			
		  		
		  	}
	  	}
	  	$html .= "</ul>";
	  	return $html;
  	');

  

	$html = $func($func,$menuArray);

	return $html;

  }



   public static function getMenuFromDyn($val,$submenu=null){
  	$menuArray = self::getMenuDyn($val);

	

  	$modulesMenuArray = array();
  	$menuStorageArray = Suite_components::storage('menu');

  	if(isset($menuStorageArray[$val])){


  		if($submenu!= null){  			
  			if(isset($menuStorageArray[$val][$submenu])){
  				
  				$menuArray = $menuStorageArray[$val][$submenu];
  			}
  		}else{
  			$menuArray = $menuStorageArray[$val];
  		}
  	}



  	if($submenu!= null && isset($menuArray[$submenu])){
  		$menuArray = $menuArray[$submenu];
  	}
  
  	$html = self::makeMenu($menuArray);
  	return $html;
  }


  public static function getMenuData($val,$submenu){
  	$result = '';

  	$domain = Suite_globals::get('http/domain/url');  		
	$action = Suite_globals::get('http/action');
	$prefix = Suite_globals::get('http/prefix');
	$posfix = Suite_globals::get('http/posfix');

	$appDir = Suite_globals::get('app/dir');
  	$modulesDir = $appDir . self::$controlerDir.DIRECTORY_SEPARATOR;

		$submenuArray = explode('/', $submenu);
	
		$act = $prefix;

	
		$menu = self::getMenuFromModule('modules');		

		
		$join = '';
		$joinBase = '';
		foreach ($submenuArray as $key => $value) {
			$joinBase .= $value.'/';
		
			$join .= '["'.$joinBase.'"]';
			$join = str_replace('/"]', '"]', $join);
		}

		eval('$m = isset($menu'.$join.')?$menu'.$join.':null;');


		if($m ==null)return '';

		$result = self::makeMenu($m);
	
	

  	return $result;
  }





  public static function  array_merge_recursive_ex(array & $array1, array & $array2){
    $merged = $array1;

    foreach ($array2 as $key => & $value)
    {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
        {
            $merged[$key] = self::array_merge_recursive_ex($merged[$key], $value);
        } else if (is_numeric($key))
        {
             if (!in_array($value, $merged))
                $merged[] = $value;
        } else
            $merged[$key] = $value;
    }

    return $merged;
}


  public static function checkChild($child){
  	$ifChild = false;
  	foreach ($child as $key => $value) {
  		if(substr($key, 0,1)=='@')continue;

  		if(is_array($value))
  			$ifChild = true;
  	}

  	return $ifChild;
  }

}