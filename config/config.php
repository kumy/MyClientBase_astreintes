<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

$config = array(
	'module_path'			=>	'astreintes',
	'module_name'			=>	'Astreintes',
	'module_description'		=>	'Calculateur d\'astreintes.',
	'module_author'			=>	'Mathieu Alorent',
	'module_homepage'		=>	'http://www.inktomis.com',
	'module_version'		=>	'0.0.1',
	'module_config'			=>	array(
		'dashboard_widget'	=>	'astreintes/dashboard_widget',
		'settings_view'		=>	'astreintes/astreintes_settings/display',
		'settings_save'		=>	'astreintes/astreintes_settings/save',
		'dashboard_menu'	=>	'astreintes/header_menu'
	)
);

?>
