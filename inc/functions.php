<?php
/* ====================
Copyright (c) 2007-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED]
File=plugins/bdaycards/inc/bdaycards.php
Version=0.1
Updated=2007-oct-23
Type=Plugin
Author=Trustmaster
Description=Auxilliary functions
[END_SED]
==================== */

// Age calculator
function bcard_age($user_birthdate)
{
	return ((int) date('Y', time())) - ((int) date('Y', $user_birthdate));
}

// Message parser
function bcard_parse($message, $row)
{
	global $cfg;
	$message = str_replace('{user_id}', $row['user_id'], $message);
	$message = str_replace('{user_name}', $row['user_name'], $message);
	$message = str_replace('{user_email}', $row['user_email'], $message);
	$message = str_replace('{user_birthdate}', date('Y/m/d', $row['user_birthdate']), $message);
	$message = str_replace('{user_age}', bcard_age($row['user_birthdate']), $message);
	$message = str_replace('{maintitle}', $cfg['maintitle'], $message);
	$message = str_replace('{mainurl}', $cfg['mainurl'], $message);
	return $message;
}
?>