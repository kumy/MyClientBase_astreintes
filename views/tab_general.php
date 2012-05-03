<div class="left_box">
<dl>
<dt><label>* <?php echo $this->lang->line('client');?>: </label></dt>
<dd>
<select name="client_id">
<?php foreach ($clients as $client) {?>
	<option value="<?php echo $client->client_id;?>" <?php if($this->mdl_astreintes->form_value('client_id') == $client->client_id) {?>selected<?php }?>><?php echo $client->client_name;?></option>
		<?php }?>
		</select>
		</dd>
		</dl>

		<dl>
		<dt><label>* <?php echo $this->lang->line('forfait');?>: </label></dt>
		<dd>
		<select name="inventory_id" id="inventory_id">
		<option value=""><?php echo $this->lang->line('choose_astreinte_forfait'); ?></option>

		<?php foreach ($inventory_items as $item) { ?>
			<option value="<?php echo $item->inventory_id; ?>"
				<?php if ($this->mdl_astreintes->form_value('inventory_id') == $item->inventory_id) { ?>selected="selected"<?php } ?>><?php echo $item->inventory_name; ?>
			</option>
		<?php } ?>
		</select>
		</dd>
		</dl>

		<dl>
		<dt><label>* <?php echo $this->lang->line('start_date');?>: </label></dt>
		<dd><input class="datepicker" type="text" name="start_date" value="<?php echo $this->mdl_astreintes->form_value('start_date');?>" /></dd>
		</dl>

		<dl>
		<dt><label>* <?php echo $this->lang->line('end_date');?>: </label></dt>
		<dd><input class="datepicker" type="text" name="end_date" value="<?php echo $this->mdl_astreintes->form_value('end_date');?>" /></dd>
		</dl>

		<dl>
		<dt><label>* <?php echo $this->lang->line('title');?>: </label></dt>
		<dd><input id="title" type="text" name="title" value="<?php echo $this->mdl_astreintes->form_value('title');?>" /></dd>
		</dl>

		<dl>
		<dt><label><?php echo $this->lang->line('description');?>: </label></dt>
		<dd><textarea id="description" name="description" rows="10" cols="50"><?php echo $this->mdl_astreintes->form_value('description');?></textarea></dd>
		</dl>

		<input type="submit" id="btn_submit" name="btn_submit" value="<?php echo $this->lang->line('submit');?>" />
		<!--input type="submit" id="btn_submit_and_create_invoice" name="btn_submit_and_create_invoice" value="<?php echo $this->lang->line('submit_and_create_invoice');?>" /-->
		<input type="submit" id="btn_cancel" name="btn_cancel" value="<?php echo $this->lang->line('cancel');?>" />

</div>

<div class="right_box">
<?php

$hours_of_astreinte = 0;
$hours_of_astreinte_billed = 0;
$nb_of_tickets = 0;
$hours_of_astreinte_h = '0h00';
$hours = array();
$amount = 0;


if ($astreinte_id) {
	$query = $this->mdl_astreintes_interventions->get_tickets();

	if ( is_object( $query ) ) {
		foreach ($query->result() as $row) {
			$tmp_hour = explode(':', $row->duration);
			$hours_of_astreinte += number_format($tmp_hour[0]+$tmp_hour[1]/60, 2);
			$tmp_hour = explode(':', $row->duration_billed);
			$hours_of_astreinte_billed += number_format($tmp_hour[0]+$tmp_hour[1]/60, 2);
		}

		$nb_of_tickets = $query->num_rows;
		$hours_of_astreinte_h = floor($hours_of_astreinte);
		$hours_of_astreinte_m = ($hours_of_astreinte-floor($hours_of_astreinte) != 0 ? round(60*($hours_of_astreinte-floor($hours_of_astreinte))) : '00');
		$hours_of_astreinte_h .= 'h'.$hours_of_astreinte_m;


		$query = $this->mdl_astreintes_interventions->get_details_tickets();
		if ( is_object( $query ) )
			foreach ($query->result() as $row) {
				//print_r($row);
				if ( ! isset($hours[$row->taux]) ) $hours[$row->taux] = 0;
				$hours[$row->taux] += $row->duration;
				$amount += $row->amount;
			}
	}
}
?>
        <dl>
                <dt><label><?php echo $this->lang->line('astreinte_tickets_number'); ?>: </label></dt>
                <dd><?php echo $nb_of_tickets; ?></dd>
        </dl>

        <dl>
                <dt><label><?php echo $this->lang->line('astreinte_hours_number'); ?>: </label></dt>
                <dd><?php echo $hours_of_astreinte.' ('.$hours_of_astreinte_h.')'; ?></dd>
        </dl>

        <dl>
                <dt><label><?php echo $this->lang->line('astreinte_hours_number_billed'); ?>: </label></dt>
                <dd><?php echo $hours_of_astreinte_billed; ?>h00</dd>
        </dl>

<?php foreach ($hours as $taux => $hour) { ?>
        <dl>
                <dt><label><?php echo $this->lang->line('hour')." $taux%"; ?>: </label></dt>
                <dd><?php echo $hour; ?></dd>
        </dl>
<?php } ?>
        <dl>
<?php
$astreinte_total = $amount + ( is_object($forfait) ? $forfait->inventory_unit_price : 0 );
?>
                <dt><label><?php echo $this->lang->line('amount'); ?>: </label></dt>
                <dd><?php echo display_currency($amount); ?> + prime <?php if (is_object($forfait)) echo display_currency($forfait->inventory_unit_price); ?> (<?php echo display_currency($astreinte_total); ?>)</dd>
        </dl>

</div>
<div style="clear: both;">&nbsp;</div>
