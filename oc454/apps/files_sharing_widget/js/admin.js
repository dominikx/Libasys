/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */

$(document).ready(function(){


if($('#ShareEntreeForm').length >0){
	
$("#savebutton").click( function(){
		if ($('#mySecretW').val() != '') {
			// Serialize the data
			//var post = $( "#ShareEntreeForm" ).serialize();
			var post ={mySecretWord:$("#mySecretW").val()};
			OC.msg.startSaving('#ShareEntreeForm .msg');
			$.post( OC.filePath('files_sharing_widget', 'ajax', 'sharesecret.php'), post, function(data){
				if( data.status == "success" ){
					OC.msg.finishedSaving('#ShareEntreeForm .msg', data);
					
				}
				
			});
			return false;
		} else {
			
			return false;
		}

	});
  $("#saveemailbutton").click( function(){
		if ($('#mySiteEmail').val() != '') {
			// Serialize the data
			var post ={siteemail:$("#mySiteEmail").val()};
			
			OC.msg.startSaving('#ShareEntreeForm .msg');
			$.post( OC.filePath('files_sharing_widget', 'ajax', 'sharesecret.php'), post, function(data){
				if( data.status == "success" ){
					OC.msg.finishedSaving('#ShareEntreeForm .msg', data);
					
				}
				
			});
			return false;
		} else {
			
			return false;
		}

	});	
}

});

OC.msg={
	startSaving:function(selector){
		$(selector)
			.html( t('settings', 'Saving...') )
			.removeClass('success')
			.removeClass('error')
			.stop(true, true)
			.show();
	},
	finishedSaving:function(selector, data){
		if( data.status == "success" ){
			 $(selector).html( data.data.message )
				.addClass('success')
				.stop(true, true)
				.delay(3000)
				.fadeOut(600);
		}else{
			$(selector).html( data.data.message ).addClass('error');
		}
	}
};
