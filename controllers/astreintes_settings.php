<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Astreintes_Settings extends Admin_Controller {

	function display() {

		$this->load->view('settings');

	}

	function save() {

		/*
		 * As per the config file, this function will
		 * execute when the system settings are saved.
		 */

        $this->mdl_mcb_data->save('astr_hour_base_amount', $this->input->post('astr_hour_base_amount'));
        
        $this->mdl_mcb_data->save('astr_nightly_hours_start', $this->input->post('astr_nightly_hours_start'));
        $this->mdl_mcb_data->save('astr_nightly_hours_end', $this->input->post('astr_nightly_hours_end'));

        if ($this->input->post('astr_dashboard_show_astreintes')) {
            $this->mdl_mcb_data->save('astr_dashboard_show_astreintes', "TRUE");
        } else {
            $this->mdl_mcb_data->save('astr_dashboard_show_astreintes', "FALSE");
        }
	}

}

?>
