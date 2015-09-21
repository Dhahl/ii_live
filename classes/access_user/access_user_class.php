<?php
/************************************************************************
Access_user Class ver. 1.86
Easy to use class to protect pages and register users

Copyright (c) 2004 - 2005, Olaf Lederer
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the finalwebsites.com nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

_________________________________________________________________________
available at http://www.finalwebsites.com 
Comments & suggestions: http://www.finalwebsites.com/contact.php
If you need help check this forum too:
http://olederer.users.phpclasses.org/discuss/package/1906/

*************************************************************************/

session_start();
// error_reporting (E_ALL); // I use this only for testing
//require($_SERVER['DOCUMENT_ROOT']."/classes/access_user/db_config.php"); // this path works for me...
//require"./classes/access_user/db_config.php"; // this path works for me...
require "db_config.php"; // this path works for me...

class Access_user {
	
	var $table_name = USER_TABLE; 
	
	var $user;
	var $user_pw;
	var $access_level;
	var $user_full_name;
	var $user_info;
	var $user_email;
	var $save_login = "no";
	var $cookie_name = COOKIE_NAME;
	var $cookie_path = COOKIE_PATH; 
	var $is_cookie;
	
	var $count_visit;
	
	var $id;
	var $language = "en"; // change this property to use messages in another language 
	var $the_msg;
	var $login_page;
	var $main_page;
	var $password_page;
	var $deny_access_page;
	var $auto_activation = true;
	var $send_copy = false; // send a mail copy to the administrator (register only)
	
	var $webmaster_mail = WEBMASTER_MAIL;
	var $webmaster_name = WEBMASTER_NAME;
	var $admin_mail = ADMIN_MAIL;
	var $admin_name = ADMIN_NAME;
	
	function Access_user() {
		$this->connect_db();
/**********************************
 * 
 * 	LIVE Version	
 */ 
	$this->login_page 		= '/';		
 /*********************************
  * 
  *	DEVELOPMENT Version
		$this->login_page 		= LOGIN_PAGE;
  */
 /**********************************/   	
		$this->main_page 		= START_PAGE;
		$this->password_page 	= rtrim(ACTIVE_PASS_PAGE,'.php');
		$this->deny_access_page = DENY_ACCESS_PAGE;
		$this->admin_page 		= ADMIN_PAGE;
		$this->class_path			=	rtrim(CLASS_PATH, '/');	//LE
		$this->admin_mail		= ADMIN_MAIL;	
/*	echo "Login Page".LOGIN_PAGE.'<br>';
echo "Start Page".START_PAGE.'<br>';
echo "Application path".APPLICATION_PATH.'<br>';
echo "Class Path".CLASS_PATH.'<br>';
//die;	 */
 
	}	
	function check_user($pass = "") {
		switch ($pass) {
			case "new": 
			//$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE email = '%s' OR login = '%s'", $this->table_name, $this->user_email, $this->user);
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE email = '%s'", $this->table_name, $this->user_email);
			break;
			case "lost":
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE email = '%s' AND active = 'y'", $this->table_name, $this->user_email);
			break;
			case "new_pass":
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE pw = '%s' AND id = %d", $this->table_name, $this->user_pw, $this->id);
			break;
			case "active":
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE id = %d AND active = 'n'", $this->table_name, $this->id);
			break;
			case "validate":
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE id = %d AND tmp_mail <> ''", $this->table_name, $this->id);
			break;
			default:
			$password = (strlen($this->user_pw) < 32) ? md5($this->user_pw) : $this->user_pw;
			// *LE was: $sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE BINARY login = '%s' AND pw = '%s' AND active = 'y'", $this->table_name, $this->user, $password);
			$sql = sprintf("SELECT COUNT(*) AS test FROM %s WHERE BINARY email = '%s' AND pw = '%s' AND active = 'y'", $this->table_name, $this->email, $password);
			/*
			  	var_dump($sql);//LE
				die;
				*/
		}
		$result = mysql_query($sql) or die(mysql_error());
		
		if (mysql_result($result, 0, "test") != 0) {
			return true;
		} else {
			return false;
		}
	}
	// New methods to handle the access level	
	function get_access_level() {
		$sql = sprintf("SELECT access_level FROM %s WHERE login = '%s' AND active = 'y'", $this->table_name, $this->user);
		if (!$result = mysql_query($sql)) {
		   $this->the_msg = $this->messages(14);
		} else {
			$this->access_level = mysql_result($result, 0, "access_level");
		}
	}
	function set_user() {
		$_SESSION['id'] 			= $this->id;
		//*LE $_SESSION['user'] 			= $this->user;
		$_SESSION['userFirstName'] 	= $this->firstName;
		$_SESSION['userLastName'] 	= $this->lastName;
		$_SESSION['user'] 			= $this->firstName.' '.$this->LastName;
		$_SESSION['pw'] 			= $this->user_pw;
		$_SESSION['userFullName'] 	= $this->user_full_name;
		$_SESSION['userPublisher'] 	= $this->publisher;
		$_SESSION['userEmail'] 		= $this->user_email;
		$_SESSION['userTelephone'] 	= $this->telephone;
		$_SESSION['userPosition'] 	= $this->position;
		$_SESSION['accessLevel']	=	$this->accessLevel;
/*		if (isset($_SESSION['referer']) && $_SESSION['referer'] != "") {
			$next_page = $_SESSION['referer'];
			unset($_SESSION['referer']);
		} else {
			$next_page = $this->main_page;
		}
		header("Location: ".$next_page);
*/	}
	function connect_db() {
		$conn_str = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
		//mysql_select_db(DB_NAME); // if there are problems with the tablenames inside the config file use this row 
	}
	function login_user($email,$password)	{	//	*LE was: ($user, $password) {
		
//			var_dump($_COOKIE[$this->cookie_name]);
//			die;

		if ($email != "" && $password != "") {//	*LE was: ($user != "" && $password != "") {
			//$this->user = $user;
			$this->email	=	$email;
			$this->user_pw 	= $password;
			if ($this->check_user()) {
				$this->login_saver();
				if ($this->count_visit) {
					$this->reg_visit($user, $password);
				}
				$this->set_user();
			} else {
				$this->the_msg = $this->messages(10);
			}
		} else {
			$this->the_msg = $this->messages(11);
		}
/*		var_dump($this->the_msg);
  		die;
*/
 
	}
	function login_saver() {
		
//			var_dump($_COOKIE[$this->cookie_name]);
//			die($this->save_login);

		if ($this->save_login == "no") {
			if (isset($_COOKIE[$this->cookie_name])) {
				$expire = time()-3600;
			} else {
				return;
			}
		} else {
			$expire = time()+2592000;
		}		
		$cookie_str = $this->email.chr(31).base64_encode($this->user_pw);// *LE was: $this->user.chr(31).base64_encode($this->user_pw);
		setcookie($this->cookie_name, $cookie_str, $expire); //, $this->cookie_path);

//		var_dump($this->cookie_name);
//		var_dump($this->cookie_path);
//		die;
	}
	function login_reader() {
		if (isset($_COOKIE[$this->cookie_name])) {
			
//			var_dump($_COOKIE[$this->cookie_name]);
//			die;

			$cookie_parts = explode(chr(31), $_COOKIE[$this->cookie_name]);
			$this->user = $cookie_parts[0];
			$this->user_pw = base64_decode($cookie_parts[1]);
			$this->is_cookie = true;
		}			 
	}
	function reg_visit($login, $pass) {
		//*LE $visit_sql = sprintf("UPDATE %s SET extra_info = '%s' WHERE login = '%s' AND pw = '%s'", $this->table_name, date("Y-m-d H:i:s"), $login, md5($pass));
		$visit_sql = sprintf("UPDATE %s SET extra_info = '%s' WHERE email = '%s' AND pw = '%s'", $this->table_name, date("Y-m-d H:i:s"), $login, md5($pass));
		mysql_query($visit_sql);
	}
	function log_out() {
	unset($_SESSION['user']);
		unset($_SESSION['pw']);
		unset($_SESSION);
		session_destroy();
		header("Location: ".$this->login_page);
	}
	function access_page($refer = "", $qs = "", $level = DEFAULT_ACCESS_LEVEL) {
		$refer_qs = $refer;
		$refer_qs .= ($qs != "") ? "?".$qs : "";
		if (isset($_SESSION['user']) && isset($_SESSION['pw'])) {
			$this->user = $_SESSION['user'];
			$this->user_pw = $_SESSION['pw'];
			$this->get_access_level();
			if (!$this->check_user()) {
				$_SESSION['referer'] = $refer_qs;
				header("Location: ".$this->login_page);
			}
			if ($this->access_level < $level) {
				header("Location: ".$this->deny_access_page);
			}
		} else { 
			$_SESSION['referer'] = $refer_qs;
			header("Location: ".$this->login_page);
		}
	}
	function get_user_info_by_id($key, $id) {
		$sql_info = sprintf("SELECT real_name, extra_info, email, id, publisher, lastname, position, telephone FROM %s WHERE id = '%s' AND pw = '%s'", $this->table_name, $id, $key);
		$res_info = mysql_query($sql_info);
		$this->id 				= mysql_result($res_info, 0, "id");
			//*LE $this->user_full_name = mysql_result($res_info, 0, "real_name");
		$this->firstName		=	mysql_result($res_info, 0, "real_name");
		$this->lastName			=	mysql_result($res_info, 0, "lastname");
		$this->user_full_name 	= 	$this->firstName." ".$this->lastName;
		$this->publisher 		= 	mysql_result($res_info, 0, "publisher");
		$this->user_email 		= 	mysql_result($res_info, 0, "email");
		$this->telephone		=	mysql_result($res_info, 0, "telephone");
		$this->position			=	mysql_result($res_info, 0, "position");		
	}
	function get_user_info() {
		//*LE $sql_info = sprintf("SELECT real_name, extra_info, email, id, publisher FROM %s WHERE login = '%s' AND pw = '%s'", $this->table_name, $this->user, md5($this->user_pw));
		$sql_info = sprintf("SELECT real_name, access_level, extra_info, email, id, publisher, lastname, telephone, position FROM %s WHERE email = '%s' AND pw = '%s'", $this->table_name, $this->email, md5($this->user_pw));
		//var_dump($sql_info);
		$res_info = mysql_query($sql_info);
		
		$this->id 				= mysql_result($res_info, 0, "id");
			//*LE $this->user_full_name = mysql_result($res_info, 0, "real_name");
		$this->firstName		=	mysql_result($res_info, 0, "real_name");
		$this->accessLevel		=	mysql_result($res_info, 0, "access_level");
		$this->lastName			=	mysql_result($res_info, 0, "lastname");
		$this->user_full_name 	= 	$this->firstName." ".$this->lastName;
		$this->publisher 		= 	mysql_result($res_info, 0, "publisher");
		$this->user_email 		= 	mysql_result($res_info, 0, "email");
		$this->telephone		=	mysql_result($res_info, 0, "telephone");
		$this->position			=	mysql_result($res_info, 0, "position");	
			
	}
	function update_user($new_password, $new_confirm, $new_name, $new_info, $new_mail, $new_publisher, $new_lastname, $new_telephone, $new_position) {
		if ($new_password != "") {
			if ($this->check_new_password($new_password, $new_confirm)) {
				$ins_password = $new_password;
				$update_pw = true;
			} else {
				return;
			}
		} else {
			$ins_password = $this->user_pw;
			$update_pw = false;
		}
		
		if (trim($new_mail) <> $this->user_email) {
		/*	var_dump($new_mail);
			var_dump($this);
			die;	*/
			if  ($this->check_email($new_mail)) {
				$this->user_email = $new_mail;
				if (!$this->check_user("lost")) {
					$update_email = true;
				} else {
					$this->the_msg = $this->messages(31);
					$this->msgNo		=	31;
					return;
				}
			} else {
				$this->the_msg = $this->messages(16);
				$this->msgNo	=	16;
				return;
			}
		} else {
			$update_email = false;
			$new_mail = "";
		}
		$upd_sql = sprintf("UPDATE %s SET pw = %s, real_name = %s, extra_info = %s, tmp_mail = %s, 
				publisher = %s, lastname = %s, telephone = %s, position = %s  WHERE id = %d", 
			$this->table_name,
			$this->ins_string(md5($ins_password)),
			$this->ins_string($new_name),
			$this->ins_string($new_info),
			$this->ins_string($new_mail),
			$this->ins_string($new_publisher),
			$this->ins_string($new_lastname),
			$this->ins_string($new_telephone),
			$this->ins_string($new_position),
									
			$this->id);
		$upd_res = mysql_query($upd_sql);
		if ($upd_res) {
			if ($update_pw) {
				$_SESSION['pw'] = $this->user_pw = $ins_password;
				if (isset($_COOKIE[$this->cookie_name])) {
					$this->save_login = "yes";
					$this->login_saver();
				}
			}
			$this->the_msg = $this->messages(30);		// success
			$this->msgNo	=	30;
			if ($update_email) {
				if ($this->send_mail($new_mail, 33)) {		// new e-mail address must be validated, click the following link
					$this->the_msg = $this->messages(27);	//	check your email..
				} else {
					mysql_query(sprintf("UPDATE %s SET tmp_mail = ''", $this->table_name));
					$this->the_msg = $this->messages(14);
					$this->msgNo	=	14;					// an error occurred...
				} 
			}
		} else {
			$this->the_msg = $this->messages(15);
			$this->msgNo	=	15;	//	an error occurred...
		}
	}
	function check_new_password($pass, $pw_conform) {
		if ($pass == $pw_conform) {
			if (strlen($pass) >= PW_LENGTH) {
				return true;
			} else {
				$this->the_msg = $this->messages(32);
				$this->msgNo		=	32;
				return false;
			}
		} else {
			$this->the_msg = $this->messages(38);
			$this->msgNo	=	38;
			return false;
		}	
	}
	function check_email($mail_address) {
		if (preg_match("/^[0-9a-z]+(([\.\-_])[0-9a-z]+)*@[0-9a-z]+(([\.\-])[0-9a-z-]+)*\.[a-z]{2,4}$/i", $mail_address)) {
			return true;
		} else {
			return false;
		}
	}
	function ins_string($value, $type = "") {
		$value = (!get_magic_quotes_gpc()) ? addslashes($value) : $value;
		switch ($type) {
			case "int":
			$value = ($value != "") ? intval($value) : NULL;
			break;
			default:
			$value = ($value != "") ? "'" . $value . "'" : "''";
		}
		return $value;
	}
	function register_user($first_login, $first_password, $confirm_password, $first_name, $first_info, 
										$first_email, $publisher, $lastname, $telephone, $position, $userType) {
		if ($this->check_new_password($first_password, $confirm_password)) {
			if (strlen($first_login) >= LOGIN_LENGTH) {
				if ($this->check_email($first_email)) {
					$this->user_email = $first_email;
					$this->user = $first_login;
					$this->user_full_name =	$first_login." ".$lastname;
/*					if(!ctype_alnum($this->user))	{										//	LE 
						$this->the_msg	=	'Your "First name" must be all AlphaNumeric characters with no spaces';	// LE
						$this->msgNo	=	12;												//LE
					}
					else	{																 LE */
					if ($this->check_user("new")) {
						$this->the_msg = $this->messages(12);
						$this->msgNo	=	12;
					} else {
						
			/*******************************************************************************
			 * 												User Types
			 * 												========
			 * 								 1		=		Publisher - affiliated. 	(when approved by Admin)
			 * 								 2		=		Publisher unaffilliated 	(always unafil on registration)
			 * 								 3		=		Event manager
			 * 								 4		=		Correspondent 			(default)
			 * 								10		=		Administrator
			 * 			
			 *******************************************************************************/

						$accessLevel	=	"4";						
						if($userType == 'publisher') 				$accessLevel	=	"2";
						if($userType == 'promoter') 				$accessLevel	=	"3";
						 		
						$sql = sprintf("INSERT INTO %s (id, login, pw, real_name, extra_info, email, access_level, active, lastname, publisher, telephone, position) 
							VALUES (NULL, %s, %s, %s, %s, %s, %d,'n', %s, %s, %s, %s)", 
							$this->table_name,
							$this->ins_string($first_login),
							$this->ins_string(md5($first_password)),
							$this->ins_string($first_name),
							$this->ins_string($first_info),
							$this->ins_string($this->user_email),
							$accessLevel,	
							$this->ins_string($lastname),
							$this->ins_string($publisher),
							$this->ins_string($telephone),
							$this->ins_string($position));
/*							var_dump($sql);
							die;
							*/
						$ins_res = mysql_query($sql);
						if ($ins_res) {
							$this->id = mysql_insert_id();
							$this->user_pw = $first_password;
							$sendErr	=	$this->send_mail($this->user_email);	// msgno default=29 - activation link
							if (!$sendErr) {
							//if ($this->send_mail($this->admin_mail)) {
								$this->msgNo	=	13;		//	=	check your email (Success)
								$this->the_msg = $this->messages(13);
								//$this->send_mail($this->admin_mail);
							} else {
								mysql_query(sprintf("DELETE FROM %s WHERE id = %s", $this->table_name, $this->id));
								$this->msgNo	=	14;
								$this->the_msg = $sendErr;
										}
						} else {
							$this->msgNo	=	15;
							$this->the_msg = ' That email address is already registered. Use the "Forgot Password" option on the Login page if you have forgotten your password';// .$this->messages(15);
						}
					}
//					}																		//	LE
				} else {
					$this->msgNo	=	16;
					$this->the_msg = $this->messages(16);
				}
			} else {
				$this->msgNo	=	17;
				$this->the_msg = $this->messages(17);
			}
		}
	}
	function validate_email($validation_key, $key_id) {
		if ($validation_key != "" && strlen($validation_key) == 32 && $key_id > 0) {
			$this->id = $key_id;
			if ($this->check_user("validate")) {
				$upd_sql = sprintf("UPDATE %s SET email = tmp_mail, tmp_mail = '' WHERE id = %d AND pw = '%s'", $this->table_name, $key_id, $validation_key);
				if (mysql_query($upd_sql)) {
					$this->the_msg = $this->messages(18);
				} else {
					$this->the_msg = $this->messages(19);
				}
			} else {
				$this->the_msg = $this->messages(34);
			}
		} else {
			$this->the_msg = $this->messages(21);
		}
	}
	function activate_account($activate_key, $key_id) {
		if ($activate_key != "" && strlen($activate_key) == 32 && $key_id > 0) {
			$this->id = $key_id;
			if ($this->check_user("active")) {
				if ($this->auto_activation) {
					$upd_sql = sprintf("UPDATE %s SET active = 'y' WHERE id = %s AND pw = '%s'", $this->table_name, $key_id, $activate_key);
					if (mysql_query($upd_sql)) {
					//	if ($this->send_confirmation($key_id)) {
							$this->the_msg = $this->messages(18);	// Success - activated
							$this->msgNo	=	18;	
					//	} else {
					//		$this->the_msg = $this->messages(14);
					//		$this->msgNo	=	14;
					//	}
					} else {
						$this->the_msg = $this->messages(19);
						$this->msgNo	=	19;
					}
				} else {
					if ($this->send_mail($this->admin_mail, 0, true)) {
						$this->the_msg = $this->messages(36);	// Success - being processed by admin ... 
						$this->msgNo	=	36;					
					} else {
						$this->the_msg = $this->messages(14);
						$this->msgNo	=	14;
					}
				}
			} else {
				$this->the_msg = $this->messages(20);
				$this->msgNo	=	20;
			}
		} else {
			$this->the_msg = $this->messages(21);
			$this->msgNo	=	21;
		}
	}
	function send_confirmation($id) {
		$sql = sprintf("SELECT email FROM %s WHERE id = %d", $this->table_name, $id);
		$user_email = mysql_result(mysql_query($sql), 0, "email");
		if ($this->send_mail($user_email, 37)) {
			return true;
		} else {
			return false;
		}
	}
	function send_mail($mail_address, $num = 29) {
	return false;		//	DEVELOPMENT ENV
	if($_SESSION['accessLevel']	== '10') return false;		//* LE - create/update users by admin - do not send email
		
	require("email_message.php");

	$from_address="administrator@irishinterest.ie";
	$from_name="Irish Interest";	

	$reply_name=$from_name;
	$reply_address=$from_address;
	$reply_address=$from_address;
	$error_delivery_name=$from_name;
	$error_delivery_address=$from_address;
	
	$to_name = $this->user;
	$to_address=$mail_address;

	//$subject="Testing Manuel Lemos' MIME E-mail composing and sending PHP class: HTML message";
	
	/* start set mail info */
	if (!$this->auto_activation) {
		//echo "if (!$this->auto_activation)";
		if($num	==	35 )	{	//forgot password
			$subject =	"An Irish Interest Publisher has Forgotten their Password ";
			$body 	=	"Please issue User ". $this->login." with a new Password.";
			}
		else {
			
			$subject = "New user request...";
			$body = "New user registration requested by ".$_SESSION['username']. ' on '.date("Y-m-d").":\r\n\r\nClick here to enter the admin page:\r\n\r\n"."http://".$_SERVER['HTTP_HOST'].'/login';
		}
	} else {
		// echo "body = $this->messages($num)<br>";
			$subject = "Irish Interest activation link for ".$this->user_full_name;// $this->messages($num);	
			$body = $this->messages($num);
	}
/* end set mail info  */
	$email_message=new email_message_class;
	$email_message->SetEncodedEmailHeader("To",$to_address,$to_name);
	$email_message->SetEncodedEmailHeader("Cc",$this->admin_mail,"Irish Interest Administrator");
//	$email_message->SetEncodedEmailHeader("Cc","lorcan@irishinterest.ie","Irish Interest Administrator");
	$email_message->SetEncodedEmailHeader("From",$from_address,$from_name);
	$email_message->SetEncodedEmailHeader("Reply-To",$reply_address,$reply_name);
	$email_message->SetHeader("Sender",$from_address);
	if(defined("PHP_OS")
	&& strcmp(substr(PHP_OS,0,3),"WIN"))
		$email_message->SetHeader("Return-Path",$error_delivery_address);

	$email_message->SetEncodedHeader("Subject",$subject);
/* */	
	$image=array(
		"FileName"=>"emailbackground.png",		// logo
		"Content-Type"=>"automatic/name",
		"Disposition"=>"inline",
	);
	$email_message->CreateFilePart($image,$image_part);

	$image_content_id=$email_message->GetPartContentID($image_part);

	$image=array(
		"FileName"=>"emailbackground.png",
		"Content-Type"=>"automatic/name",
		"Disposition"=>"inline",
	);
	
	$email_message->CreateFilePart($image,$background_image_part);

		$background_image_content_id="cid:".$email_message->GetPartContentID($background_image_part);

	$html_message="<html>
<head>
<title>$subject</title>
<style type=\"text/css\"><!--
body { color: black ; font-family: arial, helvetica, sans-serif ; background-color: #A3C5CC }
A:link, A:visited, A:active { text-decoration: underline }
--></style>
</head>
<body  background=\"cid:".$image_content_id."\"style=\"width:100%; height:100%;\" >
<table background=\"cid:".$image_content_id."\"style=\"width:100%; height:100%;background-repeat:no-repeat;\" >
<tr style=\"width:100%;\"><td style=\"width:15%;height:450px\"></td>
<td style=\"width:100%;\"><h1 style=\"color:white\">$subject</h1>
<p style=\"color:white\">Hello ".strtok($to_name," ").",".$body."</p><p style=\"color:white\">". // .  // second image not required (LE)
//	"<center><img src=\"cid:".$image_content_id."\"></center>".	 //
/*
 * This example of embedding images in HTML messages is commented out
 * because not all mail programs support this method.
 *
 * <center><h2>Here is an image embedded directly in the HTML:</h2></center>
 * <center><img src=\"".$image_data_url."\"></center>
 */
"Thank you,<br><b>$from_name</b></p>
</td>
</tr>
</table>
</body>
</html>";
	$email_message->CreateQuotedPrintableHTMLPart($html_message,"",$html_part);

	$text_message="This is an HTML message. Please use an HTML capable mail program to read this message.";
	$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($body),"",$text_part);

	$alternative_parts=array(
		$text_part,
		$html_part
	);
	$email_message->CreateAlternativeMultipart($alternative_parts,$alternative_part);

/*
 *  All related parts are gathered in a single multipart/related part.
 */
	$related_parts=array(
		$alternative_part,
		$image_part,
//		$background_image_part
	);
	$email_message->AddRelatedMultipart($related_parts);

/*
 *  One or more additional parts may be added as attachments.
 *  In this case a file part is added from data provided directly from this script.
 */
	$attachment=array(
		"Data"=>$body,
		"Name"=>"attachment.txt",
		"Content-Type"=>"automatic/name",
		"Disposition"=>"attachment",
/*
 *  You can set the Cache option if you are going to send the same message
 *  to multiple users but this file part does not change.
 *
		"Cache"=>1
 */
	);
	$email_message->AddFilePart($attachment);

/*
 *  The message is now ready to be assembled and sent.
 *  Notice that most of the functions used before this point may fail due to
 *  programming errors in your script. You may safely ignore any errors until
 *  the message is sent to not bloat your scripts with too much error checking.

	var_dump($email_message);
*/	

	/*	
	 * Send the Message	
	 * 
	 */
 	$error=$email_message->Send();
 	
 	
/* 	var_dump($error);
	var_dump($email_message);
	die;
	*/
	return($error);
/*	if(strcmp($error,""))
		echo "Error: $error\n";
	else
		echo "Message sent to $to_name\n";
*/		
/*	Old Code - start
 * 
 		$header = "From: \"".$this->webmaster_name."\" <".$this->webmaster_mail.">\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Mailer: Irish Interest\r\n";
		$header .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n";
/ *				var_dump($num);
				var_dump($this);
				var_dump($_SESSION);
				die;
					die("509 access uers");
* /
		if (!$this->auto_activation) {
					//die("511 access uers");
			if($num	==	35 )	{	//forgot password
				$subject =	"An Irish Interest Publisher has Forgotten their Password ";
				$body 	=	"Please issue User ". $this->login." with a new Password.";
				}
			else {
			
				$subject = "New user request...";
				//** LE **$body = "New user registration on ".date("Y-m-d").":\r\n\r\nClick here to enter the admin page:\r\n\r\n"."http://".$_SERVER['HTTP_HOST'].$this->admin_page."?login_id=".$this->id;
				$body = "New user registration requested by ".$_SESSION['username']. ' on '.date("Y-m-d").":\r\n\r\nClick here to enter the admin page:\r\n\r\n"."http://".$_SERVER['HTTP_HOST'].'/login';
			}
		} else {
			//		echo "523 access uers";
			//$subject = $this->messages(29);	//		(was 28 = "Your request must be processed...")
			$subject = "Irish Interest activation link";// $this->messages($num);	
			$body = $this->messages($num);
		}
		if($_SESSION['accessLevel']	== '10') {		//* LE - creating users by admin - do not send email
			//		die("526 access uers");
			return true;
		}
		else	{
			/* LE- REMOVE LINES & JUST RETURN true WHEN TESTING WITHOUT A MAIL SERVER  */
			/* **LE removed** 			
			  if (mail($mail_address, $subject, $body, $header)) {
* /
			echo $mail_address.'<br>'. $subject.'<br>'. $body.'<br>'. $header;
					//die("536 access uers");
			return true;
/* LE start
  		} else {
			return false;
		}
	LE ends	* / 
		}
***Old Code end		*/
	}
	function forgot_password($forgot_email) { 
		if ($this->check_email($forgot_email)) {
			$this->user_email = $forgot_email;
			if (!$this->check_user("lost")) {
				$this->the_msg = $this->messages(22);
				$this->msgNo	=	22;
			} else {
				$forgot_sql = sprintf("SELECT id, pw, login, real_name, lastname FROM %s WHERE email = '%s'", $this->table_name, $this->user_email); // LE chgd
				if ($forgot_result = mysql_query($forgot_sql)) {
					$this->id = mysql_result($forgot_result, 0, "id");
					$this->user_pw = mysql_result($forgot_result, 0, "pw");
					$this->login =	mysql_result($forgot_result, 0, "login");
					$_SESSION['username']	= mysql_result($forgot_result, 0, "real_name");
					$this->user	=	mysql_result($forgot_result, 0, "real_name");
					$this->user_full_name	=	mysql_result($forgot_result, 0, "real_name")." ".mysql_result($forgot_result, 0, "lastname");
					 
					//if ($this->send_mail($this->user_email, 35)) {	// LE Replaced
					//if ($this->send_mail($this->admin_mail, 35)) {
					$sendErr	=	$this->send_mail($this->user_email, 35); 
					if(!$sendErr) {
						$this->the_msg = $this->messages(23);			//successfully sent mail 
						$this->msgNo	=	23;
					} else {
							$this->the_msg = $sendErr;
							$this->msgNo	=	14;
					}
				} else {
					$this->the_msg = $this->messages(15);
					$this->msgNo	=	15;
				}
			}
		} else {
			$this->the_msg = $this->messages(16);
			$this->msgNo	=	16;
		}
	}
	function check_activation_password($controle_str, $id) {
		if ($controle_str != "" && strlen($controle_str) == 32 && $id > 0) {
			$this->user_pw = $controle_str;
			$this->id = $id;
			if ($this->check_user("new_pass")) {
				// this is a fix for version 1.76
				$sql_get_user = sprintf("SELECT login FROM %s WHERE pw = '%s' AND id = %d", $this->table_name, $this->user_pw, $this->id);
				$get_user = mysql_query($sql_get_user);
				$this->user = mysql_result($get_user, 0, "login"); // end fix
				return true;
			} else {
				$this->the_msg = $this->messages(21);
				$this->msgNo	=	21;
				return false;
			}
		} else {
			$this->the_msg = $this->messages(21);
			$this->msgNo	=	21;
			return false;
		}
	}
	function activate_new_password($new_pass, $new_confirm, $old_pass, $user_id) {
		if ($this->check_new_password($new_pass, $new_confirm)) {
			$sql_new_pass = sprintf("UPDATE %s SET pw = '%s' WHERE pw = '%s' AND id = %d", $this->table_name, md5($new_pass), $old_pass, $user_id);
			if (mysql_query($sql_new_pass)) {
				$this->the_msg = $this->messages(30);
				$this->msgNo	= 30;
				return true;
			} else {
				$this->the_msg = $this->messages(14);
				$this->msgNo	=	14;
				return false;
			}
		} else {
			return false;
		}
	}
	function messages($num) {
		$host = "http://".$_SERVER['HTTP_HOST'];
		switch ($this->language) {
			case "de":           
			$msg[10] = "Login und/oder Passwort finden keinen Treffer in der Datenbank.";
			$msg[11] = "Login und/oder Passwort sind leer!";
			$msg[12] = "Leider existiert bereits ein Benutzer mit diesem Login und/oder E-mailadresse.";
			$msg[13] = "Weitere Anweisungen wurden per E-mail versandt, folgen Sie nun den Instruktionen.";
			$msg[14] = "Es is ein Fehler entstanden probieren Sie es erneut.";
			$msg[15] = "Es is ein Fehler entstanden probieren Sie es sp�ter nochmal.";
			$msg[16] = "Die eingegebene E-mailadresse ist nicht g�ltig.";
			$msg[17] = "Das Feld login (min. ".LOGIN_LENGTH." Zeichen) muss eingegeben sein.";
			$msg[18] = "Ihr Benutzerkonto ist aktiv. Sie k�nnen sich nun anmelden.";
			$msg[19] = "Ihr Aktivierungs ist nicht g�ltig.";
			$msg[20] = "Da ist kein Konto zu aktivieren.";
			$msg[21] = "Der benutzte Aktivierung-Code is nicht g�ltig!";
			$msg[22] = "Keine Konto gefunden dass mit der eingegeben E-mailadresse �bereinkommt.";
			$msg[23] = "Kontrollieren Sie Ihre E-Mail um Ihr neues Passwort zu erhalten.";
			$msg[25] = "Kann Ihr Passwort nicht aktivieren.";
			$msg[26] = "";
			$msg[27] = "Kontrollieren Sie Ihre E-Mailbox und best�tigen Sie Ihre �nderung(en).";
			$msg[28] = "Ihre Anfrage best�tigen...";
			$msg[29] = "Hallo,\r\n\r\num Ihre Anfrage zu aktivieren klicken Sie bitte auf den folgenden Link:\r\n".$host.$this->login_page."?ident=".$this->id."&activate=".md5($this->user_pw)."&language=".$this->language;
			$msg[30] = "Ihre �nderung ist durchgef�hrt.";
			$msg[31] = "Diese E-mailadresse wird bereits genutzt, bitte w�hlen Sie eine andere.";
			$msg[32] = "Das Feld Passwort (min. ".PW_LENGTH." Zeichen) muss eingegeben sein.";
			$msg[33] = "Hallo,\r\n\r\nIhre neue E-mailadresse muss noch �berpr�ft werden, bitte klicken Sie auf den folgenden Link:\r\n".$host.$this->login_page."?id=".$this->id."&validate=".md5($this->user_pw)."&language=".$this->language;
			$msg[34] = "Da ist keine E-mailadresse zu �berpr�fen.";
			$msg[35] = "Hallo,\r\n\r\nIhr neues Passwort kann nun eingegeben werden, bitte klicken Sie auf den folgenden Link:\r\n".$host.$this->password_page."?id=".$this->id."&activate=".$this->user_pw."&language=".$this->language;
			$msg[36] = "Ihr Antrag ist verarbeitet und wird nun durch den Administrator kontrolliert. \r\nSie erhalten eine Nachricht wenn dies geschehen ist.";
			$msg[37] = "Hallo ".$this->user.",\r\n\r\nIhr Konto ist nun eigerichtet und Sie k�nnen sich anmelden.\r\n\r\nKlicken Sie hierf�r auf den folgenden Link:\r\n".$host.$this->login_page."\r\n\r\nmit freundlichen Gr�ssen\r\n".$this->admin_name;
			$msg[38] = "Das best&auml;tigte Passwort hat keine &Uuml;bereinstimmung mit dem ersten Passwort, bitte probieren Sie es erneut.";
			break;
			case "nl":
			$msg[10] = "Gebruikersnaam en/of wachtwoord vinden geen overeenkomst in de database.";
			$msg[11] = "Gebruikersnaam en/of wachtwoord zijn leeg!";
			$msg[12] = "Helaas bestaat er al een gebruiker met deze gebruikersnaam en/of e-mail adres.";
			$msg[13] = "Er is een e-mail is aan u verzonden, volg de instructies die daarin vermeld staan.";
			$msg[14] = "Het is een fout ontstaan, probeer het opnieuw.";
			$msg[15] = "Het is een fout ontstaan, probeer het later nog een keer.";
			$msg[16] = "De opgegeven e-mail adres is niet geldig.";
			$msg[17] = "De gebruikersnaam (min. ".LOGIN_LENGTH." teken) moet opgegeven zijn.";
			$msg[18] = "Het gebruikersaccount is aangemaakt, u kunt u nu aanmelden.";
			$msg[19] = "Kan uw account niet activeren.";
			$msg[20] = "Er is geen account te activeren.";
			$msg[21] = "De gebruikte activeringscode is niet geldig!";
			$msg[22] = "Geen account gevonden dat met de opgegeven e-mail adres overeenkomt.";
			$msg[23] = "Er is een e-mail is aan u verzonden, daarin staat hoe uw een nieuw wachtwoord kunt aanmaken.";
			$msg[25] = "Kan het wachtwoord niet activeren.";
			$msg[26] = "";
			$msg[27] = "Er is een e-mail is aan u verzonden, volg de instructies die daarin vermeld staan.";
			$msg[28] = "Bevestig uw aanvraag ...";
			$msg[29] = "Bedankt voor uw aanvraag,\r\n\r\nklik op de volgende link om de aanvraag te verwerken:\r\n".$host.$this->login_page."?ident=".$this->id."&activate=".md5($this->user_pw)."&language=".$this->language;
			$msg[30] = "Uw wijzigingen zijn doorgevoerd.";
			$msg[31] = "Dit e-mailadres bestaat al, gebruik en andere.";
			$msg[32] = "Het veld wachtwoord (min. ".PW_LENGTH." teken) mag niet leeg zijn.";
			$msg[33] = "Beste gebruiker,\r\n\r\nde nieuwe e-mailadres moet nog gevalideerd worden, klik hiervoor op de volgende link:\r\n".$host.$this->login_page."?id=".$this->id."&validate=".md5($this->user_pw)."&language=".$this->language;
			$msg[34] = "Er is geen e-mailadres te valideren.";
			$msg[35] = "Hallo,\r\n\r\nuw nieuw wachtwoord kan nu ingevoerd worden, klik op deze link om verder te gaan:\r\n".$host.$this->password_page."?id=".$this->id."&activate=".$this->user_pw."&language=".$this->language;
			$msg[36] = "U aanvraag is verwerkt en wordt door de beheerder binnenkort activeert. \r\nU krijgt bericht wanneer dit gebeurt is.";
			$msg[37] = "Hallo ".$this->user.",\r\n\r\nHet account is nu gereed en u kunt zich aanmelden.\r\n\r\nKlik hiervoor op de volgende link:\r\n".$host.$this->login_page."\r\n\r\nmet vriendelijke groet\r\n".$this->admin_name;
			$msg[38] = "Het bevestigings wachtwoord komt niet overeen met het wachtwoord, probeer het opnieuw.";
			break;
			case "fr":
			$msg[10] = "Le login et/ou mot de passe ne correspondent pas.";
			$msg[11] = "Le login et/ou mot de passe est vide !";
			$msg[12] = "D�sol�, un utilisateur avec le m�me email et/ou login existe d�j�.";
			$msg[13] = "V�rifiez votre email et suivez les instructions.";
			$msg[14] = "D�sol�, une erreur s'est produite. Veuillez r�essayer.";
			$msg[15] = "D�sol�, une erreur s'est produite. Veuillez r�essayer plus tard.";
			$msg[16] = "L'adresse email n'est pas valide.";
			$msg[17] = "Le champ \"Nom d'usager\" doit �tre compos� d'au moins ".LOGIN_LENGTH." carat�res.";
			$msg[18] = "Votre requete est compl�te. Enregistrez vous pour continuer.";
			$msg[19] = "D�sol�, nous ne pouvons pas activer votre account.";
			$msg[20] = "D�sol�, il n'y � pas d'account � activer.";
			$msg[21] = "D�sol�, votre clef d'authorisation n'est pas valide";
			$msg[22] = "D�sol�, il n'y � pas d'account actif avec cette adresse email.";
			$msg[23] = "Veuillez consulter votre email pour recevoir votre nouveau mot de passe.";
			$msg[25] = "D�sol�, nous ne pouvons pas activer votre mot de passe.";
			$msg[26] = "";
			$msg[27] = "Veuillez consulter votre email pour activer les modifications.";
			$msg[28] = "Votre requete doit etre ex�cuter...";
			$msg[29] = "Bonjour,\r\n\r\npour activer votre account clickez sur le lien suivant:\r\n".$host.$this->login_page."?ident=".$this->id."&activate=".md5($this->user_pw)."&language=".$this->language;
			$msg[30] = "Votre account � �t� modifi�.";
			$msg[31] = "D�sol�, cette adresse email existe d�j�, veuillez en utiliser une autre.";
			$msg[32] = "Le champ password (min. ".PW_LENGTH." char) est requis.";
			$msg[33] = "Bonjour,\r\n\r\nvotre nouvelle adresse email doit �tre valid�e, clickez sur le liens suivant:\r\n".$host.$this->login_page."?id=".$this->id."&validate=".md5($this->user_pw)."&language=".$this->language;
			$msg[34] = "Il n'y � pas d'email � valider.";
			$msg[35] = "Bonjour,\r\n\r\nPour entrer votre nouveaux mot de passe, clickez sur le lien suivant:\r\n".$host.$this->password_page."?id=".$this->id."&activate=".$this->user_pw."&language=".$this->language;
			$msg[36] = "Votre demande a �t� bien trait�e et d'ici peu l'administrateur va l 'activer. Nous vous informerons quand ceci est arriv�.";
			$msg[37] = "Bonjour ".$this->user.",\r\n\r\nVotre compte est maintenant actif et il est possible d'y avoir acc�s.\r\n\r\nCliquez sur le lien suivant afin de rejoindre la page d'acc�s:\r\n".$host.$this->login_page."\r\n\r\nCordialement\r\n".$this->admin_name;
			$msg[38] = "Le mot de passe de confirmation de concorde pas avec votre mot de passe. Veuillez r�essayer";
			break;
			default:
			$msg[10] = "Email address and/or password are not valid.";
			$msg[11] = "Email address and/or password is empty!";
			$msg[12] = "A user with this e-mail address already exists.";
			$msg[13] = "Please check your e-mail and follow the instructions.";
			$msg[14] = "Sorry, an error occurred when trying to send to this address. Please try again.";
			$msg[15] = "Sorry, an error occurred please try again later.";
			$msg[16] = "The e-mail address is not valid.";
			$msg[17] = "The User Name/ID needs to be a minimum of ".LOGIN_LENGTH." characters.";
			$msg[18] = "Your request is processed. Login to continue.";
			$msg[19] = "Sorry, cannot activate your account.";
			$msg[20] = "There is no account to activate.";
			$msg[21] = "Sorry, this activation key is not valid!";
			$msg[22] = "No active account matches the e-mail address.";
			$msg[23] = "Please check your e-mail to get your new password.";
			$msg[25] = "Sorry, cannot activate your password.";
			$msg[26] = ""; // not used at the moment
			$msg[27] = "Please check your e-mail and activate your modification(s).";
			$msg[28] = "Your request must be processed...";
			//$msg[29] = "Hello,\r\n\r\nto activate your request click the following link:\r\n".$host.$this->login_page."?ident=".$this->id."&activate=".md5($this->user_pw)."&language=".$this->language;
			$msg[29] = " to activate your request click the following link:<br />".$host.$this->class_path.$this->password_page."?ident=".$this->id."&activate=".md5($this->user_pw)."&language=".$this->language;
			$msg[30] = "Your account is modified.";
			$msg[31] = "This e-mail address already exist, please use another one.";
			$msg[32] = "The field password (min. ".PW_LENGTH." char) is required.";
			$msg[33] = "Hello,\r\n\r\nthe new e-mail address must be validated, click the following link:\r\n".$host.$this->login_page."?id=".$this->id."&validate=".md5($this->user_pw)."&language=".$this->language;
			$msg[34] = "There is no e-mail address for validation.";
			//*LE $msg[35] = "Hello,\r\n\r\nEnter your new password next, please click the following link to enter the form:\r\n".$host.$this->password_page."?id=".$this->id."&activate=".$this->user_pw."&language=".$this->language;
			$msg[35] = "please enter your new password next. Click the following link to access the form:<br />".$host.$this->class_path.$this->password_page."/?id=".$this->id."&forgot_reset=".$this->user_pw."&language=".$this->language;
			$msg[36] = "Your request is processed and is pending for validation by the admin. \r\nYou will get an e-mail if it's done.";
			$msg[37] = "Hello ".$this->user.",\r\n\r\nThe account is active and it's possible to login now.\r\n\r\nClick on this link to access the login page:\r\n".$host.$this->login_page."\r\n\r\nkind regards\r\n".$this->admin_name;
			$msg[38] = "The confirmation password does not match the password. Please try again.";
		}
		return $msg[$num];
	}
}
//	 Added from admin_user.php -LE
class Admin_user extends Access_user {
	
	var $user_found = false;
	var $user_id;
	var $user_name;
	var $old_user_email;
	var $user_access_level;
	var $activation;

	function get_userdata($for_user, $type = "login") {
		if ($type == "login") {
			$sql = sprintf("SELECT id, login, email, access_level, active FROM %s WHERE login = '%s'", $this->table_name, trim($for_user));
		} else {
			$sql = sprintf("SELECT id, login, email, access_level, active FROM %s WHERE id = %d", $this->table_name, intval($for_user));
		}
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1) {
			$obj = mysql_fetch_object($result);
			$this->user_id = $obj->id;
			$this->user_name = $obj->login;
			$this->old_user_email = $obj->email;
			$this->user_access_level = $obj->access_level;
			$this->activation = $obj->active;
			if ($this->user_name != $_SESSION['user']) {
				$this->user_found = true;
			} else {
				$this->user_found = false;
				$this->the_msg = "It's not allowed to change your own data!";
			}
			mysql_free_result($result);
		} else {
			$this->the_msg = "Sorry, no data for this loginname!";
		}	
	}
	function update_user_by_admin($new_level, $user_id, $def_pass, $new_email, $active, $confirmation = "no") {
		//var_dump($new_email);
		//die;
		$this->user_found = true;
		$this->user_access_level = $new_level;
		if ($def_pass != "" && strlen($def_pass) < 4) {
			$this->the_msg = "Password is too short. Must be at least 4 characters.";
			$this->msgNo	="1";
		} else {
			if ($this->check_email($new_email)) {
				$sql = "UPDATE %s SET access_level = %d, email = '%s', active = '%s'";
				$sql .= ($def_pass != "") ? sprintf(", pw = '%s'", md5($def_pass)) : "";
				$sql .= " WHERE id = %d";
				$sql_compl = sprintf($sql, $this->table_name, $new_level, $new_email, $active, $user_id);
				if (mysql_query($sql_compl)) {
					$this->the_msg = "Data is modified for user with ID ";
					if ($confirmation == "yes") {
						if ($this->send_confirmation($user_id)) {
							$this->the_msg .= "<br>...a confirmation mail is send to the user.";
							$this->msgNo	="0";
						} else {
							$this->the_msg .= "<br>...ERROR no confirmation mail is send to the user.";
							$this->msgNo	="2";
						}
					}
				} else {
					$this->the_msg = "Database error, please try again!";
					$this->msgNo	="3";
					
				}
			} else {
				$this->the_msg = "The e-mail address is invalid!";
				$this->msgNo	="4";
				
			}
		}
/*		*/
	}
	function access_level_menu($curr_level, $element_name = "level") {
		$menu = "<select name=\"".$element_name."\">\n";
		for ($i = MIN_ACCESS_LEVEL; $i <= MAX_ACCESS_LEVEL; $i++) {
			$menu .= "  <option value=\"".$i."\"";
			$menu .= ($curr_level == $i) ? " selected>" : ">";
			$menu .= $i."</option>\n";
		}
		$menu .= "</select>\n";
		return $menu;
	}
	function activation_switch($formelement = "activation") {
		$radio_group = "<label for=\"".$formelement."\">Active?</label>\n";
		$first = ($this->activation == "y") ? " checked" : "";
		$second = ($first == " checked") ? "" : " checked"; 
		$radio_group .= "<input name=\"".$formelement."\" type=\"radio\" value=\"y\"".$first.">yes\n";
		$radio_group .= "<input name=\"".$formelement."\" type=\"radio\" value=\"n\"".$second.">no\n";
		return $radio_group;        
	}
}
?>
