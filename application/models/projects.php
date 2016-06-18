<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/pclzip.php';

class Projects extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	public function get_list(){
		return $this->db->select('id, name')->from('project')->get()->result();
	}

	private function _project_init($project_name){
		$zipfile = new PclZip(APPPATH . '../assets/codeigniter.zip');
		$extract = $zipfile->extract('/Users/a1/Sites/' . $project_name);
	}

	public function create($project_name){
		self::_project_init($project_name);
		$this->db->query("CREATE DATABASE $project_name");
		
		$project_dir = "/Users/a1/Sites/$project_name/application/config/database.php";
		$database_file_handle = fopen($project_dir, "r");
		$database_config = fread($database_file_handle, filesize($project_dir));
		$database_config = str_replace("'database' => ''", "'database' => '$project_name'", $database_config);
		$database_file_handle = fopen($project_dir, "w+");
		fwrite($database_file_handle, $database_config);

		$project_dir = "/Users/a1/Sites/$project_name/application/config/config.php";
		$config_handle = fopen($project_dir, "r");
		$config_content = fread($config_handle, filesize($project_dir));
		$config_content = str_replace("\$config['base_url'] = ''", "\$config['base_url'] = 'http://localhost/~a1/$project_name'", $config_content);
		$config_handle = fopen($project_dir, "w+");
		fwrite($config_handle, $config_content);

		$this->db->insert('project', array('name' => $project_name));
		return $this->db->insert_id();
	}

	public function get_name($project_id){
		return $this->db->select('name')->from('project')->
			where('id', $project_id)->get()->row()->name;
	}
}

?>