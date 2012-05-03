<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_Astreintes_Interventions extends MY_Model {

    #var $debug = true;
    var $debug = false;
    var $limit = 0;

    var $oneHour;
    var $jours = array( 1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 7 => 'Dim');

    var $percents = array(
            1   => array( 'J' => 1.00, 'N' => 1.50),
            2   => array( 'J' => 1.00, 'N' => 1.50),
            3   => array( 'J' => 1.00, 'N' => 1.50),
            4   => array( 'J' => 1.00, 'N' => 1.50),
            5   => array( 'J' => 1.00, 'N' => 1.50),
            6   => array( 'J' => 1.25, 'N' => 2.00),
            7   => array( 'J' => 2.00, 'N' => 2.00)
            );

    var $h_night = array();



    function __construct() {

        parent::__construct();

        $this->table_name = 'mcb_astreintes_interventions';
        $this->primary_key = 'mcb_astreintes_interventions.astreinte_intervention_id';
        $this->select_fields = "SQL_CALC_FOUND_ROWS mcb_astreintes_interventions.*";
        $this->order_by = 'mcb_astreintes_interventions.ticket_id ASC';

        $this->h_night = array (
                'start' => $this->mdl_mcb_data->setting('astr_nightly_hours_start'),
                'end'   => $this->mdl_mcb_data->setting('astr_nightly_hours_end')
        );

        $this->load->model('inventory/mdl_inventory');

        $hour_inventory_id = $this->mdl_mcb_data->setting('astr_base_hour_inventory');
        $this->oneHour = $this->mdl_inventory->get_by_id($hour_inventory_id);

    }

    function validate() {
        $this->form_validation->set_rules('ticket_id', $this->lang->line('ticket_id'), 'required');
        $this->form_validation->set_rules('start_date_time', $this->lang->line('start_date_time'), 'required');
        $this->form_validation->set_rules('duration', $this->lang->line('duration'), 'required');

        return parent::validate($this);

    }

    function save() {

        $db_array = parent::db_array();

        $astreinte_intervention_id = uri_assoc('astreinte_intervention_id', 3);
        if (!uri_assoc('astreinte_id', 3)) {

            #$db_array['user_id'] = $this->session->userdata('user_id');

        }

        date_default_timezone_set('Europe/Paris');
        setlocale(LC_TIME, 'fr_FR', 'fra');
        $db_array['start_date_time'] = DateTime::createFromFormat('d/m/Y H:i', $db_array['start_date_time'])->getTimestamp();
        $db_array['astreinte_id'] = uri_assoc('astreinte_id', 3);

        parent::save($db_array, $astreinte_intervention_id);

        $this->regenTimes();
    }

    function delete($params) {

        parent::delete($params);

        $this->db->where('astreinte_interventions_id', $params['astreinte_interventions_id']);
    }

    function get_tickets () {
        $astreinte_id = uri_assoc('astreinte_id', 3);

        if (!$astreinte_id) return;

        $query = $this->db->get_where('mcb_astreintes_interventions', array('astreinte_id' => $astreinte_id));
        #$query = $this->db->get_where('mcb_astreintes_interventions_facturation', "astreinte_id = $astreinte_id");

        return $query;
    }


    function get_details_tickets () {
        $astreinte_id = uri_assoc('astreinte_id', 3);

        $this->db->from('mcb_astreintes_interventions_facturation');
        $this->db->where(array('astreinte_id' => $astreinte_id));
        $this->db->order_by('taux, day_night');
        $query = $this->db->get();
        #$query = $this->db->get_where('mcb_astreintes_interventions_facturation', "astreinte_id = $astreinte_id");

        return $query;
    }

    function get_total_astreinte ($astreinte_id) {
        $this->db->select_sum('amount');
        $this->db->from('mcb_astreintes_interventions_facturation');
        $this->db->where(array('astreinte_id' => $astreinte_id));
        $query = $this->db->get();
        return $query;
    }


    function regenTimes() {

        $astreinte_id = uri_assoc('astreinte_id', 3);

        if ( $this->debug )
            echo "Calculating time for astreinte #$astreinte_id\n";

        // fetch interventions for astreinte
        $this->db->where('astreinte_id', $astreinte_id);
        $this->db->order_by('start_date_time');
        $inters = $this->db->get('mcb_astreintes_interventions')->result();

        // store last seen intervention
        $last_end = false;

        foreach ($inters as $inter) {

            if ( $this->debug )
                echo "\nAnalysing Tk#$inter->ticket_id\n-------------\n\n";

            // ticket duration explode
            $tk_duration = explode (':', $inter->duration);
            $tk_duration_interval = new DateInterval('PT'.$tk_duration[0].'H'.$tk_duration[1].'M');

            // times objects
            $start = new DateTime(); $start->setTimestamp($inter->start_date_time);
            $end   = clone $start; $end->add($tk_duration_interval);

            // debug
            $this->print_time($start,    "Start1 tk");
            $this->print_time($end,      "End    tk");
            $this->print_time($last_end, "L-End  tk");
            $this->print_time($tk_duration_interval, "Ticket time (real)");

            // Check overlap with last seen ticket
            $included = $this->has_overlap_time($last_end, $start, $end);

            // Calc billed end
            $end = $this->calc_facturation_end($start, $end, $last_end, $included);

            // BILLED FOR THIS TICKET
            $this->billed_for_this_ticket($start, $end, $inter->astreinte_intervention_id);

            if ( $this->debug )
                echo "\n";
        }
        $this->regenRepartitionTime();
    }

    function print_time($datetime, $name = '') {
        if ( ! $this->debug )
            return;

        if ($name)
            echo "$name\t\t\t: ";

        if ( !is_object($datetime) ) {
            echo "NOT AN OBJECT\n";
            return;
        }

        $obj_class = get_class($datetime);

        if ('DateTime' == $obj_class) {
            echo $datetime->format('D d M Y  H:i') ."\n";
        } else if ('DateInterval' == $obj_class) {
            echo $datetime->format('%yY %Mm %dd %r%Hh%Im') ."\n";
        } else {
            echo "Can't print objectType: ". $obj_class ."\n";
        }
    }

    function billed_for_this_ticket ($start, $end, $inter_id) {

        $tk_billed_time = $start->diff($end);

        $db_array = array(
                'start_date_time_billed'    => $start->getTimestamp(),
                'duration_billed'           => $tk_billed_time->format('%H:%I')
                );
        $this->db->where('astreinte_intervention_id', $inter_id);
        $this->db->update('mcb_astreintes_interventions', $db_array);

        if ($this->debug) {
            echo "\n= TIME FOR THIS TK\n";
            echo "= start  :";
            $this->print_time($start);
            echo "= end    :";
            $this->print_time($end);
            echo "= billed :";
            $this->print_time($tk_billed_time);
        }

        return $tk_billed_time;
    }

    function calc_facturation_end($start, $end, &$last_end, $included) {
        // toute heure commencée est due ; heure indivisible

        $duration = $start->diff($end);
        $this->print_time($duration, "NEW DIFF");

        $add_hours = ceil($duration->h+$duration->i/60);

        $new_end = clone $start;
        $new_end->add( new DateInterval("PT${add_hours}H") );

        if ($this->debug) {
            echo "\n+ CALCULATING BILL END\n";
            print "+ minutes   : ".($duration->h*60+$duration->i) ."\n";
            print "+ hours     : ".number_format($duration->h+$duration->i/60, 2) ."\n";
            print "+ hours ceil: $add_hours\n";
            print "+ new end ==> ";
            $this->print_time($new_end);
        }

        if ( !$included) {
            if ( $this->debug )
                echo "\nSTORE LAST END\n";

            // Store this TK for next analysing
            $last_end   = clone $new_end;
        }

        return $new_end;
    }

    function has_overlap_time(&$last_end, &$start, &$end) {
        if ( !$last_end ) {
            // Do nothing else
            return false;
        }

        //$overlap   = ($last_end->diff($start)->format('%r') == '-' ? true : false);
        $overlap   = $this->is_included($last_end, $start);
        //$included  = ($last_end->diff($end)->format('%r') == '-' ? true : false);
        $included  = $this->is_included($last_end, $end);

        if ($this->debug && $overlap) {
            echo "\n* WE HAVE AN OVERLAP -> ";
            $this->print_time($last_end->diff($start));
            $this->print_time($start,    "*  check start");
            $this->print_time($last_end, "*  check   end");
        }

        if ($overlap) {
            if ($included) {
                if ($this->debug)
                    print "**** Tk INCLUS DANS LE PRECEDENT...\n";
                $end = clone $start;
            } else {
                $start = clone $last_end;
            }

            if ($this->debug) {
                $this->print_time($start, "*  new   start");
                $this->print_time($end,   "*  new   end  ");
            }
        }

        return $included;
    }



    function regenRepartitionTime () {

        #$this->debug = true;

        $astreinte_id = uri_assoc('astreinte_id', 3);

        // Empty old
        $this->db->query("DELETE FROM mcb_astreintes_interventions_facturation where astreinte_id = $astreinte_id");

        // fetch interventions for astreinte
        $this->db->where('astreinte_id', $astreinte_id);
        $this->db->order_by('start_date_time');
        $inters = $this->db->get('mcb_astreintes_interventions')->result();

        // 
        //$hours = array();

        foreach ($inters as $inter) {
            if ( $this->debug )
                echo "\nAnalysing Tk#$inter->ticket_id\n-------------\n\n";

            if ( $this->debug )
                print_r($inter);

            // ticket duration explode
            $tk_duration = explode (':', $inter->duration_billed);
            $tk_duration_interval = new DateInterval('PT'.$tk_duration[0].'H'.$tk_duration[1].'M');

            // times objects
            $start = new DateTime(); $start->setTimestamp($inter->start_date_time_billed);
            $end   = clone $start; $end->add($tk_duration_interval);

            // debug
            $this->print_time($start,    "Start2 tk");
            $this->print_time($end,      "End    tk");
            $this->print_time($tk_duration_interval, "Ticket time (real)");

            $hours = array();
            $this->split_by_hours($start, $end, $hours, $inter);
            if ( $this->debug )
                print_r($hours);



            if ( $this->debug )
                echo "\n";
        }
        if ( $this->debug )
            die;
    }

    function is_night ($hour) {
        return ( $this->h_night['start'] <= $hour || $hour < $this->h_night['end'] );
    }

    function get_day_repartition ( $start, $duration = false ) {
        if ( $duration ) {
            return $start->add($duration)->format('N');
        } else {
            $new_start = clone $start;
            if ( $new_start->format('H') < $this->h_night['end'])
                $new_start->sub(new DateInterval('P1D'));

            return $new_start->format('N');
        }
    }

    function convert_time_to_dec ($start, $end) {

        $duration = $start->diff($end);
        if ( $this->debug )
            $this->print_time($duration);

        return number_format($duration->h+$duration->i/60, 2);
    }

    function get_next_timezone ($start) {
        $next_timezone = clone $start;
        $hour = $start->format('H');
            if ( $this->debug )
        echo "CHECK HOUR $hour\n";

        if ( $this->is_night($hour) ) {
            if ( $this->debug )
            echo "$hour is NIGHT\n";
            // nuit... attention avant/apres minuit
            $next_timezone->setTime($this->h_night['end'], 0);
            if ( $this->h_night['start'] <= $hour && $hour <= 23 ) {
            if ( $this->debug )
                echo "$hour is NIGHT BUT SAME DAY\n";
                $next_timezone->add(new DateInterval('P1D'));
            } else {
            if ( $this->debug )
                echo "$hour is NIGHT BUT NOT SAME DAY, remove one day\n";
                #$next_timezone->sub(new DateInterval('P1D'));
            }
        } else {
            if ( $this->debug )
            echo "$hour is DAY\n";
            // jour
            $next_timezone->setTime($this->h_night['start'], 0);
        }

        return $next_timezone;
    }

    function is_included ($reference, $compared) {
            if ( $this->debug ) {
        $this->print_time($compared,  "Compare :");
        $this->print_time($reference, "To      :");
        }
        return ($reference->diff($compared)->format('%r') == '-' ? true : false);
    }

    function split_by_hours ($start, $end, &$hours, &$inter) {

//$this->debug = true;

//if ($this->limit++ >= 15)
//    return;

            if ( $this->debug ) {
        print_r($hours);
        echo "\n---------------------------------------------\n";
            $this->print_time($start,    "Start tk");
            $this->print_time($end,      "End   tk");
        echo "\n-----------------------------\n";
        }

        $day = $this->get_day_repartition($start);
        if ( $this->debug )
            echo "DAY IS: $day\n";

        $night = ( $this->is_night($start->format('H')) ? 'N' : 'J');

        if ( ! isset($hours[$day]))
            $hours[$day] = array('J' => 0, 'N' => 0);

        $next_timezone = $this->get_next_timezone ($start);
        $this->print_time($next_timezone, "Next TZ :");

        $percent = $this->percents[$day][$night];

        $ferie = clone $start; $ferie->setTime(0,0);
        if ($this->isFerie($ferie->getTimestamp())) $percent = 2.00;
        $jferie = ($this->isFerie($ferie->getTimestamp()) ? 'F' : '');

        if ( $this->is_included($next_timezone, $end) ) {
            if ( $this->debug )
            echo "INCLUDED\n";

            $count = $this->convert_time_to_dec($start, $end);
            $hours[$day][$night] += $count;

            if ( $this->debug )
                echo 'timestamp start: '.$start->getTimestamp() ."\n";        

            $this->db->query("
            INSERT INTO mcb_astreintes_interventions_facturation (astreinte_intervention_id, astreinte_id, start_date_time, end_date_time, duration, day_night, taux, amount)
            VALUES ($inter->astreinte_intervention_id, $inter->astreinte_id, ". $start->getTimestamp() .", ". $end->getTimestamp() .", $count, '". $this->jours[$day]."/$night$jferie', ". (100*$percent) .", ".($this->oneHour->inventory_unit_price * $percent * $count).")
            ");

        } else {
            if ( $this->debug )
            echo "NOT INCLUDED\n";
            $count = $this->convert_time_to_dec($start, $next_timezone);
            $hours[$day][$night] += $count;

            $this->db->query("
            INSERT INTO mcb_astreintes_interventions_facturation (astreinte_intervention_id, astreinte_id, start_date_time, end_date_time, duration, day_night, taux, amount)
            VALUES ($inter->astreinte_intervention_id, $inter->astreinte_id, ". $start->getTimestamp() .", ". $next_timezone->getTimestamp() .", $count, '". $this->jours[$day]."/$night$jferie', ". (100*$percent) .", ".($this->oneHour->inventory_unit_price * $percent * $count).")
            ");
            $this->split_by_hours($next_timezone, $end, $hours, $inter);
        }

    }

    /**
     * Cette fonction retourne un tableau de timestamp correspondant
     * aux jours fériés en France pour une année donnée.
     */
    function isFerie($day) {
        $year = intval(date('Y', $day));

        $easterDate  = easter_date($year);
        $easterDay   = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear   = date('Y', $easterDate);

        $holidays = array(
                // Dates fixes
                mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
                mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
                mktime(0, 0, 0, 5,  8,  $year),  // Victoire des alliés
                mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
                mktime(0, 0, 0, 8,  15, $year),  // Assomption
                mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
                mktime(0, 0, 0, 11, 11, $year),  // Armistice
                mktime(0, 0, 0, 12, 25, $year),  // Noel

                // Dates variables
                mktime(0, 0, 0, $easterMonth, $easterDay + 2,  $easterYear),
                mktime(0, 0, 0, $easterMonth, $easterDay + 40, $easterYear),
                mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),
                );

        //sort($holidays);

        return in_array($day, $holidays);
    }

}
















?>
