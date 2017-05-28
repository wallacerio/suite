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


$path = dirname(dirname(__FILE__)).'/';
define('suite_path', $path);
	
require_once suite_path.'core/Suite_bootstrap.php';

class Suite {		

	/**
	 * [load description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public static function load($options = null){				
		$bootstrap = new Suite_bootstrap();
		$out = $bootstrap->load($options);				
		return $out;
	}

	/**
	 * [component description]
	 * @param  [type] $name       [description]
	 * @param  [type] $parameters [description]
	 * @return [type]             [description]
	 */
	public static function component($name = null,$parameters = null){	
		$bootstrap = new Suite_bootstrap();
		return $bootstrap->component($name,$parameters);
	}

	/**
	 * [html description]
	 * @param  [type] $name    [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public static function html($name = null,$options = null){	
		$bootstrap = new Suite_bootstrap();
		return $bootstrap->html($name,$options);
	}

	/**
	 * [lib description]
	 * @param  [type] $lib        [description]
	 * @param  [type] $parameters [description]
	 * @return [type]             [description]
	 */
	public static function lib($lib,$parameters){			
		$bootstrap = new Suite_bootstrap();		
		return Suite_libs::run($lib,$parameters);
	}

}


