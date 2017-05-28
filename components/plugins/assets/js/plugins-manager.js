


// suite().button('save');
// 

// Access the suite application data
if(typeof suite != 'object'){var suite = {getCookie:function(cname) {var name = cname + "="; var ca = document.cookie.split(';'); for(var i = 0; i <ca.length; i++) {var c = ca[i]; while (c.charAt(0)==' ') {c = c.substring(1); } if (c.indexOf(name) == 0) {return c.substring(name.length,c.length); } } return ""; }, get:function(){return JSON.parse(decodeURIComponent(suite.getCookie('suite'))); } } }

var componentPluginUrl = suite.get().url + '_component/plugins/';

// form logon -------------------------------------------------
simplifyForm.focus('[name="manager-login"]');    
simplifyForm.register('[name="manager-login"]',{
    action:componentPluginUrl+'managerLogin',    
    method:"post",
    callback:function(response){        
        var responseObj = JSON.parse(response);
        if(responseObj.status=='success'){    	
            document.querySelector('#resultLogon').innerHTML = "<span class='success'></span>";  
            location.reload();
        }else{
            document.querySelector('#resultLogon').innerHTML = "<span class='error'>Access not permited</span>";  
        }

    },

    validator:function(data){	
    	document.querySelector('#resultLogon').innerHTML = "<span class='loading'>Validating access...</span>";  
    }
});



if(document.querySelector('#logout'))
document.querySelector('#logout').onclick = function(){

    simplifyForm.ajax({
            url:componentPluginUrl+'managerLogout',        
            success:function(response){            
                 var responseObj = JSON.parse(response);
                    if(responseObj.status=='success'){                          
                        location.reload();
                    }

            }
        });

};


