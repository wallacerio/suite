<?php

class CompSetupPrint{

	public static $onlyOut = false;
	public static $outFinal = null;

	
	/**
	 * [inputs description]
	 * @param  [type] $listArray   [description]
	 * @param  [type] $inputsArray [description]
	 * @return [type]              [description]
	 */
	public static function inputs($listArray = null,$inputsArray = null){		
		$resultArray = array();

		$out = '';
		foreach ($listArray as $key => $value) {
			
			$title = isset($value['title'])?$value['title']:'';			
			$valDefault = isset($value['value'])?$value['value']:'';						
			$title = self::formatStrings($title);
			$validate = isset($value['validate'])?$value['validate']:null;
			$checkValidate = false;
			
			 while($checkValidate == false){
				
				echo "    ".$title;

				$valInput = trim(fgets(STDIN));				
				if(empty($valInput)) $valInput = $valDefault;
				$val['result'] = $valInput;
				
				if($validate != null){					
					$validateArray = explode("|", $validate);
				
					if($valInput != "" && !in_array($valInput,$validateArray)){
						echo self::formatStrings("\n    - ERROR:~{\"bold\":true,\"color\":\"red\"} Value not permited");
						echo "\n";
						echo "\n";						
					}else{
						// echo self::formatStrings("\n    - SUCCESS:~{\"bold\":true,\"color\":\"green\"} Value chanded to ".$valInput."~{\"bold\":true}");						
						// echo "\n";						
						$checkValidate = true;
						$runCode = true;
					}
				}else{
					$checkValidate = true;
					$runCode = true;
				}
				$resultArray[$key] = $valInput;
			}
		}

		return $resultArray;
	}


	/**
	 * [lists description]
	 * @param  [type] $listArray   [description]
	 * @param  [type] $inputsArray [description]
	 * @return [type]              [description]
	 */
	public static function lists($listArray = null,$inputsArray = null){
		$out = '';

		foreach ($listArray as $key => $value) {		
			$type = isset($value['type'])?$value['type']:'';
			$text = isset($value['text'])?$value['text']:'';
			
			$out .= "    ";
			$out .= $text;
			$out .= "\n";
		}
		// $out = CompSetupPrint::formatStrings($out);

		return $out;
	}


	/**
	 * [listCommands description]
	 * @param  [type] $listArray   [description]
	 * @param  [type] $inputsArray [description]
	 * @return [type]              [description]
	 */
	public static function listCommands($listArray = null,$inputsArray = null){		
		$command = isset($inputsArray['command'])?$inputsArray['command']:null;
		$inpCommand = false;
		$out = '';

		if($command != null){
			$out .= "    ";
			$inpCommand = CompSetupPrint::output($command,array('bold'=>true,'forecolor'=>'yellow'));			
			$out .= $inpCommand;	
		}

		$loop = function($loop,$listArray,$inpCommand,$pathKey = '',$nivel = 0){			
			$out = '';

			foreach ($listArray as $key => $value) {
				 
				 if(substr($key,0,1)=='@')
				 	continue;

				 $type = isset($value['@type'])?$value['@type']:'unknown';
				 $description = isset($value['@description'])?$value['@description']:'';
				 
				 $breakline = "";

				 if($type == 'category'){
				 	$bold = true;
				 	$color = 'yellow';
				 	$tabs = "    ";
				 	if($inpCommand !== false){				 		
				 		$tabs .= "|--~{\"color\":\"yellow\"} ";
				 	}

				 	for ($i=0; $i < $nivel; $i++) { 
				 		$tabs .= "|--~{\"color\":\"yellow\"} ";
				 	}
				 	
				 	$icon = "";
				 	if($inpCommand !== false)
				 		$breakline = "";
				 	else
				 		$breakline = "\n";
				 	if($key == 'setup.default')
				 		$titulo = "";
				 	else
				 		$titulo = CompSetupPrint::fixedStringSize($key,15);

				 }else if($type == 'command'){
				 	$bold = false;
				 	$color = 'yellow';
				 
				 	$tabs = "    ";
				 	if($inpCommand !== false){
				 		$tabs .= "\n    ";
				 		$tabs .= "|--~{\"color\":\"yellow\"} ";
				 	}
				 		
				 	if( substr($pathKey, 0,14) != '/setup.default'){
					 	for ($i=0; $i < $nivel; $i++) { 
					 		$tabs .= "|--~{\"color\":\"yellow\"} ";
					 	}
					 }else{
					 	$tabs .= "    ";
					 }
				 	if($inpCommand === false)
				 		$breakline = "\n";

				 	$titulo = CompSetupPrint::fixedStringSize($key,25);
				 	$icon = "";
				 }else{
				 	$bold = false;
				 	$color = 'cian';

				 	$tabs = "    ";
				 
				 	if($inpCommand !== false){
				 		$tabs .= "\n    ";
				 		$tabs .= "|--~{\"color\":\"yellow\"} ";				 		
				 	}else{
				 		$breakline = "\n";
				 	}

				 	for ($i=0; $i < $nivel; $i++) { 
				 		$tabs .= "|--~{\"color\":\"yellow\"} ";
				 	}
				 	
				 	$titulo = CompSetupPrint::fixedStringSize($key,25);
				 	$icon = "";
				 }

				 $colorDescription = 'white';

				 $out .= $tabs;
				 $out .= $icon.CompSetupPrint::output($titulo,array('bold'=>$bold,'forecolor'=>$color));			
				 $out .= CompSetupPrint::output($description,array('bold'=>$bold,'forecolor'=>$colorDescription));			
				 $out .= $breakline;
				 

				 if(is_array($value)){
				 	$out .= $loop($loop,$value,$inpCommand,$pathKey.'/'.$key,$nivel + 1);				 	
				 }
			}

			return $out;
		};

		$out .= $loop($loop,$listArray,$inpCommand);		
		return $out;
	}


	/**
	 * [formatWord description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public static function formatWord($array = null){
		$out = '';
	    $html = '';
	    $spanaction = null;
	    $format = null;
	    $color = "white";
	    $bold = false;
	    		
	    $html = isset($array[0])?$array[0]:'';
	    $format = isset($array[1])?"{".$array[1]:null;	    
	    $html = str_replace('<br>', "\n", $html);
	    
	    if($format !== null){	    	
	        $format = json_decode($format,true);
	        $bold = isset($format['bold'])?$format['bold']:$bold;	        
	        $color = isset($format['color'])?$format['color']:$color;	            
	        $width = isset($format['width'])?$format['width']:null;
	    	
	    	

	    	

	    	if($width!=null)
	    	$html = CompSetupPrint::fixedStringSize($html,$width);

	    	
	    	$spaceArray = explode('[:space:]', $html);	    		    	
	    	$outJoin = '';

	    	foreach ($spaceArray as $key => $value) {	    		
	    		$outJoin .= CompSetupPrint::output($value,array('bold'=>$bold,'forecolor'=>$color));			
	    		if(count($spaceArray)>1)
	    		$outJoin .= ' ';
	    	}
	    	// $out = $outJoin;

	    	$spaceArray = explode('\s', $outJoin);	    		    	
	    	$outJoin = '';
	    	if(count($spaceArray)>0)
	    	foreach ($spaceArray as $key => $value) {	    		
	    		$outJoin .= CompSetupPrint::output($value,array('bold'=>$bold,'forecolor'=>$color));			
	    		if(count($spaceArray)>1)
	    		$outJoin .= ' ';
	    	}
	    	
	        
	        $out = $outJoin;
	        
	        

	    }else{
	    	// $out = $html;
	    	$out = CompSetupPrint::output($html,array('bold'=>$bold,'forecolor'=>$color));				        
	    }

	    return $out;
	}


	/**
	 * [formatInputs description]
	 * @param  [type] $array          [description]
	 * @param  [type] $execfunc_after [description]
	 * @return [type]                 [description]
	 */
	public static function formatInputs($array = null,$execfunc_after = null){
		$out = '';
		$execfunc_after = base64_decode($execfunc_after);		

		foreach ($array as $key => &$val) {			
			$type = isset($val['type'])?$val['type']:null;
			$title = isset($val['title'])?$val['title']:null;
			$value = isset($val['value'])?$val['value']:null;
			$validate = isset($val['validate'])?$val['validate']:null;
			$checkValidate = false;
			
			 while($checkValidate == false){
				
				echo $title;

				$valInput = trim(fgets(STDIN));
				if(empty($valInput)) $valInput = $value;
				$val['result'] = $valInput;
				
				if($validate != null){					
					$validateArray = explode("|", $validate);
					if(!in_array($valInput,$validateArray)){
						echo self::formatStrings("\n Value not permited \n\n");					
					}else{
						$checkValidate = true;
						$runCode = true;
					}
				}else{
					$checkValidate = true;
					$runCode = true;
				}
			}

			$execfunc_after = str_replace('{'.$key.'}', $valInput, $execfunc_after);

		 }
				
		eval($execfunc_after);
		
		return $out;
	}



	/**
	 * [formatStrings description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	public static function formatStrings($string = null){
		$stringArray = explode(' ',$string);
            $html2 = '';
            foreach ($stringArray as $key2 => $value2) {            	
                $par = $value2;          
                $parArray = explode('~{',$par);
               	
               	// echo json_encode($parArray);
                $word = self::formatWord($parArray);
                
                $html2 .= ''.$word.' ';
            }

            

            return $html2;
	}



	/**
	 * [fixedStringSize description]
	 * @param  [type]  $string [description]
	 * @param  integer $size   [description]
	 * @return [type]          [description]
	 */
	public static function fixedStringSize($string,$size = 30){
		$countString = strlen($string);		
		$restString = $size-strlen($string);		
		$space = '';
		for($i=0;$i<$restString;$i++) $space .= ' ';
		return $string.$space;
	}
	
	/**
	 * [output description]
	 * @param  [type] $text    [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public static function output($text,$options = null){		
		$colors = self::setColor($options);		
		$foreColor = $colors['forecolor'];
		$backColor = $colors['backcolor'];		
		return $foreColor.$backColor."".$text."\033[0m";
	}


	/**
	 * [setColor description]
	 * @param [type] $options [description]
	 */
	private static function setColor($options = null){

		$fc = isset($options['forecolor'])?$options['forecolor']:'white';
		$bc = isset($options['backcolor'])?$options['backcolor']:null;
		$bold = isset($options['bold'])?$options['bold']:null;

		if($bold == true) $bold = '1'; else $bold = '0';

		switch ($fc) {
			case 'red':
				$forecolor =  $bold . ";31";
				break;
			case 'white':
				$forecolor =  $bold.";37";
				break;
			case 'green':
				$forecolor = $bold . ";32";
				break;	
			case 'blue':
				$forecolor = $bold . ";34";
				break;	
			case 'yellow':
				$forecolor = $bold . ";33";
				break;	
			case 'purple':
				$forecolor = $bold . ";35";
				break;	
			case 'cian':
				$forecolor = $bold . ";36";
				break;	
			case 'black':
				$forecolor = $bold . ";30";
				break;	
			
			default:
				$forecolor = '';
				break;
		}

		switch ($bc) {
			case 'black':
				$backcolor = "40";
			case 'red':
				$backcolor = "41";
				break;
			case 'green':
				$backcolor = "42";
				break;	
			case 'yellow':
				$backcolor = "43";
				break;	
			case 'blue':
				$backcolor = "44";
				break;	
			case 'magenta':
				$backcolor = "45";
				break;	
			case 'cyan':
				$backcolor = "46";
				break;	
			case 'light_gray':
				$backcolor = "47";
				break;	
					
			default:
				$backcolor = '';
				break;
		}


		$forecolor = "\033[" . $forecolor . "m";

		if($bc != null)
			$backcolor = "\033[" . $backcolor . "m";

		return array('forecolor'=>$forecolor,'backcolor'=>$backcolor);
	}

	/**
	 * [clear description]
	 * @return [type] [description]
	 */
	public static function clear(){
		self::revCommand('clear');
	}

	/**
	 * [revCommand description]
	 * @param  [type] $command [description]
	 * @return [type]          [description]
	 */
	private static function revCommand($command = null){
		if($command == 'clear'){	
			array_map(create_function('$a', 'print chr($a);'), array(27, 91, 72, 27, 91, 50, 74));
		}
	}


	/**
	 * [out description]
	 * @param  string  $out       [description]
	 * @param  boolean $useHeader [description]
	 * @param  boolean $legend    [description]
	 * @return [type]             [description]
	 */
	public static function out($out = '',$useHeader = true,$legend = false){
		$outFinal = '';

		if(self::$onlyOut == true){
			self::$outFinal = $out;
			return false;
		}
		
		self::clear();

		$header = '';
		if($useHeader)
			$header = CompSetupPrint::output(CompSetupPrint::fixedStringSize("[Suite.console: 1.0]"),array('bold'=>true,'forecolor'=>'blue'))."\n"
					. CompSetupPrint::output(CompSetupPrint::fixedStringSize("--------------------"),array('bold'=>true,'forecolor'=>'blue')).""		
					. ""
					. "\n"
					. "Use: console [category/category/command] [parameters]"
					. "\n\n";

					if($legend == true)
						$header .= "\t".'white:~{"bold":true,"color":"yellow","width":0} '
								. 'category~{"bold":false,"color":"white","width":11} '
								. 'brown:~{"bold":false,"color":"yellow","width":0} '
								. 'local command~{"bold":false,"color":"white","width":10} '
								. 'blue:~{"blue":false,"color":"cian","width":0} '
								. 'remote command~{"bold":false,"color":"white","width":10} '
								. "\n\n"
								. '';

		if($useHeader)
			$outFinal = $header;

		$outFinal .= $out;
		$outFinal = self::formatStrings($outFinal);
		echo $outFinal;
	}
	

	
}