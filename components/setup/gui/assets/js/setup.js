var setup = {

    options:{
    	getFunction:function(functions,parameters,callback){
    		// var parameters = '';
	    	events.ajax({
		        "url":suite.get().url+'_component/setup/gui/getfunction',
		        'data':{'function':functions,'parameters':parameters},
		        success:function(response){            		        
		            if(callback)
		            	callback(response);
		        }
		    });	

	    },

	    get:function(path,parameters,callback){

	    	events.ajax({
		        "url":suite.get().url+'_component/setup/gui/get',
		        "data":{
		        	command:path,
		        	'parameters':parameters
		        },
		        success:function(response){            		        	
		            if(callback)        
		            	callback(response);
		        }
		    });	
	    }

	}

}