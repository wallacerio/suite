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

class Suite_server{
	function __construct(){
		$this->load();
	}

	/**
	 * [load description]
	 * @return [type] [description]
	 */
	public function load(){
		$server = array(
			'separator'=>DIRECTORY_SEPARATOR
		);
		Suite_globals::set('server',$server);
	}
}