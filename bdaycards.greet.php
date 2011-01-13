<?php
/* ====================
Copyright (c) 2007-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License

[BEGIN_SED_EXTPLUGIN]
Code=bdaycards
Part=main
File=bdaycards.greet
Hooks=admin.home
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$bcard_now = time();
$bcard_today = date('Y/m/d', $bcard_now);

if($cfg['plugin']['bdaycards']['on'] && (!isset($bcard_sent) || $bcard_sent != $bcard_today) && !empty($cfg['plugin']['bdaycards']['body']))
{
	// Duplicate sending protection
	sed_cache_store('bcard_sent', $bcard_today, 60*60*24*2);
	
	// Link the libs
	require_once($cfg['plugins_dir'].'/postman/inc/postoffice.class.php');
	require_once($cfg['plugins_dir'].'/bdaycards/inc/functions.php');
	$mailer = new PostOffice();
	$mailer->IsHTML(true);
	$eml = $cfg['plugin']['bdaycards']['from'];
	$eml = trim(str_replace('&lt;', '<', str_replace('&gt;', '>', $eml)), ' 	');
	$sndr = array('addr' => $eml, 'name' => 'Post service');
	if(mb_strpos($eml, '<') < mb_strpos($eml, '>'))
	{
		$sndr['name'] = mb_substr($eml, 0, mb_strpos($eml, '<'));
		$sndr['addr'] = trim(mb_substr($eml, mb_strpos($eml, '<') + 1, mb_strpos($eml, '>') - mb_strpos($eml, '<') - 1));
	}
	$mailer->From = $sndr['addr'];
	$mailer->FromName = $sndr['name'];
	
	// Find people with month/day of the birth date same as today's and send them the parsed message
	$bcard_md = date('md', $bcard_now);
	$bcard_sql = sed_sql_query("SELECT user_id, user_name, user_birthdate, user_email FROM $db_users WHERE user_maingrp > 3");
	while($row = sed_sql_fetcharray($bcard_sql))
	{
		if(date('md', $row['user_birthdate']) == $bcard_md)
		{
			$mailer->AddAddress($row['user_email'], sed_cc($row['user_name']));
			$mailer->Subject = bcard_parse($cfg['plugin']['bdaycards']['subject'], $row);
			$mailer->Body = bcard_parse($cfg['plugin']['bdaycards']['body'], $row);
			$bcard_state = $mailer->Send();
			sed_log('Sent birthday greeting email to '.sed_cc($row['user_name'])."<{$row['user_email']}>, result: $bcard_state");
			$mailer->ClearAddresses();
		}
	}
}

?>