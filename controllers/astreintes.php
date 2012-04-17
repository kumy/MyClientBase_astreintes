<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Astreintes extends Admin_Controller {

	function __construct() {

		parent::__construct();

		if (!$this->mdl_mcb_modules->check_enable('astreintes')) {

			redirect('dashboard');

		}

		$this->load->model('mdl_astreintes');
		$this->load->model('astreintes/mdl_astreintes_interventions');
		$this->load->model('astreintes/mdl_astreintes_interventions_facturation');

	}

	function index() {

		$this->_post_handler();

		$this->redir->set_last_index();

		$params = array(
			'limit'		=>	20,
			'paginate'	=>	TRUE,
			'page'		=>	uri_assoc('page', 3)
		);

		$data = array(
			'astreintes'			=>	$this->mdl_astreintes->get($params),
			#'show_astreinte_selector'	=>	TRUE
		);

		$this->load->view('index', $data);

	}

	function print_details() {

		$this->_post_handler();
		$this->redir->set_last_index();
        $astreinte_id = uri_assoc('astreinte_id', 3);

		$params1 = array(
            'where' => array ( 'mcb_astreintes.astreinte_id' => $astreinte_id )
		);

		$params2 = array(
            'where' => array ( 'astreinte_id' => $astreinte_id )
		);

		$params3 = array(
            'where' => array ( 'astreinte_id' => $astreinte_id ),
            'order_by' => 'taux'
		);

		$data = array(
			'astreinte'			                         =>	$this->mdl_astreintes->get($params1),
			'astreinte_intervention'			        =>	$this->mdl_astreintes_interventions->get($params2),
			'astreinte_intervention_facturation'		=>	$this->mdl_astreintes_interventions_facturation->get($params3),
			#'show_astreinte_selector'	=>	TRUE
		);

		$this->load->view('print_details', $data);

	}

    function dashboard_widget() {

        if ($this->mdl_mcb_data->setting('dashboard_show_astreintes') == "TRUE") {

            $params = array(
                    'select'   =>  'sum(amount) as total',
                    'order_by' =>  'astreinte_intervention_facturation_id'
                    );

            $data = array(
                    'astreintes' =>  $this->mdl_astreintes_interventions_facturation->get($params)
                    );

            $this->load->view('dashboard_widget', $data);
        }
    }


	function add() {

		$this->_post_handler();
		$this->load->helper('form');
		$this->load->helper('text');
		$this->load->model('clients/mdl_clients');
		$this->load->model('inventory/mdl_inventory');

		$astreinte_id = uri_assoc('astreinte_id', 3);
		$tab_index = ($this->session->flashdata('tab_index')) ? $this->session->flashdata('tab_index') : 0;

		if (!$this->mdl_astreintes->validate()) {

			$param_forfait_price = array();

			if (!$_POST AND $astreinte_id) {
				$this->mdl_astreintes->prep_validation($astreinte_id);
				$param_forfait_price = array(
						'where' => array( 'mcb_inventory.inventory_id' => $this->mdl_astreintes->form_values['inventory_id'])
						);
			} elseif (!$_POST AND !$astreinte_id) {
				$this->mdl_astreintes->set_form_value('start_date', format_date(time()));
			}

			$params_inventory = array(
					'where'		=>	array( 'mcb_inventory.inventory_type_id' => 3)
					);

			$params = array(
					'where'		=>	array( 'astreinte_id' => $astreinte_id),
					'order_by'	=> 	'start_date_time'
				       );

			$data = array(
				'tab_index'		=> $tab_index,
				'astreinte_id'		=> $astreinte_id,
				'inventory_items' 	=> $this->mdl_inventory->get($params_inventory),
				'forfait'		=> $this->mdl_inventory->get($param_forfait_price),
				'interventions'		=> $this->mdl_astreintes_interventions->get($params),
				'clients'		=> $this->mdl_clients->get()
			);

			$this->load->view('add', $data);
		} else {
			$this->mdl_astreintes->save();

			if (!$astreinte_id) {
				$astreinte_id = $this->db->insert_id();
			}

			$params_inventory = array(
					'where'		=>	array( 'mcb_inventory.inventory_type_id' => 3)
					);
			$param_forfait_price = array(
					'where' => array( 'mcb_inventory.inventory_id' => $this->mdl_astreintes->form_values['inventory_id'])
					);

			$data = array(
					'tab_index'		=> $tab_index,
					'inventory_items' 	=> $this->mdl_inventory->get($params_inventory),
					'clients'		=> $this->mdl_clients->get(),
					'forfait'		=> $this->mdl_inventory->get($param_forfait_price),
					'astreinte_id'		=> $astreinte_id
				     );

			#$this->load->view('add', $data);
			$this->redir->redirect('astreintes/add/astreinte_id/'.$astreinte_id);

		}

	}

	function ticket() {
		$this->_post_handler();

		$astreinte_id = uri_assoc('astreinte_id', 3);
		$astreinte_intervention_id = uri_assoc('astreinte_intervention_id', 3);


		$this->load->helper('form');
		$this->load->helper('text');

		if (!$this->mdl_astreintes_interventions->validate()) {
			$this->load->helper('form');
			$this->load->helper('text');

			if (!$_POST AND $astreinte_id) {
				$this->mdl_astreintes_interventions->prep_validation($astreinte_intervention_id);
			}

			$data = array(
					'astreinte_id'			=> $astreinte_id,
					'astreinte_intervention_id'	=> $astreinte_intervention_id
				     );

			$this->load->view('ticket', $data);
		} else {
			$this->mdl_astreintes_interventions->save();

			if (!$astreinte_id) {
				$astreinte_id = $this->db->insert_id();
			}

			$this->session->set_flashdata('tab_index', 1); # ?????
			#redirect($this->session->userdata('last_index'));
			$this->redir->redirect('astreintes/add/astreinte_id/'.$astreinte_id);
		}
	}


	function delete() {

		if (uri_assoc('astreinte_intervention_id', 3)) {
			$this->mdl_astreintes_interventions->delete(array('astreinte_intervention_id'=>uri_assoc('astreinte_intervention_id', 3)));
			$this->session->set_flashdata('tab_index', 1); # ?????
			$this->redir->redirect('astreintes/add/astreinte_id/'.uri_assoc('astreinte_id'));
		} else if (uri_assoc('astreinte_id', 3)) {
			$this->mdl_astreintes->delete(array('astreinte_id'=>uri_assoc('astreinte_id', 3)));
		}

		$this->redir->redirect('astreintes');
		#redirect($this->session->userdata('last_index'));

	}

	function save_settings() {

		if ($this->input->post('dashboard_show_open_astreintes')) {

			$this->mdl_mcb_data->save('dashboard_show_open_astreintes', "TRUE");

		}

		else {

			$this->mdl_mcb_data->save('dashboard_show_open_astreintes', "FALSE");

		}

	}

	function _post_handler() {

		if ($this->input->post('btn_add')) {
			redirect('astreintes/add');
		}

		elseif ($this->input->post('btn_add_new_ticket')) {
			redirect('astreintes/ticket/astreinte_id/' . uri_assoc('astreinte_id'));
		}

		elseif ($this->input->post('btn_cancel')) {
			redirect('astreintes/index');
		}

		elseif ($this->input->post('btn_return_to_list')) {
			redirect('astreintes/index');
		}

		elseif ($this->input->post('btn_return_to_astreinte')) {
			$this->session->set_flashdata('tab_index', 1); # ?????
			redirect('astreintes/add/astreinte_id/' . uri_assoc('astreinte_id'));
		}

		elseif ($this->input->post('btn_regen_ticket_times')) {
			$this->mdl_astreintes_interventions->regenTimes();
			$this->session->set_flashdata('tab_index', 1); # ?????
			redirect('astreintes/add/astreinte_id/' . uri_assoc('astreinte_id'));
		}
	}
}

?>
