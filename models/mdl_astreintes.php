<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_Astreintes extends MY_Model {

	function __construct() {

		parent::__construct();

		$this->table_name = 'mcb_astreintes';

		$this->primary_key = 'mcb_astreintes.astreinte_id';

		$this->select_fields = "
		SQL_CALC_FOUND_ROWS mcb_astreintes.*,
		mcb_clients.client_name,
		mcb_users.last_name AS user_last_name,
		mcb_users.first_name AS user_first_name,
		count(astreinte_intervention_id) as nb_tickets,
        format(sum(substring(duration, 1,2) + substring(duration, 4,2)/60), 2) as nb_hours
		";

		$this->order_by = 'mcb_astreintes.end_date, mcb_astreintes.astreinte_id DESC';

		$this->group_by= array(
			'mcb_astreintes.astreinte_id'
		);

		$this->joins = array(
			'mcb_astreintes_interventions'	=>	array( 'mcb_astreintes_interventions.astreinte_id = mcb_astreintes.astreinte_id', 'LEFT'),
			'mcb_clients'			=>	'mcb_clients.client_id = mcb_astreintes.client_id',
			'mcb_users'			=>	'mcb_users.user_id = mcb_astreintes.user_id'
		);

	}

	function validate() {

		$this->form_validation->set_rules('client_id', $this->lang->line('client'), 'required');
		$this->form_validation->set_rules('inventory_id', $this->lang->line('inventory_id'), 'required');
		$this->form_validation->set_rules('start_date', $this->lang->line('start_date'), 'required');
		$this->form_validation->set_rules('end_date', $this->lang->line('end_date'), 'required');
		$this->form_validation->set_rules('title', $this->lang->line('title'), 'required');
		$this->form_validation->set_rules('description', $this->lang->line('description'));

		return parent::validate();

	}

	function save() {

		$db_array = parent::db_array();

		if (!uri_assoc('astreinte_id', 3)) {

			$db_array['user_id'] = $this->session->userdata('user_id');

		}

		$db_array['end_date'] = strtotime(standardize_date($db_array['end_date']));
		$db_array['start_date'] = strtotime(standardize_date($db_array['start_date']));

		parent::save($db_array, uri_assoc('astreinte_id', 3));

	}


	function prep_validation($key) {

		parent::prep_validation($key);

		if (!$_POST) {

			if ($this->form_value('end_date')) {
				$this->set_form_value('end_date', format_date($this->form_value('end_date')));
			}

			if ($this->form_value('complete_date')) {
				$this->set_form_value('complete_date', format_date($this->form_value('complete_date')));
			}

			if ($this->form_value('start_date')) {
				$this->set_form_value('start_date', format_date($this->form_value('start_date')));
			}

		}

	}

	function delete($params) {

		parent::delete($params);

		$this->db->where('astreinte_id', $params['astreinte_id']);
		$this->db->delete('mcb_astreintes_invoices');

		$this->db->where('astreinte_id', $params['astreinte_id']);
		$this->db->delete('mcb_astreintes_interventions');

		$this->db->where('astreinte_id', $params['astreinte_id']);
		$this->db->delete('mcb_astreintes_interventions_facturation');

	}

}

?>
