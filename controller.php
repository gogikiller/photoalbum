<?
if(!isset($_SESSION))session_start();
spl_autoload_register(function ($class) {include 'lib/'.$class.'.php';});
class controller extends expandinglib
{
public $BaseUrl='/';
public $NameSystem='';
public $bd=false;
public $global_user_id=0;	
function __construct($BaseUrl)
	{
	$this->BaseUrl=$BaseUrl;
	$this->bd=new connector;
	date_default_timezone_set('Asia/Yekaterinburg');
	}
function __destruct()
	{
	$this->bd->close();
	}
function controller_query($query,$file)
	{	
	$q=$this->check_in($query['query']);	
	if($q=='on_logon')
		$inp=autorization::access_on($query['paket']);
	else
		{
		if($page=$this->checkJSkey($query['JSkey']))
			{
			$this->accesscheck();
			if($q=='savephoto'&&$page=='index')
				$inp=photos::savephoto($file);
			elseif ($q=='dellphoto'&&$page=='index') 
				$inp=photos::dellphoto($query);
			elseif($q=='getphoto'&&$page=='index')
				$inp=photos::showJSONphoto($query);
			elseif ($q=='getphotostat'&&$page=='index') 
				$inp=photos::showJSONstat($query);		
			else
				$inp=$this->displayError('400','json');				
			}
		else
			$inp=$this->displayError('403','json');
		}
	return $inp;
	}		
}
header('Content-Type: application/json');
$controller=new controller('/test/');
echo $controller->controller_query($_POST,$_FILES);
?>