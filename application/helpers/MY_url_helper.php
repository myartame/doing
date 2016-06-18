<?php defined('BASEPATH') OR exit('No direct script access allowed');
	function get_asset_url($asset_kind, $asset_file_name){
		return base_url() . 'assets/' . $asset_kind . '/' . $asset_file_name;
	}
?>