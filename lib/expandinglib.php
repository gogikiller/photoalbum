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
function test()
	{
	return 'Successfully loading expandinglib...';
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
function detectGender($middlename)
    {if(empty($middlename))return 0;
   	else{mb_internal_encoding('utf-8');
        switch(mb_substr(mb_strtolower($middlename),-2))
        {case 'ич': return 1; break;
        case 'на': return 2; break;
        default: return 0; break;}}}
function rename_date_time($date)
	{
	$data_time=explode(" ",$date);
	$datam=explode("-",$data_time[0]);
		switch ($datam['1'])
			{
			case 01: $mes='января';
			break;
			case 02: $mes='февраля';
			break;
			case 03: $mes='марта';
			break;
			case 04: $mes='апреля';
			break;
			case 05: $mes='мая';
			break;
			case 06: $mes='июня';
			break;
			case 07: $mes='июля';
			break;
			case 8: $mes='августа';
			break;
			case 9: $mes='сентября';
			break;
			case 10: $mes='октября';
			break;
			case 11: $mes='ноября';
			break;
			case 12: $mes='декабря';
			break;
			default: $mes="-";
			break;
			}
		if(date("Y")!=$datam[0])
			$year="&nbsp;".$datam[0];
		else 
			$year='';
		if($datam['2'][0]=="0")
			$datam['2']=substr($datam['2'],1);
		$insert=$datam['2'].'&nbsp;'.$mes.$year.",&nbsp;".substr_replace($data_time[1],"",-3);
		return $insert;
	}
//function sign($n) {return ($n>0)?1:(($n<0)?-1:0);}				
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
function loader_form_red($obj)
	{
	$q_b=$this->bd->query('Select * from '.$obj->bd.' where id="'.$obj->bd_id.'"');
	if($q_b->num_rows>0){
	$b_i=$q_b->fetch_assoc();
	$form.='<div class="form-group"><form onsubmit="'.$obj->onsubmit.'(\''.$obj->bd_id.'\')">';
	foreach ($obj->element as $key=>$v) 
		{
		$v=(object)$v;
		if($v->id){	
		$form.=(($v->label)?'<label for="FR_'.$v->id.'">'.$v->label.'</label>':'');
		$in=' '.(($v->onclick)?'onclick="'.$v->onclick.'(\''.$obj->bd_id.'\')"':'').' '.(($v->onclick_f)?'onclick="'.$v->onclick_f.'"':'').' '.(($v->class)?'class="'.$v->class.'"':'').
		(($v->tooltip)?' data-toggle="tooltip" data-placement="right" title="'.$v->tooltip.'"':'');
		if($v->global_type=='' || $v->global_type=='input')
			$form.='<input id="FR_'.$v->id.'" class="form-control" type="'.(($v->type)?$v->type:'text').'" '.$in.' value="'.(($v->value)?($v->value):($v->d_value?'':($v->d_funct?call_user_func($v->d_funct,$b_i[($v->id)]):$b_i[($v->id)]))).'" autocomplete="off">';
		elseif($v->global_type=='textarea')
			$form.='<textarea '.$in.'>'.(($v->value)?$v->value:($v->d_value?'':$b_i[($v->id)])).'</textarea></br>';
		elseif($v->global_type=='select'||$v->global_type=='multiple')
			{
			$form.='<select class="form-control" id="FR_'.$v->id.'" '.$in.' data-placeholder="'.($v->placeholder!=''?$v->placeholder:'...').'"'.($v->global_type=='multiple'?' multiple ':'').'data-placeholder="'.($v->placeholder!=''?$v->placeholder:'...').'"'.($v->global_type=='multiple'?' multiple ':'').'>';
			foreach ($v->options as $k_o=>$v_o)
				{
				$form.='<option value="'.$k_o.'"';	
				if($v->global_type=='multiple')
					$form.=(($v->options_select{$k_o})?' selected ':'');
				else
					$form.=((!$v->d_value && $k_o==$b_i[($v->id)])?' selected ':'');
				$form.='>'.$v_o.'</option>';
				}
			$form.='</select>';
			}
		elseif($v->global_type=='span')
			$form.='<span  id="'.$v->id.'" '.$in.'>'.(($v->value)?$v->value:'').'</span>';
		elseif ($v->global_type=='button') 
			$form.='<button data-loading-text="Загрузка..." id="'.$v->id.'" type="button" '.$in.'>'.($v->icon!=""?'<span class="glyphicon '.$v->icon.'" aria-hidden="true"></span> ':'').$v->value.'</button>';		
		}}
	} else return $this->displayError('404');
	return $form.'</form></div>';
	}	
function loader_form_add($obj)
	{
	$form.='<div class="'.((!$obj->dclass)?'form-group':$obj->dclass).'"><form onsubmit="'.$obj->onsubmit.'()" '.(($obj->fclass)?'class="'.$obj->fclass.'"':'').'>';
	foreach ($obj->element as $key=>$v) 
		{
		$v=(object)$v;
		if($v->id){	
		$form.=(($v->label)?'<label for="FR_'.$v->id.'">'.$v->label.'</label>':'');
		$in=' '.(($v->onclick)?'onclick="'.$v->onclick.'(\''.$obj->page.'\')"':'').' '.(($v->onclick_f)?'onclick="'.$v->onclick_f.'"':'').' '.(($v->class)?'class="'.$v->class.'"':'').
		(($v->tooltip)?'data-toggle="tooltip" data-placement="right" title="'.$v->tooltip.'"':'');
		if($v->global_type=='' || $v->global_type=='input')
			$form.='<input id="FR_'.$v->id.'" class="form-control" type="'.(($v->type)?$v->type:'text').'" '.$in.' '.(($v->value)?'value="'.$v->value.'"':'').' autocomplete="off">';
		elseif($v->global_type=='textarea')
			$form.='<textarea class="form-control" id="FR_'.$v->id.'" '.$in.'>'.(($v->value)?$v->value:'').'</textarea>';
		elseif($v->global_type=='select'||$v->global_type=='multiple')
			{
			$form.='<select class="form-control" id="FR_'.$v->id.'" '.$in.' data-placeholder="'.($v->placeholder!=''?$v->placeholder:'...').'"'.($v->global_type=='multiple'?' multiple ':'').'>';
			foreach ($v->options as $k_o=>$v_o) 
				$form.='<option value="'.$k_o.'">'.$v_o.'</option>';
			// $form.=($v->options).'';
			$form.='</select>';
			}
		elseif($v->global_type=='span')
			$form.='<span id="'.$v->id.'" '.$in.'>'.(($v->value)?$v->value:'').'</span>';
		elseif ($v->global_type=='button') 
			$form.='<button data-loading-text="Загрузка..." id="'.$v->id.'" type="button" '.$in.'>'.($v->icon!=""?'<span class="glyphicon '.$v->icon.'" aria-hidden="true"></span> ':'').$v->value.'</button>';
		}
		unset($in);
		}
	return $form.'</form></div>';
	}
function modalwin($obj)
	{
	$inp->title=($obj->icon!=""?'<span class="glyphicon '.$obj->icon.'" aria-hidden="true"></span> ':'').$obj->title;
	$inp->header_title=$obj->title;
	$inp->content=$obj->content;
	$inp->status='ok';
	if($obj->buttons)
		{
		foreach ($obj->buttons as $v) 
			{
			$v=(object)$v;
			$in=' '.(($v->onclick)?'onclick="'.$v->onclick.'(\''.$obj->page.'\')"':'').' '.(($v->onclick_f)?'onclick="'.$v->onclick_f.'"':'').' '.(($v->class)?'class="'.$v->class.'"':'').(($v->tooltip)?'data-toggle="tooltip" data-placement="right" title="'.$v->tooltip.'"':'');
			if($v->global_type=='span')
			$inp->footer.='<span id="'.$v->id.'" '.$in.'>'.(($v->value)?$v->value:'').'</span>';
			elseif ($v->global_type=='button') 
			$inp->footer.='<button data-loading-text="Загрузка..." id="'.$v->id.'" type="button" '.$in.'>'.($v->icon!=""?'<span class="glyphicon '.$v->icon.'" aria-hidden="true"></span> ':'').$v->value.'</button>';
			}
		}
	return $inp;
	}
function constructor_table($obj)
	{
	if($obj->table){$q=$this->bd->query('Select id,'.implode(',',$obj->th_bd).' from '.($obj->table).(($obj->table_sql)?$obj->table_sql:''));
	$pagination.='<nav>
	<ul class="pagination">
	<li><a href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
	<li class="active"><a href="#">26 Января - 1 Февраля 2015<span class="sr-only">(current)</span></a></li>
	<li><a href="#" aria-label="Next"><span aria-hidden="true">»</span></a></li></ul></nav>';
	$t.='
	<table class="table table-striped table-hover">';
	$c_t=count($obj->th);
	foreach($obj->th as $name_th)
		$t.='<th>'.$name_th.'</th>';
	while($in_tb=$q->fetch_row())
		{
		$t.='<tr '.(($obj->tr_click)?'onclick="'.$obj->tr_click.'('.$in_tb[0].')"':'').'>';
		for($c=0;$c<$c_t;$c++)
			$t.='<td>'.(($obj->td_funct[$c])?call_user_func($obj->td_funct[$c],$in_tb[$c+1]):$in_tb[$c+1]).'</td>';
		$t.='</tr>';
		}
	$t.='</table>';}
	else
		$t.=$this->displayError(403);
	return $t;	
	}
function rudate($date) {
        $translate = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "Января",
            "Jan" => "Январь",
            "February" => "Февраля",
            "Feb" => "Февраль",
            "March" => "Марта",
            "Mar" => "Март",
            "April" => "Апреля",
            "Apr" => "Апрель",
            "May" => "Мая",
            "May" => "Май",
            "June" => "Июня",
            "Jun" => "Июнь",
            "July" => "Июля",
            "Jul" => "Июль",
            "August" => "Августа",
            "Aug" => "Август",
            "September" => "Сентября",
            "Sep" => "Сентябрь",
            "October" => "Октября",
            "Oct" => "Октябрь",
            "November" => "Ноября",
            "Nov" => "Ноябрь",
            "December" => "Декабря",
            "Dec" => "Декабрь",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое"
        );
        return strtr($date, $translate);
    }
function dateforform($data)
	{
	return DateTime::createFromFormat('Y-m-d',$data)->format('d/m/Y');
	}
function constructor_tables($obj)
	{
	//var_dump($obj);	
	if($obj->pagein){
	$div.='<nav>
	<ul class="pagination">';
	$div.=(($obj->previousbutton_funct)?'<li><a href="javascript:" onclick="'.$obj->previousbutton_funct.'" aria-label="Previous"><span aria-hidden="true">«</span></a></li>':'').
	'<li class="active"><a href="javascript:">'.$obj->pagein.'</a></li>'.(($obj->nextbutton_func)?'<li><a href="javascript:" onclick="'.$obj->nextbutton_func.'" aria-label="Next"><span aria-hidden="true">»</span></a></li>':'');
	$div.='</ul>'.($obj->Bname!=''?'<button data-loading-text="Загрузка..." onclick="'.$obj->Bfunct.'" class="btn '.$obj->Bclass.'"> '.($obj->Bicon!=""?'<span class="glyphicon '.$obj->Bicon.'" aria-hidden="true"></span> ':'').$obj->Bname.'</button>':'').'</nav>';
	$div.=$obj->button_nav;
	}
	$div.='<table class="table table-striped table-hover">';
	$c_t=count($obj->th);
	foreach($obj->th as $name_th){
		$div.='<th>'.$name_th.'</th>';
	}
	$div.=$obj->t.'</table>';
	return $div;
	}			
}
?>