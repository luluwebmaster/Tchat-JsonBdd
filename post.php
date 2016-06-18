<?php
session_start();
//On inclue la base de donné
require('JSON_BDD/json_bdd.php');
require('includes/connectBdd.php');
$mode = $_POST['mode'];
if($mode == "connect")
{
	$username = $_POST['username'];
}
elseif($mode == "msg")
{
	$msg = $_POST['msg'];
}
elseif($mode == "clear")
{
	$password = $_POST['password'];
}
elseif($mode == "deleteMsg")
{
	$uid = $_POST['uid'];
	$password = $_POST['password'];
}
$dataErrorJson = array();
$dataErrorJson['error'] = false;
$dataErrorJson['connected'] = false;
$dataErrorJson['msgSend'] = false;
$dataErrorJson['clear'] = false;
$dataErrorJson['delete'] = false;
if($mode == "connect" AND $username !== "")
{
	//On verifie que le pseudo n'est pas déjà utilisé
	$bdd->get(array(
		'table' => "users",
		'where' => array(
			'username' => $username
		)
	));
	$reponse = $bdd->_reponse;
	if(!is_array($reponse[0]))
	{
		$uuid = uniqid();
		$bdd->insert(array(
			'table' => "users",
			'values' => array(
				'uuid' => uniqid(),
				'username' => $username,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'register_date' => date('d/m/Y'),
				'register_heure' => date('G:i:s'),
				'register_time' => time()
			)
		));
		$_SESSION['uuid'] = $bdd->_reponse['values']['uuid'];
		$dataErrorJson['connected'] = true;
	}
	else
	{
		$_SESSION['uuid'] = $reponse[0]['uuid'];
		$dataErrorJson['connected'] = true;
	}
}
elseif($mode == "msg" AND $msg !== "" AND isset($_SESSION['uuid']))
{
	//On recherche les infos sur le user
	$bdd->get(array(
		'table' => "users",
		'where' => array(
			'uuid' => $_SESSION['uuid']
		)
	));
	$reponse = $bdd->_reponse;
	if(is_array($reponse[0]))
	{
		//On recherche le dernier messsage du user
		$bdd->get(array(
			'table' => "tchat",
			'where' => array(
				'uuid' => $_SESSION['uuid']
			),
			'reverse' => true,
			'smax' => 1
		));
		$reponse = $bdd->_reponse;
		$timeLastMsg = $reponse[0]['time'] + 1;
		//Anti spam
		if($timeLastMsg <= time())
		{
			$bdd->insert(array(
				'table' => "tchat",
				'values' => array(
					'uid' => uniqid(),
					'uuid' => $_SESSION['uuid'],
					'msg' => $msg,
					'date' => date('d/m/Y'),
					'heure' => date('G:i:s'),
					'time' => time(),
					'delete' => "false"
				)
			));
			$dataErrorJson['msgSend'] = true;
		}
		else
		{
			$dataErrorJson['error'] = true;
			$timeRestant = $timeLastMsg - time();
			$dataErrorJson['errorMsg'] = "Attendez encore ".$timeRestant." secondes avant de poster.";
		}
	}
	else
	{
		session_destroy();
	}
}
elseif($mode == "clear")
{
	if($password == $passwordTchat)
	{
		//On clear le tchat
		$bdd->update(array(
			'table' => 'tchat',
			'where' => array(
				'delete' => "false"
			),
			'newValue' => array(
				'delete' => "true"
			)
		));
		//On recherche les infos sur le user
		$bdd->get(array(
			'table' => "users",
			'where' => array(
				'uuid' => $_SESSION['uuid']
			)
		));
		$reponse = $bdd->_reponse;
		//On envoi un message
		$bdd->insert(array(
			'table' => "tchat",
			'values' => array(
				'uid' => uniqid(),
				'uuid' => $_SESSION['uuid'],
				'msg' => "Tchat clear par ".$reponse[0]['username'],
				'date' => date('d/m/Y'),
				'heure' => date('G:i:s'),
				'time' => time(),
				'delete' => "false"
			)
		));
		$dataErrorJson['clear'] = true;
	}
	else
	{
		$dataErrorJson['clear'] = false;
		$dataErrorJson['error'] = "Mot de passe incorrect.";
	}
}
elseif($mode == "deleteMsg")
{
	if($password == $passwordTchat)
	{
		//On clear le tchat
		$bdd->update(array(
			'table' => 'tchat',
			'where' => array(
				'uid' => $uid
			),
			'newValue' => array(
				'delete' => "true"
			)
		));
		$dataErrorJson['delete'] = true;
	}
	else
	{
		$dataErrorJson['delete'] = false;
		$dataErrorJson['error'] = "Mot de passe incorrect.";
	}
}
//On encode la reponse JSON
echo(json_encode($dataErrorJson));
?>