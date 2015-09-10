<?
if(!isset($_SESSION))session_start();
class PageConstructor extends expandinglib
{
public $BaseUrl='/';
public $NameSystem='';
public $bd=false;
public $global_user_id=0;
public $CompanyName='Фотоальбом';
public $global_event='';
function __construct($BaseUrl)
	{	
	//include('connector.php');
	$this->BaseUrl=$BaseUrl;
	$this->bd=new connector;
	date_default_timezone_set('Asia/Yekaterinburg');
	}
function __destruct()
	{
	if($this->bd)
		$this->bd->close();
	}
function transform_adrr($a)
	{
	return mb_substr(str_replace(' ',"",urldecode($a)),iconv_strlen($this->BaseUrl));
	}
function load_navbar()
	{
	return '<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
         <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="'.$this->BaseUrl.'">'.$this->CompanyName.'</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        <p class="nav navbar-nav text-center"><button type="button" id="ld_ph" class="btn btn-success navbar-btn"><span class="glyphicon glyphicon-camera" aria-hidden="true"></span> Загрузить фото</button></p>
         <ul class="nav navbar-nav navbar-right">
            <li><a href="exit" id="exitButton">'.$this->global_user_name.', <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Выход</a></li>
          </ul>
        </div>
      </div>
    </nav>';
	}
function loader_pages($uri)
	{
	$uri=$this->transform_adrr($uri);	
	if($uri=='test')
  		{
  		$pg->content=autorization::generate_user();
  		}
	elseif(!$this->accesscheck())
		{
		$pg->libs='<link href="'.$this->BaseUrl.'css/signin.css" rel="stylesheet">';
		$pg->libs.='<script src="'.$this->BaseUrl.'js/jquery-2.1.1.min.js" type="text/javascript"></script>';
		$pg->libs.='<script src="'.$this->BaseUrl.'js/admins.js" type="text/javascript"></script>';
		$pg->libs.='<script src="'.$this->BaseUrl.'js/bootstrap.js" type="text/javascript"></script>';
		$pg->title='Пожалуйста войдите';
		$pg->content.=autorization::load_autorization_form();
  		}
  	elseif($uri=='exit')
		autorization::logout();
  	else
  		{
  		$pg->title='Фотоальбом';
  		$pg->libs.='<script type="text/javascript">var JSkey=encodeURIComponent("'.$this->newJSkey('index').'");</script>';
  		$pg->libs.='<script src="'.$this->BaseUrl.'js/jquery-2.1.1.min.js" type="text/javascript"></script>';
  		$pg->libs.='<script src="'.$this->BaseUrl.'js/bootstrap.js" type="text/javascript"></script>';
  		$pg->libs.='<script src="'.$this->BaseUrl.'js/ekko-lightbox.min.js" type="text/javascript"></script>';
  		$pg->libs.='<script src="'.$this->BaseUrl.'js/dropzone.js" type="text/javascript"></script>';
  		$pg->libs.='<script src="'.$this->BaseUrl.'js/photoint.js" type="text/javascript"></script>';
  		$pg->libs.='<link href="'.$this->BaseUrl.'css/dashboard.css" rel="stylesheet">';
  		$pg->libs.='<link href="'.$this->BaseUrl.'css/ekko-lightbox.min.css" rel="stylesheet">';
  		$pg->content=$this->load_navbar().photos::load_photoalbum();
  		}
	return $pg;
	}		
}
?>