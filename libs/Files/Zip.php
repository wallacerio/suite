<?php

class Files__Zip{
	
	 public static function extract($options) { 
	 	$src = isset($options['src'])?$options['src']:null;
	 	$target = isset($options['target'])?$options['target']:null;

	 	// $target = pathinfo(realpath($src), PATHINFO_DIRNAME);

		 	$zip = new ZipArchive;
			$res = $zip->open($src);
			if ($res === TRUE) {
			  // extract it to the path we determined above
			  $zip->extractTo($target);
			  $zip->close();			
			  return true;
			} else {
			  return false;
			}
	 }

}

