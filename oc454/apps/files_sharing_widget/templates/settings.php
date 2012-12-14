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
    <legend>Overview Shared Files and Folders</legend>
      <ul class="shareSettings">
          <li class="shareHeading"><span style="width:10%; float:left;display:block;">ShareType </span><span style="width:15%;float:left;display:block;">Name</span><span style="width:15%;float:left;display:block;">Share Item</span><span style="width:20%;float:left;display:block;">Expiration Date</span><span style="width:35%;float:left;display:block;">Picture Widget Token</span><br style="clear:both;" /></li> 
      <?php if(is_array($_['shares'])) {foreach($_['shares'] as $shareInfo):?>
            <li share-id="<?php echo $shareInfo['id'] ?>" ><span style="width:10%;float:left;display:block;"><a href="javascript:;" class="sendshare" send-id="<?php echo $shareInfo['id'] ?>"><img title="<?php echo $l->t('Email') ?>" src="<?php echo OCP\image_path('core', 'actions/mail.png') ?>" class="svg" /></a> <a href="javascript:;" class="delshare" data-id="<?php echo $shareInfo['id'] ?>"><img title="<?php echo $l->t('Delete') ?>" src="<?php echo OCP\image_path('core', 'actions/delete.png') ?>" class="svg" /></a><?php echo $shareInfo['shareType']; ?> </span><span style="width:15%;float:left;display:block;"><?php echo $shareInfo['shareName']; ?> </span><span style="width:15%;float:left;display:block;"><a href="<?php echo $shareInfo['link']; ?>" target="_blank"><?php echo $shareInfo['name']; ?> </a> </span><span style="width:20%;float:left;display:block;"><?php echo $shareInfo['date']; ?></span><span style="width:40%;display:block;float:left;"><a id="widgetlink-<?php echo $shareInfo['id'] ?>" href="<?php echo OC_HELPER::makeURLAbsolute(OC::$WEBROOT); ?>/widget.php?action=real&iToken=<?php echo $shareInfo['iToken']; ?>" target="_blank"><?php echo $shareInfo['iToken']; ?></a></span><br style="clear:both;" /></li> 
       
        <?php endforeach;} ?>
       </ul><br />
       <b>Configurationparameters Picture Widget:</b><br />
       <form id="shareparamform">
       	 <label style="width:140px;display:block;float:left;">Max Pics Per Page:</label> <input type="text" name="sppics" id="sppics" maxlength="2" style="width:35px;" value="<?php echo $_['sharaparam']['maxpicsperpage'] ?>" /> <br />
       	 <label style="width:140px;display:block;float:left;">Thumb Size Height:</label> <input type="text" name="spthumb" id="spthumb" maxlength="3" style="width:35px;" value="<?php echo $_['sharaparam']['imgheight'] ?>" /> <br />
       	 <label style="width:140px;display:block;float:left;">Width Widget:</label> <input type="text" name="spwidth" id="spwidth" maxlength="4" style="width:35px;" value="<?php echo $_['sharaparam']['width'] ?>" /> px <br />
       	 <label style="width:140px;display:block;float:left;">Height Widget: </label><input type="text" name="spheight" id="spheight" maxlength="4" style="width:35px;" value="<?php echo $_['sharaparam']['height'] ?>" /> px <br />
       	<input id="shareparambutton" type="submit" value="Save" />
       </form>
       <br /><br />
      Use as Widget: (For Integration HP, Facebook)<br />
      	<textarea style="width:50%;height:150px;" readonly>
<script>
 var ownWidgetOptions = {
	 	crypt:'thecryptoken', //e.g.os3Nz8rhqNnV1cfUotvc2M3H0w==
	 	path:'<?php echo OC_Helper::makeURLAbsolute(OC::$WEBROOT.'/'); ?>',
	 	display:'',
	 	modal:false,
	 	cssAddWidget:{'width':670,'height':400},
	 	cssAddButton:{'top':20,'left':20},
	 	imgWidth:110,
	 	buttonlabel:'Fotogalerie'
	}
</script>
<script src="<?php echo OC_Helper::makeURLAbsolute(OC::$WEBROOT.'/'); ?>widgetloader.php" type="text/javascript"></script></textarea>
		
</fieldset>
<!-- Dialogs -->
<div id="sharedialog_holder" title="Send Link To" style="display:none;">
	<label>E-Mail</label> <input style="width:300px;" type="text" placeholder="E-Mail-Adresse" name="shareEmail" id="shareEmail" /><br />
	<textarea name="sharelinktxt" style="width:500px;" id="sharemailtxt" placeholder="Add Comment e.g. Password"></textarea><br />
	<textarea name="sharelinktxt" style="width:500px;" id="sharelinktxt" readonly></textarea>
</div>
<!-- End of Dialogs -->
