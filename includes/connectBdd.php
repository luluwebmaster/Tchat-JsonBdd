<?php
$passwordTchat = "supermotdepasse";
//On lance une nouvelle instance de la BDD
try
{
	$bdd = new JBDD;
	$bdd->connect(array(
		'name' => "tchat",
		'password' => $passwordTchat
	));
}
catch(Exception $e)
{
	echo('Erreur : '.$e);
}
?>