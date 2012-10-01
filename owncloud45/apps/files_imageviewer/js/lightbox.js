



$(document).ready(function() {

 if($('a[rel=fancyArea]').length>0){
	$('a[rel=fancyArea]').fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 		
		'titlePosition' 	: 'over',		
		'titleFormat'       : function(title, currentArray, currentIndex, currentOpts) {
		    return '<span id="fancybox-title-over">Image ' +  (currentIndex + 1) + ' / ' + currentArray.length + ' ' + title + '</span>';
		  }
	});
	

}
	if(typeof FileActions!=='undefined'){
	//	FileActions.register('image','View', OC.PERMISSION_READ, '',function(filename){
		//	viewImage($('#dir').val(),filename);
			
	//	});
		//FileActions.setDefault('image','View');
	}
	
	OC.search.customResults.Images=function(row,item){
		var image=item.link.substr(item.link.indexOf('?file=')+6);
		var a=row.find('a');
		a.attr('href','#');
		a.click(function(){
		
			var pos=image.lastIndexOf('/')
			var file=image.substr(pos + 1);
			var dir=image.substr(0,pos);
			
			viewImage(dir,file);
		});
	}
});

function viewImage(dir, file) {
 
	if(file.indexOf('.psd')>0){//can't view those
		return;
	}
	var location = fileDownloadPath(dir, file);
	 alert(counter);
	$.fancybox({
		"href": location,
		"title": file.replace(/</, "&lt;").replace(/>/, "&gt;"),
		"titlePosition": "inside"
	});
}
