<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_users(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}
		$check = $this->conn->query("SELECT * FROM `admin` where username = '{$username}' ".(!empty($id)? " and id != '{$id}' " : ''))->num_rows;
		if($check > 0){
			return 2;
		}
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO admin set {$data}");
			if($qry){
				$id=$this->conn->insert_id;
				$this->settings->set_flashdata('success','User Details successfully saved.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(!empty($_FILES['img']['tmp_name'])){
					if(!is_dir(base_app."uploads/avatars"))
						mkdir(base_app."uploads/avatars");
					$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
					$fname = "uploads/avatars/$id.$ext";
					$accept = array('image/jpeg','image/png');
					if(!in_array($_FILES['img']['type'],$accept)){
						$err = "Image file type is invalid";
					}
					if($_FILES['img']['type'] == 'image/jpeg')
						$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
					elseif($_FILES['img']['type'] == 'image/png')
						$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
					if(!$uploadfile){
						$err = "Image is invalid";
					}
					$temp = imagescale($uploadfile,200,200);
					if(is_file(base_app.$fname))
					unlink(base_app.$fname);
					if($_FILES['img']['type'] == 'image/jpeg')
					$upload =imagejpeg($temp,base_app.$fname);
					elseif($_FILES['img']['type'] == 'image/png')
					$upload =imagepng($temp,base_app.$fname);
					else
					$upload = false;
					if($upload){
						$this->conn->query("UPDATE `admin` set `avatar` = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$id}'");
						$this->settings->set_userdata('avatar',$fname."?v=".time());
					}

					imagedestroy($temp);
				}
				return 1;
			}else{
				return 2;
			}

		}else{
			$qry = $this->conn->query("UPDATE admin set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(!empty($_FILES['img']['tmp_name'])){
					if(!is_dir(base_app."uploads/avatars"))
						mkdir(base_app."uploads/avatars");
					$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
					$fname = "uploads/avatars/$id.$ext";
					$accept = array('image/jpeg','image/png');
					if(!in_array($_FILES['img']['type'],$accept)){
						$err = "Image file type is invalid";
					}
					if($_FILES['img']['type'] == 'image/jpeg')
						$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
					elseif($_FILES['img']['type'] == 'image/png')
						$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
					if(!$uploadfile){
						$err = "Image is invalid";
					}
					$temp = imagescale($uploadfile,200,200);
					if(is_file(base_app.$fname))
					unlink(base_app.$fname);
					if($_FILES['img']['type'] == 'image/jpeg')
					$upload =imagejpeg($temp,base_app.$fname);
					elseif($_FILES['img']['type'] == 'image/png')
					$upload =imagepng($temp,base_app.$fname);
					else
					$upload = false;
					if($upload){
						$this->conn->query("UPDATE `admin` set `avatar` = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$id}'");
						$this->settings->set_userdata('avatar',$fname."?v=".time());
					}

					imagedestroy($temp);
				}

				return 1;
			}else{
				return "UPDATE admin set $data where id = {$id}";
			}
			
		}
	}
	public function delete_user(){
		extract($_POST);
		$get = $this->conn->query("SELECT avatar FROM admin where id = '{$id}'")->fetch_array();
		$qry = $this->conn->query("DELETE FROM admin where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			if(isset($get['avatar']) && is_file(base_app.$get['avatar']))
			unlink(base_app.$get['avatar']);
			return 1;
		}else{
			return false;
		}
	}
	public function save_ruser(){
		if(!empty($_POST['password']))
		$_POST['password'] = md5($_POST['password']);
		else
		unset($_POST['password']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `lecturer_list` where `email` = '{$email}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = " Email already taken.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `lecturer_list` set {$data} ";
		}else{
			$sql = "UPDATE `lecturer_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$aid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"Account successfully created.");
			else{
				if($id == $this->settings->userdata('id') && $this->settings->userdata('login_type') == 2)
					$this->settings->set_flashdata('success'," Account successfully updated.");
				else
					$this->settings->set_flashdata('success'," Registered User Account successfully updated.");
			}
			if($id == $this->settings->userdata('id') && $this->settings->userdata('login_type') == 2){
					$this->settings->set_userdata('login_type',2);
				foreach($_POST as $k =>$v){
					$this->settings->set_userdata($k,$v);
				}
				$this->settings->set_userdata('id',$aid);
			}
			if(!empty($_FILES['img']['tmp_name'])){
				if(!is_dir(base_app."uploads/rusers"))
					mkdir(base_app."uploads/rusers");
				$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$fname = "uploads/rusers/$aid.$ext";
				$accept = array('image/jpeg','image/png');
				if(!in_array($_FILES['img']['type'],$accept)){
					$err = "Image file type is invalid";
				}
				if($_FILES['img']['type'] == 'image/jpeg')
					$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
				elseif($_FILES['img']['type'] == 'image/png')
					$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
				if(!$uploadfile){
					$err = "Image is invalid";
				}
				$temp = imagescale($uploadfile,200,200);
				if(is_file(base_app.$fname))
				unlink(base_app.$fname);
				if($_FILES['img']['type'] == 'image/jpeg')
				$upload =imagejpeg($temp,base_app.$fname);
				elseif($_FILES['img']['type'] == 'image/png')
				$upload =imagepng($temp,base_app.$fname);
				else
				$upload = false;
				if($upload){
					$this->conn->query("UPDATE `lecturer_list` set `avatar` = CONCAT('{$fname}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$aid}'");
					if($id == $this->settings->userdata('id') && $this->settings->userdata('login_type') == 2){
						$this->settings->set_userdata('avatar',$fname."?v=".time());
					}
				}

				imagedestroy($temp);
			}

		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " An error occurred.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	} 
	function delete_ruser(){
		extract($_POST);
		$delete = $this->conn->query("UPDATE `lecturer_list` set delete_flag = 1 where id = '{$id}'");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Registered User successfully deleted");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
	break;
	case 'save_ruser':
		echo $users->save_ruser();
	break;
	case 'delete_ruser':
		echo $users->delete_ruser();
	break;
	case 'delete_user':
		echo $users->delete_user();
	break;
	default:
		// echo $sysset->index();
		break;
}