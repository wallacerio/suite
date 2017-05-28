// Access the suite application data
if(typeof suite != 'object'){var suite = {getCookie:function(cname) {var name = cname + "="; var ca = document.cookie.split(';'); for(var i = 0; i <ca.length; i++){var c = ca[i]; while (c.charAt(0)==' '){c = c.substring(1); } if (c.indexOf(name) == 0){return c.substring(name.length,c.length); } } return ""; }, get:function(){return JSON.parse(decodeURIComponent(suite.getCookie('suite'))); } } }



var layout = {
    
    browser:{
        resize:function(){
            layout.menu.adjust();
        }
    },

    menu:{
        adjust:function(){
            var menuColumn = document.querySelector('.menuColumn');
            var menuColumn_height = menuColumn.offsetHeight;
            var menuColumn_top = menuColumn.offsetTop;
            document.querySelector('#wrapperListOptions').style.height = '1px';
            document.querySelector('#wrapperListOptions').style.height = menuColumn_height - (menuColumn_top/2)-5+'px';
        }
    }
}


events.addEvent(window,'load',function(){
    layout.browser.resize();

});
events.addEvent(window,'resize',function(){
    layout.browser.resize();

});



window.onload = function(){

    

    // load options base
    setup.options.get('/',null,function(result){
       options.breadcrumb('');
        var html = options.response(result);        
        if(document.querySelector('#optionsHtml')){
            document.querySelector('#optionsHtml').innerHTML = html;
            options.defineOption();
        }  
    });
    
   
   



    form.focus('[name="component-form-register"]');
    
    form.register('[name="component-form-register"]',{
        action:suite.get().url+'_component/setup/register',    
    },
    // callback
    function(response){
        var responseObj = JSON.parse(response);
        if(responseObj.status=='success'){
            document.querySelector('#resultForm').innerHTML = "<span class='layout success-label'>Registred!</span>";  
            window.location = window.location;
        }else{
            document.querySelector('#resultForm').innerHTML = "<span class='layout error-label'>error</span>";  
        }

    },
    // callback
    function(data){
        if(data.username.replace(/ /g,'') == ''  ){
            document.querySelector('#resultForm').innerHTML = "<span class='layout error-label'>Fields empty</span>";  
            return false;
        }        

        if(data.password == data.passwordconfirm){
            data.password = form.md5(data.password);
            document.querySelector('#resultForm').innerHTML = "<span class='layout success-label'>Registering</span>";  
            return data;
        }

        document.querySelector('#resultForm').innerHTML = "<span class='layout error-label'>Password not confirmed</span>";  
        return false;
    });



    

    form.focus('[name="component-form-logon"]');

    form.register('[name="component-form-logon"]',{
        action:suite.get().url+'_component/setup/access',    
    },
    // callback
    function(response){                
        
        var responseObj = JSON.parse(response);
        if(responseObj.status=='success'){
            document.querySelector('#resultForm').innerHTML = "<span class='layout success-label'>Access validate</span>";  
            window.location = window.location;
        }else{
            document.querySelector('#resultForm').innerHTML = "<span class='layout error-label'>Access not permited</span>";  
        }

    },
    // validator
    function(data){
             
    });


    /*form.load('[name="component-form-logon"]',{
        action:'http://localhost/lps/lps/_component/setup/access',
        dataAttach:{
            "hash":"azq2"            
        }
    });*/

}