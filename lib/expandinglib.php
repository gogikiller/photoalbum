<?
class expandinglib
{	
function newJSkey($uri)
	{
	return urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$_SESSION['key_a_clients'],substr(rand(190,999),0,3).md5($_SESSION['key_a_clients']).substr(md5(rand(1,99)),0,2).$uri,MCRYPT_MODE_ECB,1)));
	}
function checkJSkey($key)
	{
	$str=mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$_SESSION['key_a_clients'],base64_decode(urldecode($key)),MCRYPT_MODE_ECB,1);
	if(substr($str,3,32)==md5($_SESSION['key_a_clients']))
		return  rtrim(substr($str,37));
	else
		return false;
	}		
function check_in($str)
	{
	$str=trim($str);
	$str=strip_tags($str);
	$str = htmlspecialchars($str);
	$str=addslashes($str);
	return $str;
	}	
function DtDb()
	{
	return date('Y-m-d H:i:s');
	}	
function accesscheck()
	{
	if($_COOKIE['key_aut_clients']!="" and $_SESSION['key_a_clients']=="")
		{
		$_SESSION['key_a_clients']=substr_replace(substr_replace($_COOKIE['key_aut_clients'],'',0,3),'',-3,strlen($_COOKIE['key_aut_clients']));
		SetCookie("key_aut_clients","",-1,$this->BaseUrl);
		$new_session=true;
		}	
	if($_SESSION['key_a_clients']!="")
		{
		$q1=$this->bd->query('Select * from session_a where key_a="'.$_SESSION['key_a_clients'].'"');
	    if(mysqli_num_rows($q1)>0)
	       	{
			$q2=mysqli_fetch_array($q1);
			SetCookie("key_aut_clients","");
	       	SetCookie("key_aut_clients",substr(md5(rand(100,120)),0,3).$q2['key_a'].rand(100,999),time()+1296000,$this->BaseUrl);
	       	$_SESSION['key_a_clients']=$q2['key_a'];
	       	$update_users=$this->bd->query('UPDATE session_a SET date_time="'.date('Y-m-d H:i:s').'" '.($new_session?',session_id="'.session_id().'",session_ip="'.$_SERVER["REMOTE_ADDR"].'"':'').' WHERE key_a="'.$q2['key_a'].'"');
			$this->global_user_id=$q2['user_id'];
			$dop_users_inf=$this->bd->query('Select permission,soname from users where id="'.$q2['user_id'].'"')->fetch_object();
			$this->global_user_permission=$dop_users_inf->permission;
			$this->global_user_name=$dop_users_inf->soname;
			return true;
	       	}
	    else 
	       	{
	       	//$this->logerr_error((object)array('function'=>__FUNCTION__,'text'=>$_SESSION['key_a_clients'],'id_error'=>'003','class_error'=>'1'));
	       	return false;
	       	}
		}
	else 
		return false;
	}				
function displayError($err,$type='html')
	{
	$errors=array('400'=>'Не верный запрос!','403'=>'Ошибка доступа!','404'=>'Объект не найден!');
	//отправка на емайл
		$e->content='<div class="alert alert-danger alert-dismissible fade in">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
	<h4>'.$errors[$err].'</h4></p></div>';
	if($type=='json')
		{
		$e->status='no';
		$e->codeerr=$err;
		$e->err=$errors[$err];
		//$e->content=$errors[$err];
		$e=json_encode($e);
		return $e;
		}		
	elseif($type=='html')
		return $e->content;	
	}			
}
?>