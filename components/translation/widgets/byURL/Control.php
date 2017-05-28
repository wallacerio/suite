<?php

	$content = $this->getPathTranlate();	
	
	return array(
		'replace'=>array(
			'{translate:view}' => $content
		)	
	);