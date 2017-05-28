<?php
/*
 * Suite Framework
 * ================
 * suite.wallrio.com
 *
 * This file is part of the Suite Core.
 *
 * Wallace Rio <wallacerio@wallrio.com>
 * 
 */
class CompRegister{

	public static $count = 0;
	public static $headContent = '';
	public static $footerContent = '';
	public static $allParameters = null;
	public static $allParametersFirst = null;
	public static $UnitParameters = null;
	public static $listFiles = array();
	public static $listFilesName = array();
	public static $lastScripts = array();
	public static $options = array('compress'=>false,'join'=>false);
	


	

	public static function clean(){
		self::$allParameters = null;
		// self::$UnitParameters = null;
	}

	public static function joinUnit($parameters){
		//self::$UnitParameters = null;

		if(self::$UnitParameters != null)
			$registerUnion = self::$UnitParameters;
		else
			$registerUnion = array();
	

		$index = 0 ;
		foreach ($parameters as $key0 => $value0) {

			foreach ($value0 as $key => $value) {


					$type = $key;
					$name = isset($value['name'])?$value['name']:$type.''.$index;
					$into = isset($value['into'])?$value['into']:null;
					$version = isset($value['version'])?$value['version']:null;
					$dependence = isset($value['dependence'])?$value['dependence']:null;
					$parametersArray = isset($value['parameters'])?$value['parameters']:null;
					if($parametersArray == null)continue;
					foreach ($parametersArray as $keyPar => $valuePar) {
						if(isset($registerUnion[$name])){
							if($registerUnion[$name]['name'] == $name){
								if($registerUnion[$name]['version'] > $version){
									continue;
								}
							}
						}
						
						$registerUnion[$name]['type'] = $type;
						$registerUnion[$name]['name'] = $name;
						$registerUnion[$name]['into'] = $into;
						$registerUnion[$name]['version'] = $version;
						$registerUnion[$name]['dependence'] = $dependence;
						$registerUnion[$name]['parameters'][$keyPar] = $valuePar;				
						
						$index++;
					}
					
					
			}

		}

		

		self::$UnitParameters = $registerUnion;
		
	

		return '';
	}

	public static function getHtmlUnit(){
		
		$parameters = isset(self::$UnitParameters)?self::$UnitParameters:null;
		if($parameters == null){
			return array('head'=>null,'footer'=>null);
		}


		$join = "\n";
		$headJoin = "\n";
		$footerJoin = "\n";
		foreach ($parameters as $key => $value) {
			
			$type = $value['type'];
			$name = $value['name'];
			$into = $value['into'];
			$version = $value['version'];
			$dependence = $value['dependence'];
			$parameters = $value['parameters'];

			if($type != 'include')
				$join = '<'.$type.' ';
			else
				$join = '';
		
			foreach ($parameters as $keyPar => $valuePar) {

				if($type == 'include'){
					if($keyPar == 'path')
						$join .= Functions::requireToVar($valuePar);
					else
						$join .= $valuePar;
					continue;
				}

				if(is_numeric($keyPar))
					$join .= $valuePar;
				else
					$join .= $keyPar.'="'.$valuePar.'" ';
			}
			
			if($type != 'include')
				$join .= '>';

			if($type == 'script'){
				$join .= '</'.$type.' >'."\n";
			}else{
				$join .= "\n";
			}
	

			if($into == 'head'){
				$headJoin .= $join;
			}else if($into == 'footer'){
				$footerJoin .= $join;
			}
		}
				

		return array('head'=>$headJoin,'footer'=>$footerJoin);
	}










/**
 * inclui dependencias no site, caso solicitado
 * @param  [type] $dep_name    [description]
 * @param  [type] $dep_type    [description]
 * @param  [type] $dep_version [description]
 * @param  [type] &$Reg        [description]
 * @return [type]              [description]
 */
public static function dependence($dep_name,$dep_type,$dep_version,&$Reg){
	$found = false;
	$appSuiteUrl = Globals::get('path/suite/url');
	$appSuiteDir = Globals::get('path/suite/dir');
	$dependenceUrl = $appSuiteUrl . 'dependences/';
	$dependenceDir = $appSuiteDir . 'dependences/';

	$dependenceArray = Suite_libs::core('Files/Scan/onlyDir',$dependenceDir);
	foreach ($dependenceArray as $key => $value) {
		if($value == $dep_name ){
			if($dep_version == '*'){
				$dependenceFileArray = Suite_libs::core('Files/Scan/onlyFiles',$dependenceDir . $value . '/');
				$fileFirst = isset($dependenceFileArray[count($dependenceFileArray)-1])?$dependenceFileArray[count($dependenceFileArray)-1]:null;
				if($fileFirst == null){				
					$found = false;
				}else{
					$file_name = $value;
					$fileUrl = $dependenceUrl . $value . '/'. $fileFirst;	
					$found = true;
				}
			}else{
				$file_name = $value;
				$fileUrl = $dependenceUrl . $value . '/'. $value.'-'.$dep_version.'.js';	
				$fileDir = $dependenceDir . $value . '/'. $value.'-'.$dep_version.'.js';	
				if(file_exists($fileDir)){
					$found = true;
				}else{
					$dependenceFileArray = Suite_libs::core('Files/Scan/onlyFiles',$dependenceDir . $value . '/');
					$fileFirst = isset($dependenceFileArray[count($dependenceFileArray)-1])?$dependenceFileArray[count($dependenceFileArray)-1]:null;
					if($fileFirst == null){				
						$found = false;
					}else{
						$file_name = $value;
						$fileUrl = $dependenceUrl . $value . '/'. $fileFirst;	
						$found = true;
					}
				}
				
			}
						
			break;
		}
	}

	

	if($found == true){

		$mold_name = $file_name.'';
		$mold_src =  $fileUrl;
		$mold_type = 'text/javascript';
		$mold_into = 'head';
		$moldScript = array('script'=>array('name'=>$mold_name ,'parameters'=>array('src'=>$mold_src,'type'=>$mold_type),'into'=>$mold_into));											
		$Reg['into'] = 'head';
		$Reg['name'] = $mold_name;
		$Reg['parameters']['src'] = $mold_src;
		// print_r($moldScript);
		self::joinFirst(array($moldScript));
		return true;
	}else{

		return false;
	}
	
}

public static function join($parameters,$first = false){
			
			

		if(self::$allParameters != null)
			$registerUnion = self::$allParameters;
		else
			$registerUnion = array();

		$registerUnion = self::joinAdjust($registerUnion,$parameters,$first);

		self::$allParameters = $registerUnion;
		


		return '';
		
}

public static function joinAdjust($registerUnion,$parameters,$first = false){


		$index = 0 ;
		if(count($parameters)>0)
		foreach ($parameters as $key0 => $value0) {

					

			if( is_array($value0) && count($value0)>0)
			foreach ($value0 as $key => $value) {

					if(isset($value['parameters']['parent']))
						unset($value['parameters']['parent']);
					if(isset($value['parameters']))
						unset($value['parent']);
						// print_r($value);

					self::$count++;
					$source = null;

					$type = $key;
					$nameAuto = isset($value['name'])?false:true;
					$name = isset($value['name'])?$value['name']:$type.''.self::$count;

					$into = isset($value['into'])?$value['into']:null;
					$version = isset($value['version'])?$value['version']:null;
					$dependence = isset($value['dependence'])?$value['dependence']:null;
					$content = isset($value['content'])?$value['content']:null;
					$parametersArray = isset($value['parameters'])?$value['parameters']:null;

					

					if(isset($parametersArray['src'])){
						$source = $parametersArray['src'];
					}else if(isset($parametersArray['href'])){
						$source = $parametersArray['href'];
					}else{
						$source = null;
					}

					if(!isset($source))
						$source = $content;

					// $nameKey = $name.'_'.$type;
					$nameKey = $source.'_'.$type;
			
							
					if( !in_array($name.'_'.$type, self::$listFilesName) ){
					// if( !in_array($source, self::$listFiles) && !in_array($name.'_'.$type.'_'.$into, self::$listFilesName)){
					// 
						
						$tag = array();

						if($into == 'first-head'){		
							
							$mold_name = $name;
							$mold_src =  $source;						
							$mold_into = 'head';
							if($nameAuto == false)
								$tag[$type]['name'] = $mold_name;
							$tag[$type]['into'] = $mold_into;								
							foreach ($parametersArray as $keyPar => $valuePar) {
								$tag[$type]['parameters'][$keyPar] = $valuePar;
							}		
							
							self::joinFirst(array($tag));	

							continue;	
						}else if($into == 'first-footer'){						
							$mold_name = $name;
							$mold_src =  $source;						
							$mold_into = 'footer';
							if($nameAuto == false)
								$tag[$type]['name'] = $mold_name;
							$tag[$type]['into'] = $mold_into;								
							foreach ($parametersArray as $keyPar => $valuePar) {
								$tag[$type]['parameters'][$keyPar] = $valuePar;
							}					
							self::joinFirst(array($tag));	
							continue;	
						}else if($into == 'last-footer'){						
							$mold_name = $name;
							$mold_src =  $source;						
							$mold_into = 'footer';
							if($nameAuto == false)
								$tag[$type]['name'] = $mold_name;
							$tag[$type]['into'] = $mold_into;								
							foreach ($parametersArray as $keyPar => $valuePar) {
								$tag[$type]['parameters'][$keyPar] = $valuePar;
							}			

							// echo $into.'-'.$name."\n";

							self::joinLast(array($tag));	
							continue;	
						}



							self::$listFiles[] = $source;
							self::$listFilesName[] = $name.'_'.$type;
						
						
							$registerUnion[$nameKey]['index'] = self::$count;
							$registerUnion[$nameKey]['type'] = $type;
							$registerUnion[$nameKey]['name'] = $name;
							$registerUnion[$nameKey]['nameauto'] = $nameAuto;
							$registerUnion[$nameKey]['into'] = $into;
							$registerUnion[$nameKey]['version'] = $version;
							$registerUnion[$nameKey]['dependence'] = $dependence;
							$registerUnion[$nameKey]['content'] = $content;
							$registerUnion[$nameKey]['parameters'] = $parametersArray;				
							$registerUnion[$nameKey]['source'] = $source;//$parametersArray['src'];

							

							// start: dependence -------------------------
							if($dependence != null && is_array($dependence)){

							
								// se houver uma dependencia, então é feito uma busca pelo
								// plugin requerido, se for encontrado então é alterado os parametros do plugin requerido
								// pelo plugin requerido presente do modulo.
								foreach ($dependence as $key => $value) {
									$dep_name = ($key=="0")?$value:$key;
									$dep_version = ($dep_name == $value)?'*':$value;
									// $dep_type = $type;
									$mold_showScript = false;

									foreach ($parameters as $keyReg => $valueReg) {
										

										// echo $valueReg['script']['parameters']['src'] .'-'. $dep_name.'<br>';

										if(isset($valueReg['script']['name']) && $valueReg['script']['name'] == $dep_name){
											$mold_showScript = true;											
											$mold_name = $valueReg['script']['name'];
											$mold_src =  $valueReg['script']['parameters']['src'];
											$mold_type = $valueReg['script']['parameters']['type'];
											$mold_into = $into;

											$keyReg2['into'] = $into;
											
											$moldScript = array('script'=>array('name'=>$mold_name ,'parameters'=>array('src'=>$mold_src,'type'=>$mold_type),'into'=>$mold_into));											
											self::joinFirst(array($moldScript));											
											break 1;
										}
									}

									// altera parametros do script rependente encontrado
									foreach ($registerUnion as $keyReg2 => $valueReg2) {
											
											// echo $registerUnion[$keyReg2]['name'] .'-'. $dep_name."\n";

										if($registerUnion[$keyReg2]['name'] == $dep_name && $registerUnion[$keyReg2]['type'] == $type){
											
											if($mold_showScript === true){

												$registerUnion[$keyReg2]['into'] = $into;
												$registerUnion[$keyReg2]['parameters']['src'] = $mold_src;
												// break 1;
											}else{

												// verifica se a dependencia está disponivel no diretório de dependencia do Suite
												$continue = self::dependence($dep_name,$type,$dep_version,$registerUnion[$keyReg2]);

												if($continue === false){

													// caso não tenha a dependencia no modulo é utilizado o script alheio encontrado
													$mold_name = $registerUnion[$keyReg2]['name'];
													$mold_src =  $registerUnion[$keyReg2]['parameters']['src'];
													$mold_type = $registerUnion[$keyReg2]['parameters']['type'];
													$mold_into = $into;
													$moldScript = array('script'=>array('name'=>$mold_name ,'parameters'=>array('src'=>$mold_src,'type'=>$mold_type),'into'=>$mold_into));											
													$registerUnion[$keyReg2]['into'] = $into;
													// print_r($moldScript);
													self::joinFirst(array($moldScript));											
												}else{

													break 1;
												}

											}
										}
									}
								}
								
							}
								// end: dependence -------------------------

						}
						$index++;
					
					
			}

		}


		return $registerUnion;
		// print_r($registerUnion);
		
		//$registerUnion = array_reverse($registerUnion,true);
		
		
	}




	public static function joinFirst($parameters){
		
		/*if(self::$allParameters != null)
			$registerUnion = self::$allParameters;
		else
			$registerUnion = array();



		$registerUnion = self::joinAdjust($registerUnion,$parameters,$first);

		self::$allParameters = $registerUnion;
		


		return '';*/


		if(self::$allParametersFirst != null)
			$registerUnion = self::$allParametersFirst;
		else
			$registerUnion = array();
		
		$registerUnion2 = array();


		$registerUnion2 = self::joinAdjust($registerUnion2,$parameters);


		$uni = array();
		
		$uni = array_merge($registerUnion2,$registerUnion);
		
		
		

		self::$allParametersFirst = $uni;

		return '';

	}

	public static function joinFirst3($parameters){
		
		if(self::$allParameters != null)
			$registerUnion = self::$allParameters;
		else
			$registerUnion = array();
	
		$registerUnion2 = array();

		$index = 0 ;
		foreach ($parameters as $key0 => $value0) {

			foreach ($value0 as $key => $value) {

					self::$count++;
					
					$type = $key;
					$nameAuto = isset($value['name'])?false:true;
					
					$name = isset($value['name'])?$value['name']:$type.''.self::$count;
					$into = isset($value['into'])?$value['into']:null;
					$version = isset($value['version'])?$value['version']:null;
					$dependence = isset($value['dependence'])?$value['dependence']:null;
					$content = isset($value['content'])?$value['content']:'';
					$parametersArray = isset($value['parameters'])?$value['parameters']:null;

					

					if(isset($parametersArray['src'])){
						$source = $parametersArray['src'];
					}else if(isset($parametersArray['href'])){
						$source = $parametersArray['href'];
					}else{
						$source = null;
					}

					if(!isset($source))
						$source = $content;

					// $nameKey = $name.'_'.$type;
					 $nameKey = $source.'_'.$type;
					// echo $nameKey.'<br>';
					
					

					// if($parametersArray == null)continue;


				// echo $name.'=='. $source ."\n\n";
					 if(!in_array($name.'_'.$type, self::$listFilesName) ){
					// if( !in_array($source, self::$listFiles) && !in_array($name.'_'.$type, self::$listFilesName)){
					// if( !in_array($source, self::$listFiles) && !in_array($name.'_'.$type, self::$listFilesName)){
							 // if( !in_array($source, self::$listFiles) ){
							  self::$listFiles[] = $source;
							 self::$listFilesName[] = $name.'_'.$type;
						
							$registerUnion2[$nameKey]['type'] = $type;
							$registerUnion2[$nameKey]['nameauto'] = $nameAuto;
							$registerUnion2[$nameKey]['name'] = $name;
							$registerUnion2[$nameKey]['into'] = $into;
							$registerUnion2[$nameKey]['version'] = $version;
							$registerUnion2[$nameKey]['dependence'] = $dependence;
							$registerUnion2[$nameKey]['content'] = $content;
							$registerUnion2[$nameKey]['parameters'] = $parametersArray;				
						 }

					$index++;	 
					
			}

		}

		$uni = array();
		//array_push($uni, $registerUnion2);
		//array_push($uni, $registerUnion);
		//$uni[] = $registerUnion2;
		//$uni[] = $registerUnion;
		

		$uni = array_merge($registerUnion2,$registerUnion);
		//self::array_insert(self::$allParameters, $registerUnion2, 0);
		//$registerUnion = array_reverse($registerUnion,true);
		/*echo '<pre>';
		print_r($uni);
		echo '</pre>';*/
		
		self::$allParameters = $uni;
		
		

		return '';
	}



	public static function joinLast($parameters){

		if(self::$allParameters != null)
			$registerUnion = self::$allParameters;
		else
			$registerUnion = array();

		$registerUnion2 = array();

		$registerUnion2 = self::joinAdjust($registerUnion2,$parameters);

		$uni = array();

		
		if(isset($registerUnion2))
		self::$lastScripts[] = $registerUnion2;
	}

	public static function joinLast3($parameters){
		
		if(self::$allParameters != null)
			$registerUnion = self::$allParameters;
		else
			$registerUnion = array();
	
		// print_r($parameters);

		$index = 0 ;
		foreach ($parameters as $key0 => $value0) {

			if( is_array($value0) && count($value0)>0)
			foreach ($value0 as $key => $value) {


					if(isset($value['parameters']['parent']))
						unset($value['parameters']['parent']);
					if(isset($value['parameters']))
						unset($value['parent']);
					
					self::$count++;
					
					$type = $key;
					$nameAuto = isset($value['name'])?false:true;
					$name = isset($value['name'])?$value['name']:$type.''.self::$count;
					$into = isset($value['into'])?$value['into']:null;
					$version = isset($value['version'])?$value['version']:null;
					$dependence = isset($value['dependence'])?$value['dependence']:null;
					$content = isset($value['content'])?$value['content']:'';
					$parametersArray = isset($value['parameters'])?$value['parameters']:null;
//print_r($dependence);
					if(isset($parametersArray['src'])){
						$source = $parametersArray['src'];
					}else if(isset($parametersArray['href'])){
						$source = $parametersArray['href'];
					}else{
						$source = null;
					}

					if(!isset($source))
						$source = $content;

					// $nameKey = $name.'_'.$type;
					 $nameKey = $source.'_'.$type;
					
					
					// echo '--'. $source.'=='.$name.'_'.$type .'<br>';

					// if($parametersArray == null)continue;


				

					 if(!in_array($name.'_'.$type, self::$listFilesName) ){
					// if( !in_array($source, self::$listFiles) && !in_array($name.'_'.$type, self::$listFilesName)){
							 // if( !in_array($source, self::$listFiles) ){
							 
							  self::$listFiles[] = $source;
							 self::$listFilesName[] = $name.'_'.$type;
						
							$registerUnion2[$nameKey]['type'] = $type;
							$registerUnion2[$nameKey]['nameauto'] = $nameAuto;
							$registerUnion2[$nameKey]['name'] = $name;
							$registerUnion2[$nameKey]['into'] = $into;
							$registerUnion2[$nameKey]['version'] = $version;
							$registerUnion2[$nameKey]['dependence'] = $dependence;
							$registerUnion2[$nameKey]['content'] = $content;
							$registerUnion2[$nameKey]['parameters'] = $parametersArray;				
						 }

					$index++;	 
					
			}

		}

		$uni = array();
		//array_push($uni, $registerUnion2);
		//array_push($uni, $registerUnion);
		//$uni[] = $registerUnion2;
		//$uni[] = $registerUnion;
			
			// 
		
		if(isset($registerUnion2))
		self::$lastScripts[] = $registerUnion2;

		// $uni = array_merge($registerUnion2,$registerUnion);
		// array_push($registerUnion2, $registerUnion);
		//self::array_insert(self::$allParameters, $registerUnion2, 0);
		//$registerUnion = array_reverse($registerUnion,true);
		/*echo '<pre>';
		print_r(self::$lastScripts);
		echo '</pre>';*/
		
		// self::$allParameters = $uni;
		
		/*echo '<pre>';
		print_r(self::$allParameters);
		echo '</pre>';*/

		return '';
	}








	public static function joinOnlyTime($parameters,$first = false){
		
		// if(self::$allParameters != null)
			// $registerUnion = self::$allParameters;
		// else
			$registerUnion = array();
		


		

		$index = 0 ;
		foreach ($parameters as $key0 => $value0) {

					// print_r($value0);

			if( is_array($value0) && count($value0)>0)
			foreach ($value0 as $key => $value) {

					if(isset($value['parameters']['parent']))
						unset($value['parameters']['parent']);
					if(isset($value['parameters']))
						unset($value['parent']);
						// print_r($value);

					self::$count++;
					$source = null;

					$type = $key;
					$nameAuto = isset($value['name'])?false:true;
					$name = isset($value['name'])?$value['name']:$type.''.self::$count;
					$into = isset($value['into'])?$value['into']:null;
					$version = isset($value['version'])?$value['version']:null;
					$dependence = isset($value['dependence'])?$value['dependence']:null;
					$content = isset($value['content'])?$value['content']:null;
					$parametersArray = isset($value['parameters'])?$value['parameters']:null;

					

					if(isset($parametersArray['src'])){
						$source = $parametersArray['src'];
					}else if(isset($parametersArray['href'])){
						$source = $parametersArray['href'];
					}else{
						$source = null;
					}

					if(!isset($source))
						$source = $content;

					// $nameKey = $name.'_'.$type;
					$nameKey = $source.'_'.$type;
					
					/*echo '<br><br>';
					print_r($nameKey);*/
					if( !in_array($name.'_'.$type, self::$listFilesName) ){
					// if(  !in_array($name.'_'.$type, self::$listFilesName)){
							self::$listFiles[] = $source;
							self::$listFilesName[] = $name.'_'.$type;
						
							

							$registerUnion[$nameKey]['type'] = $type;
							$registerUnion[$nameKey]['nameauto'] = $nameAuto;
							$registerUnion[$nameKey]['name'] = $name;
							$registerUnion[$nameKey]['into'] = $into;
							$registerUnion[$nameKey]['version'] = $version;
							$registerUnion[$nameKey]['dependence'] = $dependence;
							$registerUnion[$nameKey]['content'] = $content;
							$registerUnion[$nameKey]['parameters'] = $parametersArray;				
							$registerUnion[$nameKey]['source'] = $source;//$parametersArray['src'];
						}
						$index++;
					
					
			}

		}
		
		//$registerUnion = array_reverse($registerUnion,true);
		
		// self::$allParameters = $registerUnion;
		


		return $registerUnion;
	}

	public static function  array_insert(&$array, $insert, $position = -1) {
        $position = ($position == -1) ? (count($array)) : $position ;

        if($position != (count($array))) {
            $ta = $array;

            for($i = $position; $i < (count($array)); $i++) {
                if(!isset($array[$i])) {
                    die(print_r($array, 1)."\r\nInvalid array: All keys must be numerical and in sequence.");
                }

                $tmp[$i+1] = $array[$i];
                unset($ta[$i]);
            }

            $ta[$position] = $insert;
            $array = $ta + $tmp;
            //print_r($array);
        } else {
            $array[$position] = $insert;
        }

        //ksort($array);
        return true;
    }

	public static function getParameters(){

		$parameters = self::$allParameters;

		return $parameters;
	}


	public static function getHtml($forcePar = null){
		
		$par = isset(self::$allParameters)?self::$allParameters:null;


		/*echo '<pre>';
		print_r(self::$allParameters);
		echo '</pre>';*/

		foreach (self::$lastScripts as $key => $value) {
			if(isset($value)){
				if($par == null)
					$par = $value;
				else
					$par = array_merge($par,$value);
			}
		}

		if($forcePar != null){

			$par = $forcePar;
		}

			// print_r(self::$allParametersFirst);

		$parSave = $par;
		if(count(self::$allParametersFirst)>0){
			$par = array();
			foreach (self::$allParametersFirst as $key => $value) {
				$par[$key] = $value;
			}

			foreach ($parSave as $key => $value) {
				$par[$key] = $value;
			}
		}

		
		// $par[] = self::$allParametersFirst;

		// $par = array_push($par, self::$allParametersFirst);

					
		/*if(isset(self::$lastScripts[0])){
			if($par == null)
				$par = self::$lastScripts[0];
			else
				$par = array_merge($par,self::$lastScripts[0]);
		}*/

		

		if($par == null){
			$headJoin = '';
			$footerJoin = '';
			$headJoin = self::$headContent.$headJoin;

			$footerJoin = self::$footerContent.$footerJoin;

			return array('head'=>$headJoin,'footer'=>$footerJoin);
		}

		// foreach (self::$lastScripts as $key => $value) {
			// array_push($par,self::$lastScripts[]);
		// }
		

	

		
		$index = 0;
		$join = "\n";
		$headJoin = "\n";
		$footerJoin = "\n";
		foreach ($par as $key => $value) {
			
			$type = $value['type'];
			$name = $value['name'];
			$nameauto = $value['nameauto'];
			$into = $value['into'];
			$version = $value['version'];
			$dependence = $value['dependence'];
			$content = $value['content'];
			$parameters = $value['parameters'];

			
			if($type != 'include')
				$join = '<'.$type.' ';
			else
				$join = '';
			
			if(count($parameters)>0)
			foreach ($parameters as $keyPar => $valuePar) {

				if($type == 'include'){
					if($keyPar == 'path')
						$join .= Functions::requireToVar($valuePar);
					else
						$join .= $valuePar;
					continue;
				}

				if(is_numeric($keyPar)){
					$join .= $valuePar;
				}else{
					if($valuePar === null){
						$join .= $keyPar;
					}else{
						$srcJoin[$index][$keyPar] = $valuePar;
						$srcJoin[$index]['into'] = $into;
						// if($keyPar == 'href' )
						// echo $valuePar."\n";
						$join .= $keyPar.'="'.$valuePar.'" ';
					}
				}
			}

			$index++;

			$data_version = '';
			$data_name = '';


			if($version)
			$data_version = ' data-version="'.$version.'"';

			if($nameauto==false)
			$data_name = 'data-name="'.$name.'"';

			if($type != 'include')
				$join .= ''.$data_name.''.$data_version.'>';

			if($content != '')
				$join .= $content;
			

			if($type == 'script'){
				$join .= '</'.$type.'>'."\n";
			}else{
				$join .= "\n";
			}
	
			

			if($into == 'head'){
				$headJoin .= $join;
				
			

			}else if($into == 'footer'){
				$footerJoin .= $join;
			}
		}
		
		if(isset(self::$options['join']) && self::$options['join'] == true){
			
			$compress = isset(self::$options['compress'])?self::$options['compress']:false;


			$appDir = Suite_globals::get('app/dir');
			$appUrl = Suite_globals::get('app/url');
			$dataDir = $appDir.'_data/';
			$dataUrl = $appUrl.'_data/';
			$assetsDir = $dataDir.'assets/';
			$assetsUrl = $dataUrl.'assets/';

			$assetsCacheDir = $dataDir.'assets/cache/';
			$assetsCacheUrl = $dataUrl.'assets/cache/';

			if(!file_exists($assetsCacheDir))
			mkdir($assetsCacheDir,0777,true);

			$assetsCacheFile_css_head = $assetsCacheDir.'style-head.css';
			$assetsCacheFile_js_head = $assetsCacheDir.'script-head.js';

			$assetsCacheFile_css_footer = $assetsCacheDir.'style-footer.css';
			$assetsCacheFile_js_footer = $assetsCacheDir.'script-footer.js';

			$if_cached_css_head = false;
			$if_cached_css_head = false;
			$if_cached_css_footer = false;
			$if_cached_js_footer = false;

			if(file_exists($assetsCacheFile_css_head))
				$if_cached_css_head = true;
			else
				$if_cached_css_head = false;

			if(file_exists($assetsCacheFile_js_head))
				$if_cached_js_head = true;
			else
				$if_cached_js_head = false;

			if(file_exists($assetsCacheFile_css_footer))
				$if_cached_css_footer = true;
			else
				$if_cached_css_footer = false;

			if(file_exists($assetsCacheFile_js_footer))
				$if_cached_js_footer = true;
			else
				$if_cached_js_footer = false;



			$outerTag_head = '';
			$outerTag_footer = '';

			$head_css_content = '';
			$head_js_content = '';
			$footer_css_content = '';
			$footer_js_content = '';
			foreach ($srcJoin as $key => $value) {
				$into = isset($value['into'])?$value['into']:null;
				$rel = isset($value['rel'])?$value['rel']:null;
				$href = isset($value['href'])?$value['href']:null;
				$src = isset($value['src'])?$value['src']:null;
				$type = isset($value['type'])?$value['type']:null;

				if($into == 'head'){
					if($rel != 'stylesheet' && $src == null){
						$outerTag_head .='<link ';
						foreach ($value as $key => $value) {
							$outerTag_head .= $key.'="'.$value.'"';
						}
						$outerTag_head .='>'."\n";
												
					}

					if($rel == 'stylesheet' && $if_cached_css_head == false){
						$resultReq = Suite_libs::run('Http/Request/url',array(
							'url'=>$href
						));			
						$head_css_content .= self::compress($resultReq,$compress,'css');
					}

					if($src != null && $if_cached_js_head == false){

						$resultReq = Suite_libs::run('Http/Request/url',array(
							'url'=>$src
						));			
						$head_js_content .= self::compress($resultReq,$compress,'js');
					}
				}

				if($into == 'footer'){

					if($rel != 'stylesheet' && $src == null){
						$outerTag_footer .='<link ';
						foreach ($value as $key => $value) {
							$outerTag_footer .= $key.'="'.$value.'"';
						}
						$outerTag_footer .='>'."\n";
												
					}

					if($rel == 'stylesheet' && $if_cached_css_footer == false){
						$resultReq = Suite_libs::run('Http/Request/url',array(
							'url'=>$href
						));			
						$footer_css_content .= self::compress($resultReq,$compress,'css');
					}

					if($src != null && $if_cached_js_footer == false){						
						$resultReq = Suite_libs::run('Http/Request/url',array(
							'url'=>$src
						));			
						$footer_js_content .= self::compress($resultReq,$compress,'js');
					}
				}
			}

			
			
			
			
			if($if_cached_css_head == false)			
			file_put_contents($assetsCacheFile_css_head, $head_css_content);

			if($if_cached_js_head == false)
			file_put_contents($assetsCacheFile_js_head, $head_js_content);

			if($if_cached_css_footer == false)
			file_put_contents($assetsCacheFile_css_footer, $head_css_content);		

			if($if_cached_js_footer == false)
			file_put_contents($assetsCacheFile_js_footer, $footer_js_content);

			$headJoin = $outerTag."\n";
			$headJoin .= '<link rel="stylesheet"   href="'.$assetsCacheUrl.'style-head.css" />';
			$headJoin .= '<script src="'.$assetsCacheUrl.'script-head.js" type="text/javascript"></script>';
			
			$footerJoin = $outerTag_footer."\n";
			$footerJoin .= '<link rel="stylesheet"   href="'.$assetsCacheUrl.'style-footer.css" />';
			$footerJoin .= '<script src="'.$assetsCacheUrl.'script-footer.js" type="text/javascript"></script>';
			

			// $footerJoin = '<style>'.$footer_css_content.'</style>';
			// $footerJoin .= '<script>'.$footer_js_content.'</script>';

			/*$headJoin = '<style>'.$head_css_content.'</style>';
			$headJoin .= '<script>'.$head_js_content.'</script>';
			$footerJoin = '<style>'.$footer_css_content.'</style>';
			$footerJoin .= '<script>'.$footer_js_content.'</script>';*/

		}

		// if($forcePar == null){				
			$headJoin = self::$headContent.$headJoin;
			$footerJoin = self::$footerContent.$footerJoin;
		// }
		

		//self::$headContent = '';
		//self::$footerContent = '';
		return array('head'=>$headJoin,'footer'=>$footerJoin);
	}

	public static function compress($content = '',$enabled = false,$type = null){
		if($enabled == true){
			if($type == 'js'){

				// remove comentarios
				$content = preg_replace('~//<!\[CDATA\[\s*|\s*//\]\]>~', '', $content);
				$content = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\)\/\/[^"\'].*))/', '', $content);

				$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);        			
			    $content = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), ' ', $content);			  
			    $content = preg_replace(array('(( )+{)','({( )+)'), '{', $content);
			    $content = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $content);
			    $content = preg_replace(array('(;( )+)','(( )+;)'), ';', $content);


			}else if($type == 'css'){
				$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
			    $content = str_replace(["\r\n","\r","\n","\t",'  ','    ','     '], ' ', $content);
			    $content = preg_replace(['(( )+{)','({( )+)'], '{', $content);
			    $content = preg_replace(['(( )+})','(}( )+)','(;( )*})'], '}', $content);
			    $content = preg_replace(['(;( )+)','(( )+;)'], ';', $content);
			}else if($type == 'html'){
				$content = preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$content));
			}
			
		}

		return $content;
	}
}