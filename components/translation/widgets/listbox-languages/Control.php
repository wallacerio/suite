<?php



	$target = Suite_globals::get('http/target');
	$targetArray = explode('/', $target);

	$action = Suite_globals::get('http/action');
	$prefix = Suite_globals::get('http/prefix');
	$prefixArray = explode('/', $prefix);

	$languageCurrent = $this->getBrowserLanguages();


	$listLanguages = $this->listLanguages();
	

	

	
	foreach ($listLanguages as $key => $value) {		
		if( isset($targetArray[0]) && $targetArray[0] == $key ){
			unset($targetArray[0]);
		}		
	}
	$targetArray = array_values($targetArray);
	$target = implode('/', $targetArray);

	

	

	$laguages_options = ''; 
	if(count($listLanguages)>0)
	foreach ($listLanguages as $key => $value) {
		$selected = '';
		
		if(in_array($key,$prefixArray))
			$selected = 'selected';

		$targetNew = $key.'/'.$target;
		$laguages_options .= '<option value="'.$key.'" '.$selected.' data-action="'.$targetNew.'" >'.$value.' - ('.$key.')</option>';
	}

	$translation_listbox_langagesJs = $currentWidgetUrl.'assets/js/listbox-languages.js';
 
return array(
	'replace'=>array(
		'{laguages:options}' => $laguages_options
	),
	'register'=>array(		
		array('script'=>array('parameters'=>array('src'=>$translation_listbox_langagesJs,'type'=>'text/javascript','defer'),'into'=>'footer')),
		
	)
);