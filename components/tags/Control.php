<?php 

class Control{
	
	public function render($html = ''){
		
		$compTags = new CompTags();
		$html = $compTags->render($html);

		return $html;		
	}

}