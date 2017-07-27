/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var modalName='';

function getDialog(e){
    var top=$(e).parent().parent().parent().parent();
    var g=/\[(.+)\]/;
    var text=top.attr('id');
    var dialog=$('#'+modalName+'_'+text.match(g)[1]);
    return {
        dialog:{
            main:dialog,
            header:dialog.children().children().children().children('h2:first'),
            input:dialog.children().children().children('[class=modal-body]:first').children('input:first')
        },
        attr:text,
        top:top,
        a:$(e).parent().parent().children('a:first')
    };
}
var materialSelClick=function(el,e){
    console.log(el);
   // e.preventDefault();
};
var materialSelClickZakaz=function(el,e){
    console.log(el);
    e.preventDefault();
};
var materialChange=function(e){
    console.log('materialChange: '+modalName);
    var tmp=getDialog(e);
    console.log(tmp);
    tmp.dialog.header.text('Изменить');
    tmp.dialog.input.val(tmp.a.text());
    tmp.dialog.main.modal('show');
};
var materialRemove=function(){
    
};

function materialInit(mN){
    modalName=mN;
    $('[role=dialogCancel]').click(function(){
        $(this).parent().parent().parent().parent().parent().modal('hide');
    });
    $('[role=dialogSave]').click(function(){
        //$(this).parent().parent().parent().parent().parent().modal('hide');
        var top=$(this).parent().parent().parent().parent().parent().parent();
        var div=top.children('div:first');
        console.log(div);
        console.log(window.location);
        var dop=$.param({
            update:{
                attr:'val',
                index:1,
                value:'123'
            }
        });
        console.debug(dop);
        var rep=window.location.protocol+'//'+window.location.host+window.location.pathname+window.location.search+'&'+dop+window.location.hash;
        console.debug(rep);
        window.location.replace(rep);

    });
};