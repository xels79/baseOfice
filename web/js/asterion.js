/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var AlertClass=function(parentid){
    //var parent=$('#'+parentId);
    var maxRequrs=3;
    var currRequrs=0;
    var first=true;
    function renderVar(val){
        var addSub=false;
        function renerLine(id,el){
            var type=$.type(el);
            if (type==='object'||type==='array'){
                if (id!=='__proto__'){
                    var tmp=$('<div>')
                    tmp.addClass('hasObject');
                    tmp.append($('<span>'+id+'</span>'));
                    if (currRequrs++<maxRequrs)
                        tmp.append(renderVar(el));
                    else
                        tmp.append($('<span>'+el+'</span>'));
                    tmp.addClass('logItem');
                    return tmp;
                }
            }else{
                var tmp=$('<div>');
                tmp.append($('<span>'+id+'</span>'));
                tmp.append($('<span>'+el+'</span>'));
                tmp.addClass('logItem')
                return tmp;
            }
        }
        var rVal=$('<div>');
        if (!first){
            addSub=true;
        }else{
            first=false;
        }
        if (val){
            if ($.type(val)==='object'){
                rVal.append('<span>{</span>');
                $.each(val,function(id,el){
                    rVal.append(renerLine(id,el));
                });
                rVal.append('}');
                if (addSub) rVal.addClass('logSub');
            }else{
                rVal.append('<span>[</span>');
                if ($.type(val)==='array'){
                    $.each(val,function(id,el){
                        rVal.append(renerLine(id,el));
                    });
                    rVal.append(']');
                }else{
                    var rVal=$('<span>');
                    rVal.text(val);
                }
            }
        //rVal.css('padding-left',currRequrs*5);
        }else{
            rVal.append($('<span>null</span>'));
            rVal.append();
        }
        if (currRequrs) {
            currRequrs--;
        }
        return rVal;
    }
    var Obj={
        info:function(text,time){
            //if (!time) time=7;
            var tmp=$('<div>');
            tmp.addClass('log');
            currRequrs=0;
            first=true;
            tmp.append(renderVar(text));
            $.fn.AlertBefore({
                timeOut:time,
                parentId:parentid,
                type:'info',
                text:tmp
            });
        }
    };
    return Obj;
};
var baseClass=function(param){
    var parent=$('body');
    if ($.type(param)==='string')
        parent=$("#"+param);
    else
    if (param['errorShowParentId']){
        if ($.type(param['errorShowParentId'])==='object')
            parent=param['errorShowParentId'];
        else
            parent=$('#'+param['errorShowParentId']);
    }
    var Obj={
        log:new AlertClass(parent)
    };
    return Obj;
};
var addDialogClass=function(param){
    
    var container=$('#'+param['id']);
    var header=$('#'+param['id']).children(':first-child').children(':first-child').children(':first-child');
    var Obj=$.fn.jsonMerge(new baseClass({errorShowParentId:container.children('div:first-child').children('div:first-child')}),{
        label:param['label'],
        show:function(label){
            if (label){
                header.children(':last-child').replaceWith($('<h3>'+label+'</h3>'));
            }else
                if (this.label){
                    header.children(':last-child').replaceWith($('<h3>'+this.label+'</h3>'));
                };
            container.modal('show');
        },
        hide:function(){
            container.modal('hide');
        }
    });
    console.log('Dialog init');
    console.log(Obj);
    return Obj;
};
var activeSelectBaseClass=function(param){
    var rqVarName='rq'; //Название переменной запроса по умолчанию
    if(param['rqVarName']){
        rqVarName=param['rqVarName'];//Название переменной запроса
        }
    var Obj={
        //select:$('#'+selId),
        addInputList:param['addInputList'], //Список полей
                /*
                 *                 {
                    label:'Метка',
                    inputId:'Идентификатор',
                    name:'Имя',
                    placeholder:'Если надо',
                    validate:{
                        required:'Должно быть заполнено',
                        integer:'Недопустимые символы' //Целочисленное
                    }
                },
                 */
        dialog:new addDialogClass({
            id:param['dialogId'],           //id Диалога
            label:param['addDialogLabel']   //Текст заголовка диалога
        }),
        errorShowParentId:param['errorShowParentId'],   //id родителя для вывода ошибок
        rqVarName:rqVarName,                
        requestName:param['requestName'],               //Название сомого запроса
        requestStaticParam:param['requestStaticParam'], //Статичные переменные формы ключ:значение
        requestDoneCallback:param['requestDoneCallback'],   //функция вызываемая при успехе добовления
        formName:param['formName'],                         //Имя формы для запроса
        ajaxupdaterequest:param['ajaxupdaterequest'],       //Адрес экшена запроса
        buttonSave:$('#'+param['dialogId']+'Save'),         //кнопка save
        form:$('#'+param['dialogId']+'-form'),              //форма
        
        /*          Функции         */
        checkError:function(id,opt){
            console.log(id);
            console.log(opt);
            var fld=$('#'+id);
            var rVal=true;
//            this.dialog.log.info({
//                tedt:{
//                    val:1,
//                    testArr:[
//                        'v2','v2',{testObjInarr:0}
//                    ]
//                },
//                testV:0.1,
//            });
            //console.log(fld);
            //this.dialog.log.info(fld);
            if (opt['required']){
                if (fld.val()===''){
                    rVal=false;
                    fld.parent().next().text(opt['required']);
                    fld.parent().parent().addClass('has-error');
                }
            }
            if (rVal&&opt['integer']){
                if ($.fn.checkIsInteger(fld.val())){
                    rVal=false;
                    fld.parent().next().text(opt['integer']);
                    fld.parent().parent().addClass('has-error');                    
                }
            }
            if (rVal){
                fld.parent().next().text('');
                fld.parent().parent().removeClass('has-error');
                fld.parent().parent().addClass('has-success');
            }
            return rVal;
        },
        showErrors:function(list){
            console.log('showErrors');
            console.log(list);
            Obj.form.children('.has-error').removeClass('has-error');
            if ($.isPlainObject(list)){
                $.each(list,function(key,val){
                    var fld=$('#'+Obj.formName.toLowerCase()+'-'+key);
                    console.log(fld);
                    fld.parent().next().text(val[0]);
                    fld.parent().parent().addClass('has-error');
                });
            }
        },
        addSubmit:function(e){
            e.preventDefault();
            var noErr=true;
            $.each(Obj.addInputList,function(id,el){
                if ($.isPlainObject(el['validate'])){
                    $.each(el.validate,function(id2,el2){
                        var dop={};
                        dop[id2]=el2;
                        noErr=Obj.checkError(el.inputId,dop);
                        if (!noErr) return false;
                    });
                };
                if (!noErr) return false;
            });
            if (noErr){
                if (Obj.ajaxupdaterequest){
                    var tmp={};
                    $.each(this.addInputList,function(id,el){
                        if (el['inputId']){
                            if ($('#'+el.inputId)){
                                if (!$.isFunction(el['beforeSave']))
                                    tmp[el.inputId.replace(Obj.formName.toLowerCase()+'-','')]=$('#'+el.inputId).val();
                                else
                                    tmp[el.inputId.replace(Obj.formName.toLowerCase()+'-','')]=el['beforeSave']($('#'+el.inputId).val());
                            }
                        }
                    });
                    if ($.isPlainObject(Obj.requestStaticParam)){
                        $.each(Obj.requestStaticParam,function(id,val){
                            tmp[id]=val;
                        });
                    }
                 
                    var dt={
                        url:Obj.ajaxupdaterequest,
                        type:'post',
                        data:{
                            _csrf:Obj.form.children('[name=_csrf]:first-child').val(),
                            ajax:true
                        }
                    };
                    dt.data[this.rqVarName]=this.requestName;
                    dt.data[this.formName]=tmp;
                    console.log(dt);
                    $.ajax(dt).done(function(data){
                        if (data['status']==='ok'){
                            if ($.isFunction(Obj.requestDoneCallback)) 
                                Obj.requestDoneCallback(data,function(){
                                    Obj.dialog.hide();
                                });
                            else
                                Obj.dialog.hide();
                        }else{
                            Obj.showErrors(data['errors']);
                        }
                        console.log(data);
                    });
                }else{
                    Obj.dialog.hide();
                    if (Obj.errorShowParentId){
                    $.fn.AlertBefore({
                            text:'activeSelectClass::addContractorsSubmit - не задан URL ajax экшена',
                            type:'danger',
                            parentId:Obj.errorShowParentId,//'zakaz-form',
                            timeOut:7
                        });
                    }else{
                        console.log('activeSelectClass::addContractorsSubmit - не задан URL ajax экшена');
                    }
                }
            }            
            
        },
        add:function(defVal){
            if (!defVal) defVal={};
            this.buttonSave.unbind('click');
            this.buttonSave.click(function(e){Obj.addSubmit(e);});
            this.form.children().remove();
            $.fn.addField(this.form,this.addInputList,defVal);
            this.dialog.show();
        }
    };
    return Obj;
};

var activeSelectClass=function(param){
    var Obj=new activeSelectBaseClass(param);
    Obj['selectId']=param['selectId'];              //ID списка UL
    Obj['afterClick']=param['afterClick'];          //Вызывается после щелчка если не задана selectCallback
    Obj['selectCallback']=param['selectCallback'];  //Вызывается при щелчке
    Obj['hiddenInputId']=param['hiddenInputId'];    //Йди скрытого поля (для формы)
    Obj['resetInputs']=param['resetInputs'];        //Сброс при инициализации
    Obj['likeRequest']=param['likeRequest']         //Название запроса для поиска Like
    Obj['visibleInputId']=param['visibleInputId']   //Видимое поле ввода
    if (param['slaveField']){                       //Настройка зависимой колонки {}
        var tmpParam=param['slaveField'];                   
        if (tmpParam['id']&&tmpParam['requestName']&&!$.isFunction(Obj['requestDoneCallback'])){
            var rqVarName='rq';                     //Название переменной запроса по умолчанию
            if(tmpParam['rqVarName']){
                rqVarName=tmpParam['rqVarName'];    //Пользовательское Название переменной запроса
            }
            tmpParam['rqVarName']=rqVarName;
            if (!tmpParam['itemsVarName']) tmpParam['itemsVarName']='items';    //Имя переменной c ответом
            Obj['requestDoneCallback']=function(data,classBack){                //requestDoneCallback функция лбновления по умолчанию
                var dt={};
                if ($.isPlainObject(tmpParam['parametrs'])){
                    $.each(tmpParam['parametrs'],function(id,val){
                        dt[id]=val;
                    });
                }
                dt[tmpParam.rqVarName]=tmpParam.requestName;
                $.post(Obj.ajaxupdaterequest,dt,function(data){
                    console.log(data);
                    $.fn.renderUlConten(tmpParam.id,data[tmpParam['itemsVarName']]);
                    Obj.init();
                    if ($.isFunction(tmpParam['itemsVarName'])) tmpParam['itemsVarName']();
                    classBack();                
                });
            };
        }
    }
    Obj['selectClick']=function(el,e){
        console.log('activeSelectClass.selectClick');
        e.preventDefault();
        var a=$(el);
        if (a.attr('value')!=-1){
            if (Obj['removeFromList']) a.css('display','none');//Скрыть в списке выбора
            var tempData={id:a.attr('value'),label:a.text(),selectId:Obj.selectId,element:a};
            if ($.isFunction(Obj['selectCallback']))
                Obj.selectCallback(tempData);
            else{
                
                if (!Obj['hiddenInputId']){
                    var par=a.parent().parent().parent().parent().parent().parent();
                    par.find('input:first-child').val(a.attr('value'));
                    par.find('.form-control:first-child').val(a.text());
                }else{
                    console.log('hiddenInput:"'+Obj['hiddenInputId']+'"');
                    $('#'+Obj['hiddenInputId']).val(a.attr('value'));
                    $('#'+Obj['hiddenInputId']).next().val(a.text());
                }
                if ($.isFunction(Obj['afterClick'])) Obj['afterClick'](tempData);
            }
        }else{
            Obj.add();
        }        
    };
    Obj['initListRespons']=function(){
        $.each($("#"+Obj.selectId).find('[role=menuItem]'),function(id,el){
            $(el).unbind('click');
            $(el).click(function(e){
                Obj.selectClick(el,e);
            });
        });        
    };
    Obj['init']=function(){
        if (Obj.likeRequest && Obj.visibleInputId && Obj.ajaxupdaterequest){
            //var like=$('#'+Obj.visibleInputId);
            $('#'+Obj.visibleInputId).keyup(function(e){
                var id=0; 
                var thisId=$(this).attr('id');
                if (thisId.indexOf('lb_') + 1){
                    id=thisId.substring(thisId.indexOf('lb_') + 3,thisId.lenght);
                }
                var dt={
                    ajax:true
                };
                dt[Obj.rqVarName]=Obj.likeRequest;
                dt['like']=$('#'+Obj.visibleInputId).val();
                if (dt['like'].length>1){
                console.log(dt);
                $.post(Obj.ajaxupdaterequest,dt,function(data){
                    
                    console.debug('inpObj',id);
                    console.log($(data.html).find('#'+id));
                });
                }
            });
        }
        if (Obj.resetInputs){
            if (Obj.hiddenInputId){
                $('#'+Obj.hiddenInputId).val('');
                $('#'+Obj.hiddenInputId).next().val('');
            }
            Obj.resetInputs=false;
        }
        Obj.initListRespons();
    };
    Obj.init();
    console.log('activeSelectClass-Inited for "#'+Obj.selectId+'"');
    console.log(Obj);
    return Obj;
};
var zakazDialogs=function(menuId,ajaxupdaterequest){
    var customerMan=null;
    //console.debug('zakazDialogs',menuId);
    function createCustManAS(opt,resetInput){
        return new activeSelectClass({
            selectId:menuId+'_Man',
            dialogId:'askMoadal',
            addDialogLabel:'Добавить менеджера заказчика:',
            errorShowParentId:'zakaz-form',
            formName:'Manager',
            requestName:'addManager',
            ajaxupdaterequest:ajaxupdaterequest,
            addInputList:[
                {
                    label:'Имя менеджера:',
                    inputId:'manager-name',
                    name:'name',
                    placeholder:'Укажите имя менеджера',
                    help:'Или ФИО через пробел',
                },
                {
                    separator:true,
                    text:'Дополнительные параметры'
                },
                {
                    label:'Телефон:',
                    inputId:'manager-fone',
                    validate:{
                        integer:'Недопустимые символы'
                    },
                    name:'fone'
                }
            ],
            requestStaticParam:{
                firm_id:opt.id
            },
            slaveField:{
                id:menuId+'_Man',
                requestName:'managerList',
                itemsVarName:'managerList',
                parametrs:{
                    firmId:opt.id
                }
            },
            afterClick:function(dt){
                console.log(dt);
                $('#zakaz-customermanager').next().attr('title',dt.label+' т.: '+dt.element.attr('title'));
            },
            hiddenInputId:'zakaz-customermanager',
            resetInputs:!(resetInput===false),
            likeRequest:'mainLike',
            visibleInputId:'lb_'+menuId+'_Man'
        });
        
    };
    customerMan=createCustManAS({id:$('#'+menuId).parent().parent().children('input:first-child').val()},false);
    var Obj=new activeSelectClass({
        selectId:menuId,
        dialogId:'askMoadal',
        addDialogLabel:'Добавить фирму заказчика:',
        errorShowParentId:'zakaz-form',
            formName:'Firms',
            requestStaticParam:{
                firmTypes:[0]
            },
            ajaxupdaterequest:ajaxupdaterequest,
            requestName:'addFirm',
            addInputList:[
                {
                    label:'Название фирмы:',
                    inputId:'firms-name',
                    name:'name',
                    placeholder:'Укажите название фирмы'
                },
                {
                    separator:true,
                    text:'Дополнительные параметры'
                },
                {
                    label:'Телефон:',
                    inputId:'firms-fone',
                    validate:{
                        integer:'Недопустимые символы'
                    },
                    name:'fone'
                },
                {
                    label:'Юредический аддрес:',
                    inputId:'firms-addres1',
                    name:'addres1'
                },
                {
                label:'Аддрес производства:',
                inputId:'firms-addres2',
                name:'addres2'
                }
            ],
            afterClick:function(opt){
                console.log(opt);
                var data={
                    ajax:true,
                    rq:'managerList',
                    firmId:opt.id
                };
                $.ajax({url:this.ajaxupdaterequest,type:'post',data:data}).done(function(data){
                    console.log(data);
                    if (data['managerList']){
                        var tmpPar=$('#'+menuId+'_Man').parent();
                        $('#'+menuId+'_Man').remove();
                        $.fn.renderDropDown(
                            tmpPar,
                            data.managerList,
                            {
                                id:menuId+'_Man',
                                class:'pull-right'
                            }
                        );
                        if (customerMan) delete customerMan;
                        customerMan=createCustManAS(opt);
                    }
                });
            },
            slaveField:{
                id:menuId,
                requestName:'getFirmList',
                parametrs:{
                    firmId:0
                }
            },
            hiddenInputId:'zakaz-customername'
    });
    $('#zakaz-numberofcopies').focusout(function(e){
        $('#product-quantity').val($(this).val());
    });
    return Obj;
};
function onDetaliSelFileClick(dd,e){
    e.preventDefault();
    var contId=$(dd).parent().parent().parent().parent().attr('contid');
    console.debug('onDetaliSelFileClick',contId);
    if ($.type(contId)==='undefined'){
        console.error('onDetaliSelFileClick','Незадан ID приёмника');
        return;
    }
    var ajaxCont=$(contId);
    if (!ajaxCont.length){
        console.error('onDetaliSelFileClick','Ненайден приёмник с ID: "'+contId+'"');
        return;        
    }
    if (ajaxCont.text()==='Не выбран') ajaxCont.text('');
    console.debug('onDetaliSelFileClick',ajaxCont.children().length);
    if (ajaxCont.children().length>1){
        console.debug('onDetaliSelFileClick','remove');
        ajaxCont.children(':first-child').remove();
    }
    //$.fn.addBusy(ajaxCont.attr('id'));
    $.fn.addBusy(ajaxCont);
    $.post(document.URL,{
        'ajax':true,
        showFileName:$(dd).children('a:first-child').text()
    }).done(function(data){
        //ajaxCont.replaceWith($(data).find('#ajaxCont'));
        console.debug('onDetaliSelFileClick','done');
        var iframe=$(data).find(contId).children(':first-child');
        console.debug('onDetaliSelFileClick::iframe:tag',iframe[0].tagName);
        if (iframe[0].tagName==='IMG'){
            iframe.load(function(){
                $.fn.removeBusy();
                ajaxCont.append(iframe);
            });
        }else{
            iframe.ready(function(){
                $.fn.removeBusy();
                ajaxCont.append(iframe);
            });            
        }
    }).error(function(){
        console.debug('onDetaliSelFileClick','error');
        $.fn.removeBusy();
        ajaxCont.append('<div>Ошибка при загрузке</div>');
    });
};
//setInterval(function() {
//    var tmp=$('#hddPjaxInfo');
//    if (tmp.length){
//        $.pjax.reload('#hddPjaxInfo',{timeout:300000});
//        console.debug('#hddPjaxInfo','reload');
//    }
//},60000);