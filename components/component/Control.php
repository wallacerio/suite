<?php

class Control{
	
	public function load(){		
		
		$component = new CompComponent();
		
		
		if(method_exists($component, 'load')){
			$resultComponent = $component->load();		
			return $resultComponent;
		}
		
	}


	public function render($html){
		
		$compComponent = new CompComponent();
			
		$resultPlugin = $compComponent->tagToWidgets($html);	
		$html = isset($resultPlugin['html'])?$resultPlugin['html']:$html;		
		
		return $html;
	}


	
}