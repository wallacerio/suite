<?php
/*
 * Suite Framework
 * ================
 * suite.wallrio.com
 *
 * This file is part of the Suite Core.
 *
 * Wallace Rio <wallrio@gmail.com> 
 * 
 */

class Suite_class{
	
	/**
	 * [path2url description]
	 * @param  [type] $file     [description]
	 * @param  string $Protocol [description]
	 * @return [type]           [description]
	 */
	public static function path2url($file, $Protocol='http://') {		
      	$file = strtolower($file);
        $HTTP_HOST = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['PWD'];
        
        $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
        $DOCUMENT_ROOT = strtolower($DOCUMENT_ROOT);
        
        $SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

         $SERVER_SOFTWARE = isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']:$_SERVER['_'];
          $SERVER_SOFTWARE = strtolower($SERVER_SOFTWARE);
       

           $DOCUMENT_ROOT = str_replace($DOCUMENT_ROOT, '', $file);

          if(substr($DOCUMENT_ROOT, 0,1)=="/" || substr($DOCUMENT_ROOT, 0,1)=="\\"){
            $DOCUMENT_ROOT = substr($DOCUMENT_ROOT,1);
          }

          return $Protocol.$HTTP_HOST.DIRECTORY_SEPARATOR.$DOCUMENT_ROOT;
       
        
    }




    /**
     * [loadLibs description]
     * @param  [type] $target [description]
     * @return [type]         [description]
     */
	public static function loadLibs($target = null){	

		if(substr($target, 0,1)=='/'){
			$componentsLibsDir = $target . DIRECTORY_SEPARATOR.'_libs'.DIRECTORY_SEPARATOR;

		}else if(strpos($target,':')!= -1){
			$componentsLibsDir = $target . DIRECTORY_SEPARATOR. '_libs'.DIRECTORY_SEPARATOR;	

		}else{
			$coreDir = Suite_globals::get('core/dir');
			$componentsLibsDir = $coreDir . $target . DIRECTORY_SEPARATOR.'_libs'.DIRECTORY_SEPARATOR;	
		}
		
		

		$result = Suite_libs::run('Files/Scan/onlyFilesRecursiveSequence',$componentsLibsDir);	
	
		if(count($result)>0)
		foreach ($result as $key => $value) {	
			
			if(strrpos($value, '.') != false)
				$extension = substr($value, strrpos($value, '.'));
			else
				$extension = "";

			if($extension != '.php')
				continue;
		
			$className = substr($value,0,strrpos($value, '.'));


			if(class_exists($className)) continue;
			$classContent =  file_get_contents($key);				
			$classContent = str_replace('<?php', '', $classContent);																	
			eval($classContent);						
		}
	}


	/**
	 * [load description]
	 * @param  [type] $path  [description]
	 * @param  [type] $sufix [description]
	 * @return [type]        [description]
	 */
	public static function load($path = null,$sufix = null){		
		$obj = self::loadClasses($path,$sufix);
		return array('control'=>$obj['control'],'model'=>$obj['model'],'view'=>$obj['view'],'options'=>$obj['options']);
	}

	

	/**
	 * [loadClass description]
	 * @param  [type] $path    [description]
	 * @param  [type] $sufix   [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public static function loadClass($path = null,$sufix = null,$options = null){		
		$obj = self::loadClasses($path,$sufix,$options);
		return array('control'=>$obj['control'],'model'=>$obj['model'],'view'=>$obj['view'],'options'=>$obj['options']);
	}


	/**
	 * [loadClasses description]
	 * @param  [type] $path    [description]
	 * @param  [type] $sufix   [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public static function loadClasses($path = null,$sufix = null,$options = null){
		
		$posfix = '';//time();

		$controlNameFirst = isset($options['control'])?$options['control']:'Control';
		$ModelNameFirst = isset($options['model'])?$options['model']:'Model';
		$viewNameFirst = isset($options['view'])?$options['view']:'view';
		$optionsNameFirst = isset($options['options'])?$options['options']:'options';

		if(substr($path, strlen($path)-1, strlen($path))=='/')
			$path = substr($path, 0,strlen($path)-1);

		$path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

		$modelContent = null;
		$control = null;
		$model = null;
		$view = null;		
		$optionsConfig = null;		
		$nameDirClassArray = explode('/',$path);
		$nameDirClass = end($nameDirClassArray);
		$controlPath = $path . DIRECTORY_SEPARATOR . $controlNameFirst . '.php';
		$modelPath = $path . DIRECTORY_SEPARATOR . $ModelNameFirst  . '.php';
		$viewPath = $path . DIRECTORY_SEPARATOR . $viewNameFirst  . '.php';		
		$optionsPath = $path . DIRECTORY_SEPARATOR . $optionsNameFirst  . '.json';		
		$libsPath = $path . DIRECTORY_SEPARATOR . 'libs'.DIRECTORY_SEPARATOR;
		


		$dir = $path.DIRECTORY_SEPARATOR;
		$url = self::path2url($path).'/';

		Suite_globals::set('current/dir',$dir);
		Suite_globals::set('current/url',$url);


		if(file_exists($modelPath)){	
			$nameDirClassNew = (str_replace(DIRECTORY_SEPARATOR, '__', $nameDirClass));	

			$nameDirClassNew = (str_replace('\\', '__', $nameDirClassNew));
			$nameDirClassNew = (str_replace('/', '__', $nameDirClassNew));
			$nameDirClassNew = (str_replace(':', '_', $nameDirClassNew));
			$nameDirClassNew = (($sufix != null)?$sufix.'__':'').(str_replace('-', '_', $nameDirClassNew)).'___'.'Model'.$posfix;						
			$modelName = $nameDirClassNew;			
			$classContent = file_get_contents($modelPath);			
			$classContent = preg_replace('#class +?'.$ModelNameFirst .' ?(.*)?#', 'class '.$modelName.' \1', $classContent);					
			$classContent = str_replace('<?php', '', $classContent);																			
			$modelContent = $classContent;	

		
		}

			
		if(file_exists($controlPath)){		
			


			$nameDirClass = (str_replace(DIRECTORY_SEPARATOR, '__', $nameDirClass));
			$nameDirClass = (str_replace('\\', '__', $nameDirClass));
			$nameDirClass = (str_replace('/', '__', $nameDirClass));
			$nameDirClass = (str_replace(':', '_', $nameDirClass));
			$nameDirClass = strtolower($nameDirClass);
			$nameDirClass = (($sufix != null)?$sufix.'__':'').(str_replace('-', '_', $nameDirClass)).''.$posfix;					
			$nameDirClass = $nameDirClass . '_suite';
			
			if(!class_exists($nameDirClass)){

				$classContent = file_get_contents($controlPath);			
				$classContent = preg_replace('#class +?'.$controlNameFirst.' ?(.*)?#', 'class '.$nameDirClass.' \1', $classContent);								
				if (preg_match('#extends +?'.$ModelNameFirst.' ?(.*)?#', $classContent)){
					if(isset($modelName) ){												
						$classContent = preg_replace('#extends +?'.$ModelNameFirst.' ?(.*)?#', 'extends '.$modelName.' \1', $classContent);											
						
						eval($modelContent);						
						$model = new $modelName();
					}				
				}		
				$classContent = str_replace('<?php', '', $classContent);	
				
				
				eval($classContent);
				$libs = Suite_class::loadLibs($path);	
			}


			$control = new $nameDirClass();		
				
			
		}

		if(file_exists($viewPath)){		
			ob_start();
		    include($viewPath);
		    $viewContent = ob_get_clean();			
			$view = $viewContent;
		}

		if(file_exists($optionsPath)){		
			// ob_start();
		    // include($optionsPath);
		    // $optionsContent = ob_get_clean();			
			$optionsConfig = file_get_contents($optionsPath);
			$optionsConfig = json_decode($optionsConfig,true);
		}


		return array(
			'control' => $control,
			'model' => $model,
			'view' => $view,
			'options' => $optionsConfig
		);
	}


	
}