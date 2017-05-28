
// Access the suite application data
if(typeof suite != 'object'){var suite = {getCookie:function(cname) {var name = cname + "="; var ca = document.cookie.split(';'); for(var i = 0; i <ca.length; i++) {var c = ca[i]; while (c.charAt(0)==' ') {c = c.substring(1); } if (c.indexOf(name) == 0) {return c.substring(name.length,c.length); } } return ""; }, get:function(){return JSON.parse(decodeURIComponent(suite.getCookie('suite'))); } } }

var translation_widget_listbox = {
	setCookie:function(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+ d.toUTCString();
	    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}
}

document.querySelector('[name="compTranslation_listboxlanguages"]').onchange = function(){
	var languagePrefix = this.value;
	var domain = suite.get().url;
	// var action = suite.get().http.action;
	var action = this.options[this.selectedIndex].getAttribute('data-action');
	var target = suite.get().http.target;
	


	var listLang = document.querySelectorAll('[name="compTranslation_listboxlanguages"] option');
	
	
	var actionArray = action.split('/');

	for(key in listLang){
		if(listLang[key].value != undefined){
			if(actionArray[0]==listLang[key].value){
				actionArray.splice(0, 1);				
			}			
		}
	}

	action = actionArray.join('/');

	
	if(action.substr(0,1)=='/')
		action = action.substr(1);


	translation_widget_listbox.setCookie('lang-current',languagePrefix);


	var targOffDomain = languagePrefix+'/'+action;
	targOffDomain = targOffDomain.replace('//','/');
	
	var targ = domain+targOffDomain;
	
	window.location = targ;
	
};

// document.querySelector('[name="compTranslation_listboxlanguages"]').onclick = document.querySelector('[name="compTranslation_listboxlanguages"]').onchange;
/*document.querySelector('[name="compTranslation_listboxlanguages"]').onchange = function(){
	alert(2);
}*/