/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var ZakazMaterialController=function(urlUpd,totalSumm,toLoad){
    var rVal={
        totalSumm:totalSumm,
        ajaxupdaterequest:urlUpd,
        selsupplier:{},
        tblName:false,
        initEl:function(el,dopParam){
            if (!$.isPlainObject(el))
                var ulId=$(el).attr('id');
            else
                var ulId=el.attr('id');
            var opt={
                menuId:ulId,
                labelId:'lb_'+ulId,
                prevDef:true,
                likeParam:true,
                formControlID:'materialDetails-'+ulId,
                iSTIfNotFound:true,
                formId:'zakaz-form'
            };
            if (opt.menuId.indexOf('supplier')>-1){
                opt.exactValue=true;
            }
            if (dopParam) opt['likeOtherParam']=dopParam;
            if (ulId.indexOf('selSupplier')!==-1){
                opt['exactValue']=true;
            }
            if (rVal[ulId]) delete rVal[ulId];
            //paperName1
            if (ulId.substr(0,ulId.length-1)==='pcolors')
                rVal[ulId]=new ActiveDropDown(opt,null,null,null,null,null,null,rVal.dopListParam);
            else if (ulId.substr(0,ulId.length-1)==='paperName')
                rVal[ulId]=new ActiveDropDown(opt,null,null,rVal.nameClick);
            else
                rVal[ulId]=new ActiveDropDown(opt);
            console.debug('ZakazMaterialController:initEl',ulId,opt);
//                    console.log(el);

            //}            
        },
        nameClick:function(el1){
            var name='pcolors'+$(el1).parent().parent().parent().parent().parent().attr('papernum');
            console.debug('ZakazMaterialController:nameClick',name);
            if ($.type(rVal[name]==='object'))
                rVal[name].likeDefault(el1); //Загрузит стандартный список dropDownLike
        },
        dopListParam:function(el1){
            var rVal={};
            var num=$(this).parent().parent().parent().parent().parent().attr('papernum');
            var val=$('#materialDetails-paperName'+num);
            if (val.length){
                val=val.val();
                var requestVal=null;
                $.each($(this).parent().children('li'),function(id,el){
                    var a=$(el).children('a:first-child');
                    if ($(a).attr('value')==val){
                        requestVal=$(a).text();
                        return false;
                    }
                });
                console.debug('ZakazMaterialController:dopListParam.requestVal',requestVal);
                if (requestVal){
                    rVal.pcolor_description=requestVal;
                }
            }
            return rVal;
        }
    };
    rVal['supplierTypeClick']=function(e){
            var lEl=$(this).parent().parent().parent().parent().parent().children().last();
            var num=$(this).parent().parent().parent().parent().parent().attr('papernum');
            console.debug('paperName-supplierType',$(this).parent().parent().parent().parent().parent());
            var ppc=$('[id*=priceppc'+num+']');
            if (!ppc.length) ppc=$('[id*=priceppc]');
            var pricem2=$('[id*=pricem2]');
            if ($(this).val()==='2'){
                  ppc.attr('disabled',false);
                  if (pricem2.length) pricem2.attr('disabled',false);
            }else{
                  rVal.paperSumm();
                  ppc.attr('disabled',true);
                  if (pricem2.length){
                      pricem2.attr('disabled',true);
                      pricem2.val('0.00');
                  }
                  lEl.children('div:last').children('input:first').val('0.00');
                  ppc.val('0.00');
                  rVal.paperSumm();
            }
        
    }
    rVal['selsupplierInit']=function(){
//            if (html) $('#sMaterial').replaceWith(html);
        $.each($('[id^=selSupplier]'),function(id,el){
            var eId=$(el).attr('id');
            if (rVal.selsupplier[eId]) delete rVal.selsupplier[eId];
        });
        $('#addPaper').click(rVal.addPaperClick);
        $('#removePaper').click(rVal.removePaperClick);
        $('[id^=paperName-supplierType]').children().children().click(rVal.supplierTypeClick);
        $('[id^=paperName-priceppc]').keyup(rVal.priceppc_change);
        $('[id^=paperName-priceppc]').keydown(function(e){
            var key=e.key.charCodeAt(0);
             console.debug('keydown',key);
            if ((key<48||key>57)&&key!=46&&key!=66&&key!=84){
                e.preventDefault();
            }
            if (key==84&&!$(this).val().length)
                e.preventDefault();
        });
        $('[id^=paperName-pricem2]').keyup(rVal.pricem2_change);
        $('[id^=paperName-pricem2]').keydown(function(e){
            var key=e.key.charCodeAt(0);
             console.debug('keydown',key);
            if ((key<48||key>57)&&key!=46&&key!=66&&key!=84){
                e.preventDefault();
            }
            if (key==84&&!$(this).val().length)
                e.preventDefault();
        });
        $('[id^=paperName-count]').keyup(rVal.count_change);
    };
    rVal['paperSumm']=function(){
        var summ=0;
        $.each($('[id^=paperName-count]'),function(id,el){
            var cnt=0;
            if ($(el).val()) cnt=parseInt($(el).val());
            var summTmp=$(el).parent().parent().next().next().children('div:last').children('input:first');
            var tmp=cnt*parseFloat($(el).parent().parent().next().children('div:last').children('input:first').val());
            console.debug('paperSumm',summTmp);
            if (!isNaN(tmp)){
                //summTmp.attr('value',tmp.toFixed(2));
                summTmp.val(tmp.toFixed(2));
                //console.log($(el).parent().parent().next().next());//has-error
                summ+=tmp;
            }else{
                summTmp.val(0);
            }
            console.debug('paperSumm','cnt: '+cnt+' tmp: '+tmp);
        });
        $('#materialDetails_summ').val(summ.toFixed(2));
        //Zakaz.totalSumm();

    };
    rVal['pricem2_change']=function(e){
        rVal.paperSumm();
        var pricem2=$('[id*=priceppc]');
        if (pricem2.length){
            if ($(this).val()!=0||!$(this).val()){
                if (!pricem2.attr('disabled')){
                    pricem2.attr('disabled',true);
                    pricem2.val('0.00');
                }
            }else{
                pricem2.attr('disabled',false);
            }
        }
        if (isNaN(parseFloat($(this).val()))){
            $(this).parent().parent().addClass('has-error');
        }else{
            $(this).parent().parent().removeClass('has-error');
        }
    };        
    rVal['priceppc_change']=function(e){
        rVal.paperSumm();
        var pricem2=$('[id*=pricem2]');
        if (pricem2.length){
            if ($(this).val()!=0||!$(this).val()){
                if (!pricem2.attr('disabled')){
                    pricem2.attr('disabled',true);
                    pricem2.val('0.00');
                }
            }else{
                pricem2.attr('disabled',false);
            }
        }
        if (isNaN(parseFloat($(this).val()))){
            $(this).parent().parent().addClass('has-error');
        }else{
            $(this).parent().parent().removeClass('has-error');
        }
    };
    rVal['count_change']=function(e){
        rVal.paperSumm();
        if (isNaN(parseFloat($(this).val()))){
            $(this).parent().parent().addClass('has-error');
        }else{
            $(this).parent().parent().removeClass('has-error');
        }
    };
    rVal['reinit']=function(dopParam){
        $.each($('#sMaterial').find('.dropdown-menu'),function(id,el){
            rVal.initEl(el,dopParam);
        });
        $('[data-remove]').unbind('click');
        $('[data-remove]').click(function(e){
            console.log($(this).attr('data-remove'));
            var el=this;
            var fst=$(this).attr('data-remove');
            if ($.type(fst)=='undefined') return;
            var cont="Вы собираетесь сбросить дату "+$(this).attr('data-text')+"<br>";
            cont+="Продолжить ?";
            var temp=new DialogController({
                header:'ВНИМАНИЕ !',
                body:{html:cont},
                //okClick:[this.dialogOkClick],
                buttons:{
                    ok:{
                        text:'Да',
                        click:
                            function(e){
                                console.log($(el).parent().parent());
                                $(el).parent().parent().remove();
                                $(fst).val(null);
                            },
                    },
                    cansel:{
                        text:'Нет',
                    }
                }
            });
            temp.show();
        });
    };
    rVal['matSel']=function(tblName,callBack,values){
        if (rVal.ajaxupdaterequest){
            var data={
                ajax:true,
                rq:'material',
                tblName:tblName
            };
            if (values) data.values=values;
            console.debug('matSel:data',data);
            $.fn.addBusy('sMaterial');
            $.ajax({url:rVal.ajaxupdaterequest,type:'post',data:data}).done(function(data){
                console.debug('matSel:ajax:answer',data);
                if (data['status']==='ok'){
                    rVal.tblName=tblName;
                   // $('#sMaterial').replaceWith(data['html']);
                    if (data['html']) $('#sMaterial').replaceWith(data['html']);
                    rVal.selsupplierInit();
                    console.debug('matSel:ajax:tblName',tblName);
//                    var dopParam={tblName:tblName};
                    rVal.reinit({tblName:tblName});
                    $('#materialDetails').val(tblName);
                    $.fn.removeBusy();
                    if ($.isFunction(callBack)) callBack();
                }
            });
        }

    };
    rVal['matSelClick']=function(el,e){
        var tblName=$(el).children('a:first').attr('value');
        console.debug('matSelClick',tblName);
//        console.log(rVal.ajaxupdaterequest);
        e.preventDefault();
        if (tblName!=='clear')
            rVal.matSel(tblName);
        else{
            if ($('#sMaterial').length){
                $('#sMaterial').children().remove();
            }
        }
    };
    rVal['removePaperClick']=function(e){
        e.preventDefault();
        $(this).parent().prev().remove();
        var cnt=$(this).parent().parent().children().length;
        console.debug('removePaperClick','removePaper: '+cnt);
        if (cnt<3){
            $(this).remove();
            rVal.paperSumm();
        }
        if (cnt===4){
            var span=$('<span>',{
                class:'glyphicon glyphicon-plus-sign'
            });
            var butt=$('<button>',{
                id:'addPaper',
                class:'btn btn-success btn-sm',
                title:'Добавить'
            });
            butt.append(span);
            butt.click(rVal.addPaperClick);
            console.debug('removePaperClick',butt);
            $(this).parent().prepend(butt);
        }
    };
    rVal['addPaper']=function(mainPar,thisPar,callBack,values){
         console.debug('addPaper',mainPar);
        var paperNum=$(mainPar.children('div:nth-child('+(mainPar.children().length-1)+')')).attr('papernum');
        var nPaperNum=paperNum+++1;
        if (thisPar) thisPar.remove();
        var data={
            ajax:true,
            rq:'material',
            tblName:rVal.tblName,
            paperNum:nPaperNum,
            isSecond:true
        };
        if (values) data.values=values;
        $.fn.addBusy('sMaterial');
        $('#sMaterial').append(tmpInfo);
        $.ajax({url:rVal.ajaxupdaterequest,type:'post',data:data}).done(function(data){
                console.debug('addPaper]:ajax',data);
                if (data['status']==='ok'){
                    
                    if (data['html']){
                        console.debug('addPaper:ajax',$(data['html']));
                        $.fn.removeBusy();
                        $.each($(data['html']).children(),function(id,el){
                            $('#sMaterial').append($(el));
                        });
                        rVal.selsupplierInit();
                        rVal.reinit({tblName:rVal.tblName,paperNum:nPaperNum});
                        if ($.isFunction(callBack)) callBack();
                    }
                }
            });
    };
    rVal['addPaperClick']=function(e){
        e.preventDefault();
        var mainPar=$(this).parent().parent();
        console.debug('addPaperClick',mainPar);
        rVal.addPaper(mainPar,$(this).parent());
    };
    rVal['onSubmit']=function(e){
        var hasErr=false;
        $.each($('#sMaterial').find('[type=text]'),function(id,el){
            console.debug('onSubmit',el);
            hasErr=$(el).val().length===0;
            if (hasErr){
                $(el).parent().parent().addClass('has-error');
            }else{
                $(el).parent().parent().removeClass('has-error');
            }
            return !hasErr;
        });
        if (hasErr){
            e.preventDefault();
            console.debug('onSubmit',$('#zakaz-nav [href $= specification]'));//.find('[href~=specification]'));
            $('#zakaz-nav [href $= specification]').tab('show');
        }
        //e.preventDefault();
    };
    rVal['renderLoadedNextEl']=function(i,callBackAtEnd){
        if (i<toLoad.length){
            console.debug('renderLoadedEl2',$('#sMaterial').children('div:last'));
            rVal.addPaper($('#sMaterial'),$('#sMaterial').children('div:last'),function(){
                rVal.renderLoadedNextEl(i+1,callBackAtEnd);
            },toLoad[i]);
        }else{
            if ($.isFunction(callBackAtEnd)) callBackAtEnd();
        }
    };
    rVal['loadToEdit']=function(){
        //console.debug('zakazMaterialController:loadToEdit',)
        if (toLoad.length>1){
            console.debug('loadToEdit',toLoad);
            rVal.tblName=toLoad[0];
            rVal.matSel(rVal.tblName,function(){
                rVal.renderLoadedNextEl(2,function(){
                    console.debug('loadToEdit','end');
                });
            },toLoad[1]);
            $.fn.activeDDSetVal('selMatMenu_DD',rVal.tblName);
        }
    };
    /*************Инициализация*************/
    $('#materialDetails_summKomerc').change(function(e){
        if ($.isFunction(rVal.totalSumm)) rVal.totalSumm();
    });
    $('[type=submit]').click(rVal.onSubmit);
    if ($.isArray(toLoad)) rVal.loadToEdit();
    return rVal;
};