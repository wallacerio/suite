<?php

class Control extends Model{
	
	function __construct(){		
		// monta opções do console
		CompPluginsConsole::optionConsole();

	}

	
	public function load(){
			
		$plugins = new CompPlugins();
		$resultPlugin = $plugins->directAction();

		// call direct from url to method
		if($resultPlugin != null){									
			Suite_view::out($resultPlugin);					
			return $resultPlugin;	
		} 

		// verifica se o usuário esta acessando o manaer dos plugins
		$pluginsManager = new CompPluginsManager();
		$resultPlugin = $pluginsManager->managerUrlAccess();
		if($resultPlugin != null){						
			Suite_view::out($resultPlugin);			
			return $resultPlugin;	
		} 

		$resultPlugin = $plugins->load();	
		return $resultPlugin;
	}


	public function render($html){	

		$plugins = new CompPlugins();			
		$resultPlugin = $plugins->tagToWidgets($html);	
		$html = isset($resultPlugin['html'])?$resultPlugin['html']:$html;		
		$html = $plugins->render($html);	
		return $html;
	}

}