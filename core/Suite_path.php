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

class Suite_path{
	function __construct(){
		$this->load();
	}

	/**
	 * [path2url description]
	 * @param  [type] $file     [description]
	 * @param  string $Protocol [description]
	 * @return [type]           [description]
	 */
	public function path2url($file, $Protocol='http://') {
		

      	$file = strtolower($file);
        $HTTP_HOST = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['PWD'];
        


        $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
        $DOCUMENT_ROOT = strtolower($DOCUMENT_ROOT);
        
        $SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

         $SERVER_SOFTWARE = isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']:$_SERVER['_'];
          $SERVER_SOFTWARE = strtolower($SERVER_SOFTWARE);
       

           $DOCUMENT_ROOT = str_replace($DOCUMENT_ROOT, '', $file);

          if(substr($DOCUMENT_ROOT, 0,1)=="/" || substr($DOCUMENT_ROOT, 0,1)=="\\"){
            $DOCUMENT_ROOT = substr($DOCUMENT_ROOT,1);
          }

          return $Protocol.$HTTP_HOST.DIRECTORY_SEPARATOR.$DOCUMENT_ROOT;
       
        
    }

    /**
     * [load description]
     * @return [type] [description]
     */
	public function load(){

		$domain_dir = Suite_globals::get('http/domain/dir');
		$domain_url = Suite_globals::get('http/domain/url');
		
		Suite_globals::set('base',array(
				'dir'=>$domain_dir,
				'url'=>$domain_url,
			));
		

		$suiteDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
		if($domain_url == null){
			$suiteUrl= null;
		}else{
			$suiteUrl = $this->path2url(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
			$suiteUrl = str_replace('\\', '/', $suiteUrl); // verificar se pode ocorrer algum erro
		}
		Suite_globals::set('core',array(
				'dir'=>$suiteDir,
				'url'=>$suiteUrl,
			));

		
			
	}
}