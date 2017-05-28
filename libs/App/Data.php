<?php

class App__Data{
	public static function get($options = null){
		
		if($options == null) return null;

		$pluginData = null;
		$pluginName = isset($options['name'])?$options['name']:null;
		$fileName = isset($options['file'])?$options['file']:'data.json';
		$fileType = isset($options['type'])?$options['type']:'json';

		if($pluginName == null) return null;

		$appDir = Suite_globals::get('app/dir');		
		$pluginsFileData = $appDir . '_data/'.$pluginName.'/'.$fileName;
	
		if(file_exists($pluginsFileData)){
			$pluginDataContent = file_get_contents($pluginsFileData);
			if($fileType == 'json')
				$pluginData = json_decode($pluginDataContent,true);
			else
				$pluginData = $pluginDataContent;
		}

		return $pluginData;
	}


	public static function set($options = null){
		if($options == null) return null;

		$pluginData = null;
		$pluginName = isset($options['name'])?$options['name']:null;
		$fileName = isset($options['file'])?$options['file']:'data.json';
		$fileType = isset($options['type'])?$options['type']:'json';
		$pluginDataContent = isset($options['content'])?$options['content']:'';

		if($pluginName == null) return null;

		$appDir = Suite_globals::get('app/dir');
		$pluginsFileDataDir = $appDir . '_data/'.$pluginName.'/';	
		if(!file_exists($pluginsFileDataDir))
			mkdir($pluginsFileDataDir,0777,true);

		$pluginsFileData = $pluginsFileDataDir.$fileName;		

		if($fileType == 'json' && is_array($pluginDataContent)){
			$pluginDataContent = json_encode($pluginDataContent);
		}

		file_put_contents($pluginsFileData,$pluginDataContent);					

	}



	public static function del($options = null){
		if($options == null) return null;

		$pluginData = null;
		$pluginName = isset($options['name'])?$options['name']:null;
		$fileName = isset($options['file'])?$options['file']:'data.json';
		$fileType = isset($options['type'])?$options['type']:'json';
		$pluginDataContent = isset($options['content'])?$options['content']:'';

		if($pluginName == null) return null;

		$appDir = Suite_globals::get('app/dir');
		$pluginsFileDataDir = $appDir . '_data/plugins/'.$pluginName.'/';

		Suite_libs::run('Files/Scan/rmdir',$pluginsFileDataDir);
	

	}
}