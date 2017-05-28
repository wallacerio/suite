if(typeof suite != 'object')
	var suite = {};

if(typeof suite.basic != 'object')
	suite.basic = {};

if(typeof suite.plugin != 'object')
	suite.plugin = {};

if(typeof suite.plugin.manager != 'object')
	suite.plugin.manager = {};

if(typeof suite.browser != 'object')
	suite.browser = {};



suite.basic.addEvent = function(objs,event,callback,mode,par1,par2){	
	if(mode == undefined)
		mode = true;
	if(objs == undefined)
		objs = window; 
	if(objs.addEventListener){ 				
		return objs.addEventListener(event,function(e){
			if(callback) return callback(e,objs,par1,par2);
		},mode); 
	}else if(objs.attachEvent){
		return objs.attachEvent('on'+event,function(e){
			if(callback) return callback(e,objs,par1,par2);
		}); 
	}
}


suite.browser.load = function(callback){	
	suite.basic.addEvent(window,'load',callback);
}


// acrecenta um botão na área de gerencia do plugin
suite.plugin.manager.button = function(name,callback){	
	
	suite.browser.load(function(){		
		var pluginButton = document.querySelector('[name="plugin-manager-'+name+'"]');						
		if(pluginButton == undefined){
			var pluginManagerArea = document.querySelector('[data-suitearea="plugin-manager"]');						
			var li = document.createElement('li');				
			li.innerHTML = '<a name="plugin-manager-'+name+'">'+name+'</a>';
			pluginManagerArea.appendChild(li);
			pluginButton = li;			
		}

		pluginButton.onclick = function(){
			if(callback) callback();
		};
	});
		
};

// define status ao botão
suite.plugin.manager.status = function(name,status){	
	var pluginButton = document.querySelector('[name="plugin-manager-'+name+'"]');		
	if(pluginButton == undefined){
		return false;
	}
	pluginButton.setAttribute('data-designstatus',status);
	if(status == 'success')
		setTimeout(function(){
			pluginButton.removeAttribute('data-designstatus');
		},4000);
}

// cria uma lista na area de gerencia do plugin
suite.plugin.manager.list = function(name,listArray,callback){	
	var pluginButton = document.querySelector('[name="plugin-manager-'+name+'"]');		
	if(pluginButton == undefined){
		var pluginManagerArea = document.querySelector('[data-suitearea="plugin-manager"]');						
		var li = document.createElement('li');				
		var optionsListHtml = '';
		for(list in listArray){
			optionsListHtml += '<option value="'+listArray[list]+'">'+listArray[list]+'</option>';	
		};	
		li.innerHTML = '<select name="plugin-manager-'+name+'">'+optionsListHtml+'</select>';
		pluginManagerArea.appendChild(li);
		pluginButton = li.querySelector('select');
	}
	pluginButton.onclick = function(){
		if(callback) callback(this);
	};		
}