/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var DialogController=function(_opt){
    this.main={};
    this.status=false;
    this.isOpen=false;
    function dCreateObj(optName,__opt,defClass,defTag){
        var opt;
        var rVal=null;
        var text=false;
        if (!__opt) __opt={};
        if (optName){
            if ($.type(__opt[optName])==='bolean'){
                if (__opt[optName]===false) return rVal;
            }
            if ($.type(__opt[optName])==='object'){
                opt=__opt[optName];
            }else{
                opt={};
                text=__opt[optName];
            }
        }else
            opt=__opt;
        if (opt){
            if (!opt['tag']){
                if (!defTag) 
                    opt['tag']='div';
                else
                    opt['tag']=defTag;
            }
        }else{
            opt={tag:'div'};
        }
        rVal=$.fn.createObj(opt);
        if (defClass) $.each(defClass,function(id,el){
            if (!rVal.hasClass(el)) rVal.addClass(el);
        });
        if (text) rVal.text(text);
        return rVal;
    }
    function createButton(name,opt,defOpt){
        console.debug('createButton:name',name);
        console.debug('createButton:opt',opt);
        console.debug('createButton:defOpt',defOpt);
        if ($.type(defOpt)!=='object') defOpt={};
        var rVal;
        switch ($.type(opt)){
            case 'object':
                if ($.type(opt['options'])==='object'){
                    opt['options']=$.extend(opt['options'],defOpt)
                }else{
                    opt['options']=defOpt;
                }
                rVal=dCreateObj(null,opt,['btn'],'button')
                if ($.isFunction(opt.click)) rVal.click({dialog:this},opt.click);
                break;
            case 'string':
                console.debug('createButton:string',$.extend(defOpt,{text:opt}));
                rVal=dCreateObj(null,$.extend({text:opt,options:{}},{options:defOpt}),['btn'],'button');
                //if ($.isFunction(defClick)) rVal.click({self:this},defClick);
                break;
            case 'boolean':
                console.debug('createButton:boolean',$.extend(defOpt,{text:name}));
                if (opt) rVal=dCreateObj(null,$.extend({text:name,options:{}},{options:defOpt}),['btn'],'button');
                break;
        }
        console.debug('createButton:rVal',rVal);
        return rVal;
    }
    function createFooter(__opt,self){
        console.log(self);
        var rVal=dCreateObj('footer',__opt,['modal-footer']);
        if ($.type(__opt)==='object'&&rVal!==null){
            if ($.type(__opt['buttons'])!=='object'){
                if (__opt['buttons']!==false)
                    __opt['buttons']={ok:'Ок',cansel:'Отмена'};
                else
                    __opt['buttons']={};
            }
            if ($.type(__opt['buttons'])==='object'){
                $.each(__opt['buttons'],function(id,el){
//                    if ($.type(el)==='object'){
//                        rVal.append(createButton(id,el));
//                    }else{
                        switch (id){
                            case "ok":
                                var tmp=createButton(id,el,self.okClick);
                                rVal.append(tmp);
                                tmp.click({self:self},function(e){
                                    e.data.self.okClick(e);
                                });
                                break;
                            case "cansel":
                                var tmp=createButton(id,el,{'data-dismiss':'modal'});
                                rVal.append(tmp);
                                tmp.click({self:self},function(e){
                                    e.data.self.canselClick(e);
                                });
                                break;
                        }
//                    }
                });
            }
        }
        return rVal;
    };
    this.init=function(opt){
        //console.log(this);
        if (!_opt) _opt={};
        this.main=dCreateObj(null,_opt['options'],['modal','fade']);
        if (opt.footer!==false) this.footer=createFooter(opt,this);//dCreateObj('footer',opt,['modal-footer']);
        var content=dCreateObj(null,false,['modal-content']);
        var dialog=dCreateObj('dialog',_opt,['modal-dialog']);
        if (opt.size){
            dialog.addClass('modal-'+opt.size);
        }
        if (opt.header!==false){
            var close=$.fn.createObj({
                tag:'button',
                options:{
                    class:'close',
                    'data-dismiss':'modal',
                    'aria-hidden':true
                },
                html:'&times;'
            });
            close.click({self:this},this.closeClick);
            var htitle=dCreateObj('header',opt,['modal-title']);
            this.header=dCreateObj(null,false,['modal-header'],'h4');
            this.header.append(close);
            this.header.append(htitle);
            content.append(this.header);
        }
        if (opt.body!==false){
            this.body=dCreateObj('body',opt,['modal-body']);
            content.append(this.body);
        }
        if (opt.footer!==false) content.append(this.footer);
        //dialog.append(content);
        dialog.append(content);
        this.main.append(dialog);
        $('body').append(this.main);
        this.options=opt;
        if ($.type(this.options.closeOnOk)!=='boolean') this.options.closeOnOk=true;
        if ($.type(this.options.destroyAfterHide)!=='boolean') this.options.destroyAfterHide=true;
        this.main.bind('hide.bs.modal',{self:this},this.beforeClose);
        this.main.bind('hidden.bs.modal',{self:this},this.afterHide);
        
    };
    this.processUserFunction=function(event,v1){
        if ($.isFunction(v1)){
            v1(event);
        }else{
            if ($.type(v1)==='array'){
                if ($.type(v1[0])==='object') event.data=$.extend(event.data,v1[0]);
                if ($.isFunction(v1[1])) v1[1](event);
            }
        }
        
    };
    this.okClick=function(event){
        console.debug('DialogController:okClick');
        var self=event.data.self;
        self.status='ok';
        if (self.options.okClick) self.processUserFunction(event,self.options.okClick);
        if (self.options.closeOnOk) self.main.modal('hide');
    };
    this.canselClick=function(event){
        console.debug('DialogController:canselClick');
        var self=event.data.self;
        self.status='cansel';
        if (self.options.canselClick) self.processUserFunction(event,self.options.canselClick);
    };
    this.closeClick=function(event){
        var self=event.data.self;
        if (self.options.closeClick) self.processUserFunction(event,self.options.closeClick);
        self.canselClick(event);
    };
    this.beforeClose=function(event){
        var self=event.data.self;
        if (!self.status){
            event.preventDefault();
            return false;
        }
        if (self.options.beforeClose) self.processUserFunction(event,self.options.beforeClose);
    };
    this.show=function(){
        if (!this.isOpen){
            this.status=false;
            this.main.modal({show:true});
            this.isOpen=true;
        }
    };
    this.hide=function(){
        if (this.isOpen){
            this.status='close';
            this.main.modal('hide');
            this.isOpen=false;
        }
    };
    this.afterHide=function(event){
        var self=event.data.self;
        if (self.options.destroyAfterHide){
            self.main.remove();
            delete self;
        }
    };
    this.init(_opt);
    console.debug('DialogController',this);
    return this;
};


