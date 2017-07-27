/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var OtgruzkiController=function(){
    parent={};
    parent.timerV=null;
    parent.oplOpt={
            perFix:'Zakaz[shipping][oplata][',
            postFixSumm:'][summ]',
            postFixDate:'][date]',
            srId:'zakaz-totalcost',
            role:'oplata',
            summEndText:' руб.',
            finelMess:'Оплачено полностью.',
            fixed:2,
            firstTD:1
        };
    parent.otgrOpt={
            perFix:'Zakaz[shipping][otgruzka][',
            postFixSumm:'][pcs]',
            postFixDate:'][date]',
            postFixName:'][name]',
            srId:'zakaz-numberofcopies',
            role:'otgruzka',
            summEndText:' шт.',
            finelMess:'Отгружено полностью.',
            fixed:0,
            firstTD:0
        };
    parent.oplataInit=function(){
        $('[role=oplata]').unbind('keyup');
        $('[role=oplata]').unbind('focusout');
        $('[role=oplata]').unbind('focusin');
        $('[role=oplata]').unbind('keydown');
        $('[role=oplata]').keydown(parent.oplataKeyDown);
        $('[role=oplata]').keyup({opt:parent.oplOpt},parent.oplataChange);
        $('[role=oplata]').focusout(parent.oplOpt,parent.oplataFocusOut);
        $('[role=oplata]').focusin({opt:parent.oplOpt},parent.oplataFocusIn);
        
        $('[role=otgruzka]').unbind('keyup');
        $('[role=otgruzka]').unbind('focusout');
        $('[role=otgruzka]').unbind('focusin');
        $('[role=otgruzka]').unbind('keydown');
        $('[role=otgruzka]').keydown(parent.oplataKeyDown);
        $('[role=otgruzka]').keyup({opt:parent.otgrOpt},parent.oplataChange);
        $('[role=otgruzka]').focusout(parent.otgrOpt,parent.oplataFocusOut);
        $('[role=otgruzka]').focusin({opt:parent.otgrOpt},parent.oplataFocusIn);

    };
    parent.oplataKeyDown=function(e){
        var key=e.key.charCodeAt(0);
        if ((key<48||key>57)&&key!=46&&key!=66&&key!=84){
            e.preventDefault();
        }
    };
    parent.oplataFocusOut=function(e){
        var self=$(this);
        var ad=self.attr('aria-describedby');
        var dif=parent.CDiff(e.data.srId,e.data.role);
        if (ad){
            self.popover('hide');
            self.removeAttr('data-original-title');
            self.removeAttr('title');
            self.removeAttr('aria-describedby');
            $('#'+ad).remove();
        }
        if (dif>0 && self.val()&& !self.attr('readonly')){
            var tmp=self.parent().parent().clone();
            var ind=parseInt(self.parent().parent().index())-1;
            var nDate=tmp.children(':eq('+e.data.firstTD+')').children('input:first');
            var nSumm=tmp.children(':eq('+(e.data.firstTD+1)+')').children('input:first');
            nDate.attr('name',e.data.perFix+ind+e.data.postFixDate);
            nSumm.attr('name',e.data.perFix+ind+e.data.postFixSumm);
            if (e.data.postFixName) tmp.children(':eq('+(e.data.firstTD+2)+')').children('input:first').attr('name',e.data.perFix+ind+e.data.postFixName);
            self.parent().parent().after(tmp);
            self.attr('readonly',true);
            nDate.val('');
            nSumm.val('');
            var dtpId=nDate.attr('id')+'_'+ind;
            nDate.attr('id',dtpId);
            nDate.removeClass('hasDatepicker');
            nDate.datepicker({
                minDate:nDate.attr('min-date')
            });
            parent.oplataInit();
        }
    };
    parent.oplataFocusIn=function(e){
        parent.oplataCalculate($(this),e.data.opt);
    };
    parent.CDiff=function(srId,role){
        var dif=0;
        
        var totalcost=this.parseValue($('#'+srId).val());
        if (!isNaN(totalcost)){
            dif=totalcost;
            
            $.each($('[role='+role+']'),function(){
                var val=parseFloat($(this).val());
                console.debug('CDiff',val);
                if (!isNaN(val)){
                    dif-=val;
                }
            });
        }
        return dif;
    };
    parent.parseValue=function(val){
        if ($.type(val)==="string"){
            var tmp=val.split('*');
            if (tmp.length<=1){
                return parseFloat(tmp[0]);
            }else{
                return parseInt(tmp[0])*parseInt(tmp[1]);
            }
        }else if ($.type(val)==="number")
            return val;
        else
            return 0;
    };
    parent.oplataCalculate=function(self,opt){   
        if (parent.timerV) clearTimeout(parent.timerV);
        if (self.attr('readonly')) return 0;
        var totalcost=this.parseValue($('#'+opt.srId).val());
        //console.debug('oplataCalculat',parseInt($('#'+opt.srId).val()));
        var dif=0;
        if (!isNaN(totalcost)){
            dif=parent.CDiff(opt.srId,opt.role);
            var tt={};
            if (dif>0){
//                var a=$('#custNW_paymentT').find('a[value=1]');
//                console.debug('oplataCalculate',a);
//                a.click();
                $('#zakaz-payment').val(1);
                tt.title='Осталось';//+(dif.toFixed(opt.fixed))+opt.summEndText;
                tt.content=(dif.toFixed(opt.fixed))+opt.summEndText;
            }else{
                if (dif<0){
                    dif=0;
                    tt.title='Ошибка'
                    tt.content='Вы ввели больше чем осталось';
                    self.val('');
                    parent.timerV=setTimeout(function(){
                        parent.timerV=null;
                        parent.oplataCalculate(self,opt);
                    },1500);
                }else{
//                    var a=$('#custNW_paymentT').find('a[value=2]');
//                    console.debug('oplataCalculate',a);
//                    a.click();
                    $('#zakaz-payment').val(2);
                    tt.title=opt.finelMess;
                    tt.content='';
                }
            }
            var ad=self.attr('aria-describedby');
            if (ad){
                $('#'+ad).children('.popover-title').text(tt.title);
                $('#'+ad).children('div:last').html(tt.content);
            }else{
                tt.placement='right';
                tt.trigger='manual';
                tt.container='body';
                self.popover(tt);
                console.log(tt);
                ad=self.attr('aria-describedby');
                $('#'+ad).children('div:last').html(tt.content);
                self.popover('show');
                //self.tooltip('toggle');
            }
        }
    };
    parent.oplataChange=function(e){
        parent.oplataCalculate($(this),e.data.opt);
    };
    parent.oplataInit();
    $('#tDtPik').click(function(e){
        e.preventDefault();
        $(this).after('<input type="text" id="tDtPikI">');
        $('#tDtPikI').datepicker();
    });
    return parent;
};