<?php

class CompTags{

  public static function resolve($html){

    $html = self::suite($html);
    $html = self::condition($html);  
    $html = self::http($html); 
    $html = self::breadcrumb($html); 
    $html = self::app($html);  
    return $html;
  }
	public function render($html = null){
    
		$html = self::suite($html); 
    $html = self::condition($html);			  
    $html = self::http($html);  
		$html = self::breadcrumb($html);	
    $html = self::suite($html); 
    $html = self::app($html);  
		return $html;
	}



  public static function app($html){
     /* 
      $app = Suite_globals::get('app');

      echo '<pre>';
      print_r($app);
      echo '</pre>';
*/
      $appFunction = create_function('$matches','                     
        $value0 = isset($matches[0])?$matches[0]:null;      
        $value1 = isset($matches[1])?$matches[1]:null;                      
        $value2 = isset($matches[2])?$matches[2]:null;    
        
        $value1 = substr($value1, 1);     
        $result = "";

       

        if($value1==null){
          $result = Suite_globals::get("app/".$value2);
        }else{                    
          $result = (Suite_globals::get("app/".$value1."/".$value2));       
        }

         

        return $result;     
      ');

     
        $html = preg_replace_callback('/\[app(.*?):(.*?)\]/is',$appFunction,$html);
        
        return $html;
  }



  public static function http($html = null){

    $menuFunction = create_function('$matches','   
      $value0 = isset($matches[0])?$matches[0]:null;      
      $value1 = isset($matches[1])?$matches[1]:null;      
      $value2 = isset($matches[2])?$matches[2]:null;      
      $value3 = isset($matches[3])?$matches[3]:null;              
      $value4 = isset($matches[4])?$matches[4]:null;              
      $value5 = isset($matches[5])?$matches[5]:null;              

      $result = "";     
     

      $result = Suite_globals::get("http/".$value1);
 

      return $result;
    ');
    $html = preg_replace_callback('/\[http:(.*?)?\]/is',$menuFunction,$html);

    return $html;
  }


  public static function breadcrumb($html){
   
    $domain = Suite_globals::get('http/domain/url');
    $appDir = Suite_globals::get('app/dir');
    $modulesDir = $appDir . '_modules/';

    $prefix = Suite_globals::get('http/prefix');
    $action = Suite_globals::get('http/target');
    $actionArray = explode('/', $action);
    $actionArray = array_filter($actionArray);
    $actionArray = array_values($actionArray);
        

    if($actionArray[0] == $prefix){
      unset($actionArray[0]);
      $actionArray = array_values($actionArray);
      $target = implode('/', $actionArray);
    }


   
    $breadcrumb_html = '<ol itemscope itemtype="http://schema.org/BreadcrumbList">';
    $join = '';
    foreach ($actionArray as $key => $value) {

      $join .= $value.'/';
    
      $title = $value;

      $optionsFile = $modulesDir . $join .  'options.json';
    
      if(file_exists($optionsFile)){
        $optionsJson = file_get_contents($optionsFile);
        $options = json_decode($optionsJson,true);
        $title = isset($options['menu']['title'])?$options['menu']['title']:$title;
      }

      $link = $domain.$join;
      $image = '';
      
      $breadcrumb_html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
      $breadcrumb_html .= '<a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="'.$link.'">';
      $breadcrumb_html .= '<span itemprop="name">'.$title.'</span>';
      // $breadcrumb_html .= '<img itemprop="image" src="'.$image.'" alt="'.$title.'"/>';
      $breadcrumb_html .= '</a>';
      $breadcrumb_html .= '<meta itemprop="position" content="'.$key.'">';
      $breadcrumb_html .= '</li>';

      if($key < count($actionArray)-1)
        $breadcrumb_html .= '›';
      
      
    }
    $breadcrumb_html .= '</ol>';


    
    

    $html = str_replace('[breadcrumb]', $breadcrumb_html, $html);

    return $html;
  }

	public static function suite($html = null){

		$menuFunction = create_function('$matches','   
			$value0 = isset($matches[0])?$matches[0]:null;      
			$value1 = isset($matches[1])?$matches[1]:null;      
			$value2 = isset($matches[2])?$matches[2]:null;      
			$value3 = isset($matches[3])?$matches[3]:null;              
			$value4 = isset($matches[4])?$matches[4]:null;              
			$value5 = isset($matches[5])?$matches[5]:null;              

      $value2 = substr($value2, 1);

      

			$result = "";     
      if($value1 == "view"){
			   if($value2 == "_CURRENT_"){
			  	  return Suite_view::view();
         }else{          
            return Suite_view::content($value2);
         }

      }if($value1 == "app-get"){            
          $appDir = Suite_globals::get("app/dir");
          $htmlDir = $appDir.$value2;
          if(file_exists($htmlDir)){
            $result = Suite_libs::run("Http/Request/includes",$htmlDir);          
          }

      }if($value1 == "globals"){            
        $result = Suite_globals::get($value2);
        if(is_array($result)){          
          $result = print_r($result,true);
        }

        return $result;
      }

			

			return $result;
		');
		$html = preg_replace_callback('/\[suite:(.*?)?(:.*?)?\]/is',$menuFunction,$html);


    if(strpos($html, '[suite')!=false){
      $html = self::suite($html);      
    }

		return $html;
	}

	public static function condition($html){
      
    	// procura pela tag [contition] e verifica a condição para exibir o conteúdo
      $conditionFunction = create_function('$matches','                     
        $value0 = isset($matches[0])?$matches[0]:null;      
        $termo1 = isset($matches[1])?$matches[1]:null;      
        $operator = isset($matches[2])?$matches[2]:null;      
        $termo2 = isset($matches[3])?$matches[3]:null;      
        $retult1 = isset($matches[4])?$matches[4]:null;      
        $retult2 = isset($matches[6])?$matches[6]:null;      
  
      

        if(strtolower($operator) == "like"){
          
          $cond = eval("return strpos($termo1,$termo2) ;");
        }else{
          $cond = eval("return $termo1 $operator $termo2 ;");
        }

        

        if($cond)
          $result = $retult1;
        else
          $result = ($retult2 != null)?$retult2:"";

        return $result;
      ');
      $html = preg_replace_callback('/\[condition:(.*?)(==|===|!=|!==|>=|<=|>|<|like)(.*?)\](.*?)(\[condition-else\](.*?))?\[\/condition\]/is',$conditionFunction,$html);

       return $html;
  	}




  	

}