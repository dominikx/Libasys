var AjaxRightContent=null;
var AjaxLeftContent=null;
var AjaxControls=null;
var currentId;

var errorChecker=function(FORMID){
	var bError=false;
	var errorMsg='';
    
	var checkForm=document.id(FORMID);
	
	checkForm.getElements('input[type=text]').each(function(el){
	   el.removeClass('inputError');
	});
	checkForm.getElements('textarea').each(function(el){
	   el.removeClass('inputError');
	});
	
	checkForm.getElements('textarea').each(function(el){
		if(checkForm.getElement('textarea').hasClass('pflicht') && checkForm.getElement('textarea').get('value')==''){
			    bError=true;
				el.addClass('inputError');
		}
	});
	
	checkForm.getElements('input[type=text]').each(function(el){
		if(el.hasClass('pflicht') && el.get('value')==''){
			bError=true;
			el.addClass('inputError');
		}
		 if(el.hasClass('email') && el.get('value')!=''){
			 if(!nur_email(el.get('value'))){
				 errorMsg+='&raquo; Fehlerhafte E-Mail!<br />';
				 el.addClass('inputError');
			 }
		 }
	});
	
	if(bError){
		//document.id('errorOutput').set('class','isError').set('html',errorMsg);
	}
	
	return bError;
}

var nur_integer=function(wort) {
	   var zahl=true;
	        for(n=0;n<wort.length;n++){
	         	if(wort.charAt(n)<"0"||wort.charAt(n)>"9")
	         	 zahl=false;
	         	 break;
	          }

	     return zahl;
	   }

	var nur_email=function(wort){
	   var email=true;
	   if(wort!=''){
		   if(wort.match(/\w*@\w.*\.\w\w*/)==null){
		   email=false;
		   }
	   }

	  return email;
	}
	
var getKunden=function(ID){
	
	var oldId=ID;
	
	
	if(oldId!=currentId){
		if(document.id('kunde_'+currentId) !=null) document.id('kunde_'+currentId).removeClass('active');
	}
	
	AjaxRightContent.send('ajaxaction=1&kundenId='+ID);
	document.id('kunde_'+ID).addClass('active');
	if(myKundenList!=null)  myKundenList.set(document.id('kunde_'+ID));
	currentId=ID;
}

var loadNotiz=function(KUNDENID){
	
	if(!document.id('showNotice').hasClass('isShow')){
		if(document.id('iNotiz').get('html')==''){
			new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',data:'ajaxaction=8&kundenId='+KUNDENID,update:document.id('iNotiz'),evalScripts:true,
				onRequest:function(){
					
				},
				onComplete:function(){
				ScrollerRight();
				//	OC.dialogs.alert('Kontakt hinzugefuegt', 'Information');
					//showMeldung('Kunde erfolgreich zu Kontakte hinzugef&uuml;gt!');
				}
			}).send();
		
		}else{
			document.id('iNotiz').setStyle('display','block');
		}
		document.id('showNotice').addClass('isShow');
	}else{
		document.id('showNotice').removeClass('isShow');
		document.id('iNotiz').setStyle('display','none');
	}
}

var getOverview=function(KUNDENID){
	
	
	AjaxLeftContent.send('ajaxaction=4&kundenId=0');
	AjaxRightContent.send('ajaxaction=1&kundenId='+KUNDENID);
	AjaxControls.send('ajaxaction=11');
    
	
	//document.id('kunde_'+KUNDENID).addClass('active');
	//currentId=KUNDENID;
}



var addContact=function(KUNDENID){
	
	myDialogWindow('innerContentContact','Kontakt hinzuf&uuml;gen','340px','200px',false,true,'ajaxaction=9&kundenId='+KUNDENID,'');
	
	/*
	new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',data:'ajaxaction=9&kundenId='+KUNDENID,evalScripts:true,
		onRequest:function(){
			
		},
		onComplete:function(){
		//	OC.dialogs.alert('Kontakt hinzugefuegt', 'Information');
			showMeldung('Kunde erfolgreich zu Kontakte hinzugef&uuml;gt!');
		}
	}).send();*/
	
}



var showMeldung=function(TXT){
		//myShowWindow('iMeldung','Information', 300,150,false,false,'',TXT,false);
  
   var myMeldungDiv = new Element('div#iMeldung').inject(document.id('content'));
  
   document.id('iMeldung').set('html',TXT);
   var leftMove=(window.getWidth()/2)-150;
   var myMeldungFx=new Fx.Morph('iMeldung',{duration:500,transition:Fx.Transitions.Bounce.easeOut});
   myMeldungFx.start({
   	top:[0,200],
	left:[leftMove,leftMove]
   }).wait(3000).chain(function(){
   	   myMeldungFx.start({
   		top:[200,-300]
	   }).chain(function(){
	   		myMeldungDiv.destroy();
	   });
	  
   });
}

var newKunde=function(){
	
	if(document.id('newCustomer').get('value')!=''){
		
	new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',data: document.id('newKunde'),evalScripts:true,
		onRequest:function(){
			
		},
		onComplete:function(){
			AjaxLeftContent.send('ajaxaction=4&kundenId=0');
			document.id('newCustomer').set('value','');
		}
	}).send();
	
	}
	if(document.id('newCustomer').get('value')==''){
		showMeldung('Bitte geben Sie einen Namen f&uuml;r den Kunden an!');
	//	OC.dialogs.alert('Bitte geben Sie einen Namen an!', 'Information');
	}
}
var showKundenList=function(){
	
	if(!document.id('showKundenList').hasClass('isOpen')){
		 document.id('showKundenList').addClass('isOpen');
		 document.id('showKundenList').set('html', '-');
		 document.id('leftcontent').setStyle('display', 'block');
		 widthCalc=document.id('content').getCoordinates().width.toInt() - document.id('leftcontent').getCoordinates().width.toInt();
		 document.id('rightcontent').setStyles({'left': '32.5em','width':widthCalc});
		
	}else{
		document.id('showKundenList').removeClass('isOpen');
		document.id('showKundenList').set('html', '+');
		 widthCalc=window.getWidth().toInt() - document.id('navigation').getCoordinates().width.toInt();
		 
		  document.id('leftcontent').setStyle('display', 'none');
		 document.id('rightcontent').setStyles({'left': '12.5em','width':widthCalc});
		 // document.id('rightcontent').setStyle('left', '12.5em');
	}
}

var delKunde=function(KUNDENID){
	AjaxLeftContent.send('ajaxaction=4&kundenId='+KUNDENID);
	AjaxRightContent.send('ajaxaction=5');
	showMeldung('L&ouml;schen des Kunden erfolgreich!');

}

var switchDataFunction=function(EDITBUT){
	if(!document.id(EDITBUT).hasClass('isSave')){
		 document.id(EDITBUT).addClass('isSave');
		 document.id(EDITBUT).set('html', 'speichern');
		 prepareEditData();
		 ScrollerRight();
	}else{
		
		document.id(EDITBUT).removeClass('isSave');
		document.id(EDITBUT).set('html', 'bearbeiten');
		prepareSaveData();
		
	}
	
}

var showMoreInfo=function(MOD,PROJID,KUNDENID){
	
	new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',data:'ajaxaction=12&mod='+MOD+'&projectId='+PROJID+'&kundenId='+KUNDENID,update: document.id('iMoreOption'),evalScripts:true,
		onRequest:function(){
			
		},
		onComplete:function(){
			
		}
	}).send();
	
}

var prepareSaveData=function(){
	
	new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',data: document.id('kundeEdit'),evalScripts:true,
		onRequest:function(){
			
		},
		onComplete:function(){
			if($$('.editInputField').length > 1){
				$$('.editInputField').each(function(el,i){
					if(el.getProperty('readonly')){
						document.id(el.get('name')+'_Sel').setStyle('display','none');
					}
					
					tmpTxt=el.get('value');
					parEl=el.getParent('label[class="editField"]');
					if(parEl==null){
						parEl=el.getParent('label[class="editField readOnly"]');
						if(parEl==null){
							parEl=el.getParent('label[class="editField txtarea"]');
						}
					}
					
					parEl.set('html','');
					parEl.set('html',tmpTxt);
				});
			
				 
			}
			//OC.dialogs.alert('Speichern erfolgreich!', 'Information');	
			
			showMeldung('Speichern erfolgreich!');
		}
	}).send();
	
	
	
}
var prepareEditData=function(){
	
	if($$('.editField').length > 1){
		$$('.editField').each(function(el,i){
			tmpTxt=el.get('text');
			el.set('text','');
			tmpEl='';
			if(!el.hasClass('txtarea')){
				tmpEl=new Element('input',{
					'id':el.get('name')+'_temp',
					'name':el.get('name'),
					'type':'text',
					'class':'editInputField',
					'placeholder':el.get('title'),
					'value':tmpTxt
				}).inject(el);
			}
			
			if(el.hasClass('txtarea')){
				tmpEl=new Element('textarea',{
					'id':el.get('name')+'_temp',
					'name':el.get('name'),
					'class':'editInputField',					
					'placeholder':el.get('title'),
					'value':tmpTxt
				}).inject(el);
			}
			
			if(el.hasClass('readOnly')){
				tmpEl.setProperty('readonly','readonly');
				
				prepareEditSel(el.get('name'));
			}
			
			
		});
		
	 
	}
}


var switchEditData=function(COUNTER,ID){
	if(!document.id('editBut_'+COUNTER).hasClass('isSave')){
		 document.id('editBut_'+COUNTER).addClass('isSave');
		 document.id('editBut_'+COUNTER).set('html', 'save');
		 editValue(COUNTER,ID);
	}else{
		
		document.id('editBut_'+COUNTER).removeClass('isSave');
		document.id('editBut_'+COUNTER).set('html', 'edit');
		saveValue(COUNTER,ID);
	}
}

var editValue=function(COUNTER,ID){
	
	if($$('#editRow_'+COUNTER+' .editFieldRow').length > 1){
		$$('#editRow_'+COUNTER+' .editFieldRow').each(function(el,i){
			tmpTxt=el.get('text');
			el.set('text','');
			tmpEl='';
			if(!el.hasClass('txtarea')){
				tmpEl=new Element('input',{
					'id':el.get('name')+'_temp',
					'name':el.get('name'),
					'type':'text',
					'class':'editInputFieldRow',
					'placeholder':el.get('title'),
					'value':tmpTxt
				}).inject(el);
			}
			
			if(el.hasClass('txtarea')){
				tmpEl=new Element('textarea',{
					'id':el.get('name')+'_temp',
					'name':el.get('name'),
					'class':'editInputFieldRow',					
					'placeholder':el.get('title'),
					'value':tmpTxt
				}).inject(el);
			}
			
			if(el.hasClass('readOnly')){
				tmpEl.setProperty('readonly','readonly');
				
				prepareEditSel(el.get('name'));
			}
			
		});
		
	 
	}
}

var saveValue=function(COUNTER,ID){
	
//	new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',data: document.id('kundeEdit'),evalScripts:true,
	//	onRequest:function(){
			
	//	},
	//	onComplete:function(){
			if($$('#editRow_'+COUNTER+' .editInputFieldRow').length > 1){
				$$('#editRow_'+COUNTER+' .editInputFieldRow').each(function(el,i){
					if(el.getProperty('readonly')){
						document.id(el.get('name')+'_Sel').setStyle('display','none');
					}
					
					tmpTxt=el.get('value');
					parEl=el.getParent('label[class="editFieldRow"]');
					if(parEl==null){
						parEl=el.getParent('label[class="editFieldRow readOnly"]');
						if(parEl==null){
							parEl=el.getParent('label[class="editFieldRow txtarea"]');
						}
					}
					
					parEl.set('html','');
					parEl.set('html',tmpTxt);
				});
			
				 
			}
			OC.dialogs.alert('Speichern erfolgreich!', 'Information');	
	//	}
	//}).send();
	
	
	
}


var prepareEditSel=function(ELSEL){
	
	selElem=ELSEL+'_Sel';
	
	document.id(selElem).setStyle('display','inline-block');
	
	
	document.id(selElem).addEvent('change',function(evt){
		evt.stop();
		document.id(ELSEL+'_temp').set('value',evt.target.getSelected().get("text"));
	});
}
var myKundenList =null;
var myKundenBody=null;

var ScrollerLeft=function(){
	if(document.id('myScroller')!=null){
					if(document.id('myScroller').getScrollHeight() < document.id('leftcontent').getHeight()){
				document.id('vBar').setStyle('display','none');
			}else{
					document.id('myScroller').setStyle('height',document.id('myScroller').getScrollHeight());
					//new NS.Placeholder({cssClass: 'my-placeholder'});
					myKundenList = new ScrollBar('leftcontent', 'vBar', 'vKnob', {
					mode: 'vertical',
					offset: -1,
						scroll: {
						duration: 500,
						transition: 'linear'
						},
						knob: {
						duration: 500,
						transition: 'elastic:out'
						}
					});
					myKundenList.set(0);
				}
		}
}

var ScrollerRight=function(){
	if(document.id('myScrollerKunden')!=null){
			if(document.id('myScrollerKunden').getScrollHeight() < document.id('rightcontent').getHeight()){
				document.id('vBarRight').setStyle('display','none');
			}else{
					document.id('vBarRight').setStyle('display','block');
					document.id('myScrollerKunden').setStyle('height',document.id('myScrollerKunden').getScrollHeight());
					//new NS.Placeholder({cssClass: 'my-placeholder'});
					myKundenBody = new ScrollBar('rightcontent', 'vBarRight', 'vKnobRight', {
					mode: 'vertical',
					offset: -1,
						scroll: {
						duration: 1000,
						transition: 'elastic:out'
						},
						knob: {
						duration: 500,
						transition: 'elastic:out'
						}
					});
					myKundenBody.set(0);
				}
			}
}

var newNotice=function(KUNDENID){
	myDialogWindow('innerContentNotiz','Notiz verfassen','600px','400px',false,true,'ajaxaction=7&kId='+KUNDENID,'');
}

var editNotice=function(NOTIZID,KUNDENID){
	myDialogWindow('innerContentNotiz','Notiz bearbeiten','600px','400px',false,true,'ajaxaction=7&kId='+KUNDENID+'&notice_id='+NOTIZID,'');
}

var checkNotice=function(NOTIZID){
	
	var isFlag=document.id('chk_'+NOTIZID).get('checked');
	
	new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'),data:'ajaxaction=6&nId='+NOTIZID+'&noticeFlag='+isFlag,method:'post',evalScripts: true,
			onSuccess:function(){
				
				if(isFlag) document.id('tr_'+NOTIZID).setStyle('text-decoration','line-through');
				else document.id('tr_'+NOTIZID).setStyle('text-decoration','none');
				
			}
		}).send();
}

var SubmitForm = function(VALUE,FormId,UPDATEAREA) {

	if(!errorChecker(FormId)){
		document.id(FormId).getElement('input[name=hiddenfield]').set('value',VALUE);
		new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'),data:document.id(FormId),method:'post',update: document.id(UPDATEAREA),evalScripts: true,
			onSuccess:function(){
		
			if(VALUE=='newitNotice') {
				document.id('iNotiz').set('html','');
				document.id('showNotice').removeClass('isShow');
				loadNotiz(document.id('refKundenId').get('value'));
				showMeldung('Datensatz erfolgreich bearbeitet!');
			}
			if(VALUE=='addContact') {
					showMeldung('Kontakt erfolgreich hinzugef&uuml;gt!');
			}
				
			}
		}).send();
	}
	
}

var myDialogWindow=function(UPDATEFIELD,TITLE,WIDTH,HEIGHT,MODAL,AJAXAKTIV,AJAXDATA,TXT){
	
	myWindoo=new OwnWindoo(null,{
    width:WIDTH,
	height:HEIGHT,
	modal:MODAL,
	updateField:UPDATEFIELD,
	title:TITLE,
	text:TXT,
	elemPos:document.id('mainBody'),
	ajaxAktiv:AJAXAKTIV,
	ajaxFile:OC.filePath('kunden', 'ajax', 'control.inc.php'),
	ajaxData:AJAXDATA,
	onAjaxComplete: function(){
 		
       
	}.bind(this)

    });
	myWindoo.show();
}
var switchLogo=function(){
	document.id('owncloud').set('html','LIBASYS CLOUD SERVICE');
	
	var myLogoFx=new Fx.Morph('owncloud',{duration:1500,transition:Fx.Transitions.Bounce.easeOut});
   myLogoFx.start({
  	'margin-left':['-400',0]
   });
}

window.addEvent('domready', function(){

ScrollerLeft();
ScrollerRight();

	AjaxRightContent =new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',update: document.id('rightcontent'),evalScripts:true,
		onRequest:function(){
			
		},
		onComplete:function(){
			ScrollerRight();
		}
	});
	AjaxControls=new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',update: document.id('controls'),evalScripts:true,
		onRequest:function(){
		
	},
	onComplete:function(){
	}
		});
	
	AjaxLeftContent =new Request.HTML({url:OC.filePath('kunden', 'ajax', 'control.inc.php'), method:'post',update: document.id('leftcontent'),evalScripts:true,
		onRequest:function(){
			
		},
		onComplete:function(){
			ScrollerLeft();
		}
	});
	
});