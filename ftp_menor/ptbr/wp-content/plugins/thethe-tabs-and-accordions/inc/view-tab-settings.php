<fieldset>
  <legend>Main Settings</legend>
  <ul class="thethe-settings-list">
    <li>
      <label>Disable on Frontpage:</label>
      <input type="checkbox" name="frontdisable" value="true"<?php echo $frontdisable;?>/>
<a class="tooltip" href="#">?<span>Check this box to disable this plugin on the frontpage.</span></a>
    </li>
    <li>
      <label>General Style:</label>
      <select name="jqueryui-theme" class="text-field">
        <?php foreach ($jQueryUIThemes as $t) { $selected='';?>
        <?php if ($options['jqueryui-theme'] == $t) $selected = ' selected';?>
        <option <?php echo $selected;?> value="<?php echo $t?>"><?php echo $t?></option>
        <?php }?>
      </select>
<a class="tooltip" href="#">?<span>Select the color theme for this plugin.</span></a>
    </li>
    <li>
      <label>Custom Style:</label>
      <textarea class="text-field" rows="10" name="style"><?php echo htmlspecialchars(stripslashes(base64_decode($options['style'])));?></textarea>
<a class="tooltip" href="#">?<span>Enter the custom styles for this plugin.</span></a>      
    </li>
  </ul>
</fieldset>
