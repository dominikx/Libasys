		<?php 
		$bThumb=false;
		if(isset($_['thumb'])){
		   $bThumb=true;
		}
		foreach($_['files'] as $file):
             $bAddImage=false;
             if (substr($file['mimetype'], 0 , strpos($file['mimetype'], '/')) == 'image'){
                 $bAddImage=true;
             }
            
			$simple_file_size = OCP\simple_file_size($file['size']);
			$simple_size_color = intval(200-$file['size']/(1024*1024)*2); // the bigger the file, the darker the shade of grey; megabytes*2
			if($simple_size_color<0) $simple_size_color = 0;
			$relative_modified_date = OCP\relative_modified_date($file['mtime']);
			$relative_date_color = round((time()-$file['mtime'])/60/60/24*14); // the older the file, the brighter the shade of grey; days*14
			if($relative_date_color>200) $relative_date_color = 200;
			$name = str_replace('+','%20',urlencode($file['name']));
			$name = str_replace('%2F','/', $name);
			$directory = str_replace('+','%20',urlencode($file['directory']));
			$directory = str_replace('%2F','/', $directory); ?>
			<tr data-id="<?php echo $file['id']; ?>" data-file="<?php echo $name;?>" data-type="<?php echo ($file['type'] == 'dir')?'dir':'file'?>" data-mime="<?php echo $file['mimetype']?>" data-size='<?php echo $file['size'];?>' data-permissions='<?php echo $file['permissions']; ?>'>
				<td class="filename svg" style="background-image:url(<?php if($file['type'] == 'dir') echo OCP\mimetype_icon('dir'); else echo OCP\mimetype_icon($file['mimetype']); ?>)">
					<?php if(!isset($_['readonly']) || !$_['readonly']) { ?><input type="checkbox" /><?php } 
					     $relAdd='';
                         $addTitle='';
                         $addHref=$_['downloadURL'].$directory.'/'.$name;
                   
					   if($bAddImage){
					       $relAdd='rel="fancyArea"';
                           $addTitle= $name;
                            if($bThumb) $addHref=OCP\Util::linkToPublic('files').'&download&dir='.$file['path'].'&path=/'.$file['name'];
					   }
					?>
					<a  class="name" <?php echo $relAdd; ?> href="<?php if($file['type'] == 'dir') echo $_['baseURL'].$directory.'/'.$name; else echo $addHref; ?>" title="<?php echo $addTitle; ?>">
					<span class="nametext">
						<?php if($file['type'] == 'dir'):?>
							<?php 
							
							echo  htmlspecialchars($file['name']);
							?>
						<?php else:?>
							<?php 
							 if($bThumb){
                                if($bAddImage){
                                 
                                  echo '<img  src="'.OCP\Util::linkToPublic('files').'&download&dir='.$file['path'].'&path=/'.$file['name'].'" width="200">';
                               }else{
                                   echo htmlspecialchars($file['basename']);
                               }
                           }else{
							echo htmlspecialchars($file['basename']);
                           }
							?><span class='extension'><?php  if(!$bThumb || !$bAddImage){ echo $file['extension']; }
							    ?></span>
						<?php endif;?>
					</span>
					<?php if($file['type'] == 'dir'):?>
						<span class="uploadtext" currentUploads="0">
						</span>
					<?php endif;?>
					</a>
				</td>
				<td class="filesize" title="<?php echo OCP\human_file_size($file['size']); ?>" style="color:rgb(<?php echo $simple_size_color.','.$simple_size_color.','.$simple_size_color ?>)"><?php echo $simple_file_size; ?></td>
				<td class="date"><span class="modified" title="<?php echo $file['date']; ?>" style="color:rgb(<?php echo $relative_date_color.','.$relative_date_color.','.$relative_date_color ?>)"><?php echo $relative_modified_date; ?></span></td>
			</tr>
		<?php endforeach; ?>
