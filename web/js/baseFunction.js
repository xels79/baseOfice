/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var mainDebug=0;
function lg(val){
    if (mainDebug)
        console.log(val);
}
$.fn.checkIsInteger=function(val){
                    for (i=0;i<val.length;i++){
                        var c=val.charAt(i);
                        if ((c<'0'||c>'9')&&c!='-') return true;
                    };
                    return false;
                };
$.fn.jsonToStringSub=function(obj,bracked,replace){
    rVal=bracked;
    first=true;
    $.each(obj,function(key,value){
        lg(key);
        if (first){
            first=false;
        }else
            rVal+=',';
        if (bracked==='{')
            rVal+='"'+key+'":';
        if ($.isPlainObject(value)||$.isArray(value))
            rVal+=$.fn.jsonToString(value,replace);
        else
            if (value in replace)
                rVal+=replace[value];
            else
                if ($.isNumeric(value))
                    rVal+=value;
                else
                    rVal+='"'+value+'"';
   });
    if (bracked==='{')
        rVal+='}';
    else
        rVal+=']';
    return rVal;
};
$.fn.jsonToString=function(obj,replace){
    if (!$.isPlainObject(replace)) replace={};
    if ($.isPlainObject(obj))
        return $.fn.jsonToStringSub(obj,'{',replace);
    else
        if ($.isArray(obj))
            return $.fn.jsonToStringSub(obj,'[',replace);
        else
            lg( obj);
};
$.fn.jsonMerge=function (json1, json2){
    var out = {};
    for(var k1 in json1){
        if (json1.hasOwnProperty(k1)) out[k1] = json1[k1];
    }
    for(var k2 in json2){
        if (json2.hasOwnProperty(k2)) {
            if(!out.hasOwnProperty(k2)) out[k2] = json2[k2];
            else if(
                (typeof out[k2] === 'object') && (out[k2].constructor === Object) && 
                (typeof json2[k2] === 'object') && (json2[k2].constructor === Object)
            ) out[k2] = jsonMerge(out[k2], json2[k2]);
        }
    }
    return out;
};
$.fn.getValue=function(obj,key,def){
     if (def)
          rVal=def;
     else
          rVal=false;
     if(obj&&key){
          if (obj[key]){
               rVal=obj[key];
          }
     }
     return rVal;
};
$.fn.baseAjaxUpdate=function(target,ajaxOpt,onDone){
    $.ajax(ajaxOpt).done(function(data){
        console.log(data);
         if ($.fn.getValue(data,'status')==='ok'){
              content=$.fn.getValue(data,'html','error');
              if ($.isPlainObject(target)){
                  target.replaceWith(content);
              }else{
                  $('#'+target).replaceWith(content);
              }
              if ($.isFunction(onDone)) onDone();
         }
    });
};
$.fn.createObj=function(opt){
    var rVal;
    //console.log(opt);
    if ($.isPlainObject(opt)){
        if (opt['tag']){
            rVal=$('<'+opt.tag+'>');
            if (opt['html'])
                rVal.html(opt.html);
            else
                if (opt['text']) rVal.text(opt.text);
            if (opt['options']){
                $.each(opt.options,function(id,val){
                    rVal.attr(id,val);
                });
            }
        }
    }
    return rVal;
};
$.fn.enableButtons=function(){
    $.each($.find('form'),function(id,el){
        $.each($(el).find('button'),function(idS,elS){
            $(elS).removeAttr('disabled');
        });
    });
};
$.fn.addField=function(formId,option,defVal){
    if ($.type(defVal)!=='object') defVal={};
    function addOne(opt){
        var div=$('<div>');
        if (!opt['separator']){
            var lbl=$('<label>');
            var inp=$('<input>');
            lbl.addClass('control-label col-sm-4');
            lbl.attr('for',opt.inputId);
            lbl.text(opt.label);
            if (opt['placeholder']) inp.attr('placeholder',opt.placeholder);
            var div2=$('<div>');
            div2.addClass('col-sm-5');
            if (opt.inputId){
                inp.attr('type','text');
                inp.attr('id',opt.inputId);
                inp.addClass('form-control');//input-sm
                if ($.type(defVal[opt.inputId])!=="undefined"){
                    int.val(defVal[opt.inputId]);
                }
                //inp.addClass('input-sm');
                if (opt['name'])inp.attr('name',opt.name);
                div2.append(inp);
                
                if (opt['help']){
                    div2.append($('<span  class="help-block">'+opt.help+'</span>'));
                }
            }else{
                div2=false;
            }
            div.addClass('form-group');
            div.append(lbl);
            if (div2){
                div.append(div2);
                div.append($('<p class="help-block help-block-error"></p>'));
            }
        }else{
            div.addClass('row');
            div.addClass('separator');
            if (opt['text']) div.append($('<p>'+opt.text+'</p>'));
        }
        if ($.isPlainObject(formId))
            formId.append(div);
        else
            $(formId).append(div);
    };
    if ($.isArray(option)){
        $.each(option,function(id,el){
            if (el) addOne(el);
        });
    }else{
        if (option) addOne(option);
    }
};
$.fn.dialog=function(opt){
};

$.fn.AlertBefore=function(opt){
    if ($.isPlainObject(opt)){
        var div=$('<div class="alert">');
        if (!opt['type'])
            opt['type']='alert-default';
        else
            opt.type='alert-'+opt.type;
        div.addClass(opt.type);
        div.addClass('fade in');
        div.append($('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'));
        if (opt['text']){
            if ($.type(opt['text'])==='object')
                div.append(opt['text']);
            else
                div.append($('<p>'+opt.text+'</p>'));
        }
        if (opt['timeOut']){
            window.setTimeout(function(){
                div.alert('close');
            },opt.timeOut*1000);
        }
        if (opt['parentId'])
            if ($.type(opt['parentId'])==='string')
                $('#'+opt.parentId).before(div);
            else
                opt['parentId'].before(div);
        else
            $('#maincontainer').before(div);
    }
};
$.fn.AlertAfter=function(opt){
    if ($.isPlainObject(opt)){
        var div=$('<div class="alert">');
        if (!opt['type'])
            opt['type']='alert-default';
        else
            opt.type='alert-'+opt.type;
        div.addClass(opt.type);
        div.addClass('fade in');
        div.append($('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'));
        if (opt['text']){
            if ($.type(opt['text'])==='object')
                div.append(opt['text']);
            else
                div.append($('<p>'+opt.text+'</p>'));
        }
        if (opt['timeOut']){
            window.setTimeout(function(){
                div.alert('close');
            },opt.timeOut*1000);
        }
        if (opt['parentId'])
            if ($.type(opt['parentId'])==='string')
                $('#'+opt.parentId).after(div);
            else
                opt['parentId'].after(div);
        else
            $('#maincontainer').after(div);
    }
};
$.fn.creatTag=function(name,opt){
    var rVal=$('<'+name+'>');
    if ($.type(opt)==='object')
        rVal.attr(opt);
    return rVal;
};
$.fn.renderDropDown=function(_parent,items,_options){
    var parent;
    var options=_options;
    console.log('renderDropDown');
    console.log(_options);     
    if (!_options) options={};
    if ($.type(_parent)==='object')
        parent=_parent;
    else
        parent=$('#'+_parent);
    var ul=$.fn.creatTag('ul',options);//$('<ul class="dropdown-menu">');
    ul.addClass('dropdown-menu');
    $.fn.renderUlConten(ul,items);
    parent.append(ul);
};
$.fn.renderUlConten=function(parentId,dt){
    function renderIt(it){
        var a=$.fn.creatTag('a',it['linkOptions']);
        if (it['label']) a.text(it.label);
        var li=$.fn.creatTag('li');
        li.append(a);
        return li;
    }
    var parent;
    if ($.type(parentId)==='object')
        parent=parentId;
    else
        parent=$('#'+parentId);
    parent.children().remove();
    $.each(dt,function(id,val){
        parent.append(renderIt(val));
    });
};
$.fn.addBusy=function(id){
    if (id){
        if ($.type(id)==='object'){
            var targ=id;
        }else{
            var targ=$('#'+id);
        }
                var tmpInfo=$('#loadingBig').clone();
        tmpInfo.attr('id','tmpInfo');
        tmpInfo.removeAttr('style');
        targ.append(tmpInfo);
    }
};
$.fn.removeBusy=function(){
    $('#tmpInfo').remove();
};
$.fn.getUrlForAjaxFromHref=function(url){
    //return url.slice(url.indexOf('?')).split(/[&?]{1}[\w\d]+=/);
    var rVal={};
    var tmp=url.slice(url.indexOf('?')+1).split('&');
    $.each(tmp,function(id,el){
        var tmp2=el.split('=');
        rVal[tmp2[0]]=tmp2[1];
    });
    return rVal;
};
$('[data-toggle=mCollapse]').click(function(){
    console.debug('mDtT');
    $($(this).attr('data-target')).toggleClass('mIn');
    $(this).toggleClass('glyphicon-chevron-right');
    $(this).toggleClass('glyphicon-chevron-left');
});

/*          Боковое меню для Астерион       */
$('ul.side-nav:first').mouseleave(function(e){
    var ul=$('ul.side-nav:first');
    var li=ul.children('li.dropdown').children('ul').children('li');
    if (ul.length){
        if (!ul.hasClass('side-nav-open')){
            ul.addClass('side-nav-open');
            $('#page-wrapper').removeAttr('style');
        }
    }
    
});
$('ul.side-nav:first li').mouseover(function(e){
    var ul=$('ul.side-nav:first');
    var li=ul.children('li.dropdown').children('ul').children('li');
    if (ul.length){
        if (ul.hasClass('side-nav-open')){
            var nP=parseInt($('#page-wrapper').css('padding-left'))+90;
            console.debug('open padding-left:',nP);
            $('#page-wrapper').css('padding-left',nP);
            ul.removeClass('side-nav-open');
        }
    }
    
});
$('#savebase').mouseup(function(e){
    console.debug('click',e);
    if (e.button===0){
        location.href=$(this).attr('href');
        var par=$(this).parent();
        $(this).remove();
        if (!$('.nav-mess').length) par.parent().parent().remove();
    }
});
$.fn.enablePopover=function(){
    $('.nav-mess').each(function(){
        console.debug('nav-mess try init popover:',$(this));
        $(this).popover({
            trigger:'hover',
            delay:'500'
        });
    });
};