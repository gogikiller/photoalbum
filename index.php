<?
$load_time_start=microtime();
if(!isset($_SESSION))session_start();
spl_autoload_register(function ($class) {include 'lib/'.$class.'.php';});
include('lib/page_constructor.php');
$page_c=new PageConstructor('/test/');
$page=$page_c->loader_pages($_SERVER['REQUEST_URI']);
?>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?echo $page->title;?></title>
    <link href="/test/css/bootstrap.min.css" rel="stylesheet">
    <?echo $page->libs;?>
  </head>
  <body>
<?echo $page->content;?>
</body><?$w=microtime()-$load_time_start;echo '<!--зaгрузилося за:'.$w.'-->';?>
</html>