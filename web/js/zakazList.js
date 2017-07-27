/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var ZakazLisController=function(opt){
    var back=-1;
    this.reInit=function(){
        var self=this;
        $('[role=checkbexec]').unbind('click');
        $('[role=checkbexec]').click({self:this},this.bugalterExecutersOpl);
        $('[role=lInteractive]').unbind('change');
        $('[role=lInteractive]').change({self:this},this.stageChange);
        $('[role=lInteractive]').removeAttr('disabled');
        $('#zakazFind').unbind('click');
        $('#zakazFind').click({'thisObj':this},function(e){
            e.preventDefault();
            e.data.thisObj.zakazFind($(this));
        });
        $('#zakazFindNum').unbind('keydown');
        $('#zakazFindNum').keydown({'thisObj':this},function(e){
            if (e.keyCode===13)
                e.data.thisObj.zakazFind($('#zakazFind'));
        });
        $('#zakazFindNum').unbind('focusout');
        $('#zakazFindNum').focusout(function(e){
            //console.log(e.originalEvent.relatedTarget.id);
            var id=null;
            if (e.originalEvent.relatedTarget) id=e.originalEvent.relatedTarget.id;
            if (id!=='zakazFind'){
                $(this).val('');
                $(this).parent().parent().removeClass('has-error');
                $(this).parent().parent().children('p').text('');
                //$(this).parent().next().text(data.errorText);
            }
        });
        $('#reset-date-search').click(function(){
            $('[role=tofilter]').each(function(){
                if ($(this)[0].tagName==='INPUT') $(this).val('');
            });
            $('#listZakazovBugalter').yiiGridView("applyFilter");
        });
        $('.hidden').removeClass('hidden');
        $('#loader').hide();
        $('[type=checkbox]').each(function(){
            if (!$(this).attr('name')){
                $(this).click({'thisObj':self},function(e){
                    e.data.thisObj.zakazSetIs_material_recived(this);
                });
            }else{
                //console.debug('reInit',this);
                if ($(this).attr('name')==='received'){
                    $(this).click({'thisObj':self},function(e){
                        e.data.thisObj.zakazSetIs_material_recived2(this);
                    });
                }else if ($(this).attr('name')==='ordered'){
                    $(this).click({'thisObj':self},function(e){
                        e.data.thisObj.zakazSetIs_material_ordered(this);
                    });
                }else{
                    $(this).click(function(e){
                        if (!$(this).attr('checked')){
                            $(this).attr('checked',true);
                            $(this).prev().val('on');
                        }else{
                            $(this).attr('checked',false);
                            $(this).prev().val('');
                        }
                    });   
                }
            }
        });
        $('.prDone').click({self:this},this.stageChange);
        $('[name=chb1cont]').bind('contextmenu',{'thisObj':this}, function(e) {
            console.debug('contextMenu',$('#contMenu'));
            if (!$(this).children('input').length){
                if (!$(this).children('.dropdown').length){
                    var ul=$('#contMenu').clone();
                    var div=$('<div class="dropdown"></div>');
                    var a=$('<a data-toggle="dropdown" href="#" aria-expanded="true" style="display:none;"></a>');
                    div.append(a);
                    div.append(ul);
                    $(this).append(div);
                }else{
                    var a=$(this).children('.dropdown').children('a:first-child');
                }
                a.dropdown('toggle');
                $('a[href=chgdt]').unbind('click');
                $('a[href=chgdt]').click({'thisObj':e.data.thisObj},function(e){
                    var p=$(this).parent().parent().parent().parent().parent();
                    console.debug('menuItAction',p);
                    e.preventDefault();
                    e.data.thisObj.zakazSetIs_material_recived(p,$(p).attr('back'));
                });
                $('a[href=canceldt]').unbind('click');
                $('a[href=canceldt]').click({'thisObj':e.data.thisObj},function(e){
                    var p=$(this).parent().parent().parent().parent().parent();
                    console.debug('menuItAction',p);
                    e.preventDefault();
                    e.data.thisObj.zakazSetIs_material_recived(p,$(p).attr('back'),null);
                });
            }
            return false;
        });
        
        $('[title]').tooltip({
            container:'body',
            delay:200,
        });
        var tmpPickerOptions=this.datePickerOptions;
        $('[role=datepicker]').each(function(){
            $(this).datepicker('destroy');
            $(this).datepicker(tmpPickerOptions);
        });
    }
    this.loadBanner=null;
    this.init=function(){
        var load=$('#loadingBig').clone();
        if ($.type(opt.datePickerOptions)==='object')
            this.datePickerOptions=opt.datePickerOptions;
        else
            this.datePickerOptions={};
        load.removeAttr('style');
        load.addClass('center-block');
        this.loadBanner=new DialogController({
            body:{html:''},
            dialog:{options:{class:'zakazList'}},
            header:false,
            footer:false,
            size:'sm',
        });
        this.loadBanner.body.append(load);
        if (!this.options) this.options=opt;
        this.reInit();
        $('#listPjax').on('pjax:success',{thisObj:this},function(e){
            e.data.thisObj.reInit();
        });//pjax:beforeSend
        $('#listPjax').on('pjax:beforeSend',{thisObj:this},function(e){
            $('[title]').tooltip('destroy');
        });
        $('#listPjax').on('pjax:send',{self:this},function(e){
            $('#loader').show();
            e.data.self.loadBanner.show();
        });
        $('#listPjax').on('pjax:end',{self:this},function(e){
            $('#loader').hide();
            e.data.self.loadBanner.hide();
        });
        console.debug('ZakazLisController','start');
    };
    this.dialogOkClick=function(e){
        var self=e.data.zList;
        console.debug('dialogOkClick',e.data);
        var value={};
        value[e.data.materialDateParam.varName]=$('#list-mordDtSel').val();
        var data={
            ajax:true,
            id:$(e.data.element).attr('data-key'),
            materialDateParam:$.extend(e.data.materialDateParam,{value:value}),
//            attrToSave:['is_material_ordered']
        };
        data[self.options.rqVarName]=self.options.rqName;
        var rq={url:self.options.ajaxupdaterequest,type:'post',data:data};
        console.debug('rq',rq);
        $('#list-mordDtSel').datepicker('destroy');
        //var bs=self.showLoad();
        self.loadBanner.show();
        $.ajax(rq).done(function(dt){
            console.debug('dialogOk',dt);
            //bs.hide();
            
            if (dt.status==='ok'||dt.status==='Ok'){
                $.fn.AlertAfter({
                    type:'success',
                    text:dt.html,
                    timeOut:5,
                    parentId:'listPjax'
                });
                //if (zakaz['payment']){
                    $.pjax.reload('#listPjax',{timeout:10000});
                //}
            }else{
                self.loadBanner.hide();
                var bs=new DialogController({
                    body:{html:dt.errorText},
                    header:'Ошибка',
                    buttons:{
                        cansel:'Закрыть'
                    }
                });
                bs.show();
            }
        });

    };
    this.dateChcngHeader=function(number,text,manager){
        return {
            header:'Заказ № '+number+'<small> (менеджер : '+manager+')</small>',
            body:'<div class="row"><div class="col-sm-6"><div class="input-group input-group-sm">'+
                 '<span class="input-group-addon" for="list-mordDtSel">'+text+': </span>'+
                 '<input type="data" class="form-control" id="list-mordDtSel" /></div></div></div>'
        };
    };
    this.dialogData=function(dialogData,chng){
        var test=new DialogController({
            header:{html:dialogData.header},
            body:{html:dialogData.body},
            okClick:dialogData.okClick,
            canselClick:dialogData.canselClick
        });

        test.show();
        if (!chng)
            var d=new Date();
        else
            var d=new Date(chng);

        $('#list-mordDtSel').val(d.getDate()+'.'+(d.getMonth()+1)+'.'+d.getFullYear());
        var tmpPickerOpt=this.datePickerOptions;
        $('#list-mordDtSel').datepicker($.extend({
            changeYear:true,
            changeMonth :true,
            gotoCurrent:true,
            showOtherMonths:true,
            defaultDate:d
        },tmpPickerOpt));
        console.debug('curDate',d);        
    };
    this.findFirst=function(el){
        var i=0;
        while($.type($(el).attr('data-key'))==='undefined'&&i<5){
            el=$(el).prev();
            i++;
        }
        return (el);
    }

    this.zakazSetIs_material_recived2=function(elm,chng,newVal){
        console.debug('[type=checkbox]','click');
        if (!$(elm).attr('data-key')) 
            var el=this.findFirst($(elm).parent().parent());
        else
            var el=elm;
        console.debug('[type=checkbox]Parent',el);
        if ($.type(newVal)==='undefined'){
            var dialogData=this.dateChcngHeader($(el).attr('data-key'),'Материал получен',$(el).attr('manName'));
            dialogData.okClick=[{
                    element:el,
                    zList:this,
                    newVal:newVal,
                    materialDateParam:{
                        varName:'dateOfGet',
                        'material-index':$(elm).attr('material-index'),
                    }
            },this.dialogOkClick];
            dialogData.canselClick=[{element:elm},function(e){
                $('#list-mordDtSel').datepicker('destroy');
                if (!chng)
                    $(e.data.element).attr('checked',false);
            }];
            this.dialogData(dialogData,chng);
        }else{
            var header='<span class="text-danger">Внимание!</span>';
            var mess='<p><b>Заказ № '+$(el).attr('data-key');
            mess+=' (менеджер : '+$(el).attr('manName')+')</b></p>';
            mess+='<b class="text-danger">Отметить материал как не заказанный?</b>';
            var test=new DialogController({
                header:{html:header},
                body:{html:mess},
                okClick:[{element:el,zList:this,newVal:newVal},this.dialogOkClick],
            });
            test.show();
        }
    }
    this.zakazSetIs_material_ordered=function(elm,chng,newVal){
        console.debug('[type=checkbox]','click');
        if (!$(elm).attr('data-key')) 
            var el=this.findFirst($(elm).parent().parent());
        else
            var el=elm;
        if ($.type(newVal)==='undefined'){
            var dialogData=this.dateChcngHeader($(el).attr('data-key'),'Материал заказан',$(el).attr('manName'));
            dialogData.okClick=[{
                    element:el,
                    zList:this,
                    newVal:newVal,
                    materialDateParam:{
                        varName:'dateOfOrder',
                        'material-index':$(elm).attr('material-index'),
                    }                    
            },this.dialogOkClick];
            dialogData.canselClick=[{element:elm},function(e){
                $('#list-mordDtSel').datepicker('destroy');
                if (!chng)
                    $(e.data.element).attr('checked',false);
            }];
            this.dialogData(dialogData,chng);
        }else{
            var header='<span class="text-danger">Внимание!</span>';
            var mess='<p><b>Заказ № '+$(el).attr('data-key');
            mess+=' (менеджер : '+$(el).attr('manName')+')</b></p>';
            mess+='<b class="text-danger">Отметить материал как не заказанный?</b>';
            var test=new DialogController({
                header:{html:header},
                body:{html:mess},
                okClick:[{element:el,zList:this,newVal:newVal},this.dialogOkClick],
            });
            test.show();
        }
    }

    this.zakazFind=function(p){
        $('#loader').show();
        var a=p;
        var id=$('#zakazFindNum').val();
        var param={id:id};
        if (this.options.isBugalter) param.isBugalter=true;
        $.post(a.attr('href'),param).done(function(data){
            if ($.type(data)==='object'){
                //a.parent().parent().next().text(data.errorText);
                console.log(data);
                $('#zakazFindNum').parent().next().text(data.errorText);
                a.parent().parent().parent().addClass('has-error');
                $('#zakazFindNum').focus();
            }
            $('#loader').hide();
        });
        
    };
//    this.showLoad=function(show){
//        var load=$('#loadingBig').clone();
//        load.removeAttr('style');
//        load.addClass('center-block');
//        var bs=new DialogController({
//            body:{html:''},
//            dialog:{options:{class:'zakazList'}},
//            header:false,
//            footer:false,
//            size:'sm',
//        });
//        bs.body.append(load);
//        if (show) bs.show();
//        return bs;
//    };
    this.dialogOk=function(e){
        var self=e.data.zList;
        var zakaz={};
        zakaz[$(e.data.element).attr("name")]=$(e.data.element).val();
        var data={
            ajax:true,
            id:$(e.data.element).parent().parent().attr('data-key'),
            Zakaz:zakaz,
            attrToSave:['stage']
        };
        data[self.options.rqVarName]=self.options.rqName;
        var rq={url:self.options.ajaxupdaterequest,type:'post',data:data};
        self.loadBanner.show();
        console.debug('dialogOk:rq',rq);
        $.ajax(rq).done(function(dt){
            console.debug('dialogOk',dt);
            if (dt.status==='ok'){
                $(e.data.element).attr('back',$(e.data.element).val());
                $.fn.AlertAfter({
                    type:'success',
                    text:dt.html,
                    timeOut:5,
                    parentId:'listPjax'
                });
                //if (zakaz['payment']){
                    $.pjax.reload('#listPjax',{timeout:10000});
                //}
            }else{
                self.loadBanner.hide();
                var bs=new DialogController({
                    body:{html:dt.errorText},
                    header:'Ошибка',
                    buttons:{
                        cansel:'Закрыть'
                    }
                });
                bs.show();
            }
        });
    };
    this.dialogCansel=function(e){
        var el=e.data.element;
        console.debug('dialogCansel',$(el));
        if($(el)[0].tagName==='SELECT')
            $(el).val($(el).attr('back'));
    };
    this.stageChange=function(event){
        var self=event.data.self;
        var it=this;
        var val=$(this).val();
        var isButton=$(this)[0].tagName==='BUTTON';
        if(isButton)
            val=$(this).attr('value');
        console.debug('ZakazLisController:stageChange:Click',val);
        var messTxt='';
        switch ($(this).attr('name')){
            case 'stage':
                messTxt='Этап работы';
                break;
            case 'payment':
                messTxt='Оплата';
                break;
        }
        var backTxt='';
        var toTxt='';
        if (isButton&&$(this).attr('name')==='stage'){
            backTxt=$(this).text();
            toTxt='Готов';
        }else{
            backTxt=$(this).children('[value='+$(this).attr('back')+']').text();
            toTxt=$(this).children('[value='+val+']').text();            
        }
        if ($(this).attr('newstagetext')) toTxt=$(this).attr('newstagetext');
        var mess='<p>Изменить значение поля <b>"'+messTxt+'"</b>';
        mess+=' с <b>"'+backTxt
        mess+='"</b> на <b>"'+toTxt+'"</b></p>';
        mess+='<p>В заказе № '+$(this).parent().parent().attr('data-key');
        mess+=' менеджер : '+$(this).parent().parent().children('[man=manager]').text()+'</p>';
        var test=new DialogController({
            header:"Внимание!",
            body:{html:mess},
            okClick:[{element:this,zList:self},self.dialogOk],
            canselClick:[{element:this,zList:self},self.dialogCansel]
        });
        test.show();
    };
    this.bugalterExecutersOpl=function(e){
        console.log(this);
        var self=e.data.self;
        var zId=$(this).parent().parent().parent().attr('data-key');
        var infoText=$(this).parent().attr('info-text');
        if (!infoText) infoText='Исполнитель';
        var dialogData=self.dateChcngHeader(zId,infoText+' оплачен',$(this).parent().parent().parent().children(':nth-child(3)').text());
        dialogData.okClick=[{
            element:$(this).parent().parent().parent(),
            zList:self,
//            newVal:newVal,
            materialDateParam:{
                requestFor:$(this).parent().attr('requestfor'),//'executer',
                varName:'payed',
                index:$(this).parent().attr('elnum'),
            }                    
        },self.dialogOkClick];
        dialogData.canselClick=[function(e){$('#list-mordDtSel').datepicker('destroy');}];
        self.dialogData(dialogData,null);

    };
    this.init();
    return this;
};