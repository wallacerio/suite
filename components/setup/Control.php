<?php

class Control extends Model{
	
	function __construct(){
		CompConsoleOptionsBasics::opDefault();
		CompConsoleOptionsBasics::opBasics();
		CompConsoleOptionsBasics::opComponents();
		


	}



	public function load($args){

		$baseDir = Suite_globals::get('base/dir');
		$coreDir = Suite_globals::get('core/dir');

		if(!file_exists($baseDir.'console')){
			copy($coreDir.'cp_console',$baseDir.'console');			
		}
		
		$target = Suite_globals::get('http/target');
		$targetArray = explode('/', $target);
		

		if($targetArray[0] == '_setup'){				
			$CompConsoleGui = Suite_components::callDirectClass('setup/gui/_libs/CompConsoleGui');
			return $CompConsoleGui->gui($args);	
		}

	}




	
}