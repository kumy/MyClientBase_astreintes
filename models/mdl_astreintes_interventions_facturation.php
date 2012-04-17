<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_Astreintes_Interventions_Facturation extends MY_Model {

	function __construct() {

		parent::__construct();

		$this->table_name = 'mcb_astreintes_interventions_facturation';

		$this->primary_key = 'mcb_astreintes_interventions_facturation.astreinte_intervention_facturation_id';

		$this->select_fields = "SQL_CALC_FOUND_ROWS mcb_astreintes_interventions_facturation.*";

		$this->order_by = 'astreinte_intervention_facturation_id ASC';

	}


}

?>
