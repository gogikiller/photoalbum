<?
class connector extends mysqli
{
private $login='name';
private $passwd='12345';
private $bd='photoalbum';
private $ad='localhost';
function __construct()
	{
	parent::__construct($this->ad,$this->login,$this->passwd,$this->bd);
	if(mysqli_connect_error()) die('База данных не доступна...');
	parent::set_charset("utf8");
	}
function disconnect()
	{
	parent::close();
	}		
}
?>