/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var modalName;
var mManOnOK;
var activefoneStore;
var mManUrl;
//var onAllOk=null;
$.fn.mManShowHideError=function(arrKeys,key,data){
    if ($.inArray(key,arrKeys)){
        var inp=$('#'+modalName.toLowerCase()+'-'+key);
        inp.parent().removeClass('has-error');
        inp.parent().addClass('has-success');
        inp.next().text('');
    }else{
        noError=false;
        var inp=$('#'+modalName.toLowerCase()+'-'+key);
        inp.parent().addClass('has-error');
        inp.next().text(data.errors[key][0]);
    }
};
$.fn.mManValidate=function(attrs,okFunction){
    var noError=true;
    dt={};
    dt['_csrf']=$('.'+modalName.toLowerCase()+'-form').children(':first').val();
    dt[modalName]={};
    $.each($('[name ^='+modalName),function(ind,el){
        var t=$(el).attr('name').split('[');
        dt[t[0]][t[1].split(']')[0]]=$(el).val();
    });
    dt['ajax']=true;
    var opt={
        url:mManUrl,
        type: 'POST',
        data:dt
    };
    if ($.isArray(attrs))
        dt['validateAttributes']=attrs;
    $.ajax(opt).done(function(data){
        console.log(data);
        if (data.status==='ok'){
            if ($.isFunction(okFunction)) okFunction();
        }else{
            noError=false;
            arrKeys=$.map(data.errors,function(ind,el){return el;});
            $.each($('[name ^='+modalName),function(ind,el){
                var t=$(el).attr('name').split('[');
                key=t[1].split(']')[0];
                if (!$.isArray(data['validateAttributes'])){
                    $.fn.mManShowHideError(arrKeys,key,data);
                }else{
                    if (!$.inArray(key,data['validateAttributes']))
                        $.fn.mManShowHideError(arrKeys,key,data);
                }
            });
        }
    });
    return noError;
};
$.fn.mManClickAddManager=function(self,onOk,subMText){
        if (subMText) $('#'+modalName+'AddManagerCreate').text(subMText);
        if ($.isFunction(onOk)) mManOnOK=onOk;
        mManUrl=$(self).attr('url');
        if (!mManUrl)
            mManUrl=$(self).attr('href');
        if (mManUrl){
            var opt={
                url:mManUrl,
                type: 'POST',
                data:{
                    'ajax':true,
                    '_csrf':$('[name="_csrf"]').val(),
                    firm_id:$(self).attr('firm-id')

                }
            };
            $.ajax(opt).done(function(data){
                console.log(data);
                $('#'+modalName).children(':first').children(':first').children('.modal-body').html(data.html);
                $('[name ^='+modalName).focusout(function(){
                    var key=$(this).attr('name').split('[')[1].split(']')[0];
                    $.fn.mManValidate([key]);
                });
                activefoneStore=$.fn.activefoneInit(data.options);
                $('#'+modalName).modal({
                    show:true,
                    backdrop:true
                });
            });
        }else{
            //consoel.log("$.fn.mManClickAddManager:");
            console.error($(self),'$.fn.mManClickAddManager: Незадан путь к экшену (свойства: href или url)');
        }
        return false;

};
$.fn.mManInit=function(mName,onOk){
    modalName=mName;
    mManOnOK=onOk;
    //onAllOk=onOk;
    $('#'+modalName+'AddManagerCansel').click(function(e){
        e.preventDefault();
         $('#'+modalName).modal('hide');
    });
    $('#'+modalName+'AddManagerCreate').click(function(e){
        e.preventDefault();
        if ($.fn.activefoneValidate(activefoneStore)){
            $.fn.mManValidate(null,function(){
                $('#'+modalName).modal('hide');
                if ($.isFunction(onOk))  mManOnOK();
            });
        }
    });
    $('#'+modalName+'AddManager').click(function(e){
        return $.fn.mManClickAddManager(this);
    });
};