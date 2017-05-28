<?php

class Control{
	public function load(){
	}

	public function render($html = ''){
		
		$dataArray = Suite_libs::run('App/Data/get',array(
			'name'=>'assets'
		));
	
		CompRegister::$options = $dataArray;
		
		$CompAssets = new CompAssets();
		$html = $CompAssets->render($html);
		
		return $html;
	}
}