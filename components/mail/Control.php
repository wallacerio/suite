<?php

class Control extends Model{
	
	function __construct(){}
	
	public function send($args){

		
		$currentDir = Suite_globals::get('current/dir');
		
		$methodmail = isset($args['methodmail'])?$args['methodmail']:null;		
		if($methodmail == 'phpmailer'){
			return $this->phpmailer($currentDir,$args);
		}else{
			return $this->mail($currentDir,$args);
		}
	}

}