<?php
	session_start();
	include("inc.php");
    $username	= isset($_POST['username']) ? trim($_POST['username']) : '';
    $password	= isset($_POST['password']) ? trim($_POST['password']) : '';
    $email		= isset($_POST['email']) ? trim($_POST['email']) : '';
	$other		= isset($_POST['other']) ? $_POST['other'] : array();
	$action		= isset($_POST['action']) ? trim($_POST['action']) : '';

/* 注册新会员 */
if ($action == 'register')
{
    if(empty($_POST['agreement']))
    {
	   echo "- 您没有接受协议";
    }
    elseif (strlen($username) < 3)
    {
		echo "- 用户名长度不能少于 3 个字符。";
    }

    elseif (strlen($password) < 6)
    {
	   echo "- 登录密码不能少于 6 个字符。";
    }
	
	else
	{
		$sql = "Insert users (user_name,email,`password`,msn,qq,office_phone,home_phone,mobile_phone,reg_time,visit_count) values ('" . $username . "','" . $email . "',md5('" . $password . "'),'" . $other['msn'] ."','" . $other['qq'] . "','" . $other['office_phone'] . "','" . $other['home_phone'] . "','" . $other['mobile_phone'] . "',now(),1)";

		if($db->query($sql))
		{
			$_SESSION['username']   = $username;
			$_SESSION['user_id']    = $db->insert_id();
			$_SESSION['visit']	    = 1;
			$_SESSION['user_login'] = 1;
			$_SESSION['etc']	    = "您已成功注册为本站会员，";
			header("Location:user_index.php");
		}
	}

}

/* 会员修改资料 */
if ($action == 'mod')
{
	$sql = "update users set email='" . $email . "',msn='" . $other['msn'] . "',qq='" . $other['qq'] . "',office_phone='" . $other['office_phone'] . "',home_phone='" . $other['home_phone'] . "',mobile_phone='" . $other['mobile_phone'] . "' where user_id = '" . $_SESSION['user_id'] . "'";

	if($db->query($sql))
	{
		$_SESSION['etc'] = "您已成功修改了会员资料，";
		header("Location:user_index.php");
	}
}

/* 验证用户注册用户名是否可以注册 */
elseif ($action == 'check_user')
{
    if (strlen($username) < 3)
    {
		echo "- 用户名长度不能少于 3 个字符。";
    }
	elseif(!check_user($username))
	{
		echo "* 用户名已经存在,请重新输入";
	}
	else
	{
		echo "* 可以注册";
	}
    //$username = trim($_GET['username']);
}

/* 验证用户邮箱地址是否被注册 */
elseif($action == 'check_email')
{
    $email = trim($_POST['email']);
    if (!check_email($email))
    {
        echo "* 邮箱已存在,请重新输入";
    }
    else
    {
        echo "* 可以注册";
    }
}

/* 验证用户欲修改的邮箱地址是否被注册 */
elseif($action == 'check_mod_email')
{
    $email = trim($_POST['email']);
	if (!check_mod_email($username,$email))
	{
		echo "* 邮箱已存在,请重新输入";
	}
	else
	{
		echo "* 可以更改为该邮箱";
	}
}

/* 会员上传照片 */
if ($action == 'upload')
{
	if (!move_uploaded_file($_FILES['upload_file']['tmp_name'],"member_photo/" . $_SESSION['user_id'] . ".jpg")) {
		$_SESSION['etc'] = "上传照片失败，请重新上传，";
	}
	else
	{
		$_SESSION['etc'] = "您已成功上传照片，";
	}
		header("Location:user_index.php");
}



function check_user($username)
{
	global $db;
	if($db->getOne("select count(*) from users where user_name='" . $username . "'") > 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function check_email($email)
{
	global $db;
	if($db->getOne("select count(*) from users where email='" . $email . "'") > 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function check_mod_email($user_name,$email)
{
	global $db;
	if($db->getOne("select count(*) from users where user_name<>" . $user_name . " and email='" . $email . "'") > 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

?>