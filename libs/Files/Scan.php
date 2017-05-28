<?php

class Files__Scan{
	
  /**
    * Captura somente diretórios
    * @param  [type] $dir [description]
    * @return [type]      [description]
    */   
    public function files($dir = null,$extra = null ){

        // if(isset($_POST['dir']))
            // $dir = $_POST['dir'];


    if(!file_exists($dir))
        return null;

    if($extra == null){
      $extra = create_function('$value', 'return true;');    
    }

    $resultArray = null;
    $dirArray = scandir($dir);
    foreach($dirArray as $key=>$value){

         
        if($value != '.' && $value != '..' && !is_dir($dir.DIRECTORY_SEPARATOR.$value) && ($extra($value))){

         /*   if(substr($value, 0,1)=='/')
              $value = substr($value, 1);

            if(substr($dir, strlen($dir),strlen($dir)-1)=='/')
              $dir = substr($dir, 0,strlen($dir)-1);*/

            $path = $dir.'/'.$value;

            $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

            $resultArray[$path] = $value;
        }
    }
    return $resultArray;
   }

    /**
     * Cria diretórios e arquivos, baseado no parametro passado
     * @param  [array] $arrayStruct [parametro com as instruções de criação]
     * @param  [type] $dirBase     [parametro null]
     * @param  [type] $newDir      [parametro null]
     * @return [type]              [parametro null]
     *
     * examplo:
     *
     * Suite_libs::run("Files/Scan/mkstructure",
     *      array(
     *         "dir"=>$appDir,                        // diretório raiz de onde a criação será iniciada
     *         "structure"=>array(                    // array com a estrutura de diretórios e arquivos
     *           "_hmvc/new/test.txt"=>"test",
     *           "_hmvc/"=>array(
     *             "options.json"=>"{}",
     *             "home/"=>array(
     *               "Control.php"=>"control",
     *               "Model.php"=>"Model",
     *               "view.php"=>"view"
     *             )
     *           )
     *           
     *         )
     *       )
     *     );
     *     
     */
    public static function mkstructure($arrayStruct = null,$dirBase = null,$newDir = null) { 
      if($dirBase == null)
        $dirBase = isset($arrayStruct['dir'])?$arrayStruct['dir']:null;

      if($newDir != null){
        $structure = $newDir;
      }else{
        $structure = isset($arrayStruct['structure'])?$arrayStruct['structure']:null;
      }

      foreach ($structure as $key => $value) {
   
        $target = (gettype($key) == "integer")?$value:$key;
        $targetFull = $dirBase.$target;

        if(substr($target, strlen($target)-1,strlen($target))=='/'){$type="dir";}else{$type="file";}
          
          if($type == 'dir'){           
              if(!file_exists($targetFull))
              mkdir($targetFull,0777,true);
              if(is_array($value))
                self::mkstructure(null,$targetFull,$value);           
          }else if($type == 'file'){
            if( !file_exists( dirname($targetFull)) ){
              echo $targetFull."\n";
              if(!file_exists(dirname($targetFull)))
              mkdir(dirname($targetFull),0777,true);
            }
            // echo $value;
            $value = str_replace('[?php', '<?php', $value);
            file_put_contents($targetFull, $value);
          }
      }
    }

    public static function rmdir($dir) { 
       if (is_dir($dir)) { 
         $objects = scandir($dir); 
         foreach ($objects as $object) { 
           if ($object != "." && $object != "..") { 
             if (filetype($dir."/".$object) == "dir") self::rmdir($dir."/".$object); else unlink($dir."/".$object); 
           } 
         } 
         reset($objects); 
         rmdir($dir); 
       } 
     }

     
     /**
    * Captura somente diretórios
    * @param  [type] $dir [description]
    * @return [type]      [description]
    */
   public  function scanDir($dir,$extra = null ){
    // print_r($dir);
    // echo '>>'.$dir;

    if(!file_exists($dir) ){       
        return null;
      }

       if(is_file($dir))
        return null;

      

    if($extra == null){
      //$extra = function(){return true;};    
      $extra = create_function('$value', 'return true;');    
      // return false;   
    }

    $resultArray = null;
    $dirArray = scandir($dir);

 

    foreach($dirArray as $key=>$value){

       
        if($value != '.' && $value != '..' &&  ($extra($value))){
            $resultArray[] = $value;
        }
    }

    
    return $resultArray;
   }


	/**
    * Captura somente diretórios
    * @param  [type] $dir [description]
    * @return [type]      [description]
    */   
    public function onlyFiles($dir = null,$extra = null ){

        // if(isset($_POST['dir']))
            // $dir = $_POST['dir'];


    if(!file_exists($dir))
        return null;

    if($extra == null){
      $extra = create_function('$value', 'return true;');    
    }

    $resultArray = null;
    $dirArray = scandir($dir);
    foreach($dirArray as $key=>$value){

         
        if($value != '.' && $value != '..' && !is_dir($dir.DIRECTORY_SEPARATOR.$value) && ($extra($value))){

         /*   if(substr($value, 0,1)=='/')
              $value = substr($value, 1);

            if(substr($dir, strlen($dir),strlen($dir)-1)=='/')
              $dir = substr($dir, 0,strlen($dir)-1);*/

            $path = $dir.'/'.$value;

            $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

            $resultArray[$path] = $value;
        }
    }
    return $resultArray;
   }


   /**
    * Captura somente diretórios
    * @param  [type] $dir [description]
    * @return [type]      [description]
    */   
    public function onlyFilesRecursiveSequence($dir = null,$extra = null ){
      $data = Array();
      $result = $this->onlyFilesRecursive($dir,$extra);

      $func = create_function('$func,$result', '
        $data = Array();
         if(count($result) > 0)
        foreach ($result as $key => $value) {
          if(is_array($value)){
            $dataNew = $func($func,$value);         
            $data = array_merge($data,$dataNew);          
          }else{
            $data[$key] = $value;
          }
        }

        return $data;
      ');

    /*  $func = function($func,$result){
         $data = Array();
         if(count($result) > 0)
        foreach ($result as $key => $value) {
          if(is_array($value)){
            $dataNew = $func($func,$value);         
            $data = array_merge($data,$dataNew);          
          }else{
            $data[$key] = $value;
          }
        }

        return $data;
      };
*/
      $data = $func($func,$result);
      return $data;
    }

    /**
    * Captura somente diretórios
    * @param  [type] $dir [description]
    * @return [type]      [description]
    */   
    public function onlyFilesRecursive($dir = null,$extra = null ){

        // if(isset($_POST['dir']))
            // $dir = $_POST['dir'];

      if(substr($dir, strlen($dir),strlen($dir)-1)=='/'){
        $dir = substr($dir, 0,strlen($dir)-1);
      }

      $dir = str_replace('//', '/', $dir);

   
      // echo substr($dir, 0,strlen($dir))."\n\n";

    if(!file_exists($dir))
        return null;
       
    if($extra == null){
      $extra = create_function('$value', 'return true;');    
    }

    $resultArray = null;
    $dirArray = scandir($dir);
    
    
    // echo $dir."\n\n";

    foreach($dirArray as $key=>$value){

       $path = $dir.DIRECTORY_SEPARATOR.$value;
           $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR, $path);   

        if($value != '.' && $value != '..' && ($extra($path))){

           // $path = $dir.DIRECTORY_SEPARATOR.$value;
           // $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR, $path);           

           // echo $path."\n\n";

            if( !is_dir($dir.DIRECTORY_SEPARATOR.$value) && $value != '-folder.json' ){              
              $resultArray[$path] = $value;
            }

            // echo $dir.''.$value."\n\n";

            if(is_dir($path)){

                // if(substr($value, 0,1)=='/'){
                  // $path = $dir.''.$value;
                // }else{
                  
                // }



                $resultArray[$path] = $this->onlyFilesRecursive($path,$extra);
            }

        }
    }
    return $resultArray;
   }


    /**
    * Captura somente diretórios
    * @param  [type] $dir [description]
    * @return [type]      [description]
    */
   public static function onlyDir($dir,$extra = null ){
    // print_r($dir);
    // echo '>>'.$dir;

    if(!file_exists($dir))
        return null;

      

    if($extra == null){
      //$extra = function(){return true;};    
      $extra = create_function('$value', 'return true;');    
      // return false;   
    }

    $resultArray = null;
    $dirArray = scandir($dir);



    foreach($dirArray as $key=>$value){

       
        if($value != '.' && $value != '..' && is_dir($dir.DIRECTORY_SEPARATOR.$value) && ($extra($value))){
            $resultArray[] = $value;
        }
    }

    
    return $resultArray;
   }


   /**
    * captura somente diretórios recursivamente
    * @param  [type] $dir   [description]
    * @param  [type] $extra [description]
    * @return [type]        [description]
    */
   public function onlyDirRecursive($dir,$extra = null,$modeRecursive = true){
        if($extra == null){
      //$extra = function(){return true;};    
      $extra = create_function('$value', 'return true;');    
      // return false;   
    } 

    
       // print_r($extra);

        $dirAppArray = $this->scanDir($dir,$extra);

        $dirArray = Array();
        $dirGetArray = Array();

        if(count($dirAppArray)<1)return null; 
        foreach ($dirAppArray as $key => $value) {
            $path = $dir.DIRECTORY_SEPARATOR.$value;       
            $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

            if($modeRecursive == 'true')
            $dirGetArray = $this->onlyDirRecursive($path,$extra);     
         

            if(count($dirGetArray)<1)$dirGetArray = null;
            
            if(is_dir($path)){
              if(is_array($dirGetArray))
                $dirArray[$value] = $dirGetArray;     
              else
                $dirArray[$value] = null;     
              
            }else{
             
            }
        }
        return $dirArray;
   }


   /**
    * captura somente diretórios recursivamente
    * @param  [type] $dir   [description]
    * @param  [type] $extra [description]
    * @return [type]        [description]
    */
   public function dir($dir,$modeRecursive = true,$extra = null){
        if($extra == null){
      //$extra = function(){return true;};    
      $extra = create_function('$value', 'return true;');    
      // return false;   
    } 

    
       // print_r($extra);

        $dirAppArray = $this->scanDir($dir,$extra);

        $dirArray = Array();
        $dirGetArray = Array();

        if(count($dirAppArray)<1)return null; 
        foreach ($dirAppArray as $key => $value) {
            $path = $dir.DIRECTORY_SEPARATOR.$value;       
            $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

            if($modeRecursive == 'true')
            $dirGetArray = $this->dir($path,$modeRecursive,$extra);     
         

            if(count($dirGetArray)<1)$dirGetArray = null;
            
            if(is_dir($path)){
              if(is_array($dirGetArray))
                $dirArray[$value] = $dirGetArray;     
              else
                $dirArray[$value] = null;     
              
            }else{
             
            }
        }
        return $dirArray;
   }

    public static function fileDirRecursive($dir,$extra = null,$modeRecursive = true){
        if($extra == null){
      //$extra = function(){return true;};    
      $extra = create_function('$value', 'return true;');    
      // return false;   
    } 


    
       // print_r($extra);

        $dirAppArray = self::scanDir($dir,$extra);

        $dirArray = Array();
        $dirGetArray = Array();

        if(count($dirAppArray)<1)return null; 
        foreach ($dirAppArray as $key => $value) {
            $path = $dir.'/'.$value.'/';       
            
            if($modeRecursive == 'true')
            $dirGetArray = self::fileDirRecursive($path,$extra);     
            
            // echo $path.'<Br>';


            // if(is_dir($path)){
              if(is_array($dirGetArray)){
                $dirArray[$value] = $dirGetArray;     
              }else{
                $dirArray[$value] = Array();     
              }
              
            // }else{
             
              /*  $filename = $dir.'/'.$value;

                $mimeType = '';

                if(function_exists('mime_content_type')!=1){                            
                  $mimeType = trim ( exec ('file -bi ' . escapeshellarg ( $filename ) ) ) ;                               
                }else{
                  $mimeType = mime_content_type($filename);
                }
                if($mimeType == 'directory')
                  continue;
              $dirArray[$value] = $mimeType;  */ 
            // }
        }
        return $dirArray;
   }




    public static function dirFileRecursive($dir,$extra = null ){
        if($extra == null){
      //$extra = function(){return true;};    
      $extra = create_function('$value', 'return true;');    
      // return false;   
    } 

    
       // print_r($extra);

        $dirAppArray = Functions::scanDir($dir,$extra);

        $dirArray = Array();

        if(count($dirAppArray)<1)return null; 
        foreach ($dirAppArray as $key => $value) {
            $path = $dir.$value.'/';       
            
            $dirGetArray = Functions::scanDirFileRecursive($path,$extra);     
            
            


            if(is_dir($path)){
              /*if(is_array($dirGetArray))
                $dirArray[$value] = $dirGetArray;     
              else
                $dirArray[$value] = Array();     */
              
              $dirArray['folder'][$value] = $dirGetArray;     
            }else{
             if(substr($value, 0,1)=='-')
                continue;
              $dirArray['files'][] = $value;   
            }
        }
        return $dirArray;
   }



}