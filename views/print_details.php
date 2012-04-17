<?php if ($astreinte) {?>

    <h3><?php
    echo $this->lang->line('astreinte_id');
    echo $astreinte->astreinte_id;
    ?></h3>
    <h4><?php
    echo $astreinte->title;
    ?></h4>

From: <?php echo strftime('%d-%m-%y', $astreinte->start_date); ?><br />
To: <?php echo strftime('%d-%m-%y', $astreinte->end_date); ?><br />
<?php if ($astreinte->description) echo 'Comments: '.$astreinte->description; ?><br />
<br />

<?php //print_r($astreinte); ?>
<table style="width: 100%;" border=1>
    <tr>
		<th scope="col"><?php echo $this->lang->line('astreinte_intervention_id');?></th>
		<th scope="col"><?php echo $this->lang->line('ticket_id');?></th>
		<th scope="col"><?php echo $this->lang->line('start_date');?></th>
		<th scope="col"><?php echo $this->lang->line('duration');?></th>
		<th scope="col"><?php echo $this->lang->line('100_percent');?></th>
		<th scope="col"><?php echo $this->lang->line('125_percent');?></th>
		<th scope="col"><?php echo $this->lang->line('150_percent');?></th>
		<th scope="col"><?php echo $this->lang->line('200_percent');?></th>
    </tr>
<?php $hours_sums = array( 100 => 0, 125 => 0, 150 => 0, 200 => 0 ); ?>
<?php foreach ($astreinte_intervention as $inter) {?>
    <?php //print_r($inter); ?>
    <tr>
		<td><?php echo $inter->astreinte_intervention_id; ?></td>
		<td><?php echo $inter->ticket_id; ?></td>
		<td><?php echo strftime('%d-%m-%y %H:%M', $inter->start_date_time); ?></td>
		<td><?php echo $inter->duration; ?> (<?php echo $inter->duration_billed; ?>)</td>
    <?php
    $sorted_taux = array();

    foreach ($astreinte_intervention_facturation as $fact) {
        if ($fact->astreinte_intervention_id == $inter->astreinte_intervention_id) {
            $sorted_taux[$fact->taux] = $fact;
            $hours_sums[$fact->taux] += $fact->duration;
        }
    }

    foreach ( array(100, 125, 150, 200) as $taux ) {
    ?>
		<td><?php
        if (isset( $sorted_taux[$taux] )) {
            if ( 0 == $sorted_taux[$taux]->duration) {
                echo '<small><i>included in the earlier statement</i></small>';
            } else {
                echo $sorted_taux[$taux]->duration;
                echo ' <small>('.$sorted_taux[$taux]->day_night;
                echo ' - '.strftime('%H:%M', $sorted_taux[$taux]->start_date_time).' - '.strftime('%H:%M', $sorted_taux[$taux]->end_date_time).')</small>';
            }
        } else
            echo '&nbsp;';
        ?></td>
    <?php }?>
    </tr>
    <?php }?>
    <tr>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <?php
            foreach ( $hours_sums as $taux => $amount ) {
                echo '<td>'. number_format($hours_sums[$taux], 2) .'</td>';
            }
        ?>
    </tr>
</table>

<?php } else {?>
	<p><?php echo $this->lang->line('no_records_found');?>.</p><br />
<?php }?>
