
function options(){}

options.path = null;

options.analise = function(response){
    var out = '';
        

    if(response.type == 'list-commands'){
       out = prints.listCommands(response.output);
    }else if(response.type == 'text'){
       out = prints.text(response.output);       
    }else if(response.type == 'list'){
       out = prints.list(response.output);       
    }else if(response.type == 'code'){
        var functions = response.output;
        setup.options.getFunction(functions,' ',function(response){            
           var response = JSON.parse(response);    
           var out = options.analise(response);           
            options.print(out);

        });
    }


   
    return out;
}


options.response = function(response){
    var out = '';
    var response = JSON.parse(response);
    if(response.status == 'success'){
        out = options.analise(response.data);
    }else if(response.status == 'not-found'){

    }else if(response.status == 'token-invalid'){
        out = response.msg;
    }

    return out;
}

options.printLoad = function(string){
    document.querySelector('#optionResult').innerHTML = "wait...";
}

options.print = function(string){
    string = prints.formatStrings(string);    
    document.querySelector('#optionResult').innerHTML = string;
}

options.printInput = function(inputs){  
    if(inputs == undefined){
        document.querySelector('#optionInputsResult').innerHTML = '';
        return;
    }
    var out = prints.inputs(inputs);
    document.querySelector('#optionInputsResult').innerHTML = out;

    document.querySelector('[name="submit_inputs"]').onclick = function(){
        var val = '';
        var inputsAll = document.querySelectorAll('[data-rel="inputs"]');
        for (var i = 0; i < inputsAll.length; i++) {
            val += inputsAll[i].value + ' ';
        };        

        setup.options.get(options.path,val,function(result){

           var out = options.response(result);
            options.print(out); 
            document.querySelector('#optionInputsResult').innerHTML = '';
        });
    }
}

options.breadcrumb = function(string){
    var out = prints.breadCrumb(string);
    document.querySelector('#breadcrumb').innerHTML = out;
    var aAll = document.querySelectorAll('#breadcrumb a');
    for (var i = 0; i < aAll.length; i++) {
        aAll[i].onclick = function(){
            var path = this.getAttribute('data-path');
            options.loadCategory(path);
        }
    }
}

options.loadCategory = function(path){
    if(path == '/') path = '';

    options.breadcrumb(path);
    setup.options.get(path,null,function(result){

        var html = options.response(result);
        if(document.querySelector('#optionsHtml')){
            document.querySelector('#optionsHtml').innerHTML = html;
            options.defineOption();
        }  
    });
}

options.defineOption = function(){

    var aAll = document.querySelectorAll('#optionsHtml a');
    for (var i = 0; i < aAll.length; i++) {
        aAll[i].onclick = function(){
            var type = this.getAttribute('data-type');
            var path = this.getAttribute('data-path');
            var functions = this.getAttribute('data-function');

            options.path = path;

            
            if(type == 'command' || type == 'remote-command'){                
                if(functions != null){                    
                     setup.options.getFunction(functions,' ',function(response){
                       alert(response);
                        var response = JSON.parse(response);
                        var out = options.analise(response);                    
                        options.print(out); 
                        options.printInput(response.inputs);                         
                     });
                }

               
            }else  if(type == 'category'){

                options.loadCategory(path);

                
                
            }
        }
    };
  
}




options.listOption = Object();

options.makeList = function(cat,obj,path,nivel){

    if(nivel === undefined) nivel = 1;
    if(path === undefined) path = '';
    options.listOption[cat] = Object();

    

    var html = '';    
    for(key in obj){       
        if(key.substr(0,1) == '@')
            continue;

        var type = obj[key]['@type'];
        var description = obj[key]['@description'];
        var functions = obj[key]['@function'];
        var preinput = obj[key]['@preinput'];
        var space = 20 * nivel;
         
        options.listOption[cat][key] =  obj[key];     
        
        html += '<div >';
        
        var pathFinal = path+'/'+key;        

        if(type == 'category'){
            html += '<h4 class="layout category" data-type="'+type+'" data-cat="'+cat+'" data-item="'+key+'" data-path="'+pathFinal+'" style="padding-left: '+space+'px;"> + '+key+'</h4>';        
            html += '<div class="layout sub-layout" >';
            html += options.makeList(cat+'/'+key,obj[key],path+'/'+key,nivel+1);
            html += '</div>';    
        }else{
            html += '<a class="layout" data-type="'+type+'" data-cat="'+cat+'" data-item="'+key+'" data-path="'+pathFinal+'" style="padding-left: '+(space+0)+'px;">'+key+'</a>';        
        }

        html += '</div>';
    }

    

    return html;
}





options.makeHtml = function(json){
    var boxClass = 'box-layout';
    var html = '';

    var obj = JSON.parse(json);
    for(key in obj){
        if(key =='setup.default'){
            boxClass = '';
            title = '';
        }else{
            boxClass = 'box-layout';
            title = key;
        }    



        html += '<div class="layout '+boxClass+'" >';
        html += '<h3 class="layout">'+title+'</h3>';
        html += options.makeList(key,obj[key],key);
        html += '</div>';
    }
    return html;
}


