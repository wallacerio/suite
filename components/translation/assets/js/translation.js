// Access the suite application data
if(typeof suite != 'object'){var suite = {getCookie:function(cname) {var name = cname + "="; var ca = document.cookie.split(';'); for(var i = 0; i <ca.length; i++) {var c = ca[i]; while (c.charAt(0)==' ') {c = c.substring(1); } if (c.indexOf(name) == 0) {return c.substring(name.length,c.length); } } return ""; }, get:function(){return JSON.parse(decodeURIComponent(suite.getCookie('suite'))); } } }

function translation(){}

translation.setCookie = function(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
};

translation.getCookie = function(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
};


translation.hashCode = function(string) {
	if(string == '' || string == null)return string;

  var hash = 0, i, chr;
  if (string.length === 0) return hash;
  for (i = 0; i < string.length; i++) {
    chr   = string.charCodeAt(i);
    hash  = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};

translation.addEvent = function(objs,event,callback,mode,par1,par2,par3){           
    if(mode == undefined)
        mode = true;

    if(objs == undefined)
        objs = window; 
    if(objs.addEventListener){              
        return objs.addEventListener(event,function(){
            if(callback)
                callback(objs,par1,par2,par3);
        },mode); 
    }else if(objs.attachEvent){
        return objs.attachEvent('on'+event,function(){
            if(callback)
                callback(objs,par1,par2,par3);
        }); 
    }
};   


translation.ajax = function(options){

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
	

		xhr.onreadystatechange = function () {

			if(xhr.readyState > 3)					
				if(success)
					success(xhr.responseText);				
		};

		
		var dataForm = new FormData();					
		for (key in data) {
	        if (data.hasOwnProperty(key)){	
	        	if(data[key])
	        		dataForm.append(key,data[key]);
	        	else
	        		dataForm.append(key,data);
	        }
	    }

	    

	    xhr.send(dataForm);
	
				
	};

translation.replaceBody = function(){

	var hashSaved = translation.getCookie('suite_translation');
	var content = '';

	

	
	if(document.documentElement.innerHTML)
		content = document.documentElement.innerHTML;
	
		

	translation.getTranslates(content,function(content){
		
		var hash = translation.hashCode(content);	

		if(hashSaved != hash && content != undefined){			
			translation.setCookie('suite_translation',hash);
			document.documentElement.innerHTML = content;
		}
	});
	
	
}

// Intercept Alert box
translation.replaceAlert = function(){
/*
	var alertNativeCode = window.alert;
	window.alert = function(message) {
	    translation.getTranslates(message,function(response){	    	    	
	    	alertNativeCode.call(this, response);
	    });
	    return null;	
	};*/
}


translation.getTranslates = function(content,callback){	
	if(content == '' || content == undefined){
		if(callback)
			callback();
		return false;
	}



	var url = suite.get().url + '_component/translation/reference';

	
	var data = {
		string:content
	};

	var prefix = suite.get().http.prefix;
	if(prefix != '')
		data['target'] = prefix;
	

	translation.ajax({
		url:url,
		type:'post',
		data:data,
		success:function(response){	
			// console.log(response);		
			if(callback)
			callback(response);
		}
	});

	
}


// translation.setCookie('suite_translation','');


translation.replaceAlert();

translation.addEvent(document,'load',function(){
	
	/*translation.replaceBody();	
	setInterval(function(){
		translation.replaceBody();	
	},300);
	*/
	
});





