<?php if ($astreintes) {?>

<table style="width: 100%;">
    <tr>
		<?php if (isset($show_astreinte_selector)) { ?><th scope="col" class="first">&nbsp;</th><?php } ?>
		<th scope="col" <?php if (!isset($show_astreinte_selector)) { ?>class="first"<?php } ?>><?php echo $this->lang->line('client');?></th>
		<!--th scope="col"><?php echo $this->lang->line('astreinte_id');?></th-->
		<th scope="col"><?php echo $this->lang->line('start_date');?></th>
		<!--th scope="col"><?php echo $this->lang->line('end_date');?></th-->
		<th scope="col"><?php echo $this->lang->line('title');?></th>
		<th scope="col"><?php echo $this->lang->line('number_of_tickets');?></th>
		<th scope="col"><?php echo $this->lang->line('number_of_tickets_hours');?></th>
		<th scope="col"><?php echo $this->lang->line('amount');?></th>
		<th scope="col"><?php echo $this->lang->line('edit');?></th>
		<th scope="col"><?php echo $this->lang->line('print');?></th>
		<th scope="col" class="last"><?php echo $this->lang->line('delete');?></th>
    </tr>
	<?php foreach ($astreintes as $astreinte) {?>
    <tr>
<?php
$query = $this->mdl_astreintes_interventions->get_total_astreinte($astreinte->astreinte_id);
?>

		<?php if (isset($show_astreinte_selector)) { ?><td class="first"><input type="checkbox" class="astreinte_id_check" name="astreinte_id[]" value="<?php echo $astreinte->astreinte_id; ?>" /></td><?php } ?>
		<td <?php if (!isset($show_astreinte_selector)) { ?>class="first"<?php } ?>><?php echo $astreinte->client_name . " ($astreinte->astreinte_id)";?></td>
		<!--td><?php echo $astreinte->astreinte_id;?></td-->
		<td><?php if($astreinte->start_date){echo format_date($astreinte->start_date);}?></td>
		<!--td><?php if($astreinte->end_date){echo format_date($astreinte->end_date);}?></td-->
		<td><?php echo $astreinte->title;?></td>
		<td><?php echo "<i>$astreinte->nb_tickets</i>"; ?></td>
		<td><?php echo "<i>$astreinte->nb_hours</i>"; ?></td>
		<td><?php 
		foreach ($query->result() as $row) {
			echo display_currency($row->amount);
		}
		?></td>
		<td><?php echo anchor('astreintes/add/astreinte_id/' . $astreinte->astreinte_id, icon('edit'), array('class'=>'edit'));?></td>
		<td><?php echo anchor('astreintes/print_details/astreinte_id/' . $astreinte->astreinte_id, icon('quote'), array('class'=>'print'));?></td>
		<td class="last"><?php echo anchor('astreintes/delete/astreinte_id/' . $astreinte->astreinte_id, icon('delete'), array('class'=>'delete', 'onclick'=>"javascript:if(!confirm('" . $this->lang->line('confirm_delete') . "')) return false"));?></td>
    </tr>
	<?php }?>
</table>

<?php if ($this->mdl_astreintes->page_links) { ?>
<div id="pagination">
	<?php echo $this->mdl_astreintes->page_links; ?>
</div>
<?php } ?>

<?php } else {?>
	<p><?php echo $this->lang->line('no_records_found');?>.</p><br />
<?php }?>
