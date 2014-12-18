<?php

global $user_ID, $wp_roles;
$permissions = $this -> get_option('permissions');
$cu = wp_get_current_user();

?>

<?php if (!empty($cu -> roles) && in_array('administrator', $cu -> roles)) : ?>    
    <table class="form-table">
    	<thead>
    		<tr>
    			<th></th>
    			<?php foreach ($wp_roles -> role_names as $role_name) : ?>
    				<th style="font-weight:bold; text-align:center;"><?php echo esc_attr(stripslashes($role_name)); ?></th>
    			<?php endforeach; ?>
    		</tr>
    	</thead>
        <tbody>
        	<?php $class = false; ?>
            <?php foreach ($this -> sections as $section_key => $section_menu) : ?>
                <tr class="<?php echo $class = (empty($class)) ? 'arow' : ''; ?>">
                    <th style="white-space:nowrap; text-align:right;"><label for="perm_<?php echo $section_key; ?>"><?php echo GalleryHtmlHelper::section_name($section_key); ?></label></th>
                	<?php foreach ($wp_roles -> role_names as $role_key => $role_name) : ?>
                		<td style="text-align:center;"><label><input <?php echo (!empty($permissions[$section_key]) && in_array($role_key, $permissions[$section_key])) ? 'checked="checked"' : ''; ?> type="checkbox" name="permissions[<?php echo $section_key; ?>][]" value="<?php echo esc_attr(stripslashes($role_key)); ?>" id="<?php echo $section_key; ?>_<?php echo $role_key; ?>" /></label></td>
                	<?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
	<p class="howto"><?php _e('Only the administrator role can edit roles/permissions.', $this -> plugin_name); ?>
<?php endif; ?>