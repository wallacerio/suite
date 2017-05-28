/**
 * Design
 * Struct for layout on your application
 * 
 * Author: Wallace Rio <wallrio@gmail.com>
 * Last Update: 25/04/2017 21:20
 * Version: 1.2
 * 
 */

function design(){}

design.addEvent  = function(objs,event,callback,mode,par1,par2){		
	if(mode == undefined)
		mode = true;

	if(objs == undefined)
		objs = window; 
	if(objs.addEventListener){ 				
		return objs.addEventListener(event,function(e){
			if(callback)
				return callback(e,objs,par1,par2);
		},mode); 
	}else if(objs.attachEvent){
		return objs.attachEvent('on'+event,function(e){
			if(callback)
				return callback(e,objs,par1,par2);
		}); 
	}
};

/**
 * mostra/esconde menu em modo mobile
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
design.toggleNav = function(obj){	
	if(obj.className.indexOf('show-nav')!=-1)
		obj.className = obj.className.replace('show-nav','');
	else
		obj.className = obj.className+' show-nav';
};




design.buttonToggleClass = function(dataDesignAll){
	var buttonAll = dataDesignAll.querySelectorAll('.button-toggle-class');
	for (var i = 0; i < buttonAll.length; i++) {
		var events = buttonAll[i].getAttribute('data-event') || 'click';

		design.addEvent(buttonAll[i],events,function(e,obj,par1){				
			var target = obj.getAttribute('data-target');
			var targetArray = target.split(' ');
			for (var i = 0; i < targetArray.length; i++) {

				if(document.querySelector(targetArray[i]).className.indexOf('active') === -1){
					document.querySelector(targetArray[i]).className = document.querySelector(targetArray[i]).className + ' active';
				}else{
					document.querySelector(targetArray[i]).className = document.querySelector(targetArray[i]).className.replace(' active','');					
				}
				
			};

		});
	};
}


design.construct = function(){

	
	design.addEvent(window,'load',function(e,obj,par1){
		var dataDesignAll = document.querySelectorAll('[data-design]');
		
		for (var i = 0; i < dataDesignAll.length; i++) {
			var menu = dataDesignAll[i].querySelector('.menu');			
			/*if(menu){
				design.addEvent(menu,'click',function(e,obj,par1){				
					design.toggleNav(obj);
				});
			}*/

			design.buttonToggleClass(dataDesignAll[i]);
			
		};


		

	});

};


design.construct();