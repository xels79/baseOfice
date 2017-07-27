/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var ZakazController=function(custManId,contractors,formId){
    var cMan=custManId;
    if (formId){
        var form=$('#'+formId);
    }else{
        var form=null;
    }
    console.debug('ZakazController:init',"ZakazController Init("+custManId+", "+contractors+")");
    var contrListCount=0;   //Счётчик исполнителеё (чтобы не повторялись)
    var Obj= $.extend({
        contractors:contractors,
        managerSelList:{},
        checkPressedKey:function(e){
                if (e.keyCode>57&&e.keyCode!==190){
                    e.preventDefault();
                    return true;
                }else if (e.key==='.'){
                    if ($(this).val().indexOf('.')!==-1){
                        e.preventDefault();
                        return true;
                    }
                }
            },
        showExecutersToEdit:function(opt){
            console.debug('ZakazController:showExecutersToEdit',opt);
            $.each(opt,function(id){
                this.selectId='zakaz-contractors-sel';
                Obj.selContractor(this);
                console.debug('ZakazController:showExecutersToEdit',this);
            });
        },
        DDValidate:function(data){
            var id=$(data).parent().parent().parent().children('input:first-child').attr('id');
            console.log(id);
            if (form){
                form.yiiActiveForm('validateAttribute', id);
            }
        },
        itemClick: function(el){
            console.debug('Zakaz::itemClick',el);
            var a=$(el).children('a:first');
            $.post(a.attr('url'),{ajax:true,id:a.attr('value')}).done(function(data){
                if (cMan){
                    window[cMan].loadList(data.list);
                }
            });
            Obj.DDValidate(el);
        },
        selContractor:function(opt){
            console.debug('ZakazController.selContractor',opt);
            var firmSel=$("#"+contractors.selId);
            var tmp=$('#'+Obj.contractors.tblId).children('tbody').children('tr:last').clone();
            tmp.removeAttr('style');
            tmp.attr({role:'item',firmId:opt.id});
            tmp.children(':nth-child(2)').children('span:first').text(opt.label); 
            $('#'+Obj.contractors.tblId).children('tbody').children('tr:last').before(tmp);
            if ($('#'+Obj.contractors.tblId).children('tbody').children().length===3){//Первый
                tmp.children(':nth-child(1)').text('Основной'); 
            }
            //var sel=tmp.children(':nth-child(3)').children('select:first-child');   //Select с менеджервм
            var likeInp=tmp.children(':nth-child(3)').find('.form-control:first');
            var inp=tmp.children(':nth-child(5)').children('div:first').children('input:first-child');    //Поле с ценой
            var inpFirmId=tmp.children(':nth-child(2)').children('input:first-child');    //Поле с ID фирмы исполнителя
            console.debug('inpFirmId',inpFirmId);
            var inpId=tmp.children(':nth-child(3)').children('input:first-child');    //Поле с ID менеджера исполнителя
            var workType=tmp.children(':nth-child(4)').find('input:first'); //Поле с типом работ
            var workTypeUl=tmp.children(':nth-child(4)').find('ul:first');
            var workTypeLbl=workType.next();
            var attrN=inp.attr('name');
            var perf='Zakaz['+attrN+'][value]['+contrListCount+']';
            var payedDate=tmp.children(':nth-child(10)').find('input:first');
            console.debug('selContractor:inp',inp);
            workType.attr('name',perf+'[methodOfExecution]');
            payedDate.attr('name',perf+'[payed]');
            var workTypeId='wtInp'+attrN+'_'+contrListCount;
            var workTypeUlId='wtUl'+attrN+'_'+contrListCount;
            var workTypeLblId='lbl_wtUl'+attrN+'_'+contrListCount;
            workTypeLbl.attr('id',workTypeLblId);
            workType.attr('id',workTypeId);
            workTypeUl.attr('id',workTypeUlId);
            console.debug('selContractor',workType);
            console.debug('selContractor',workTypeUl);
            console.debug('selContractor',workTypeLbl);
            console.debug('payed',payedDate);
            payedDate.val('0');
            if (opt.values)
                if ($.type(opt.values.payed)!=='undefined')
                    payedDate.val(opt.values.payed);
                
            new ActiveDropDown({
                menuId:workTypeUlId,
                labelId:workTypeLblId,
                prevDef:true,
                formControlID:workTypeId
            });
            
            for (var i=0;i<3;i++){
                var inpDop=tmp.children(':nth-child('+(6+i)+')').children('div:first-child').children('input:first-child');
                var nm=inpDop.attr('name');
                inpDop.attr('name',perf+'['+nm+']');
                if (opt.values){
                    if (opt.values[nm]) inpDop.val(opt.values[nm]);
                }
            }
            inp.attr('name',perf+'[coast]');
            inpId.attr('name',perf+'[idManager]');
            inpFirmId.attr('name',perf+'[idFirm]');
            inpFirmId.val(opt.id);
            //inpId.val(opt.id);
            likeInp.attr('id','manSel_'+contrListCount+'LikeInput');
            if (opt.values){
                if (opt.values.coast) inp.val(opt.values.coast);
                if (opt.values.idManager) inpId.val(opt.values.idManager);
                if (opt.values.methodOfExecution){
                    workType.val(opt.values.methodOfExecution);
                    workTypeUl.children().each(function(){
                        console.debug('workTypeUl',$(this).children('a:first-child'));
                        if ($(this).children('a:first-child').attr('value')==opt.values.methodOfExecution){
                            if (workType.next()[0].tagName==='INPUT'){
                                workType.next().val($(this).children('a:first-child').text());
                            }else{
                                workType.next().text($(this).children('a:first-child').text());
                            }
                            return false;
                        }
                    });
                }
            }
            contrListCount++;
            console.log(inpId);
            //sel.attr('name','Zakaz['+attrN+']['+tmp.index()+'][manager]');
            //console.log(sel);
            var data={
                ajax:true,
                rq:'managerList',
                firmId:opt.id
            };
            $('[role=zaryad]').unbind('keyup');
            $('[role=zaryad]').keyup(Obj.profitCalculation);
            $('[role=paymet]').unbind('keyup');
            $('[role=paymet]').keyup(Obj.profitCalculation);
            $('[role=zaryad]').unbind('keydown');
            $('[role=zaryad]').keydown(Obj.checkPressedKey);
            $('[role=paymet]').unbind('keydown');
            $('[role=paymet]').keydown(Obj.checkPressedKey);
            $.ajax({url:Obj.contractors.ajaxupdaterequest,type:'post',data:data}).done(function(data){
                console.debug('selContractor:ajax',data);
                var isSel=false;
                if (data.managerList){
                    var tmpId='manSel_'+tmp.index();
                    $.fn.renderDropDown(
                            tmp.children(':nth-child(3)').find('.dropdown'),
                            data.managerList,
                            {
                                id:tmpId
                            }
                    );
                    if (opt.values){
                        $.each(tmp.children(':nth-child(3)').find('.dropdown-menu').children().children(),function(id,el){
                            console.debug('idMnager:ans',$(el).children(':first'));
                            if ($(el).attr('value')==opt.values.idManager){
                               console.debug('idMnager:found',el);
                               if ($(el).attr('title')) likeInp.attr('title',$(el).attr('title'));
                               if (likeInp[0].tagName==='INPUT'){
                                   likeInp.val($(el).text());
                               }else{
                                   likeInp.text($(el).text());
                               }
                               return false;
                            }
                        });
                    }
                    if (Obj.managerSelList[tmpId]){
                        delete Obj.managerSelList[tmpId];
                    }
                    Obj.managerSelList[tmpId]=new activeSelectClass({
                        selectId:tmpId,
                        dialogId:contractors['dialogId'],
                        addDialogLabel:'Добавить менеджера:',
                        errorShowParentId:'zakaz-form',
                        formName:'Manager',
                        requestName:'addManager',
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
                        ajaxupdaterequest:contractors['ajaxupdaterequest'],
                        slaveField:{
                            id:tmpId,
                            requestName:'managerList',
                            itemsVarName:'managerList',
                            parametrs:{
                                firmId:opt.id
                            }
                        }
                    });
                }else{
                    tmp.children(':nth-child(3)').find('.input-group').remove();
                }
                //sel.removeAttr('style');
                tmp.children(':nth-child(3)').removeClass('progress');
            });
            tmp.children(':nth-child(9)').children('button:first-child').click(function(e){
                e.preventDefault();
                firmSel.find('[value='+opt.id+']').removeAttr('style');
                if ($(this).parent().parent().index()===1&&$(this).parent().parent().parent().children().length>3){//Стал Первый
                    $(this).parent().parent().next().children(':nth-child(1)').text('Основной');
                }
                $(this).parent().parent().remove();
                Obj.profitCalculationTotal();
            });
        }
    },new OtgruzkiController);
    Obj['addContractors']=new activeSelectClass({
            selectId:Obj.contractors.selId,
            dialogId:contractors['dialogId'],
            addDialogLabel:'Добавить фирму исполнитель:',
            errorShowParentId:'zakaz-form',
            formName:'Firms',
            requestStaticParam:{
                firmTypes:[1]
            },
            ajaxupdaterequest:contractors['ajaxupdaterequest'],
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
                    label:'Юридический адрес:',
                    inputId:'firms-addres1',
                    name:'addres1'
                },
                {
                label:'Адрес производства:',
                inputId:'firms-addres2',
                name:'addres2'
                }
            ],
            slaveField:{
                id:contractors.selId,
                requestName:'getFirmList'
            },
            selectCallback:Obj.selContractor
        });
    Obj['orderType']=function(e){
        console.debug('orderType',e);
        $('#product-name').val($(e).children('a:first').text());
    };
    Obj['colorsTypeChange']=function(e){
        var face=$('#faceType').children(':nth-child('+(parseInt($('#faceType').val())+1)+')').text();
        var back=$('#backType').children(':nth-child('+(parseInt($('#backType').val())+1)+')').text();
        var tbody=$(this).parent().parent().parent();
        //panton-face
        //panton-back
        //Пантон:
        if (face==='CMYK'){
            face=0;
        }else{
            if (face.length>1){
                face=parseInt(face.substr(face.length-2));
            }else{
                face=parseInt(face);
            }
        }
        if (back==='CMYK'){
            back=0;
        }else{
            if (back.length>1){
                back=parseInt(back.substr(back.length-2));
            }else{
                back=parseInt(back);
            }
        }
        if (face>back)
            var cnt=face;
        else
            var cnt=back;
        if (cnt>tbody.children().length-4){
            for (var i=tbody.children().length-3;i<cnt;i++){
                var tmp=tbody.children(':last').clone();
                if (i===0) tmp.children(':first').text('Пантон:');
                tmp.children(':nth-child(2)').children('input:first').attr('name','Zakaz[parameters][pantonFace]['+i+']');
                tmp.children(':nth-child(3)').children('input:first').attr('name','Zakaz[parameters][pantonBack]['+i+']');
                tmp.removeAttr('style');
                tmp.insertBefore(tbody.children(':last'));
            }
        }else{
            if (cnt<tbody.children().length-4){
                for (var i=tbody.children().length-3;i>cnt;i--){
                    tbody.children(':last').prev().remove();
                }
            }
        }
        if (tbody.children().length-3){
            //tbody.children(':nth-child(3)').removeAttr('style');
            tbody.children(':nth-child(2)').children(':last').attr('rowspan',tbody.children().length-4+2);
        }else{
            //tbody.children(':nth-child(3)').css('display','none');
            tbody.children(':nth-child(2)').children(':last').removeAttr('rowspan');
        }
        console.debug('colorsTypeChange:cnt',cnt);
        
    };
    Obj['paymentMethodClick']=function(e){
        var vl=parseInt($(e).children('a:first').attr('value'));
        console.debug('paymentmethodClick',vl);
        if (!vl){
            $('#zakaz-accountnumber').removeAttr('disabled');
        }else{
            $('#zakaz-accountnumber').attr('disabled',true).val('');
        }
    };
    Obj['profitCalculationTotal']=function(){
        var summ=0;
        var prSumm=0;
        var supPrSumm=0;
        var paymentsSumm=0;
        $.each($('[role=zaryad]'),function(id,el){
            var vl=parseFloat($(el).val());
            var paymentsVl=parseFloat($(el).parent().parent().next().children('div:first').children('input:first').val());
            var prVl=parseFloat($(el).parent().parent().next().next().children('div:first').children('input:first').val());
            var sprVl=parseFloat($(el).parent().parent().next().next().next().children('div:first').children('input:first').val());;
            console.debug('profitCalculationTotal:vl',vl);
            console.debug('profitCalculationTotal:prVl',prVl);
            console.debug('profitCalculationTotal:sprVl',sprVl);
            if (!isNaN(vl))
                summ+=vl;
            if (!isNaN(prVl))
                prSumm+=prVl;
            if (!isNaN(sprVl))
                ;//supPrSumm+=sprVl;
            if (!isNaN(paymentsVl))
                paymentsSumm+=paymentsVl;
        });
        $('#execCoast_summ').val(summ);
        $('#execCoast_profit').val(prSumm);
        $('#execCoast_superprofit').val(supPrSumm);
        $('#execCoast_payments').val(paymentsSumm);
        Obj.totalSumm();
    };
    Obj['profitCalculation']=function(e){
        console.log(e);
        var first;
        if ($(this).attr('role')==='zaryad'){
            first=$(this);
        }else{
            first=$(this).parent().parent().prev().children('div:first').children('input:first');
        }
        var zar=parseInt(first.val());
        var vip=parseInt(first.parent().parent().next().children('div:first').children('input:first').val());
        var prof=first.parent().parent().next().next().children('div:first').children('input:first');
        var supProf=first.parent().parent().next().next().next().children('div:first').children('input:first');
        //var
        var p1=zar-vip;
        var procent=0.25;
        prof.val(p1);
        //supProf.val(p1);

//        if (p1<=vip*procent){
//            prof.val(p1);
//            supProf.val('0');
//        }else{
//            prof.val(vip*procent);
//            supProf.val(p1-vip*procent);
//        }
        Obj.profitCalculationTotal();
        console.debug('profitCalculation',zar);
        console.debug('profitCalculation',vip);
    };
    Obj['totalSumm']=function(){
        var summ=0;
        $.each($('[role=totalCoast]'),function(id,el){
            var vl=parseInt($(el).val());
            if (!isNaN(vl)) summ+=vl;
        });
        $('#zakaz-totalcost').val(summ);
    };
    /*******************Инициализация**********************/
    
    $('#faceType').change(Obj.colorsTypeChange);
    $('#backType').change(Obj.colorsTypeChange);
    if (form){
        form.on('afterValidate',function(e){
            $.each($('#z-main .has-error'),function(id,el){
                console.debug('ZakazController',el);
                $('#zakaz-nav [href $= z-main]').tab('show');
                //$(el).focus();
                return false;
            });
        });
    }
    return Obj;//OtgruzkiController(Obj);
};

