<?php $this->load->view('dashboard/jquery_table_dnd'); ?>

<table style="width: 100%;" id="dnd">

        <tr>
                <th scope="col" style="" class="first"><?php echo $this->lang->line('astreinte_intervention_id'); ?></th>
                <th scope="col" style="" class="col_ticket_id"><?php echo $this->lang->line('ticket_id'); ?></th>
                <th scope="col" style="" class="col_start_date_time"><?php echo $this->lang->line('start_date_time'); ?></th>
                <th scope="col" style="" class="col_end_date_time"><?php echo $this->lang->line('end_date_time'); ?></th>
                <th scope="col"  style="" class="col_duration"><?php echo $this->lang->line('duration'); ?></th>
                <!--th scope="col"  style="" class="col_duration_billed"><?php echo $this->lang->line('duration_billed'); ?></th-->
                <th scope="col"  style="" class="col_night"><?php echo $this->lang->line('day_night'); ?></th>
                <!--th scope="col"  style="" class="col_taux"><?php echo $this->lang->line('taux'); ?></th-->
                <!--th scope="col" style="" class="col_amount"><?php echo $this->lang->line('amount'); ?></th-->
                <th scope="col" class="last" style=""><?php echo $this->lang->line('actions'); ?></th>
        </tr>

        <?php if (isset($interventions)) foreach ($interventions as $intervention) {
                if(!uri_assoc('astreinte_id', 4) OR uri_assoc('astreinte_id', 4) <> $intervention->astreinte_id) { ?>

		<?php
			$durations = explode(':', $intervention->duration);
			$duration = new DateInterval('PT'.$durations[0].'H'.$durations[1].'M');
			$start = new DateTime(); $start->setTimestamp($intervention->start_date_time);
			$end   = new DateTime(); $end->setTimestamp($intervention->start_date_time);
			$end   = date_add($end, $duration);
			$duration_base10        = number_format(round($duration->format('%h') + $duration->format('%I')/60, 2), 2);

		?>

                <tr id="<?php echo $intervention->astreinte_intervention_id; ?>" class="hoverall">
                        <td class="first"><?php echo $intervention->astreinte_intervention_id; ?></td>
                        <td class="col_ticket_id"><?php echo $intervention->ticket_id; ?></td>
                        <td class="col_start_date_time"><?php echo $start->format('d/m/Y H:i'); ?></td>
                        <td class="col_end_date_time"><?php echo $end->format('H:i'); ?></td>
                        <td class="col_duration"><?php echo $duration->format('%H:%I') ." ($intervention->duration_billed)"; ?></td>
                        <!--td class="col_duration_billed"><?php echo $intervention->duration_billed; ?></td-->
                        <td class="col_night"><?php //echo $intervention->day_night; ?>
<?php

$query = $this->mdl_astreintes_interventions->get_details_tickets();

foreach ($query->result() as $row) {
	if ($row->astreinte_intervention_id == $intervention->astreinte_intervention_id) {
		echo "$row->day_night $row->duration ($row->taux%) ".display_currency($row->amount).'<br />';
	}
}

?>
			</td>
                        <!--td class="col_taux"><?php //echo $intervention->taux .'%' ?></td-->
                        <!--td class="col_amount"><?php //echo display_currency(132) ?></td-->
                        <td class="last">
                                <a href="<?php echo site_url('astreintes/ticket/astreinte_id/' . uri_assoc('astreinte_id') . '/astreinte_intervention_id/' . $intervention->astreinte_intervention_id); ?>" title="<?php echo $this->lang->line('edit'); ?>">
                                        <?php echo icon('edit'); ?>
                                </a>
                                <a href="<?php echo site_url('astreintes/delete/astreinte_id/' . uri_assoc('astreinte_id') . '/astreinte_intervention_id/' . $intervention->astreinte_intervention_id); ?>" title="<?php echo $this->lang->line('delete'); ?>" onclick="javascript:if(!confirm('<?php echo $this->lang->line('confirm_delete'); ?>')) return false">
                                        <?php echo icon('delete'); ?>
                                </a>
                        </td>
                </tr>
        <?php } } ?>

</table>
