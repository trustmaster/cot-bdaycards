<?php
/* ====================
Copyright (c) 2007-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License

[BEGIN_SED_EXTPLUGIN]
Code=bdaycards
Part=admin
File=bdaycards.admin
Hooks=tools
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

require_once($cfg['plugins_dir'].'/postman/inc/postoffice.class.php');
require_once($cfg['plugins_dir'].'/bdaycards/inc/functions.php');

$plugin_title = 'Birthday Card Sender';
$plugin_body = '<p>This tool will send a test birthday greeting e-mail to a custom user.</p>';

$act = sed_import('act', 'G', 'ALP');

if($act == 'test')
{
	// Check for valid user name
	$usr_name = sed_sql_prep(sed_import('usr_name', 'P', 'STX'));
	if(empty($usr_name))
		$plugin_body .= '<div class="error">User name was empty!</div>';
	else
	{
		if($row = sed_sql_fetcharray(sed_sql_query("SELECT user_id, user_name, user_birthdate, user_email FROM $db_users WHERE user_name = '$usr_name'")))
		{
			$eml = $cfg['plugin']['bdaycards']['from'];
			$eml = trim(str_replace('&lt;', '<', str_replace('&gt;', '>', $eml)), ' 	');
			$sndr = array('addr' => $eml, 'name' => 'Post service');
			if(mb_strpos($eml, '<') < mb_strpos($eml, '>'))
			{
				$sndr['name'] = mb_substr($eml, 0, mb_strpos($eml, '<'));
				$sndr['addr'] = trim(mb_substr($eml, mb_strpos($eml, '<') + 1, mb_strpos($eml, '>') - mb_strpos($eml, '<') - 1));
			}
			// Send the test message
			$mailer = new PostOffice();
			$mailer->IsHTML(true);
			$mailer->From = $sndr['addr'];
			$mailer->FromName = $sndr['name'];
			$mailer->AddAddress($row['user_email'], sed_cc($row['user_name']));
			$mailer->Subject = bcard_parse($cfg['plugin']['bdaycards']['subject'], $row);
			$mailer->Body = bcard_parse($cfg['plugin']['bdaycards']['body'], $row);
			$bcard_state = $mailer->Send();
			sed_log('Sent birthday greeting email to '.sed_cc($row['user_name'])."<{$row['user_email']}>, result: $bcard_state");
			$plugin_body .= '<div style="border:2px solid green;padding:10px;margin:10px">The message has been sent:<pre>'.sed_cc($mailer->Body).'</pre>';
			if(!empty($mailer->ErrorInfo)) $plugin_body .= '<br />Error message: '.$mailer->ErrorInfo;
			$plugin_body .= '</div>';
		}
		else $plugin_body .= '<div class="error">User not found!</div>';
	}
}

// Input form
$plugin_body .= '
<form action="'.sed_url('admin', 'm=tools&p=bdaycards&act=test').'" method="post">
User name: <input type="text" name="usr_name" /><br />
<input type="submit" value="Send" />
</form>';

?>