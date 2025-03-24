<?php
// database settings
error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(0);
$GLOBALS['SITE_ID'] = 0;
$GLOBALS['CIT_CFG']['SITE_BASE_PATH'] = dirname(realpath(dirname(__FILE__)));
$GLOBALS['CIT_CFG']["CharacterSet"] = 'UTF-8'; 
$GLOBALS['CIT_CFG']["DisplayDateFormat"] = 'jS M Y';

// include path for front end
$GLOBALS['CIT_CFG']['WWW'] = '/includes/template';
$GLOBALS['CIT_CFG']['CLASSES'] = 'includes/classes';
// include path

// Timezone
date_default_timezone_set('Australia/Adelaide');

$getConfig = $GLOBALS['DB']->row("SELECT * FROM config WHERE id=1");
$rowgetConfig = unserialize($getConfig['name']);	

foreach($rowgetConfig as $getConfigKey=>$getConfigValue)
{	
	$GLOBALS['CIT_CFG'][$getConfigKey] = $getConfigValue;
	
}

$GLOBALS['CIT_CFG']['SITE_URL'] = 'http://127.0.0.1/webhook-handler/';
$GLOBALS['CIT_CFG']['cit_dbdebug'] = 0;


