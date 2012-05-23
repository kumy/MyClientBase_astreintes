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
    $sorted_taux = array( 100 => array(), 125 => array(), 150 => array(), 200 => array() );

    foreach ($astreinte_intervention_facturation as $fact) {
        if ($fact->astreinte_intervention_id == $inter->astreinte_intervention_id) {
            array_push($sorted_taux[$fact->taux], $fact);
            $hours_sums[$fact->taux] += $fact->duration;
        }
    }

    foreach ( array(100, 125, 150, 200) as $taux ) {
    ?>
		<td><?php
        if ( sizeof( $sorted_taux[$taux] )) {
            foreach ( $sorted_taux[$taux] as $a_chunck ) {
                if ( 0 == $a_chunck->duration) {
                    echo '<small><i>included in the earlier statement</i></small><br />';
                } else {
                    echo $a_chunck->duration;
                    echo ' <small>('.$a_chunck->day_night;
                            echo ' - '.strftime('%H:%M', $a_chunck->start_date_time).' - '.strftime('%H:%M', $a_chunck->end_date_time).')</small><br />';
                }
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
<br />
<?php

$width = 1500;
$timeline_width = $astreinte->end_date - $astreinte->start_date +86400;
$time_unit = $timeline_width / $width;


function getTimes ($astreinte, $start, $duration, $width, $timeline_width, $time_unit) {

    $hours = preg_split('/:/', $duration);
    $ticket_start_time = new DateTime(); $ticket_start_time->setTimestamp($start);
    $ticket_end_time   = clone $ticket_start_time; $ticket_end_time->add(new DateInterval("PT$hours[0]H$hours[1]M"));

    $ticket_start = round (($start - $astreinte->start_date) / $time_unit);
    $ticket_width = round (($ticket_end_time->getTimestamp() - $start) / $time_unit);

    return array( 'start' => $ticket_start, 'end' => $ticket_width);
}

echo "<div style='width: ".(9+$width)."px; height: 22px; border: 0px solid red; position: relative;'>";

$nbday = ($astreinte->end_date - $astreinte->start_date +86400) / 86400;
for ( $i = 1; $i <= $nbday; $i++) {
  echo "<div style='float: left; width: ".round(6* 3600 /$time_unit)."px; height: 22px; background-color: blue'></div>";
  echo "<div style='float: left; width: ".round(16*3600 /$time_unit)."px; height: 22px; background-color: orange'></div>";
  echo "<div style='float: left; width: ".round(2* 3600 /$time_unit)."px; height: 22px; background-color: cyan'></div>";
}

foreach ($astreinte_intervention as $inter) {

    $tktimebilled = getTimes ($astreinte, $inter->start_date_time_billed, $inter->duration_billed, $width, $timeline_width, $time_unit);
    echo "<div style='position: absolute; background-color: green; top: 3px; height: 17px; border: 0px solid green; left: ". $tktimebilled['start'] ."px; width: ". $tktimebilled['end'] ."px;'>";
    $tktime = getTimes ($astreinte, $inter->start_date_time, $inter->duration, $width, $timeline_width, $time_unit);
    echo "<div title='$inter->ticket_id' style='position: relative; background-color: yellow; top: 2; height: 13px; border: 0px solid yellow; left: ". ($tktime['start'] - $tktimebilled['start']) ."px; width: ". $tktime['end'] ."px;'>";
    echo "<div style='position: absolute; background-color: red; top: 1; height: 11px; left: 1px; width: ". ($tktime['end']-2) ."px;'>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}
echo "</div>";
?>
<?php } else {?>
	<p><?php echo $this->lang->line('no_records_found');?>.</p><br />
<?php }?>
