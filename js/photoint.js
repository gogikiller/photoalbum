$(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {event.preventDefault();$(this).ekkoLightbox();}).delegate('[id^="dell_photo_"]','click',dell_photo);
$(document).delegate('#href_all','click',function(){
	getPhotosJson({down_id:$('[id^="dell_photo_"]:last').attr('id').substr(11)});}
	);
$.ajaxSetup({dataType:'json',type:'POST',url:'controller.php',complete:function(d){if(false)console.log(d);}});
function dell_photo()
	{
	var p=new Object();
	p.id=this.id.substr(11);
	$.ajax({but:'#'+this.id,data:'query=dellphoto&JSkey='+JSkey+'&p='+encodeURIComponent(JSON.stringify(p)),success:
		function(data)
			{
			$(this.but).button('reset');
			if(data.status=='ok')
				{
				var b=$(this.but);
				var dp=b.closest('div.row');
				b.closest('div.col-xs-7.col-md-3').remove();
				shake_div_down(dp);
				getPhotosJson({down_id:$('[id^="dell_photo_"]:last').attr('id').substr(11),count:1});
				}
			else
				openModal({id:genID(4),title:'Ошибка!',content:'Файл: <b>'+data.name+'</b></br>Не может быть удален!</br>'+data.err,footer:''});
			}
		});
	$(this).button('loading');
	}
function shake_div_down(bin_element)
	{	
	var down_element=$(bin_element).next();
	var sz=$(bin_element).find('button').size();
	if(sz<4&&sz>0)
		{
		$(down_element).find('div.col-xs-7.col-md-3:first').appendTo($(bin_element));
		shake_div_down(down_element);
		}
	if(sz==0)
		$(bin_element).remove();
	}
aa=0;	
function shake_div_up(bin_element)
	{
	aa++;
	var down_element=$(bin_element).next();
	var sz=$(bin_element).find('button').size();
	var nz=down_element.size();
	if(nz==0&&sz>4)
		{
		$('<div class="row"></div>').appendTo('.container:first');
		var down_element=$(bin_element).next();
		var sz=$(bin_element).find('button').size();
		}
	if(sz>4)
		{	
		$(bin_element).find('div.col-xs-7.col-md-3:last').prependTo($(down_element));
		shake_div_up(down_element);
		}
	}
$(document).ready(function() 
	{
	$(window).on( "scroll", function(){if($('#href_all').css('display')=='inline-block')
	if($('#href_all').html()!='Loading..')getPhotosJson({down_id:$('[id^="dell_photo_"]:last').attr('id').substr(11)});});
	var dd=new Dropzone(".container",{url:"controller.php",previewsContainer:'#mm',addRemoveLinks:true,clickable:'#ld_ph',acceptedFiles:'image/*'});
	dd.on("sending",function(file,xhr,frd)
		{
		frd.append('JSkey',decodeURIComponent(JSkey));
		frd.append('query','savephoto');
		});
	dd.on("success", function(file) 
		{
		var stat=jQuery.parseJSON(file.xhr.response);
		if(stat.status=='ok')
			{
			var thumb=$('#load_ph_'+file.iddiv).find('.thumbnail');
			thumb.find('img').remove();
			$('<a href="'+stat.dir+'" data-toggle="lightbox" data-title="'+stat.name+'" data-gallery="galleryone"><img src="'+stat.Thumbdir+'"></a>').prependTo(thumb);
			thumb.find('button').attr({class:'btn btn-danger pull-right btn-xs',id:'dell_photo_'+stat.id}).html('&nbsp;<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;');
			$('<a href="'+stat.dir+'" class="btn btn-link btn-sm"><span class="glyphicon glyphicon-save-file" aria-hidden="true"></span></a>').appendTo(thumb.find('em'));
			}
		else
			{
			openModal({id:genID(4),title:'Ошибка!',content:'Файл: <b>'+file.name+'</b></br>'+stat.err,footer:''});
			var dp=$('#load_ph_'+file.iddiv).closest('div.row');
			$('#load_ph_'+file.iddiv).remove();
			shake_div_down(dp);	
			}
		});//смотрим отдаем ошибки....
	dd.on("addedfile",function(file)
		{
		file.iddiv=genID(8);
		div='<div class="col-xs-7 col-md-3" id="load_ph_'+file.iddiv+'">\
				<div class="thumbnail">\
					<img src="img/loading.gif">\
					<div class="caption">\
						<p>\
      						<em>'+file.name+'</em>\
        					<button type="button" class="btn btn-success pull-right btn-xs">\
        						<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Загрузка\
        					</button>\
        				<p>\
					</div>\
				</div>\
			</div>';
		var updiv=$('div.row:first');
		if(updiv.size()==0)
			{
			$('<div class="row" id="new"></div>').appendTo('.container');
			updiv=$('div.row:first');
			}
		$(div).prependTo(updiv);
		shake_div_up(updiv);
		});
	dd.on("thumbnail",function(file,img){$('#load_ph_'+file.iddiv).find('img').attr('src',img);});
	dd.on('error',function(file,err)
		{
		openModal({id:genID(4),title:'Ошибка!',content:'Файл: <b>'+file.name+'</b></br>Не может быть передан!',footer:''});
		var dp=$('#load_ph_'+file.iddiv).closest('div.row');
		$('#load_ph_'+file.iddiv).remove();
		shake_div_down(dp);
		});
	});
function genID(idLength)
	{
	var nId = '',
	allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	for (var i = 0; i <idLength; ++i)
		nId += allowedChars.charAt(Math.floor(Math.random() * allowedChars.length));
	return nId;
	}
function openModal(mw)
	{
	var Xtitle=document.title;
	$('body').append('<div class="modal fade" id="myModal_'+mw.id+'"><div class="modal-dialog">\
    <div class="modal-content"><div class="modal-header">\
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
     <h4 class="modal-title">'+mw.title+'</h4></div><div class="modal-body">'+mw.content+'</div><div class="modal-footer">'+mw.footer+'</div>\
    </div></div></div>');
	$('#myModal_'+mw.id).modal({backdrop:'static'});
	document.title=mw.header_title;
	$('#myModal_'+mw.id).on('hidden.bs.modal',function (e){$('#myModal_'+mw.id).remove();document.title = Xtitle;})
	}
function getPhotosJson(obj)
	{
	var div='';
	$('#href_all').button('loading');
	$.ajax({but:'#href_all',data:'query=getphoto&JSkey='+JSkey+'&p='+encodeURIComponent(JSON.stringify(obj)),success:
		function(data)
			{
			$(this.but).button('reset');
			if(data.status=='ok')
				{
				var mass=data.arrayPhoto;
				$.each(mass,function (i,e){
					div='<div class="col-xs-7 col-md-3">\
				<div class="thumbnail">\
					<a href="'+e.dir+'" data-toggle="lightbox" data-title="'+e.name+'" data-gallery="galleryone"><img src="'+e.Thumbdir+'"></a>\
					<div class="caption">\
						<p>\
      						<em>'+e.name+'</em><a href="'+e.dir+'" class="btn btn-link btn-sm"><span class="glyphicon glyphicon-save-file" aria-hidden="true"></span></a>\
        					<button type="button" class="btn btn-danger pull-right btn-xs" id="dell_photo_'+e.id+'">\
        						&nbsp;<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;\
        					</button>\
        				<p>\
					</div>\
				</div>\
			</div>';
				if(data.pos=='down')
					{
					//$('div.row:last').prependTo(div);
					$(div).appendTo('div.row:last').find('div.col-xs-7.col-md-3:first');
					shake_div_up($('div.row:last'));
					}
				});
				}
			if(data.showhref)
					$('#href_all').css('display','inline-block');
				else
					$('#href_all').css('display','none');	
			}
		});
	}