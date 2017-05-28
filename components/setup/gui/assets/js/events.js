function events(){}

// manipulate events on browser and elements
events.addEvent = function(objs,event,callback,mode,par1,par2,par3,par4){			
	if(mode == undefined)
		mode = true;

	if(objs == undefined)
		objs = window; 

	if(typeof objs == 'string')
		objs = document.querySelector(objs);

	if(objs.addEventListener){ 				
		return objs.addEventListener(event,function(e){
			
			if(callback)
				var returns = callback(e,objs,par1,par2,par3,par4);
			if(returns === false)
				e.preventDefault();

			return returns;
		},mode); 
	}else if(objs.attachEvent){					
		return objs.attachEvent('on'+event,function(e){
			if(callback)
				var returns = callback(e,objs,par1,par2,par3,par4);
			if(returns === false)
				e.preventDefault();

			return returns;
		}); 
	}
};



events.ajax = function(options){
    // alert(3);
    var url = options['url'] || null;
    var success = options['success'] || null;
    var progress = options['progress'] || null;
    var data = options['data'] || null;
    var type = options['type'] || 'post';

    

    var xhr = (function(){
        try{return new XMLHttpRequest();}catch(e){}try{return new ActiveXObject("Msxml3.XMLHTTP");}catch(e){}try{return new ActiveXObject("Msxml2.XMLHTTP.6.0");}catch(e){}try{return new ActiveXObject("Msxml2.XMLHTTP.3.0");}catch(e){}try{return new ActiveXObject("Msxml2.XMLHTTP");}catch(e){}try{return new ActiveXObject("Microsoft.XMLHTTP");}catch(e){}return null;
    })();
    
        xhr.open(type, url, true);

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                if(progress)
                progress(e.loaded,e.total);          
            }
        }
        xhr.upload.onloadstart = function (e) {             
            if(progress)
            progress(0,e.total);
        }
        xhr.upload.onloadend = function (e) {             
            if(progress)
            progress(e.loaded,e.total);
        }
        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                var ratio = Math.floor((e.loaded / e.total) * 100) + '%';                        
            }
        }

        xhr.onreadystatechange = function () {

            if(xhr.readyState > 3)
                if(success)
                    success(xhr.responseText);              
        };

        var dataForm = new FormData();
                
        

        for (key in data) {
            if (data.hasOwnProperty(key)){                                      
                dataForm.append(key,data[key]);
            }
        }
    

        xhr.send(dataForm);
};