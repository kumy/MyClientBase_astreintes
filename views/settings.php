<dl>
	<dt><?php echo $this->lang->line('astr_hour_base_amount');?></dt>
	<dd>
		<input type="text" name="astr_hour_base_amount" value="<?php echo $this->mdl_mcb_data->setting('astr_hour_base_amount');?>" />
	</dd>
</dl>
<dl>
	<dt><?php echo $this->lang->line('astr_nightly_hours_start');?></dt>
	<dd>
		<input type="text" name="astr_nightly_hours_start" value="<?php echo $this->mdl_mcb_data->setting('astr_nightly_hours_start');?>" />
	</dd>
</dl>
<dl>
	<dt><?php echo $this->lang->line('astr_nightly_hours_end');?></dt>
	<dd>
		<input type="text" name="astr_nightly_hours_end" value="<?php echo $this->mdl_mcb_data->setting('astr_nightly_hours_end');?>" />
	</dd>
</dl>
<dl>
    <dt><?php echo $this->lang->line('astr_dashboard_show_astreintes');?></dt>
    <dd>
        <input type="checkbox" name="astr_dashboard_show_astreintes" value="TRUE" <?php if($this->mdl_mcb_data->setting('astr_dashboard_show_astreintes') == "TRUE"){?>checked<?php }?> />
    </dd>
</dl>

