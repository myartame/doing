<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Api extends REST_Controller{
	public function __construct(){
		parent::__construct();

		$this->load->model('projects', '', true);
		$this->load->model('apis', '', true);
		$this->load->model('user_db', '', true);
	}

	public function index_get(){
		$this->load->view('api.html', array(
			'project_url' => 'http://localhost/~a1/' . 
				$this->projects->get_name($this->get('project_id')) . '/index.php/'
		));
	}

	public function create_post(){
		$this->apis->api_create($this->post('project_id'), 
			$this->post('page_name'), $this->post('html_content'), 
			$this->post('api_kind'), $this->post('success_callback'));
	}

	public function db_list_get(){
		$this->response($this->user_db->get_db_list($this->get('project_id')));
	}
}

?>