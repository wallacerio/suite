<?php

class Class__Class{

	public function requires($arrayDir = null){

		/*echo '<pre>';
		print_r(Suite_globals::get(''));
		echo '</pre>';*/
		
		$dirArray = Array();

		foreach ($arrayDir as $key => $value) {
			if(substr($value, 0,1)=='/'){
				$pathDir = Suite_globals::get('base/dir');
			}

			$dirArrayCurrent = Suite_libs::run('Files/scan/files',$pathDir.$value);
			$dirArray = array_merge($dirArray,$dirArrayCurrent);
			
		}

		foreach ($dirArray as $key => $value) {
			require_once $key;
		}

	}
	
}