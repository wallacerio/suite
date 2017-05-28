<?php

class Model{
	



	public function insertTag(){
		$domainUrl = Suite_globals::get('domain/url');
	
		$action = Suite_globals::get('http/action');
		$posfix = Suite_globals::get('http/posfix');

		$listLanguages = $this->listLanguages();
		
		if($listLanguages == null)
			return ;

		foreach ($listLanguages as $key => $value) {						

			$idlang = $key;
			$idlang = str_replace('.json', '', $idlang);			
			
			$languageHref = $domainUrl.$idlang.'/'.(($action == 'home')?'':$action.'/').$posfix;

			$register[] = array('link'=>array('parameters'=>array('href'=>$languageHref,'rel'=>'alternate',"hreflang"=>$idlang),'into'=>'head'));


		}

		return $register;

	}

	public function listLanguages(){
		$appDir = Suite_globals::get('app/dir');
		$languagesDir = $appDir . '_data/translation/languages/';

		$languagesDirScanArray = Suite_libs::run('Files/Scan/onlyFiles',$languagesDir);

		if(count($languagesDirScanArray)<1)
			return null;


		foreach ($languagesDirScanArray as $key => $value) {			
			$prefix = substr($value, 0,strrpos($value, '.'));
			
			$name = $prefix;

			if(file_exists($languagesDir.$value)){
				$languageJson = file_get_contents($languagesDir.$value);
				$language = json_decode($languageJson,true);					
				$name = $language['header']['name'];
			}

			
			$languageDisponible[$prefix] = $name;
		}

		return $languageDisponible;
	}

	public function getBrowserLanguages(){

		$languageDisponible = array();

		$prefix = Suite_globals::get('http/prefix');

		$language = Suite_globals::get('http/language');
		$appDir = Suite_globals::get('app/dir');
		$languagesDir = $appDir . '_data/translation/languages/';

		$languagesDirScanArray = Suite_libs::run('Files/Scan/onlyFiles',$languagesDir);
		if(count($languagesDirScanArray)>0)
		foreach ($languagesDirScanArray as $key => $value) {			
			$name = substr($value, 0,strrpos($value, '.'));
			$languageDisponible[$name] = $name;
		}

		$languageArray = explode(',', $language);
		if(count($languageArray)>0)
		foreach ($languageArray as $key => $value) {
			$name = strtolower($value);
			$nameArray = explode(';', $name);
			$name = isset($nameArray[0])?$nameArray[0]:$name;
			$languageOnBrowserArray[$name]=$name;
		}
		
	
		// caso seja setado na barra de endereço a linguagem
		// se existir na lista, então retorna o prefixo da linguagem
		if(in_array($prefix, $languageDisponible)){
			return $prefix;
		}
		
		foreach ($languageOnBrowserArray as $key => $value) {			
			if(in_array($key, $languageDisponible)){
				return $key;
			}
		}

		

	}


	public function getLanguageDefault(){
		$target = 'en-us';
		$currentDir = Suite_globals::get('current/dir');
		$languagesDir = $currentDir . 'languages/';
		$languageFile = $languagesDir . $target.'.json';
		if(file_exists($languageFile)){
			$languageJson = file_get_contents($languageFile);
			$language = json_decode($languageJson);

			return $language;
		}

		return null;
	}

	public function getLanguage($target = 'en-us',$dir = null){
		if($dir == null){
			$coreDir = Suite_globals::get('core/dir');			
			$languagesDir = $coreDir . 'components/translation/' . 'languages/';
			
		}else{		
			$languagesDir = $dir . '';
		}

		$languageFile = $languagesDir . $target.'.json';					

		if(file_exists($languageFile)){
			$languageJson = file_get_contents($languageFile);
			$language = json_decode($languageJson,true);

			return $language;
		}

		return null;
	}

	public function getPathTranlate(){
		$target = Suite_globals::get('http/target');

		$langCurrent = isset($_COOKIE['lang-current'])?$_COOKIE['lang-current']:'en';
		
		$listLanguagesArray = $this->listLanguages();
	
		// remove o prefixo de linguagem da url (target)
		$targetArray = explode('/', $target);	
		if(count($listLanguagesArray)>0)
		foreach ($listLanguagesArray as $key => $value) {
			if($targetArray[0] == $key){
				$langCurrent = isset($targetArray[0])?$targetArray[0]:$langCurrent;
				$targetArray[0] = str_replace($key, '', $targetArray[0]);
				break;
			}
		}
		$target = implode('/', $targetArray);
		
		

		$appDir = Suite_globals::get('app/dir');
		$languagesDir = $appDir . '_data/translation/path/';	

		$currentFileLang = $languagesDir.$target.'/'.$langCurrent.'.html';
		$currentFileLang = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $currentFileLang);
		
		
		$content = '';

		if(file_exists($currentFileLang)){
			$content = file_get_contents($currentFileLang);
			return $content;				
		}
		return false;
	}

	public function pathTranlate($view){
		

		$content = $this->getPathTranlate();		
		if($content){
			$view = str_replace('@_translation_by_current_path_@', $content, $view);	
		}
		
		return $view;
	}

	public function reference($string,$target){
		
		$appDir = Suite_globals::get('app/dir');
		$languagesDir = $appDir . '_data/translation/languages/';
		


		$targetLanguageCustom = $this->getLanguage($target,$languagesDir);

		

		$targetLanguage = $this->getLanguage($target);

		if(count($targetLanguage['reference']) > 0)
		foreach ($targetLanguage['reference'] as $key => $value) {				
			$wordTo = $targetLanguage['reference'][$key];	

			$string = str_replace('@'.$key.'@', $wordTo, $string);
		}


		$nameLanguage = isset($targetLanguageCustom['header']['name'])?$targetLanguageCustom['header']['name']:'';

		
		if(count($targetLanguageCustom['reference']) > 0)
		foreach ($targetLanguageCustom['reference'] as $key => $value) {	
			$wordTo = $targetLanguageCustom['reference'][$key];				
			$string = str_replace('@'.$key.'@', $wordTo, $string);
			
		}

		$string = str_replace('@name-language@', $nameLanguage, $string);
		$string = str_replace('@prefix-language@', $target, $string);

	
		return $string;
	}


	public function translate($string,$target,$source = 'en-us'){
		
		$sourceLanguage = $this->getLanguage($source);
		$targetLanguage = $this->getLanguage($target);

		$stringArray = explode(' ', $string);


		return $string;
	}

	

	public function referenceAction(){

		$target = isset($_POST['target'])?$_POST['target']:$this->getBrowserLanguages();
		$string = isset($_POST['string'])?$_POST['string']:'';

		
	
		$string = $this->reference($string,$target);
		
	
		return array('response'=>$string);
	}

}