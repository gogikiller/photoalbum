<?
class photos
{
static private $SupMIME=array('image/gif','image/jpeg','image/png','image/x-png','image/bmp','image/x-icon','image/tiff');
static private $DirUpload='/var/www/test/userdir/';
static private $NameThumb='th_';
static public $DirPhoto='userdir/';
static private $SizeThumb=240;
function load_photoalbum()
	{
	$div.='<div id="mm" style="display:none;"></div>';
	$phq=$this->bd->query('Select * from photo where autor_id="'.$this->global_user_id.'" order by id desc limit 0,8');
	$c=0;
	$phq_all=$this->bd->query('Select * from photo where autor_id="'.$this->global_user_id.'"');
	if($phq_all->num_rows>0)
		{
		while($ph=$phq->fetch_object())
			{
			if($c%4==0)
				$div.='<div class="row">';
			$div.='
			<div class="col-xs-7 col-md-3">
				<div class="thumbnail">
      				<a href="'.$this->BaseUrl.self::$DirPhoto.$ph->dir.'" data-toggle="lightbox" data-title="'.$ph->name.'" data-gallery="galleryone">
      					<img src="'.$this->BaseUrl.self::$DirPhoto.($ph->thumb?self::$NameThumb:'').$ph->dir.'" alt="'.$ph->name.'">
      				</a>
      				<div class="caption">
      					<p>
      						<em>'.$ph->name.'</em><a href="'.$this->BaseUrl.self::$DirPhoto.$ph->dir.'" class="btn btn-link btn-sm"><span class="glyphicon glyphicon-save-file" aria-hidden="true"></span></a>
        					<button id="dell_photo_'.$ph->id.'" type="button" class="btn btn-danger pull-right btn-xs">
        						&nbsp;<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;
        					</button>
        				</p>
        			</div>
       			</div>
       	</div>';
      	if($c%4==3)
      		$div.='</div>';
		$c++;
		}
		if($c%4>0)
      		$div.='</div>';
      		$href.='
      	<p class="text-center">
      		<a onclick="return false;" '.($phq_all->num_rows>8?'':'style="display:none"').' id="href_all" href="javascript:void(0);" class=" btn btn-sm btn-info">
      			<span class="glyphicon glyphicon-chevron-down"></span> Посмотреть еще
      		</a>
      	</p>';
      	}
    else $div.='';
	return '<div class="container">'.$div.'</div>'.$href;
	}
function dellphoto($p)
	{
	$jn->status='no';
	$jn->name='...:';
	$p=json_decode($p['p']);
	$p->id=$this->check_in($p->id);
	if(is_numeric($p->id))
		{
		$file_q=$this->bd->query('Select * from photo where id="'.$p->id.'"');
		if($file_q->num_rows>0)
			{
			$file=$file_q->fetch_object();
			if(!$this->bd->query('Select id from photo where hash="'.$file->hash.'" and id<>"'.$file->id.'"')->num_rows>0)
				{
				unlink(self::$DirUpload.$file->dir);
				if($file->thumb)
					unlink(self::$DirUpload.self::$NameThumb.$file->dir);
				}
			$this->bd->query('Delete from photo where id="'.$file->id.'"');	
			if($this->bd->affected_rows>0)
				$jn->status='ok';
			else
				{
				$jn->err='Ошибка удаления из бд!!!';
				$jn->name=$file->name;
				}
			}
		else
			$jn->err='Проверть ввод!!!';
		}
	else
		$jn->err='Проверть ввод!!!';
	return json_encode($jn);
	}
function savephoto($p)
	{
	$p=(object)$p['file'];
	$file->hash=hash_file('md5',$p->tmp_name);
	$jn->status='no';
	$jn->err='';
	$old_query_bd=$this->bd->query('Select * from photo where hash="'.$file->hash.'"');
	if($old_query_bd->num_rows)
		{
		$file=$old_query_bd->fetch_object();
		$jn->dir=$file->dir;
		$file->name=$this->check_in($p->name);
		}
	else
		{
		if(in_array(mime_content_type($p->tmp_name),self::$SupMIME))
			{
			$file->name=$this->check_in($p->name);
			$jn->dir=uniqid(8).'.'.(mb_substr(strrchr($p->name,'.'),1));
			$file->dir=self::$DirUpload.$jn->dir;
			if(!move_uploaded_file($p->tmp_name,$file->dir))
				$jn->err='Файл не загружен!';
			else
				$file->thumb=(self::makeThumbnails($file->dir,$jn->dir)?1:0);
			}
		else
			$jn->err='Формат файла не поддерживается!!!';
		}
	if(!$jn->err)
		{
		$query_save_photo=$this->bd->prepare('Insert into photo values (0,?,?,?,?,?,?)');
		$query_save_photo->bind_param('sssssi',$file->name,$this->global_user_id,$jn->dir,$this->DtDb(),$file->hash,$file->thumb);
		if($query_save_photo->execute())
			{
			$jn->status='ok';
			$jn->Thumbdir=$this->BaseUrl.self::$DirPhoto.($file->thumb?self::$NameThumb:'').$jn->dir;
			$jn->dir=$this->BaseUrl.self::$DirPhoto.$jn->dir;
			$jn->id=$query_save_photo->insert_id;
			$jn->name=$file->name;
			}
		else
			$jn->err='Ошибка базы данных!!!';
		}
	return json_encode($jn);
	}
function makeThumbnails($file,$file_name)
	{
	$supformat=array('image/gif','image/jpeg','image/png','image/x-png');
	$file_info=getimagesize($file);
	if(in_array($file_info['mime'],$supformat))
		{
		if($file_info['mime']=='image/gif')
			$type_sr='gif';
		elseif($file_info['mime']=='image/png'||$file_info['mime']=='image/x-png')
			$type_sr='png';
		elseif($file_info['mime']=='image/jpeg')
			$type_sr='jpeg';
		$imagecreatefrom_func='imagecreatefrom'.$type_sr;
		$image_func='image'.$type_sr;
		$src=$imagecreatefrom_func($file);
		if($file_info[0]>self::$SizeThumb)
			{
			$ratio=$file_info[0]/self::$SizeThumb;
			$w_new=round($file_info[0]/$ratio);
			$h_new=round($file_info[1]/$ratio);
			}
		elseif($file_info[0]<self::$SizeThumb)
			{
			$ratio=self::$SizeThumb/$file_info[0];
			$w_new=round($file_info[0]*$ratio);
			$h_new=round($file_info[1]*$ratio);
			}
		else
			{
			$w_new=$file_info[0];
			$h_new=$file_info[1];
			}
		$dest=imagecreatetruecolor($w_new, $h_new);
		imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_new, $h_new, $file_info[0], $file_info[1]);
		$new_file_name=self::$DirUpload.self::$NameThumb.$file_name;
		if(file_exists($new_file_name))
			unlink($new_file_name); 
		$image_func($dest,$new_file_name);
		imagedestroy($dest);
		imagedestroy($src);
		return true;
		}
	else 
		return false;
	}
function showJSONstat($p)
	{}
function showJSONphoto($p)
	{
	$jn->status='no';
	$jn->arrayPhoto=array();
	$jn->showhref=false;
	$p=json_decode($p['p']);
	$p->down_id=$this->check_in($p->down_id);
	$p->up_id=$this->check_in($p->up_id);
	$p->count=$this->check_in($p->count);
	if(is_numeric($p->down_id))
		{
		$jn->pos='down';
		$query_show_photo=$this->bd->query('Select * from photo where id<'.$p->down_id.' order by id desc limit 0,'.(is_numeric($p->count)?$p->count:8));	
		$query_all_photo=$this->bd->query('Select * from photo where id<'.$p->down_id.' order by id desc limit '.(is_numeric($p->count)?$p->count:8).',8');	
		}
	elseif(is_numeric($p->up_id))
		{
		$jn->pos='up';
		$query_show_photo=$this->bd->query('Select * from photo where id>'.$p->up_id.' order by id asc limit 0,'.(is_numeric($p->count)?$p->count:8));	
		$query_all_photo=$this->bd->query('Select * from photo where id<'.$p->up_id.' order by id desc limit '.(is_numeric($p->count)?$p->count:8).',8');
		}
	if($query_show_photo->num_rows>0)
		{	
		while($ph=$query_show_photo->fetch_object())
			{
			$jn->arrayPhoto[]=array(
				'id'=>$ph->id,
				'dir'=>$this->BaseUrl.self::$DirPhoto.$ph->dir,
				'name'=>$ph->name,
				'Thumbdir'=>$this->BaseUrl.self::$DirPhoto.($ph->thumb?self::$NameThumb:'').$ph->dir);
			}
		$jn->status='ok';
		}
	if($query_all_photo->num_rows>0)
		{
		$jn->showhref=true;
		}
	return json_encode($jn);
	}
}
?>