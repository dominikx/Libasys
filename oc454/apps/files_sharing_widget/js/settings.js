/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */

$(document).ready(function(){

if($('.delshare').length >0){
	
	$('.delshare').each(function(i,el){
		$(el).click(function(){
		$.ajax({
				type: 'POST',
				url: OC.filePath('files_sharing_widget', 'ajax', 'delshare.php'),
				data: { delid: $(el).attr('data-id')},
				success: function(result) {
					
					 $('li[share-id='+result.data.msg+']').remove();
				}
		});
		});
	});
	$('.sendshare').each(function(i,el){
		$(el).click(function(){
			tokenlink=$('#widgetlink-'+$(this).attr('send-id')).attr('href');
		    $('#sharelinktxt').val(tokenlink);
			
			$('#sharedialog_holder').dialog({
					height : 'auto',
					width : 'auto',
					buttons : {
						"SEND" : function() {
							if($('#shareEmail').val()!=''){
							
							$.ajax({
								type: 'POST',
								url: OC.filePath('files_sharing_widget', 'ajax', 'sendlink.php'),
								data: { email: $('#shareEmail').val(),sharelink:$('#sharelinktxt').val(),mailtxt:$('#sharemailtxt').val()},
								success: function(result) {
									      $('#sharelinktxt').val('');
							              $('#shareEmail').val('');
								         $('#sharemailtxt').val('');
								 }
								 
							  });
							   
							   $(this).dialog("close");
							}
							
							//$(this).dialog("close");
						},
						Cancel : function() {
							$('#sharelinktxt').val('');
							    $('#shareEmail').val('');
							$(this).dialog("close");
						}
					}
				});
		});
	});
	
	
}

if($('#shareparamform').length >0){
	
$("#shareparambutton").click( function(){
		if ($('#sppics').val() != '' && $('#spthumb').val() != '' && $('#spwidth').val() != '' && $('#spheight').val() != '') {
			// Serialize the data
			var post = $( "#shareparamform" ).serialize();
			
			// Ajax foo
			$.post( OC.filePath('files_sharing_widget', 'ajax', 'saveparam.php'), post, function(data){
				if( data.status == "success" ){
					alert(data.data.msg);
				}
				
			});
			return false;
		} else {
			
			return false;
		}

	});
}
});
