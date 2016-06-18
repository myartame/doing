<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Project extends REST_Controller{
	public function __construct(){
		parent::__construct();

		$this->load->model('projects', '', true);
	}

	public function index_get(){
		$this->load->view("project.html", array(
			'project' => $this->projects->get_list()
		));
	}

	public function create_post(){
		$this->response($this->projects->create($this->post('project_name')));
	}
}

?>