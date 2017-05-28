<?php

class Control{

	private $time_start = null;

	function __construct(){}

	
	public function load(){
		
		$this->time_start = microtime(true); 
		
	
		return array(
			'replace'=>array(				
			)
		);
	}

	public function render($html){
		
		$time_end = microtime(true);
		$execution_time = microtime(true) - $this->time_start;
		
		$duration = $execution_time;
		$hours = (int)($duration/60/60);
		$minutes = (int)($duration/60)-$hours*60;
		$seconds = (int)$duration-$hours*60*60-$minutes*60;		
		$ms = (float)substr($duration, 0, 5);;

		$html = str_replace('[loadtime]', $execution_time, $html);
		$html = str_replace('[loadtime:seconds]', $seconds, $html);
		$html = str_replace('[loadtime:ms]', $ms, $html);

		
		return $html;
	}
}