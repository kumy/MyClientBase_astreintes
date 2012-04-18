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
<dl>
    <dt><?php echo $this->lang->line('astr_forfait_filter_inventory_type');?></dt>
    <dd>
        <select name="astr_forfait_filter_inventory_type" id="astr_forfait_filter_inventory_type">
            <option value=""></option>
            <?php foreach ($inventory_types as $item) { ?>
            <option value="<?php echo $item->inventory_type_id; ?>"
                <?php if ($this->mdl_mcb_data->setting('astr_forfait_filter_inventory_type') == $item->inventory_type_id) { ?>selected="selected"<?php } ?>><?php echo $item->inventory_type; ?>
            </option>
        <?php } ?>
        </select>
    </dd>
</dl>
<dl>
    <dt><?php echo $this->lang->line('astr_base_hour_inventory');?></dt>
    <dd>
        <select name="astr_base_hour_inventory" id="astr_base_hour_inventory">
            <option value=""></option>
            <?php foreach ($inventory_items as $item) { ?>
            <option value="<?php echo $item->inventory_id; ?>"
                <?php if ($this->mdl_mcb_data->setting('astr_base_hour_inventory') == $item->inventory_id) { ?>selected="selected"<?php } ?>><?php echo $item->inventory_name; ?>
            </option>
        <?php } ?>
        </select>
    </dd>
</dl>

