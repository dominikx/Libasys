$(document).ready(function(){
	$('#loglevel').change(function(){
		$.post(OC.filePath('settings','ajax','setloglevel.php'), { level: $(this).val() },function(){
			OC.Log.reload();
		} );
	});
	
	$('#app_switcher').bind('change', function(){
		$.ajax({
			type:'POST',
			url:OC.filePath('settings','ajax','appdefault.php'),
			data:{a:$(this).val()},
			success:function(data){
				alert('Default App: '+data.data.app);
				//document.location.reload(true);
			}
		});
	});
	
     $("#changeGroups").click( function(){
 	    
 	    var post=$("#chGroupForm").serialize();
 	     $.post( 'ajax/changegrouprights.php', post, function(data){
				if( data.status == "success" ){
					alert("Geaendert\n"+data.data.changed);
					location.reload();
				}else{
					alert('Problem');
				}
				
			});
			
		return false;

	});
	
	$('#backgroundjobs input').change(function(){
		if($(this).attr('checked')){
			var mode = $(this).val();
			if (mode == 'ajax' || mode == 'webcron' || mode == 'cron') {
				OC.AppConfig.setValue('core', 'backgroundjobs_mode', mode);
			}
		}
	});

	$('#shareAPIEnabled').change(function() {
		$('.shareAPI td:not(#enable)').toggle();
	});

	$('#shareAPI input').change(function() {
		if ($(this).attr('type') == 'checkbox') {
			if (this.checked) {
				var value = 'yes';
			} else {
				var value = 'no';
			}
		} else {
			var value = $(this).val();
		}
		OC.AppConfig.setValue('core', $(this).attr('name'), value);
	});
});
