<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>Tchat géré avec JSON BDD.</title>
		<link rel="stylesheet" href="http://bootswatch.com/cosmo/bootstrap.min.css"/>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"/>
		<link href="includes/style.css" rel="stylesheet" type="text/css"/>
		<link rel="icon" type="image/x-icon" href="http://www.luluwebmaster.fr/img/logo.png" />
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="includes/actions.js"></script>
		<script>
			//Quelques variables
			var lastMsgId = 0;
			var pauseLoad = false;
			//Quand la page est chargée
			$(document).ready(function (){
				//On load les messages
				loadMsgs();
				//On actualise les messages
				setInterval(function (){
					loadMsgs();
				}, 1000);
				//Quand on clique sur le bouton de clear
				$('.tp-clear').click(function (){
					$.post('post.htm', {mode:"clear", password:prompt('Entrez le mot de passe de clear pour clear le tchat :\n')}, function(data){
						if(data['clear'] == true)
						{
							loadMsgs();
							alert('Tchat cleat !');
						}
						else if(data['clear'] == false)
						{
							alert(data['error']);
						}
					}, "JSON");
					return false;
				});
			});
		</script>
	</head>
	<body>
		<div class="tchat">
			<div class="t-msgs"></div>
			<div class="t-post">
				<form method="POST" action="post.php" onSubmit="postTchat('msg');return false;">
					<input type="text" class="tp-msg form-control" placeholder="Votre message"/><!--
					--><input type="submit" class="tp-submit btn btn-primary" value="Envoyer"/><!--
					--><input type="submit" class="tp-clear btn btn-default" value="Clear"/>
				</form>
			</div>
		</div>
		<?php
		if(empty($_SESSION['uuid']))
		{
			?>
			<div class="t-connect">
				<h1 class="tc-texte">Choisissez un pseudo pour entrer.</h1>
				<form method="POST" action="post.php" onSubmit="postTchat('connect');return false;">
				<input type="text" class="tc-username form-control" placeholder="Pseudo"/><br >
				<input type="submit" class="tc-enter btn btn-primary" value="Entrer sur le tchat"/>
				</form>
				<div class="tc-info alert alert-dismissible alert-warning">
					<h4>C'est quoi ce tchat ?</h4><br />
					<p>Ce petit tchat est en fait une mise en pratique de mon systeme de base de donné géré en JSON.<br /><br />
					Vous pouvez trouver le code complet de la basse de donnée ici : <a href="#" class="alert-link">JSON BDD</a>.<br /><br />
					Ainsi que le code complet du tchat ici : <a href="#" class="alert-link">Un tchat en JSON</a></p>
				</div>
			<div>
			<?php
		}
		?>
		<div class="listUsers"></div>
		<div class="listUsername"></div>
	</body>
</html>