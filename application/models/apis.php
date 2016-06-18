<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Apis extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	private function _file_create($project_name, $dir, $file_name, $write_content){
		$project_dir = "/Users/a1/Sites/$project_name/application/" . $dir . '/';
		$file_handle = fopen($project_dir . $file_name, "w+");
		fwrite($file_handle, $write_content);
	}

	private function _get_model_code($api_kind, $input_column){
		$model_name = ucfirst($api_kind . '_model');

		$model_func_code = null;
		$model_argument = null;
		if ($api_kind == 'join'){
			$join_insert_array_code = null;
			foreach ($input_column as $key => $value) {
				$rest_flag = count($input_column) - 1 != $key ? ", " : '';
				$join_insert_array_code .= "'$value' => \$$value" . $rest_flag;
				$model_argument .= "\$$value" . $rest_flag;
			}

			$create_table_query = 
			"CREATE TABLE IF NOT EXISTS User(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), ";
			foreach ($input_column as $key => $value) {
				$rest_flag = count($input_column) - 1 != $key ? ", " : '';
				$create_table_query .= $value . " VARCHAR(32)" . $rest_flag;
			}
			$create_table_query .= ')';
			$model_func_code = 
	"public function $api_kind($model_argument){
		\$this->db->query('$create_table_query');
		\$this->db->insert('User', array(
			$join_insert_array_code			
		));
	}";
		}
		else if($api_kind == 'login'){
			foreach ($input_column as $key => $value) {
				$rest_flag = count($input_column) - 1 != $key ? ", " : '';
				$model_argument .= "\$$value" . $rest_flag;
			}

			$model_func_code = 
	"public function $api_kind($model_argument){
		return \$this->db->from('User')->where('email', \$email)->where('password', \$password)->get()->row() ? true : false;
	}";
		}

		$model_code = 
"<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class $model_name extends CI_Model{
		public function __construct(){
			parent::__construct();
		}

		$model_func_code
	}
?>";
		return $model_code;
	}

	private function _controller_create($project_name, $page_name, $api_kind, $html_code, $callback_code){
		// view cretate(html file)
		$html_code_block = explode("</head>", $html_code);
		$ajax_type = 'post';

		preg_match_all("/input.*name=\"(\w+)\"/", $html_code_block[1], $input_column);
		$controller_method_name = $api_kind . '_controller';
		$ajax_request_data = '{';
		foreach ($input_column[1] as $key => $value) {
			$rest_flag = count($input_column[1]) - 1 != $key ? ", " : '';
			$ajax_request_data .= "'$value':\$('input[name=\"$value\"]').val()" . $rest_flag;
		}
		$ajax_request_data .= '}';
		$ajax_code = null;
		if ($api_kind != null){
			// $login_callback = ($api_kind != null ? "alert(data)" : null);
				$ajax_code = 
			"<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js\"></script>
			<script>
			\$('document').ready(function(){
				\$('button').click(function(){
					\$.ajax({
						url: 'http://localhost/~a1/$project_name/index.php/$page_name/$controller_method_name',
						type: '$ajax_type',
						data: $ajax_request_data,
						success: function(response){
							$callback_code
						},
						error: function(){
							alert('error')
						}
					})
				})
			})
			</script>
			</head>";
		}

		self::_file_create($project_name, 'views', $page_name . '.html', $html_code_block[0] . $ajax_code . $html_code_block[1]);

		// controller create
		$controller_name = ucfirst($page_name);
		$html_name = $page_name . '.html';
		
		$controller_code = null;
		$model_name = null;

		if ($api_kind != null){
			$column_argument = null;
			foreach ($input_column[1] as $key => $value) {
				$rest_flag = count($input_column[1]) - 1 != $key ? ", " : '';
				$column_argument .= "\$this->input->$ajax_type('$value')" . $rest_flag;
			}
			$model_name = $api_kind . '_model';
			$controller_code = 
				"public function $controller_method_name(){
					echo json_encode(\$this->$model_name->$api_kind($column_argument));
				}";

			self::_file_create($project_name, 'models', ucfirst($model_name) . ".php", self::_get_model_code($api_kind, $input_column[1]));
		}
		// else if($api_kind == 'mail'){
		// 	$controller_code = 
		// 		"public function $controller_method_name(){
					
		// 		}";
		// }

		$import_model_source = ($controller_code != null ? "\$this->load->model('$model_name', '', true);" : '');
		$source = 
			"<?php defined('BASEPATH') OR exit('No direct script access allowed');
				class $controller_name extends CI_Controller{
					public function __construct(){
						parent::__construct(); 
						$import_model_source
					}

					public function index(){
						\$this->load->view('$html_name');
					}

					$controller_code
				}
			?>";
		
		self::_file_create($project_name, 'controllers', $controller_name . '.php', $source);
	}

	public function api_create($project_id, $page_name, $html_code, $api_kind, $callback_code){
		$project_name = $this->db->select('name')->from('project')->where('id', $project_id)->get()->row()->name;
		self::_controller_create($project_name, $page_name, $api_kind, $html_code, $callback_code);
	}
}

?>