<?php

/* ====================
Copyright (c) 2007-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License

[BEGIN_SED_EXTPLUGIN]
Code=bdaycards
Name=Birthday Cards
Description=Sends Birthday greetings automatically by e-mail
Version=0.3
Date=2009-jan-30
Author=Trustmaster
Copyright=(c) Vladimir Sibirov, 2007-2009
Notes=This plugin requires T3 PostMan
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
subject=01:string::Happy Birthday!:Message subject
body=02:text:::HTML message body. Valid patterns are {user_id}, {user_name}, {user_age}, {user_birthdate}, {user_email}, {maintitle}, {mainurl}
from=03:string::noreply@somehost.net:From address
on=04:radio::0:Auto-sending on/off
[END_SED_EXTPLUGIN_CONFIG]

==================== */

if (!defined('SED_CODE')) { sed_diefatal('Wrong URL.'); }

?>