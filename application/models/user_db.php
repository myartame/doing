<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_db extends CI_Model{
	public function get_db_list($project_id){
		$custom_db = $this->load->database(array(
			'hostname' => '127.0.0.1',
			'username' => 'root',
			'password' => '486255',
			'database' => $this->db->select('name')->from('project')->
				where('id', $project_id)->get()->row()->name,
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE
		), true);

		$return_arr = array();
		$table_list = $custom_db->list_tables();
		foreach ($table_list as $value) {
			$return_arr[$value] = $custom_db->list_fields($value);
		}

		return $return_arr;
	}
}

?>