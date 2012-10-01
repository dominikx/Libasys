var OwnWindoo = new Class({

	Implements: [Events, Options],

	options: {
			width:'300px',
			height:'400px',
			updateField:'updateArea',
			modal:false,
			draggMe:true,
			resizeMinMax:true,
			limitContainer:null,
			resizeMe:true,
			stayalive:true,
			stayaliveDelay:2000,
			resizeLimit:{y: [100, 500],x:[200,800]},
			position:'center',
			animationIn:{duration:1000,transition:Fx.Transitions.Elastic.easeIn},
			animationOut:{duration:5000,transition:Fx.Transitions.Elastic.easeOut},
			title:'',
			text:'',
			elemPos:null,
			desktopUse:false,
			desktopBar:false,
			className: 'myWin',
			ajaxAktiv:false,
			ajaxFile:null,
			ajaxData:null,
			onAjaxComplete: function(){},
			onReadyLoad:function(){}
    },

	initialize: function(element, options){
	    this.setOptions(options);
        
	    //Positioning
		if($(element)==null) this.element=document.body;
		else this.element = $(element);
		//if(this.options.limitContainer==null) this.options.limitContainer=this.element;
		this.WindooClass=this.options.className;

		this.WindooMain = new Element('div',{'class':this.WindooClass+'-WinMain','morph':{duration: 1000, transition: this.options.transitionEffect},'events':{'mousedown':this._zIndexSwitch.bind(this)},'styles':{'visibility':'hidden','width':this.options.width,'height':this.options.height}}).inject(document.body);
		this.WindooMain.uid=Date.now();
		
        this._modalActivitiesStart();
        
  	    this.WindooHead =new Element('div',{'class':this.WindooClass+'-WinHeader','html':this.options.title}).inject(this.WindooMain);
  	    
  	    if(this.options.draggMe)    {
  	    	this.DragAreaElement=new Element('div',{'class':this.WindooClass+'-WinDragTool','events':{'click':this._makeDragable.bind(this)}}).inject(this.WindooHead);
  	    	//Fix for strange Issue
  	    	this._makeDragable.call(this);
  	    }
  	    if(this.options.desktopUse) {
  	    	this.MakeLilElement=new Element('div',{'class':this.WindooClass+'-WinMakeLil','events':{'click':this._makeLil.bind(this)}}).inject(this.WindooHead);
  	    	this.DesktopBarElement=$(this.options.desktopBar);
  	    } 
  	    if(this.options.resizeMinMax)  this.ResizeMinMaxElement=new Element('div',{'class':this.WindooClass+'-WinMinMax','events':{'click':this._resizeWindooMinMax.bind(this)}}).inject(this.WindooHead);
  	    if(this.options.stayalive)  this.CloseElement=new Element('div',{'class':this.WindooClass+'-WinClose','html':'X','events':{'click':this._makeClose.bind(this)}}).inject(this.WindooHead);
  	    this.WindooBody =new Element('div',{'id':this.options.updateField,'class':this.WindooClass+'-WinBody'}).inject(this.WindooMain);
  	    this.WindooFooter =new Element('div',{'class':this.WindooClass+'-WinFooter'}).inject(this.WindooMain);
  	    if(this.options.resizeMe) this.ResizeElement=new Element('div',{'class':this.WindooClass+'-WinResize','events':{'click':this._makeResizable.bind(this)}}).inject(this.WindooFooter); 
	    this._reCalcBodySize(this.options.height);
 	},
 	getWinUid:function(){
 		return this.WindooMain.uid;
 	},
	_makeDragable:function(){
		var myDrag = new Drag.Move(this.WindooMain,{handle:this.DragAreaElement,container:this.options.limitContainer});	
	},
	
    _makeResizable:function(){
		
		this.cloneDiv=new Element('div',{'class':this.WindooClass+'-resizeDiv','html':'<br />Resize Me','styles':{'opacity':0.8}}).setStyles(this.WindooMain.getCoordinates()).inject(document.body);
        this.WindooMain.setStyles({'opacity':'0.8'});
        this.cloneDiv.makeResizable({
	        	  limit: this.options.resizeLimit,
	          	  onComplete:function(){
                  	var savedCoordClone=this.cloneDiv.getCoordinates();
                  	this.WindooMain.setStyles({'width':savedCoordClone.width,'height':savedCoordClone.height,'opacity':'1'});
                    this._reCalcBodySize(savedCoordClone.height);	
    	          	this.cloneDiv.destroy();
               }.bind(this)
        });
	},
	
	_reCalcBodySize:function(SavedHeight){
		var MinusCalc=(2+this.WindooHead.getCoordinates().height.toInt()+this.WindooFooter.getCoordinates().height.toInt());
		    this.WindooBody.setStyles({'height':(SavedHeight-MinusCalc)+'px'});
	},
	
    _makeLil:function(){
		this._ElementClose();
		this.LilWindow.addClass(this.WindooClass+'-onBackground');
		if(this.LilWindow.hasClass(this.WindooClass+'-aktivLilWindow')) this.LilWindow.removeClass(this.WindooClass+'-aktivLilWindow');
		if(this.WindooMain.hasClass(this.WindooClass+'-aktivWindow')) this.WindooMain.removeClass(this.WindooClass+'-aktivWindow');
		this._modalActivitiesEnd();
	},
	
	_makeBig:function(){
		this._ElementOpen();
		this.LilWindow.removeClass(this.WindooClass+'-onBackground');
		this._desktopUse();
		$$('div.'+this.WindooClass+'-WinMain').removeClass(this.WindooClass+'-aktivWindow');
		
		this.WindooMain.addClass(this.WindooClass+'-aktivWindow');	
		this._modalActivitiesStart();
	},
	
	_resizeWindooMinMax:function(){
		if(!this.ResizeMinMaxElement.hasClass(this.WindooClass+'-maxAktiv')){
			this._prepareMinMax(this.options.resizeLimit['x'][1], this.options.resizeLimit['y'][1]);
			this.ResizeMinMaxElement.addClass(this.WindooClass+'-maxAktiv');
		}else{
			this._prepareMinMax(this.options.resizeLimit['x'][0], this.options.resizeLimit['y'][0]);
			this.ResizeMinMaxElement.removeClass(this.WindooClass+'-maxAktiv');
		}
	},
	
	_prepareMinMax:function(X,Y){
		//this.WindooMain.setStyles({'clip':'rect(auto,auto,auto,auto)'});
		this.WindooMain.get('morph').start({'opacity':1,'width':X,'height':Y});
		this._reCalcBodySize(Y);
	},
	
   _makeClose:function(){
		this.end();
	},
	
	_modalActivitiesEnd:function(){
		if(this.options.modal==true){
		      this.divBG.destroy();
			  if(Browser.ie) this.iframeBG.destroy();
		}
	},
	
	_modalActivitiesStart:function(){
		if(this.options.modal==true){
			if(Browser.ie) {
				this.iframeBG= new Element('iframe',{'class':this.WindooClass+'-DivBg','styles':{'height':Window.getScrollHeight()+'px','opacity':0.1}}).inject(this.WindooMain,'before');
			}
			this.divBG= new Element('div',{'class':this.WindooClass+'-DivBg','styles':{'height':Window.getScrollHeight()+'px','opacity':0.7}}).inject(this.WindooMain,'before');
		}
	},
	
    end: function(){
		var rect = this._rect();
		this.WindooMain.get('morph').start({
			'opacity':0,
			'clip': [[rect.top, rect.right+2, rect.bottom+2, rect.left],[(rect.top-rect.height)/-2,0, 0, (rect.left-rect.width)/-2]] 
		}).wait(500).chain(function(){
			if(this.options.desktopUse) this.LilWindow.destroy();
			this.WindooMain.destroy();
			this._modalActivitiesEnd();
		}.bind(this));
	},
	
	_zIndexSwitch:function(){
		$$('div.'+this.WindooClass+'-WinMain').removeClass(this.WindooClass+'-aktivWindow');
		this.WindooMain.addClass(this.WindooClass+'-aktivWindow');	
		if(this.options.desktopUse) this._desktopUse();
	},
	
	lilWindowSwitch:function(){
 		if(this.LilWindow.hasClass(this.WindooClass+'-onBackground')) this._makeBig();
		else this._makeLil();
	},
	
	createLilWindow:function(){
        this.LilWindow=new Element('div',{'class':this.WindooClass+'-WinLil','html':this.options.title,'events':{'click':this.lilWindowSwitch.bind(this)}}).inject(this.DesktopBarElement);
    },

    _desktopUse:function(){
    	$$('div.'+this.WindooClass+'-WinLil').removeClass(this.WindooClass+'-aktivLilWindow');
    	this.LilWindow.addClass(this.WindooClass+'-aktivLilWindow');
    },

    _centerElementOpenBegin:function(){
    	var rect = this._rect();
    	var top=((window.getSize().y/2)-(this.options.height.toInt() / 2));
    	
    	var left=((window.getSize().x/2)-(this.options.width.toInt() / 2));
    	
    	if(this.options.elemPos!=null){
    		top=this.options.elemPos.getCoordinates().top.toInt();
    	}
 		this.WindooMain.get('morph').start({
			'opacity':1,
			'top':[top,top],
			'left':[((window.getSize().x/2)-(this.options.width.toInt() / 2)),left],
			'clip': [[(rect.top-rect.height)/-2,0, 0, (rect.left-rect.width)/-2], [rect.top, rect.right+2, rect.bottom+2, rect.left]] 
		}).chain(function(){
			this._zIndexSwitch();
			this.WindooMain.setStyle('clip','');
			if(this.options.desktopUse) this._desktopUse();
			this.fireEvent('onReadyLoad',this.options.onReadyLoad);
		}.bind(this));
    },

    _ElementOpen:function(){
    	
    	var rect = this._rect();
		this.WindooMain.get('morph').start({
			'opacity':1,
			'clip': [[(rect.top-rect.height)/-2,0, 0, (rect.left-rect.width)/-2], [rect.top, rect.right+2, rect.bottom+2, rect.left]] 
		}).chain(function(){
			this.WindooMain.setStyle('clip','');
		});
    },
    
    _ElementClose:function(){
    	var rect = this._rect();
		this.WindooMain.get('morph').start({
			'opacity':0,
			'clip': [[rect.top, rect.right+2, rect.bottom+2, rect.left],[(rect.top-rect.height)/-2,0, 0, (rect.left-rect.width)/-2]] 
		}).chain(function(){
			this.WindooMain.setStyle('clip','');
		});
    },
    
    _rect: function(){
		var rect = this.WindooMain.getCoordinates();
		rect.right = (rect.right > window.getSize().x) ? window.getSize().x - rect.left : rect.width;
		rect.bottom = (rect.bottom >window.getSize().y) ? window.getSize().y - rect.top : rect.height;
		rect.top = (rect.top < 0) ? Math.abs(rect.top) : 0;
		rect.left = (rect.left < 0) ? Math.abs(rect.left) : 0;
		return rect;		
	},
	
    position: function(){
        if(this.options.position=='center'){
        	this._centerElementOpenBegin();
			this._reCalcBodySize(this.WindooMain.getCoordinates().height.toInt());
        }
	},
   
	show: function(){
		if(this.options.ajaxAktiv) this._ajaxload();
		else this.WindooBody.set('html', this.options.text);
		if(this.options.desktopUse) this.createLilWindow();
		this.position();
		if(this.options.stayalive==false){
			this._destroyWindow.delay(this.options.stayaliveDelay,this);
		}
	},
	_destroyWindow:function(){
		this.WindooMain.destroy();
	},
    
	setText:function(sTEXT){
		this.options.text=sTEXT;
	},

    _ajaxload: function(){
	 	new Request.HTML({url:this.options.ajaxFile, method:'post',data:this.options.ajaxData,update:this.WindooBody,evalScripts:true,
	 		onRequest:function(){
	 		this.WindooBody.set('html','<br /><br />LOADING ...');
	 	    }.bind(this),
			onFailure:function(){
				this.WindooBody.set('html','Content konnte nicht geladen werden');
			}.bind(this),
			onComplete:function(){
			this.fireEvent('onAjaxComplete',this.options.onAjaxComplete);
			}.bind(this)}).send();
	}

});