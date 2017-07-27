/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function findFirst(el){
    var i=0;
    while($.type($(el).attr('data-key'))==='undefined'&&i<5){
        el=$(el).prev();
        i++;
    }
    return (el);
}
function initMaterialList(){
    var strip='#f0f0f0';
    $('#listPjax').on('pjax:complete',function(){initMaterialList();});
    $('.materialList>table>tbody>tr').unbind('mouseenter');
    $('.materialList>table>tbody>tr').unbind('mouseleave');
    $('.materialList>table>tbody>tr').mouseenter(function(e){
        var el=findFirst(this);
        var cnt=$(el).children(':first-child').attr('rowspan');
        $(el).css('background-color','#b6daff');
        el=$(el).next();
        for (i=1;i<cnt;i++){
            $(el).css('background-color','#b6daff');
            el=$(el).next();
        }
    });
    $('.materialList>table>tbody>tr').mouseleave(function(e){
        var el=findFirst(this);
        var cnt=$(el).children(':first-child').attr('rowspan');
        $(el).removeAttr('style')
        if ($(el).attr('bcolor')){
            $(el).css('background-color',strip);
        }
        el=$(el).next();
        for (i=1;i<cnt;i++){
            $(el).removeAttr('style');
            if ($(el).attr('bcolor')){
                $(el).css('background-color',strip);
            }
            el=$(el).next();
        }
    });
    var line=0;
    $('.materialList>table>tbody>tr[data-key]').each(function(ind,el){
        line++;
        if (line%2){
            var cnt=$(el).children(':first-child').attr('rowspan');
            $(el).css('background-color',strip);
            $(el).attr('bcolor','true');
            for (var i=0;i<cnt;i++){
                $(el).css('background-color',strip);
                $(el).attr('bcolor','true');
                el=$(el).next();
            }
        }
    });
}
initMaterialList();