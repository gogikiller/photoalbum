<?
class autorization
{  
function logout()
    {
    if($_SESSION['key_a_clients']!="")
        $del_s=$this->bd->query('Delete from session_a where key_a="'.$_SESSION['key_a_clients'].'"');
    SetCookie("key_aut_clients","",0,$this->BaseUrl);
    $_SESSION['key_a_clients']="00000000000000";
    header('location:'.$this->BaseUrl);
    return true;
    }    
function load_autorization_form()
	{
	$frm='<form class="form-signin" onsubmit="on_logon(); return false;">
        <h2 class="form-signin-heading">Авторизация</h2>
        <label for="inputEmail" class="sr-only">Логин</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Логин" required autofocus>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
        <div id="captcha">'.($_SESSION['captcha_count']>3?'
		<img src="'.$this->BaseUrl.'img/captcha.php?'.rand(1,49).'">
		<input type="text" class="form-control" id="captha_inp" placeholder="Цифры с картинки">':'').'
		</div>
        <p class="bg-danger" id="error_form_login"></p>
        <button class="btn btn-lg btn-primary btn-block" type="submit" id="logon_button" data-loading-text="Загрузка..." onclick="return false;" autocomplete="off">Войти</button>
      </form>';
	return $frm;
	}
function checklogin($login)
    {
    return ((($this->bd->query('Select name from users where login="'.$login.'"')->num_rows)>0)?true:false);
    }
function access_on($paket)
    {
    $paket=json_decode($paket);
    $paket->email=$this->check_in($paket->email); 
    $paket->password=$this->check_in($paket->password);
    $paket->captcha=$this->check_in($paket->captcha);
    $input->status="no";
    $input->err='';
    $input->img=$this->BaseUrl.'img/captcha.php?'.rand(0,99);
    $input->patch=$this->BaseUrl;
    if(!filter_var($paket->email, FILTER_VALIDATE_EMAIL))
        $input->err.="Эл.почта введена не коректно!</br>";
    elseif(!self::checklogin($paket->email))
        $input->err.="Пользователь не зарегистрирован!</br>";
    if($paket->password=="")
        $input->err.="Введите пароль!</br>";    
    if($_SESSION['captcha_count']>3)
        if($_SESSION['capthaid']!=$paket->captcha)
            $input->err.="Код с картинки введен не коректно!</br>"; 
    if($input->err=='')
        {
        if($user_q=$this->bd->query('Select * from users where login="'.$paket->email.'"'))
            {
            $user=$user_q->fetch_object();   
            if($user->password==md5(md5($user->salt.($paket->password)."lalalalalala")))
                {  
                $new_key=substr(md5(md5(rand(10,99).time().uniqid().$_SERVER["REMOTE_ADDR"])),0,20); 
                $update_user=$this->bd->query("UPDATE users SET last_date='".$this->DtDb()."',last_ip='".$_SERVER["REMOTE_ADDR"]."' WHERE id='".$user->id."'");
                $ins_session=$this->bd->query('Insert into session_a values ("'.$new_key.'","'.$user->id.'","'.session_id().'","'.$_SERVER["REMOTE_ADDR"].'","'.$this->DtDb().'")');
                SetCookie("key_aut_clients",substr(md5(rand(100,500)),0,3).$new_key.substr(rand(100,999),0,3),time()+1296000,$this->BaseUrl);//шифровать куки!!!
                $_SESSION['captcha_count']=0;   
                $_SESSION['key_a_clients']=$new_key;
                $input->status="ok";
                }
            else
                $input->err.="Эл.почта и пароль введены не коректно!</br>";
            }
        else
            $input->err.="Пользователь зарегистрирован!</br>";
        }       
    if($input->err!='')
        {
        if($_SESSION['captcha_count']=="")
            $_SESSION['captcha_count']=1;
        else
            $_SESSION['captcha_count']++;
        }               
    if($_SESSION['captcha_count']>3)
        $input->captcha=true;  
    return json_encode($input); 
    }
function generate_user()
	{
	$new->login='test@test.ru';
	$new->password='111111';
	$new->name='User';
	$new->soname='Super';	
    $us=self::save_user(json_encode($new));
    return $us->status!='ok'?$us->err:$us->div;
	}
function save_user($paket)
	{
	$paket=json_decode($paket);
	$paket->name=$this->check_in($paket->name);	
	$paket->soname=$this->check_in($paket->soname);	
	$paket->password=$this->check_in($paket->password);	
	$paket->login=$this->check_in($paket->login);	
	$paket->id=$this->check_in($paket->id);	
	$input->status="no";
	$input->err='';
	if($paket->id!="")
		{
        $user_q=$this->bd->query('Select login from users where id="'.$paket->id.'"');
		if($user_q->affected_rows==0)
			$input->err='Ошибка';
		}
	if(!preg_match("/[а-я a-z]/i", $paket->name) || mb_strlen($paket->name)<2)
		$input->err.="Имя введено не коректно!</br>";
	if(!preg_match("/[а-я a-z]/i", $paket->soname) || mb_strlen($paket->soname)<2)
		$input->err.="Фамилия введена не коректно</br>";
	if(!preg_match("/[a-z]/i", $paket->login) || mb_strlen($paket->login)<4)
		$input->err.="Логин введен не коректно!</br>";
	else
		{
		if($paket->id!="")
			{
			if($user_q->fetch_object->login!=$paket->login && self::checklogin($paket->login))
				$input->err.="Пользователь с таким логином уже зарегистрирован!</br>";	
			}
		elseif(self::checklogin($paket->login))
			$input->err.="Пользователь с таким логином уже зарегистрирован!</br>";
		}
	if($paket->password=="")
		$input->err.="Пароль введен не коректно!</br>";
	elseif(mb_strlen($paket->password)<5)
		$input->err.="Пароль должен быть больше пяти симбволов!</br>";
	if($input->err=='')
		{
		$salt=md5(substr(uniqid().time(),0,10));
		$query_id=md5(uniqid().time());              
		$password_bd=md5(md5($salt.$paket->password."lalalalalala"));
		if($paket->id=="")
			$add_new_user=$this->bd->query('Insert into users values (0,"'.$paket->login.'","'.$password_bd.'","'.$paket->name.'","'.$paket->soname.'","","'.$salt.'",1,"'.$_SERVER["REMOTE_ADDR"].'","'.$this->DtDb().'","")');
		else
			$add_new_user=$this->bd->query('Update users set login="'.$paket->login.'",password="'.$password_bd.'",name="'.$paket->name.'",soname="'.$paket->soname.'",salt="'.$salt.'" where id="'.$paket->id.'"');
		//$add_new_user=true;
		if($add_new_user)
			{
			$user_id=$add_new_user->insert_id;
			$input->status="ok";
			$input->div='Пользователь удачно добавлен!</br>';
			}
		else
			$input->err.="База данных временно не доступна..</br>";	
		}
    return $input;
	}			
}
?>