function on_logon()
	{
	var paket=new Object();
	paket={
		email:$('#inputEmail').val(),
		password:$('#inputPassword').val()
		}
	if($("#captha_inp").length)
		paket.captcha=$('#captha_inp').val();
	$('#error_form_login').html('');
	$('#logon_button').button('loading');
	console.log(paket);
	$.ajax({dataType:'json',type:'POST',url:'controller.php',success:'console.log,',
		data:'paket='+encodeURIComponent(JSON.stringify(paket))+'&query=on_logon',
		complete:activ_onlogon});	
	}
function activ_onlogon(data)
	{
	console.log(data);
	var big_data=jQuery.parseJSON(data.responseText);
	if(big_data.status=='no'){
		$('#error_form_login').html(big_data.err);
		$('#logon_button').button('reset');
		if($("#captha_inp").length)
			$('#captcha').html('<img src="'+big_data.img+'"> \
<input type="text" class="form-control" id="captha_inp" placeholder="Цифры с картинки">');
		else if(big_data.img!='')
			{
			$('#captcha').html('<img src="'+big_data.img+'"> \
<input type="text" class="form-control" id="captha_inp" placeholder="Цифры с картинки">');
			}
		}
	else
		window.location=big_data.patch;
	}
$(function(){
	$('#logon_button').click(function(){on_logon()});});	