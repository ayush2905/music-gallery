<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category Code already exists. Code must be unique";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = " Category successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_music(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO `music_list` set {$data} ";
		}else{
			$sql = "UPDATE `music_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$mid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['mid'] = $mid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Music successfully saved.";
			else
				$resp['msg'] = " Music successfully updated.";

			if(!empty($_FILES['banner_img']['tmp_name'])){
				$file_parts = pathinfo($_FILES['banner_img']['name']);
				$fname = $file_parts['filename'];
				$ext = $file_parts['extension'];
				$accept = array('image/jpeg','image/png');
				if(!in_array($_FILES['banner_img']['type'],$accept)){
					$resp['msg'] .= "But failed to upload Banner image due to invalid file type.";
				}else{
					if(!is_dir(base_app."uploads/music_banners/"))
					mkdir(base_app."uploads/music_banners/");
					$fname = "uploads/music_banners/".$fname;
					$i = 0;
					while(true){
						$tmp_fname = $fname.($i > 0 ? "_{$i}" : "").".".$ext;
						/**
						 * Check if filename already exists
						 */
						if(is_file(base_app.$tmp_fname)){
							$i++;
						}else{
							$fname = $tmp_fname;
							break;
						}
					}
					if($_FILES['banner_img']['type'] == 'image/jpeg')
						$uploadfile = imagecreatefromjpeg($_FILES['banner_img']['tmp_name']);
					elseif($_FILES['banner_img']['type'] == 'image/png')
						$uploadfile = imagecreatefrompng($_FILES['banner_img']['tmp_name']);
					if(!$uploadfile){
						$err = "Image is invalid";
					}
					list($width,$height) = getimagesize($_FILES['banner_img']['tmp_name']);
					$max_size = 480;
					if($width > $height){
						if($width > $max_size){
							$perc = ($width - $max_size) / $width;
							$new_width = $max_size;
							$new_height = $height - ( $height * $perc);
						}else{
							$new_width = $width;
							$new_height = $height;
						}
					}elseif($height > $width){
						if($height > $max_size){
							$perc = ($height - $max_size) / $height;
							$new_height = $max_size;
							$new_width = $width - ( $width * $perc);
						}else{
							$new_width = $width;
							$new_height = $height;
						}
					}else{
						if($height > $max_size){
							$new_width = $max_size;
							$new_height = $max_size;
						}else{
							$new_width = $width;
							$new_height = $height;
						}
					}

					$temp = imagescale($uploadfile,$new_width,$new_height);
					if(is_file(base_app.$fname))
					unlink(base_app.$fname);
					$upload =imagepng($temp,base_app.$fname);
					if($upload){
						$this->conn->query("UPDATE `music_list` set `banner_path` = '{$fname}' where `id` = $mid ");
					}else{
						$resp['msg'].= "Uploading Banner Image file failed due to unknown reason.";
					}
					imagedestroy($temp);
				}
				
			}
			
			if(!empty($_FILES['audio_file']['tmp_name'])){
				$file_parts = pathinfo($_FILES['audio_file']['name']);
				$ext = $file_parts['extension'];
				$fname = $file_parts['filename'];
				if(!is_dir(base_app."uploads/audio/"))
				mkdir(base_app."uploads/audio/");
				$fname = "uploads/audio/".$fname;
				if(!stristr(mime_content_type($_FILES['audio_file']['tmp_name']), 'audio')){
					$resp['msg'] .= "But failed to upload audio file due to invalid file type.";
				}else{
					$i = 0;
					while(true){
						$tmp_fname = $fname.($i> 0 ? "_{$i}" : ""). ".".$ext;
						if(is_file(base_app.$tmp_fname)){
							$i++;
						}else{
							$fname = $tmp_fname;
							break;
						}
					}

					$upload  = move_uploaded_file($_FILES['audio_file']['tmp_name'], base_app.$fname);
					if($upload){
						$this->conn->query("UPDATE `music_list` set `audio_path` = '{$fname}' where `id` = '{$mid}'");
					}else{
						$resp['msg'].= "Uploading Audio file failed due to unknown reason.";
					}
				}

			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_music(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `music_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Music successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_sale(){
		extract($_POST);
		if(empty($invoice_code)){
			$code_pref = date("Ymd");
			$code = 1;
			while(true){
				$tmp_code = $code_pref. (sprintf("%'.05d", $code));
				$check_invoice_code = $this->conn->query("SELECT `id` FROM `sales` where `invoice_code` = '{$tmp_code}' and `delete_flag` = 0")->num_rows;
				if($check_invoice_code > 0){
					$code++;
				}else{
					$_POST['invoice_code'] = $tmp_code;
					$invoice_code = $tmp_code;
					break;
				}
			}
		}else{
			$check_invoice_code = $this->conn->query("SELECT `id` FROM `sales` where `invoice_code` = '{$invoice_code}' and `delete_flag` = 0 ".(isset($id) && !empty($id) ? " and `id` != '{$id}' " : "" ))->num_rows;
			if($check_invoice_code > 0){
				return json_encode(['status' => 'error', 'error' => "Entered sales invoice code already exists."]);
			}
		}
		if(isset($_POST['music_id']) && empty($_POST['music_id'])){
			$_POST['music_id'] = "";
		}
		if(isset($_POST['is_guest'])){
			$_POST['is_guest'] = 1;
		}else{
			$_POST['is_guest'] = 0;
		}
		$_POST['user_id'] = $_SESSION['userdata']['id'];
		// print_r($_POST);
		$data = "";
		$sales_allowed_field = ['invoice_code', 'music_id', 'notes', 'total', 'tendered', 'is_guest', 'user_id'];
		foreach($_POST as $k => $v){
			if(!in_array($k, $sales_allowed_field))
			continue;
			if(!is_numeric($v) && !empty($v)){
				$v = $this->conn->real_escape_string(addslashes($v));
			}
			if(!empty($data)) $data .= ", ";
			if(empty($v) && !is_numeric($v)){
				$data .= " `{$k}` = NULL ";
			}else{
				$data .= " `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sales_sql = "INSERT INTO `sales` set {$data}";
		}else{
			$sales_sql = "UPDATE `sales` set {$data} where `id` = '{$id}'";
		}
		$save_sales = $this->conn->query($sales_sql);
		if($save_sales){
			if(empty($id)){
				$sid = $this->conn->insert_id;
			}else{
				$sid = $id;
			}
			$data2 = "";
			foreach($category_id as $k => $v){
				if(!empty($data2)) $data2 .= ", ";
				$data2 .= "('{$sid}', '{$v}', '$price[$k]', '{$quantity[$k]}')";
			}
			$this->conn->query("DELETE FROM `sales_items` where `sales_id` = '{$sid}'");
			if(!empty($data2)){
				$sales_item_sql = "INSERT INTO `sales_items` (`sales_id`, `category_id`, `price`, `quantity`) VALUES {$data2}";
				$sales_items_save = $this->conn->query($sales_item_sql);
			}

			if(!isset($this->conn->error) || (isset($this->conn->error) && empty($this->conn->error))){
				if(empty($id)){
					$this->settings->set_flashdata('success', " Sales Data has been added successfully.");
				}else{
					$this->settings->set_flashdata('success', " Sales Data has been updated successfully.");
				}
				return json_encode(['status' => "success", "sid" => $sid]);
			}else{
				if(isset($sid))
				$this->conn->query("DELETE FROM `sales` where `id` = '{$sid}'");
				return json_encode(['status' => "error", "error" => "An error occurred while savig the data.", "error_details" => $this->conn->error]);
			}
		}else{
			return json_encode(['status' => "error", "error" => "An error occurred while savig the data.", "error_details" => $this->conn->error]);
		}
	}
	function delete_sale(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `sales` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Sale Details has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	function get_music_details(){
		extract($_GET);
		$qry = $this->conn->query("SELECT * FROM `music_list` where `id` = '{$id}'");
		if($qry->num_rows > 0){
			$result = $qry->fetch_assoc();
			$data = [
				"title"=>$result['title'],
				"artist"=>$result['artist'],
				"discPath"=>base_url.$result['audio_path'],
				"coverPath"=>base_url.$result['banner_path']
			];
		}
		if(isset($data))
		return json_encode($data);
		else
		return false;
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_music':
		echo $Master->save_music();
	break;
	case 'delete_music':
		echo $Master->delete_music();
	break;
	case 'save_sale':
		echo $Master->save_sale();
	break;
	case 'delete_sale':
		echo $Master->delete_sale();
	break;
	case 'get_music_details':
		echo $Master->get_music_details();
	break;
	default:
		// echo $sysset->index();
		break;
}