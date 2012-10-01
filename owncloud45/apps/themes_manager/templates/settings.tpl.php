<?php

/**
* ownCloud - DjazzLab Themes Manager plugin
*
* @author Xavier Beurois
* @copyright 2012 Xavier Beurois www.djazz-lab.net
* 
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either 
* version 3 of the License, or any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*  
* You should have received a copy of the GNU Lesser General Public 
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
* 
*/

?>

<form id="themes_manager_uploader" action="<?php print(OCP\Util::linkTo('themes_manager', 'ajax/upload.php')); ?>" method="post" enctype="multipart/form-data" target="file_upload_target_1">
	<fieldset class="personalblock">
		<label>Themes Manager - Uploader</label><br />
		<label>ZIP file</label>
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" id="max_upload" />
		<input class="file_upload_start" type="file" name='file' />
		<br />
		<button id="sendactive">Send & Active</button>
		<iframe name="file_upload_target_1" class='file_upload_target' src="" style="height:2em;overflow:hidden;width:100%;"></iframe>
	</fieldset>
</form>

<form id="themes_manager_switcher">
	<fieldset class="personalblock">
		<label>Themes Manager - Switcher</label><br />
		<select id="tm_switcher">
			<option value='0'<?php $_['current']=='0'?' selected':'' ?>>Default</option>
			<?php foreach($_['themes'] as $theme): ?>
			<option value='<?php print(strtolower($theme)); ?>'<?php print($_['current']==strtolower($theme)?' selected':''); ?>><?php print(ucfirst($theme)); ?></option>
			<?php endforeach;?>
		</select>
	</fieldset>
</form>