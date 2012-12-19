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
	<legend>
		Share Pics Widget Secret Key and Site-E-Mail
	</legend>
	<form id="ShareEntreeForm">
		<label style="width:280px;display:block;float:left;">Secret Word for Token:</label>
		<input id="mySecretW" name="mySecretWord" type="password" value="<?php echo $_['secretword'] ?>" />
		<input id="savebutton" type="button" value="Save" />
		<span class="msg"></span>
		<br />
		<label style="width:280px;display:block;float:left;">Site E-Mail for Shared Link via E-Mail:</label>
		<input id="mySiteEmail" name="mySiteEmail" type="email" value="<?php echo $_['siteemail'] ?>" />
		<input id="saveemailbutton" type="button" value="Save" />
		<br />
	</form>
</fieldset>