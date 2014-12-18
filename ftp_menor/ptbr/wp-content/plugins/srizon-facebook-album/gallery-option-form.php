<form action="admin.php?page=SrzFb-Galleries&srzf=save" method="post">
	<?php SrizonFBUI::BoxHeader('box1', "Gallery Title", true);?>
	<div><input type="text" name="title" size="50" value="<?php echo $value_arr['title'];?>" /></div>
	<?php 
	SrizonFBUI::BoxFooter();
	SrizonFBUI::BoxHeader('box2', "Fanpage ID", true);
	?>
	<div>
		<div>If the fanpage link(URL) is <span style="color:blue;">http://www.facebook.com/<strong>pagename</strong></span> then the ID is <strong>pagename</strong> which should be put in this field. <br />
		If the fanpage link(URL) is <span style="color:blue;">http://www.facebook.com/pages/name/<strong>number</strong></span> then the ID is the <strong>number</strong> which should be put in this field.
		</div>
		<input type="text" name="pageid" size="25" value="<?php echo $value_arr['pageid'];?>" /></div>
	<?php 
	SrizonFBUI::BoxFooter();
	SrizonFBUI::BoxHeader('box3', "Albums to include/exclude: Put Album ID(s)", true);
	if(!file_exists(dirname(__FILE__).'/srizon-fb-album-front-pro.php')){
		echo '<em>Only in pro version</em>';
	}
	else{
	?>
	<div>
		<div>If the album link(URL) is <span style="color:blue;">http://www.facebook.com/media/set/?set=a.<strong>number1</strong>.number2.number3...</span> then the ID is <strong>number1</strong> which should be put in this field.</div>
		<textarea name="options[excludeids]" rows="5" cols="20"><?php echo $value_arr['excludeids'];?></textarea>
		<div>Separate multiple IDs with newline or whitespace</div></div>

		<?php
			$chk1 = $chk2 = '';
			if($value_arr['include_exclude'] == 'include'){
				$chk2 = ' checked="checked"';
			}
			else{
				$chk1 = ' checked="checked"';
			}
		?>
		<br />
		<input type="radio" name="options[include_exclude]" value="exclude"<?php echo $chk1;?> />Exclude These Albums
		<br />
		<input type="radio" name="options[include_exclude]" value="include"<?php echo $chk2;?> />Show Only These Albums

	<?php 
	}
	SrizonFBUI::BoxFooter();
	SrizonFBUI::BoxHeader('box4', "Options", true);
	?>
	<table>
		<tr>
			<td>
				<span class="label">Sync After Every # minutes</span>
			</td>
			<td>
				<input type="text" size="5" name="options[updatefeed]" value="<?php echo $value_arr['updatefeed'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Shuffle Photos inside albums?</span>
			</td>
			<td>
				<?php
					$chk1 = $chk2 = '';
					if($value_arr['shuffle_images'] == 'no'){
						$chk2 = ' checked="checked"';
					}
					else{
						$chk1 = ' checked="checked"';
					}
				?>
				<input type="radio" name="options[shuffle_images]" value="yes"<?php echo $chk1;?> />Yes
				<input type="radio" name="options[shuffle_images]" value="no"<?php echo $chk2;?> />No
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Shuffle Album List?</span>
			</td>
			<td>
				<?php
					$chk1 = $chk2 = '';
					if($value_arr['shuffle_albums'] == 'no'){
						$chk2 = ' checked="checked"';
					}
					else{
						$chk1 = ' checked="checked"';
					}
				?>
				<input type="radio" name="options[shuffle_albums]" value="yes"<?php echo $chk1;?> />Yes
				<input type="radio" name="options[shuffle_albums]" value="no"<?php echo $chk2;?> />No
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Thumbnail Size</span>
			</td>
			<td>
				<input type="text" size="3" name="options[thumbwidth]" value="<?php echo $value_arr['thumbwidth'];?>" /> x 
				<input type="text" size="3" name="options[thumbheight]" value="<?php echo $value_arr['thumbheight'];?>" /> px
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Paginate After # Images</span>
			</td>
			<td>
				<input type="text" size="5" name="options[paginatenum]" value="<?php echo $value_arr['paginatenum'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Show title under thumb? (Gallery Page)</span>
			</td>
			<td>
				<?php
					$chk1 = $chk2 = '';
					if($value_arr['showtitlethumb'] == 'no'){
						$chk2 = ' checked="checked"';
					}
					else{
						$chk1 = ' checked="checked"';
					}
				?>
				<input type="radio" name="options[showtitlethumb]" value="yes"<?php echo $chk1;?> />Yes
				<input type="radio" name="options[showtitlethumb]" value="no"<?php echo $chk2;?> />No
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Truncate title under thumb after X character <br />(if the selection above is Yes)</span>
			</td>
			<td>
				<input type="text" size="5" name="options[truncate_len]" value="<?php echo $value_arr['truncate_len'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Height of the title under thumb</span>
			</td>
			<td>
				<input type="text" size="5" name="options[titlethumb_height]" value="<?php echo $value_arr['titlethumb_height'];?>" />
			</td>
		</tr>
		
		<?php if(0){?>
		<tr>
			<td>
				<span class="label">Total Number of Images</span>
			</td>
			<td>
				<input type="text" size="3" name="options[totalimg]" value="<?php echo $value_arr['totalimg'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Layout</span>
			</td>
			<td>
				<select name="options[liststyle]">
					<option value="horizontal"<?php if($value_arr['liststyle']=='horizontal') echo ' selected="selected"';?>>Horizontal (max fit in a row)</option>
					<option value="slidergrid"<?php if($value_arr['liststyle']=='slidergrid') echo ' selected="selected"';?>>SliderGrid</option>
					<option value="slidergridv"<?php if($value_arr['liststyle']=='slidergridv') echo ' selected="selected"';?>>SliderGrid Vertical</option>
				</select>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td>
				<span class="label">Shadow color</span>
			</td>
			<td>
				<?php
					$chk1 = $chk2 = '';
					if($value_arr['tpltheme'] == 'black'){
						$chk2 = ' checked="checked"';
					}
					else{
						$chk1 = ' checked="checked"';
					}
				?>
				<input type="radio" name="options[tpltheme]" value="white"<?php echo $chk1;?> />Dark Gray
				<input type="radio" name="options[tpltheme]" value="black"<?php echo $chk2;?> />Light Gray
			</td>
		</tr>
	</table>
	<?php 
	if(0){
	SrizonFBUI::BoxFooter();
	SrizonFBUI::BoxHeader('box5', "Options for Horizontal Layout", true);
	?>
	<table>
		<tr>
			<td>
				<span class="label">Paginate After # Thumbs</span>
			</td>
			<td>
				<input type="text" size="5" name="options[paginatenum]" value="<?php echo $value_arr['paginatenum'];?>" />
			</td>
		</tr>
	</table>
	<?php 
	SrizonFBUI::BoxFooter();
	SrizonFBUI::BoxHeader('box6', "Options for SliderGird or SliderGrid Vertical layout", true);
	?>
	<table>
		<tr>
			<td>
				<span class="label">Grid row x column</span>
			</td>
			<td>
				<input type="text" size="3" name="options[gridrow]" value="<?php echo $value_arr['gridrow'];?>" /> x 
				<input type="text" size="3" name="options[gridcol]" value="<?php echo $value_arr['gridcol'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">GridCell Top and Bottom Margin</span>
			</td>
			<td>
				<input type="text" size="3" name="options[grid_margin_top]" value="<?php echo $value_arr['grid_margin_top'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">GridCell Left and Right Margin</span>
			</td>
			<td>
				<input type="text" size="3" name="options[grid_margin_right]" value="<?php echo $value_arr['grid_margin_right'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Auto Slide?</span>
			</td>
			<td>
				<?php
					$chk1 = $chk2 = '';
					if($value_arr['autoplay'] == '0'){
						$chk2 = ' checked="checked"';
					}
					else{
						$chk1 = ' checked="checked"';
					}
				?>
				<input type="radio" name="options[autoplay]" value="1"<?php echo $chk1;?> />Yes
				<input type="radio" name="options[autoplay]" value="0"<?php echo $chk2;?> />No
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Slide interval (ms)</span>
			</td>
			<td>
				<input type="text" size="5" name="options[slide_interval]" value="<?php echo $value_arr['slide_interval'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Show/Hide Prev/Next Buttons</span>
			</td>
			<td>
				<?php
					$chk1 = $chk2 = '';
					if($value_arr['showbutton'] == '0'){
						$chk2 = ' checked="checked"';
					}
					else{
						$chk1 = ' checked="checked"';
					}
				?>
				<input type="radio" name="options[showbutton]" value="1"<?php echo $chk1;?> />Yes
				<input type="radio" name="options[showbutton]" value="0"<?php echo $chk2;?> />No
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Total time for sliding animation (ms)</span>
			</td>
			<td>
				<input type="text" size="5" name="options[slide_speed]" value="<?php echo $value_arr['slide_speed'];?>" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="label">Slide Style</span>
			</td>
			<td>
				<select name="options[slide_style]">
					<option value="<?php echo $value_arr['slide_style'];?>"><?php echo $value_arr['slide_style'];?></option>
					<option value="jswing">jswing</option>
					<option value="easeInQuad">easeInQuad</option>
					<option value="easeOutQuad">easeOutQuad</option>
					<option value="easeInOutQuad">easeInOutQuad</option>
					<option value="easeInCubic">easeInCubic</option>
					<option value="easeOutCubic">easeOutCubic</option>
					<option value="easeInOutCubic">easeInOutCubic</option>
					<option value="easeInQuart">easeInQuart</option>
					<option value="easeOutQuart">easeOutQuart</option>
					<option value="easeInOutQuart">easeInOutQuart</option>
					<option value="easeInQuint">easeInQuint</option>
					<option value="easeOutQuint">easeOutQuint</option>
					<option value="easeInOutQuint">easeInOutQuint</option>
					<option value="easeInSine">easeInSine</option>
					<option value="easeOutSine">easeOutSine</option>
					<option value="easeInOutSine">easeInOutSine</option>
					<option value="easeInExpo">easeInExpo</option>
					<option value="easeOutExpo">easeOutExpo	</option>
					<option value="easeInOutExpo">easeInOutExpo</option>
					<option value="easeInCirc">easeInCirc</option>
					<option value="easeOutCirc">easeOutCirc</option>
					<option value="easeInOutCirc">easeInOutCirc</option>
					<option value="easeInElastic">easeInElastic</option>
					<option value="easeOutElastic">easeOutElastic</option>
					<option value="easeInOutElastic">easeInOutElastic</option>
					<option value="easeInBack">easeInBack</option>
					<option value="easeOutBack">easeOutBack	</option>
					<option value="easeInOutBack">easeInOutBack	</option>
					<option value="easeInBounce">easeInBounce</option>
					<option value="easeOutBounce">easeOutBounce</option>
					<option value="easeInOutBounce">easeInOutBounce</option>
				</select>
			</td>
		</tr>
	</table>
<?php 
	SrizonFBUI::BoxFooter();
	}
?>
<div>
	<span class="label"><?php wp_nonce_field('SrzFbGalleries', 'srjfb_submit');?></span>
	<?php
	if(isset($value_arr['id'])){
		echo '<input type="hidden" name="id" value="'.$value_arr['id'].'" />';
	}
	?>
	<input type="submit" class="button-primary" name="submit" value="Save Gallery" />
</div>
</form>