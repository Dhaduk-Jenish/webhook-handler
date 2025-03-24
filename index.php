<?php 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
error_reporting(1);
	require_once('config/db.php'); 
	require_once('config/config.php');  // configuration of sites
	require_once('lib/general.php');  // general settings of site
	require_once(GetConfig('SITE_BASE_PATH').'/lib/template/class.template_engine.php');

	/// define special variable
	$GLOBALS['BASE_LINK'] =  GetConfig('SITE_BASE_PATH');
	$GLOBALS['ROOT_LINK'] = GetConfig('SITE_URL');	 /// http://www.domain.com

	// classes 
	$GLOBALS['CLASSES'] = $GLOBALS['BASE_LINK'].'/includes/classes';		
	$GLOBALS['CURRENT_URL'] = url();	
	// get template class
	$GLOBALS['CLA_HTML'] = GetClass('CIT_Html');

	$GLOBALS['WWW_TPL'] = $GLOBALS['ROOT_LINK'].GetConfig('WWW');
	// coding for index page
	require_once($GLOBALS['CLASSES'].'/home.php');		
	$GLOBALS['CLA_INDEX'] = GetClass('CIT_INDEX');	
	$GLOBALS['CLA_INDEX']->displayPage();
	// coding for index page