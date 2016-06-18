<?php
//On inclue la base de donné
require('JSON_BDD/json_bdd.php');
require('includes/connectBdd.php');
//On recherche les messages
$bdd->get(array(
	'table' => "tchat",
	'where' => array(
		'delete' => "false"
	),
	'reverse' => true,
	'smax' => 60
));
$reponse = $bdd->_reponse;
krsort($reponse);
//On set la liste
$listeMsgs = array();
if(is_array($reponse))
{
	foreach($reponse AS $key => $value)
	{
		//On recherche les infos sur le user qui a posté
		$bdd->get(array(
			'table' => "users",
			'where' => array(
				'uuid' => $value['uuid']
			)
		));
		$infosUser = $bdd->_reponse[0];
		$listeMsgs[] = array(
			'username' => $infosUser['username'],
			'uuid' => $infosUser['uuid'],
			'uid' => $value['uid'],
			'msg' => htmlentities($value['msg']),
			'date' => $value['date'],
			'heure' => $value['heure']
		);
	}
}
$listeData = array();
$listeData['listMsgs'] = $listeMsgs;
//On recherche les infos sur les users
$bdd->get(array(
	'table' => "users",
	'all' => true
));
$reponse = $bdd->_reponse;
//On set la liste
$listeUsers = array();
if(is_array($reponse))
{
	foreach($reponse AS $key => $value)
	{
		//On compte le nombre de messages du user
		$bdd->get(array(
			'table' => "tchat",
			'where' => array(
				'uuid' => $value['uuid']
			)
		));
		$nbMsgs = count($bdd->_reponse);
		$listeUsers[] = array(
			'uuid' => $value['uuid'],
			'username' => $value['username'],
			'register_date' => $value['register_date'],
			'register_heure' => $value['register_heure'],
			'nb_msg' => $nbMsgs
		);
	}
}
$listeData = array();
$listeData['listMsgs'] = $listeMsgs;
$listeData['listeUsers'] = $listeUsers;
//On retourne les infos
echo(json_encode($listeData));
?>