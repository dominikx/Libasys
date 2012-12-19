<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
?>
<fieldset class="personalblock">
    <legend>Geteilte Dateien und Ordner</legend>
       <ul class="shareSettings">
		<li class="shareHeading">
			<span style="width:10%; float:left;display:block;">ShareType </span>
			<span style="width:15%;float:left;display:block;">Shared With</span>
			<span style="width:15%;float:left;display:block;">Share Item</span>
			<span style="width:20%;float:left;display:block;">Expiration Date</span>
			<span style="width:35%;float:left;display:block;">Picture Widget Token</span><br style="clear:both;" />
		</li>
		<?php if(is_array($_['shares'])) {foreach($_['shares'] as $shareInfo):
?>
<li share-id="<?php echo $shareInfo['id'] ?>" >
	<span style="width:10%;float:left;display:block;">
		<a href="javascript:;" class="sendshare" send-id="<?php echo $shareInfo['id'] ?>"><img title="<?php echo $l->t('Email') ?>" src="<?php echo OCP\image_path('core', 'actions/mail.png') ?>" class="svg" /></a> <a href="javascript:;" class="delshare" data-id="<?php echo $shareInfo['id'] ?>"><img title="<?php echo $l->t('Delete') ?>" src="<?php echo OCP\image_path('core', 'actions/delete.png') ?>" class="svg" /></a><?php echo $shareInfo['shareType']; ?>
   </span>
<span style="width:15%;float:left;display:block;"><?php echo $shareInfo['shareName']; ?></span>
<span style="width:15%;float:left;display:block;"><a href="<?php echo $shareInfo['link']; ?>" target="_blank"><?php echo $shareInfo['name']; ?></a></span>
<span style="width:20%;float:left;display:block;"><?php echo $shareInfo['date']; ?></span>
<span style="width:38%;display:block;float:left;"><a id="widgetlink-<?php echo $shareInfo['id'] ?>" href="<?php echo OC_HELPER::makeURLAbsolute(OC::$WEBROOT); ?>/widget.php?action=real&iToken=<?php echo $shareInfo['iToken']; ?>" target="_blank"><?php echo $shareInfo['iToken']; ?></a></span><br style="clear:both;" /></li>
<?php endforeach;}
	$getRelativeAppsPath=OC_Widget_Helper::getRelativeAppWebPath();
	$getRelativeAppsPath=substr($getRelativeAppsPath,1,strlen($getRelativeAppsPath)-1);
?>
</ul><br />
<b>Configurationparameters:</b><br />
<form id="shareparamform">
<label style="width:140px;display:block;float:left;">Max Pics Per Page:</label> <input type="text" name="sppics" id="sppics" maxlength="2" style="width:35px;" value="<?php echo $_['sharaparam']['maxpicsperpage'] ?>" /> <br />
<label style="width:140px;display:block;float:left;">Thumb Height Size:</label> <input type="text" name="spthumb" id="spthumb" maxlength="3" style="width:35px;" value="<?php echo $_['sharaparam']['imgheight'] ?>" /> <br />
<label style="width:140px;display:block;float:left;">Width Widget:</label> <input type="text" name="spwidth" id="spwidth" maxlength="4" style="width:35px;" value="<?php echo $_['sharaparam']['width'] ?>" /> px <br />
<label style="width:140px;display:block;float:left;">Height Widget: </label><input type="text" name="spheight" id="spheight" maxlength="4" style="width:35px;" value="<?php echo $_['sharaparam']['height'] ?>" /> px <br />
<label style="width:140px;display:block;float:left;">Watermark Text:</label> <input type="text" name="spWMTxt" id="spWMTxt" maxlength="50" style="width:200px;" value="<?php echo $_['sharaparam']['watermarktxt'] ?>" /><br />
<label style="width:140px;display:block;float:left;">Widget Title: </label><input type="text" name="spTitle" id="spTitle" maxlength="100" style="width:200px;" value="<?php echo $_['sharaparam']['title'] ?>" /> <br />

<input id="shareparambutton" type="submit" value="Save" />
</form>
<br /><br />
Use as Widget: (For Integration Homepage, Facebook)<br />
<textarea style="width:60%;height:120px;" readonly>
<script>
	var ownWidgetOptions = {
	crypt:'thecryptoken', //e.g.os3Nz8rhqNnV1cfUotvc2M3H0w==
	path:'<?php echo OC_Helper::makeURLAbsolute(OC::$WEBROOT . '/'); ?>',
	appspath:'<?php echo $getRelativeAppsPath; ?>',
	cssAddWidget:{'width':670,'height':400}
	}
</script>
<script src="<?php echo OC_HELPER::makeURLAbsolute(OC::$WEBROOT) . '/' . $getRelativeAppsPath . '/'; ?>files_sharing_widget/widgetloader.php" type="text/javascript"></script></textarea>

</fieldset>
<!-- Dialogs -->
<div id="sharedialog_holder" title="Send Link To" style="display:none;">
<label>E-Mail</label> <input style="width:300px;" type="text" placeholder="E-Mail-Adresse" name="shareEmail" id="shareEmail" /><br />
<textarea name="sharelinktxt" style="width:500px;" id="sharemailtxt" placeholder="Add Comment e.g. Password"></textarea><br />
<textarea name="sharelinktxt" style="width:500px;" id="sharelinktxt" readonly></textarea>
</div>
<!-- End of Dialogs -->
