function prints(){}




prints.breadCrumb = function(string){
    var out = '';
    var joinPath = '';
    var nameRaiz = 'raiz';

    string = nameRaiz+string;

    var stringArray = string.split('/');

    out += '<ul>';
    for(key in stringArray){
        var name = stringArray[key];
        if(name != nameRaiz)
            joinPath += name+'/';
        else
            joinPath += '/';
        // joinPath = joinPath.substr(joinPath.length,20);

        out += '<li>'; 
        out += '<a data-path="'+joinPath+'">';
        out += name;
        out += '</a>';
        out += '</li>';
    }
    out += '</ul>';

    return out;
}



prints.inputs = function(inputs){
    var out = '';        
    out += ' ';    
    for(key in inputs){

        var type = inputs[key]['type'];
        var title = inputs[key]['title'];
        var value = inputs[key]['value'];
        var validate = inputs[key]['validate'];
        
        out += '<label>'+title+'</label>';
        out += '<input name="'+key+'" placeholder="'+title+'" data-rel="inputs" type="'+type+'" value="'+value+'" >';
        
    }

    out += '<input name="submit_inputs" type="submit" value="Send" >';

    return out;

}


prints.text = function(string){
   
    var out = '';
        
    out = string;


    return out;
}

prints.list = function(array){
    
    
    var out = '';
        
    for(key in array){
        var type = array[key]['type'];
        var text = array[key]['text'];
        var action = array[key]['action'];
        // alert(JSON.stringify(array[key]));
        out += '<div>'+text+'</div>';
    }


    return out;
}


prints.listCommands = function(array,path,nivel){
    

    var html = '';
    if(nivel === undefined) nivel = 1;
    if(path === undefined) path = '';

    for(key in array){
         if(key.substr(0,1) == '@')
            continue;



        var type = array[key]['@type'];
        var description = array[key]['@description'];
        var functions = array[key]['@function'] || null;
        // var validate = array[key]['@function'];
        

        var space = 20 * nivel;
        var pathFinal = path+'/'+key;       
        
        var hTitle = 3;

        if(nivel > 1)
            hTitle = 4;


        if(type == 'category'){
            html += '<a class="layout category"  data-type="'+type+'"  data-item="'+key+'" data-path="'+pathFinal+'"><h'+hTitle+' class="layout category" data-path="'+pathFinal+'" style="padding-left: '+space+'px;"> '+key+'</h'+hTitle+'></a>';        
            html += '<div class="layout sub-layout" >';            
            html += prints.listCommands(array[key],path+'/'+key,nivel+1);
            html += '</div>';    
        }else{
            html += '<a class="layout" data-type="'+type+'" data-function="'+functions+'" data-item="'+key+'" data-path="'+pathFinal+'" style="padding-left: '+(space+0)+'px;">'+key+'</a>';        
        }

        html += '</div>';


    }
    return html;
};


// Formata a palavra
prints.formatWord = function(array){
    var out = '';
    var html = '';
    var spanaction = null;
    var format = null;
    var color = "white";
    var bold = "normal";
    var width = 'auto';
    var display = 'inline-table';
    
    html = array[0];
    format = array[1];
    
    // alert('['+html+']');
    if(html == '') return '';
 
    if(format!== undefined){

        format = '{'+format;        
        format = JSON.parse(format);

        if(format.bold) bold = "bold";        
        if(format.color) color = format.color;
        if(format.width) width = (format.width*5)+'px';
        if(format.display) display = format.display;
        
        html = html.replace(/\\s/g, ' ');
        html = html.replace(/\[\:space\:\]/g, ' ');
        

        out = '<span style="color:'+color+';font-weight:'+bold+';display: '+display+';min-width:'+width+'"   >'+html+' </span>';
    }else{
        out = '<span style="color:'+color+';font-weight:'+bold+';display: '+display+';min-width:'+width+'" >'+html+' </span>';
    }

    return out;
}




// filtra o texto
prints.formatInputs = function(input,multpleInput){
    var html = '';

    html += '<br>';
        
        
        // var listArray = list.split('|');

        // if(type == 'radio'){
            /*html += '<label>'+key+'</label>';
            for(keyL in listArray){
                html += '<label data-rel="radio"><input name="'+key+'" type="'+type+'" > '+listArray[keyL]+' </label>';
            }            
            html += '<br>';
        }else{*/
            // html += '<label>'+title+'</label>';
            // 
            if(multpleInput==true){
                for(key in input){
                    var type = input[key]["type"] || "text";
                    var title = input[key]["title"] || "";
                    var value = input[key]["value"] || "";
                    var list = input[key]["list"] || "";

                    html += '<input name="'+key+'" data-rel="inputOption" type="'+type+'" placeholder="'+title+'" value="'+value+'">';
                    html += '<br>';
                }
            }else{
                var type = input["type"] || "text";
                var title = input["title"] || "";
                var value = input["value"] || "";
                var list = input["list"] || "";
                html += '<input name="inputOption" type="'+type+'" placeholder="'+title+'" value="'+value+'">';
                html += '<br>';
            }
        // }
   
    html += '';
    html += '<input name="sendOptions" type="submit" value="Send">';

    return html;
}

prints.formatStrings = function(string){
         // string = string.trim();
         

        var stringArray = string.split(' ');
            var html2 = '';
            for(key in stringArray) {                
                var par = stringArray[key];          
                // var parArray = explode('~{',$par);
                var parArray = par.split('~{');
                
                // echo json_encode($parArray);
                var word = prints.formatWord(parArray);
                
                html2 += ''+word+' ';
            }

            

            return html2;
    }


prints.mining = function(response){
    
    var inputs = Array();
    var html = '';
    var string = '';
    var par = '';
    var word = '';
    var preinput = '';
    var parameters = null;

    try{        
        var obj = JSON.parse(response);        
        for(key in obj){
            var html2 = '';

            // alert(JSON.stringify(obj[key]));

            string = obj[key]['string'];
            input = obj[key]['input'];
            inputs = obj[key]['inputs'];
            // alert(JSON.stringify(inputs));
            /*preinput = obj[key]['preinput'];

            if(preinput){
                var promptVal = prompt(preinput);
            }*/

            parameters = obj[key]['parameters'] || null;
            
            var stringArray = string.split(' ');
            for(key2 in stringArray){
                par = stringArray[key2];            
                parArray = par.split('::');            
                word = options.formatWord(parArray);
                html2 += ' '+word+' ';
            }

            if(parameters){
                html += '<a data-spanaction="'+parameters+'">'+html2+'</a>';
            }else{
                html += '<pre>';
                html += html2;
                html += '</pre>';
                // html += '<br>';
            }

            if(input != undefined)
                html += options.formatInputs(input);

            if(inputs != undefined)
                html += options.formatInputs(inputs,true);


        }
    }catch(e){
        html = response;
    }



    return html;
}