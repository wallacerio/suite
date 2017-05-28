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



class Suite_globals{

	private static $arrays = Array();

	/**
	 * cria e define uma variavel
	 * @param [string] $target  [key]
	 * @param [any value] $content [content]
	 *
	 * @example Suite_globals::set('app/dir','value');
	 */
	public static function set($target,$content){
		$arrays = self::$arrays;		
		$targetArray = explode('/', $target);
		$joinString = '';
		foreach ($targetArray as $key => $value) {			
			$joinString .= '["'.$value.'"]';				
		}		
		eval('$arrays'.$joinString.' = $content;');	
		self::$arrays = $arrays;		
	}

	public static function get($target = null){
		$arrays = self::$arrays;
		$targetArray = explode('/', $target);
		$targetArray = array_filter($targetArray);
		$joinString = '';
		if(count($targetArray)>0)
		foreach ($targetArray as $key => $value) {			
			$joinString .= '["'.$value.'"]';				
		}				
		eval('$returns = isset($arrays'.$joinString.')?$arrays'.$joinString.':null;');
		return $returns;
	}

}