//Fonction pour load les messages
function loadMsgs()
{
	if(pauseLoad == false)
	{
		$.getJSON('get.htm', function (data){
			if(pauseLoad == false)
			{
				var listeMsg = data['listMsgs'];
				var listeUsers = data['listeUsers'];
				//On set les messages
				var lastMsgIdTest = 0;
				var listeElement = [];
				var listeElementTest = [];
				$('.tm-msg').each(function (){
					listeElement.push($(this).attr('uid'));
				});
				for(i=0;i<listeMsg.length;i++)
				{
					if($.inArray(listeMsg[i]['uid'], listeElement) == -1)
					{
						$('.t-msgs').append('<div class="tm-msg" uid="'+listeMsg[i]['uid']+'" title="Uid : '+listeMsg[i]['uid']+'"><div class="tmm-username" title="Uuid : '+listeMsg[i]['uuid']+'" onClick="$(\'.lu-user[uuid=\\\''+listeMsg[i]['uuid']+'\\\']\').css(\'display\', \'block\');pauseLoad = true;">'+listeMsg[i]['username']+'</div><div class="tmm-texte">'+listeMsg[i]['msg']+'</div><button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="postTchat(\'deleteMsg\', \''+listeMsg[i]['uid']+'\');">&times;</button></div>');
						if(listeElement.length > 60)
						{
							$('.tm-msg').first().remove();
						}
					}
					lastMsgIdTest = listeMsg[i]['uid'];
					listeElementTest.push(listeMsg[i]['uid']);
				}
				if(lastMsgIdTest !== lastMsgId)
				{
					lastMsgId = lastMsgIdTest;
					$('.t-msgs').scrollTop(9999999);
				}
				$('.tm-msg').each(function (){
					if($.inArray($(this).attr('uid'), listeElementTest) == -1)
					{
						$(this).remove();
					}
				});
				//On set la llise des users
				$('.listUsers').html('');
				for(i=0;i<listeUsers.length;i++)
				{
					$('.listUsers').append('<div class="lu-user" uuid="'+listeUsers[i]['uuid']+'">'
					+'<div class="luu-block panel-success">'
					+'<div class="panel-heading"><div class="luu-username panel-title">'+listeUsers[i]['username']+'<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="$(\'.lu-user[uuid=\\\''+listeUsers[i]['uuid']+'\\\']\').css(\'display\', \'none\');pauseLoad = false;">&times;</button></div></div><div class="panel-body">'
					+'<div class="panel panel-info luu-uuid"><div class="panel-heading"><h3 class="panel-title">Uuid</h3></div><div class="panel-body">'+listeUsers[i]['uuid']+'</div></div>'
					+'<div class="panel panel-info luu-nb_msg"><div class="panel-heading"><h3 class="panel-title">Nombre de messages</h3></div><div class="panel-body">'+listeUsers[i]['nb_msg']+'</div></div>'
					+'<div class="panel panel-warning luu-register_date"><div class="panel-heading"><h3 class="panel-title">Inscrit le</h3></div><div class="panel-body">'+listeUsers[i]['register_date']+'</div></div>'
					+'<div class="panel panel-warning luu-register_heure"><div class="panel-heading"><h3 class="panel-title">À</h3></div><div class="panel-body">'+listeUsers[i]['register_heure']+'</div></div>'
					+'</div></div></div>');
				}
				$('.listUsername').html('');
				for(i=0;i<listeUsers.length;i++)
				{
					$('.listUsername').append('<div class="tmm-username" title="Uuid : '+listeUsers[i]['uuid']+'" onClick="$(\'.lu-user[uuid=\\\''+listeUsers[i]['uuid']+'\\\']\').css(\'display\', \'block\');pauseLoad = true;" style="margin:10px;">'+listeUsers[i]['username']+'</div><br />');
				}
			}
		});
	}
}
//Fonction pour poster un message
function postTchat(mode, setting)
{
	if(mode == "connect")
	{
		var username = $('.tc-username').val();
		if(username !== "")
		{
			$.post('post.htm', {mode:mode, username:username}, function (data){
				if(data['connected'] == true)
				{
					$('.tc-username').val('');
					$('.t-connect').css('display', 'none');
				}
			}, "JSON");
		}
		else
		{
			alert('Merci d\'entrer un pseudo.');
		}
	}
	else if(mode == "msg")
	{
		var msg = $('.tp-msg').val();
		if(msg !== "")
		{
			$.post('post.htm', {mode:mode, msg:msg}, function (data){
				if(data['msgSend'] == true)
				{
					$('.tp-msg').val('');
					loadMsgs();
				}
				if(data['error'] == true)
				{
					alert(data['errorMsg']);
				}
			}, "JSON");
		}
		else
		{
			alert('Merci d\'entrer un message.');
		}
	}
	else if(mode == "deleteMsg")
	{
		var uid = setting;
		if(uid !== "")
		{
			$.post('post.htm', {mode:mode, uid:uid, password:prompt('Mot de passe de sécurité :\n')}, function (data){
				if(data['delete'] == false)
				{
					alert(data['error']);
				}
				else
				{
					$(".tm-msg[uid="+uid+"]").remove();
				}
			}, "JSON");
		}
		else
		{
			alert('Uid invalide.');
		}
	}
}