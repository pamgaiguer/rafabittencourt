<div class="wrap">
<?php if(!empty($this->message)):?>
<?php echo $this->message; ?>
<?php endif; ?>
	<div id="poststuff">
	<div id="post-body">							
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">	    	    
		<div class="postbox">
    		<h3><label for="title">General Settings</label></h3>    		
			<div class="inside">
			    <table width="100%" border="0" cellspacing="0" cellpadding="6">
			    	<tbody>
			    	<tr valign="top">
					    <td width="25%" align="left">
					    	<strong>Album URL</strong>
				    	</td>
				    	<td align="left">
				    	<input type="text" id="setting-album-url" name="setting[album_url]" size="100" value="<?php echo stripslashes($setting['album_url']);?>">
			    		<p class="description">Go To Your Facebook Album page that is publicly shared.Scroll down to the bottom of the page where you'll Something like the following:<img src="<?php echo $this->plugin_url;?>/images/facebook-link.png" />
                        <br/>Copy the link address and paste in the text field above.</p>
				    	</td>
				    </tr>
			    	<tr valign="top">
					    <td width="25%" align="left">
					    	<strong>Generated Shortcode</strong>
				    	</td>
				    	<td align="left">
				    	<input type="text" id="setting-shortcode" readonly="readonly" name="setting[shortcode]" size="100" value="<?php echo stripslashes($setting['shortcode']);?>" onclick="this.select();" onfocus="this.select();">
			    		<p class="description">When You press The "Generate Shortcode " Button, The shortcode will appear in the text field above.</p>
				    	</td>
				    </tr>

				   </tbody></table>	
			</div>	    	    
			<div class="submit" style="margin:15px;">
				<input type="submit" class="button-primary" id="<?php echo $this->plugin_id;?>_setting_submit" name="<?php echo $this->plugin_id;?>_setting_submit" value="Generate Shortcode">  
			</div>			
		</div>
	    </form>	    
	</div>
	</div>
</div>
