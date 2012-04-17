<dl>
	<dt><?php echo $this->lang->line('nightly_hours_start');?></dt>
	<dd>
		<input type="text" name="nightly_hours_start" value="<?php echo $this->mdl_mcb_data->setting('nightly_hours_start');?>" />
	</dd>
</dl>
<dl>
	<dt><?php echo $this->lang->line('nightly_hours_end');?></dt>
	<dd>
		<input type="text" name="nightly_hours_end" value="<?php echo $this->mdl_mcb_data->setting('nightly_hours_end');?>" />
	</dd>
</dl>
<dl>
    <dt><?php echo $this->lang->line('dashboard_show_astreintes');?></dt>
    <dd>
        <input type="checkbox" name="dashboard_show_astreintes" value="TRUE" <?php if($this->mdl_mcb_data->setting('dashboard_show_astreintes') == "TRUE"){?>checked<?php }?> />
    </dd>
</dl>

