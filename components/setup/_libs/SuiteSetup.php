<?php

class SuiteSetup{

	/**
	 * adiciona uma opção para listagem no console terminal 
	 * @param [type] $component [description]
	 * @param [type] $array     [description]
	 */
	public static function addOption($component = null,$array){
		if($component == '' || $component == null)
			$component = 'setup.default';
		Suite_globals::set('setup/'.$component,$array);
	}	
}