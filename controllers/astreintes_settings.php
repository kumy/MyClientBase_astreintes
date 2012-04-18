<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Astreintes_Settings extends Admin_Controller {

    function display() {

        $this->load->model(
                array(
                    'inventory/mdl_inventory_types',
                    'inventory/mdl_inventory',
                    )
                );

        $params = array(
                'select'    =>  '*',
                );

        $inventory_types = $this->mdl_inventory_types->get($params);

        $params = array(
                'select'    =>  '*',
                );

        $inventory_items = $this->mdl_inventory->get($params);

        $data = array(
                'inventory_types'     =>  $inventory_types,
                'inventory_items'     =>  $inventory_items,
                );

        $this->load->view('settings', $data);

	}

	function save() {

		/*
		 * As per the config file, this function will
		 * execute when the system settings are saved.
		 */

        $this->mdl_mcb_data->save('astr_forfait_filter_inventory_type', $this->input->post('astr_forfait_filter_inventory_type'));
        $this->mdl_mcb_data->save('astr_base_hour_inventory', $this->input->post('astr_base_hour_inventory'));
        
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
