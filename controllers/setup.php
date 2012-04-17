<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Setup extends Admin_Controller {

	function __construct() {

		parent::__construct(TRUE);

	}

	function index() {

	}

	function install() {

		$queries = array(
			"CREATE TABLE IF NOT EXISTS `mcb_astreintes_interventions_facturation` (
			`astreinte_intervention_facturation_id` int(11) NOT NULL AUTO_INCREMENT,
			`astreinte_intervention_id` int(11) NOT NULL,
			`astreinte_id` int(11) NOT NULL,
			`start_date_time` varchar(25) NOT NULL DEFAULT '',
			`duration` varchar(25) NOT NULL DEFAULT '',
			`day_night` varchar(25) NOT NULL DEFAULT '',
			`taux` int(3) NOT NULL DEFAULT '100',
			`amount` int(6) NOT NULL DEFAULT '0',
			PRIMARY KEY (`astreinte_intervention_facturation_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `mcb_astreintes_interventions` (
			`astreinte_intervention_id` int(11) NOT NULL AUTO_INCREMENT,
			`astreinte_id` int(11) NOT NULL,
			`ticket_id` int(11) NOT NULL,
			`start_date_time` varchar(25) NOT NULL DEFAULT '',
			`duration` varchar(25) NOT NULL DEFAULT '',
			`start_date_time_billed` varchar(25) NOT NULL DEFAULT '',
			`duration_billed` varchar(25) NOT NULL DEFAULT '',
			PRIMARY KEY (`astreinte_intervention_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `mcb_astreintes` (
			`astreinte_id` int(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(11) NOT NULL,
			`client_id` int(11) NOT NULL,
			`inventory_id` int(11) NOT NULL,
			`start_date` varchar(25) NOT NULL DEFAULT '',
			`end_date` varchar(25) NOT NULL DEFAULT '',
			`complete_date` varchar(25) NOT NULL DEFAULT '',
			`title` varchar(255) NOT NULL DEFAULT '',
			`description` longtext NOT NULL,
			PRIMARY KEY (`astreinte_id`),
			KEY `user_id` (`user_id`,`client_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",

			"CREATE TABLE IF NOT EXISTS `mcb_astreintes_invoices` (
			`astreinte_invoice_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`astreinte_id` INT NOT NULL ,
			`invoice_id` INT NOT NULL ,
			INDEX ( `astreinte_id` , `invoice_id` )
			) ENGINE = MYISAM DEFAULT CHARSET=utf8;"
		);

		foreach ($queries as $query) {

			$this->db->query($query);

		}

	}

	function uninstall() {

		$queries = array(
			"DROP TABLE IF EXISTS `mcb_astreintes`",
			"DROP TABLE IF EXISTS `mcb_astreintes_invoices`",
			"DROP TABLE IF EXISTS `mcb_astreintes_interventions`"
		);

		foreach ($queries as $query) {

			$this->db->query($query);

		}

	}

	function upgrade() {

		$installed_version = $this->mdl_mcb_modules->custom_modules['astreintes']->module_version;

#		if ($installed_version < '0.2.6') {
#			$this->u026();
#		}
#
#		elseif ($installed_version == '0.9.2') {
#			$this->u093();
#		}

	}

#	function u026() {
#
#		$this->db->set('complete_date', '');
#		$this->db->where('complete_date', 0);
#		$this->db->update('mcb_astreintes');
#
#		$this->db->set('due_date', '');
#		$this->db->where('due_date', 0);
#		$this->db->update('mcb_astreintes');
#
#		$this->db->set('module_version', '0.2.6');
#		$this->db->where('module_path', 'astreintes');
#		$this->db->update('mcb_modules');
#
#	}

}

?>
