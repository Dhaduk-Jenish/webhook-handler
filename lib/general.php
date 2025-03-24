<?php  
	/***
	* get url of page.
	* 
	* @return Strings  
	***/
	function url(){
		// Using path info
		if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '' && basename($_SERVER['PATH_INFO']) != 'index.php') {
			$path = $_SERVER['PATH_INFO'];
			if (isset($_SERVER['SCRIPT_NAME'])) {
				$getUrlTest = str_ireplace($_SERVER['SCRIPT_NAME'], "", $path);
				if($getUrlTest != '') {
					$getUrl = $getUrlTest;
				}
			} else if (isset($_SERVER['SCRIPT_FILENAME'])) {
				$file = str_ireplace(ISC_BASE_PATH, "", $_SERVER['SCRIPT_FILENAME']);
				$getUrlTest = str_ireplace($file, "", $path);
				if($getUrlTest != '') {
					$getUrl = $getUrlTest;
				}
			}
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/index.php/";
		}
		// Using HTTP_X_REWRITE_URL for ISAPI_Rewrite on IIS based servers
		if(isset($_SERVER['HTTP_X_REWRITE_URL']) && !isset($getUrl)) {
			$getUrl = $_SERVER['HTTP_X_REWRITE_URL'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}
		// Using REQUEST_URI
		if (isset($_SERVER['REQUEST_URI']) && !isset($getUrl)) {
			$getUrl = $_SERVER['REQUEST_URI'];	
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}
		// Using SCRIPT URL
		if (isset($_SERVER['SCRIPT_URL']) && !isset($getUrl)) {
			$getUrl = $_SERVER['SCRIPT_URL'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}
		// Using REDIRECT_URL
		if (isset($_SERVER['REDIRECT_URL']) && !isset($getUrl)) {
			$getUrl = $_SERVER['REDIRECT_URL'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}
		// Using REDIRECT URI
		if (isset($_SERVER['REDIRECT_URI']) && !isset($getUrl)) {
			$getUrl = $_SERVER['REDIRECT_URI'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}
		// Using query string?
		if (isset($_SERVER['QUERY_STRING']) && !isset($getUrl)) {
			$getUrl = $_SERVER['QUERY_STRING'];			
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}
		if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
			$_SERVER['QUERY_STRING'] = $_SERVER['REDIRECT_QUERY_STRING'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ROOT_LINK'] . "/";
		}

		if(!isset($getUrl)) {
			$getUrl = '';
		}	
		
		$shopPath = rtrim($GLOBALS['ROOT_LINK'], '/');
		$shopPathParse = ParseShopPath($shopPath);
		if(!empty($shopPathParse)) {
			$sitePath = $shopPathParse['folderPath'];
		}
		else {
			$sitePath = '';
		}		
		$sitePath = preg_quote(trim($sitePath, "/"), "#");
		$getUrl = trim($getUrl, "/");
		$getUrl = trim(preg_replace("#".$sitePath."#i", "", $getUrl,1), "/");		
		return  $GLOBALS['ROOT_LINK'].'/'.$getUrl;
		
	}
	
	/**
	 * Redirect to module page in admin side
	 *
	 * @param string The name of the class to load.
	 * @return object The instantiated version fo the class.
	 */
	function AdminRedirectUrl($currenturl = ''){				
		$redirectlink = '';
		if($currenturl == ''){
			if(isset($_REQUEST['page'])){	
				if($GLOBALS['SeoUnabled']){	
					$redirectlink = sprintf('Location: %s/%s/%s',$GLOBALS['ADMIN_LINK'],$_REQUEST['module'],$_REQUEST['page']);					
				} else {
					$redirectlink = sprintf('Location: %s/index.php?module=%s&page=%d',$GLOBALS['ADMIN_LINK'],$_REQUEST['module'],$_REQUEST['page']);
				}
			} else {
				if($GLOBALS['SeoUnabled']){	
					$redirectlink = sprintf('Location: %s/%s',$GLOBALS['ADMIN_LINK'],$_REQUEST['module']);					 
				} else {
					$redirectlink = sprintf('Location: %s/index.php?module=%s',$GLOBALS['ADMIN_LINK'],$_REQUEST['module']);
				}
			}
		} else {				
			$redirectlink = sprintf('Location: %s',$currenturl);
		}
		return @header($redirectlink);
	}
	/**
	 * Redirect to module page in admin side
	 *
	 * @param string The name of the class to load.
	 * @return object The instantiated version fo the class.
	 */
	function GetAdminRedirectUrl($currenturl = ''){		
		if(!headers_sent()){	
			AdminRedirectUrl($currenturl);
		} else {
			if($currenturl == ''){
				if(isset($_REQUEST['page'])){
					if($GLOBALS['SeoUnabled']){	
						JavascriptHeader(sprintf('%s/%s/%d',$GLOBALS['ADMIN_LINK'],$_REQUEST['module'],$_REQUEST['page']));
					} else {
						JavascriptHeader(sprintf('%s/index.php?module=%s&page=%d',$GLOBALS['ADMIN_LINK'],$_REQUEST['module'],$_REQUEST['page']));
					}
				} else {
					if($GLOBALS['SeoUnabled']){	
						JavascriptHeader(sprintf('%s/%s',$GLOBALS['ADMIN_LINK'],$_REQUEST['module']));
					} else {
						JavascriptHeader(sprintf('%s/index.php?module=%s',$GLOBALS['ADMIN_LINK'],$_REQUEST['module']));
					}
				}
			} else {
				JavascriptHeader(sprintf('%s',$currenturl));
			}
		}	
		exit;
	}
	/**
	 * Return an already instantiated (singleton) version of a class. If it doesn't exist, will automatically
	 * be created.
	 *
	 * @param string The name of the class to load.
	 * @return object The instantiated version fo the class.
	 */
	function GetClass($className){
		static $classes;
		if(!isset($classes[$className])) {
			$classes[$className] = new $className;
		}
		$class = &$classes[$className];
		return $class;
	}

	/**
	 * Fetch a configuration variable from the store configuration file.
	 *
	 * @param string The name of the variable to fetch.
	 * @return mixed The value of the variable.
	 */
	function GetConfig($config){
		if (array_key_exists($config, $GLOBALS['CIT_CFG'])) {
			return $GLOBALS['CIT_CFG'][$config];
		}
		return '';
	}
	
	/**
	 * Convert a text string in to a search engine friendly based URL.
	 *
	 * @param string The text string to convert.
	 * @return string The search engine friendly equivalent.
	 */
	function MakeURLSafe($val){
		$val = str_replace("-", "%2d", $val);
		$val = str_replace("+", "%2b", $val);
		$val = str_replace("+", "%2b", $val);
		$val = str_replace("/", "{47}", $val);
		$val = urlencode($val);
		$val = str_replace("+", "-", $val);
		return $val;
	}

	/**
	 * Convert an already search engine friendly based string back to the normal text equivalent.
	 *
	 * @param string The search engine friendly version of the string.
	 * @return string The normal textual version of the string.
	 */
	function MakeURLNormal($val){
		$val = str_replace("-", " ", $val);
		$val = urldecode($val);
		$val = str_replace("{47}", "/", $val);
		$val = str_replace("%2d", "-", $val);
		$val = str_replace("%2b", "+", $val);
		return $val;
	}


	/**
	 * Checks if the passed string is a valid email address.
	 *
	 * @todo refactor
	 * @param string The email address to check.
	 * @return boolean True if the email is a valid format, false if not.
	 */
	function is_email_address($email){
		return preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $email);
	}	

	// Return the extension of a file name
	// @todo refactor
	function GetFileExtension($FileName)
	{
		$data = explode(".", $FileName);
		return $data[count($data)-1];
	}


	/**
	 * Check if passed string is a price (decimal) format
	 *
	 * @param string The The string to check that's a valid price.
	 * @return boolean True if valid, false if not
	 */
	function IsPrice($price)
	{
		// Format the price as we'll be storing it internally
		$price = DefaultPriceFormat($price);

		// If the price contains anything other than [0-9.] then it's invalid
		if(preg_match('#[^0-9\.]#i', $price)) {
			return false;
		}

		return true;
	}

	/**
	* Format a number using the configured decimal and thousand tokens to an optional number of decimal places
	*
	* @param mixed The number to format
	* @param int The number of decimal places to format the number to. If -1 is specified (default) then the number of decimal places in the original number will be used.
	* @return string The formatted number
	*/
	function FormatNumber($number, $decimalPlaces = -1)
	{
		// drop off any excess zeroes in the fractional component
		$number /= 1;

		if ($decimalPlaces == -1) {
			if (strrchr($number, '.')) {
				$decimalPlaces = strlen(strrchr($number, '.')) - 1;
			}
		}

		if ($decimalPlaces < 0) {
			$decimalPlaces = 0;
		}

		$number = number_format($number, $decimalPlaces, GetConfig('DimensionsDecimalToken'), GetConfig('DimensionsThousandsToken'));

		return $number;
	}

	function ConvertDateToTime($Stamp)
	{
		$vals = explode("/", $Stamp);
		return isc_gmmktime(0, 0, 0, $vals[0], $vals[1], $vals[2]);
	}


	

	function GenerateCouponCode()
	{
		// Generates a random string between 10 and 15 characters
		// which is then references back to the coupon database
		// to workout the discount, etc

		$len = rand(8, 12);

		// Always start the coupon code with a letter
		$retval = chr(rand(65, 90));

		for ($i = 0; $i < $len; $i++) {
			if (rand(1, 2) == 1) {
				$retval .= chr(rand(65, 90));
			} else {
				$retval .= chr(rand(48, 57));
			}
		}

		return $retval;
	}
	
	/**
	 * Get the current location of the current visitor.
	 *
	 * @param $fileOnly boolean Set to true to only receive only the file name + query string
	 * @return string The current location.
	 */
	function GetCurrentLocation($fileOnly = false)
	{
		if(isset($_SERVER['REQUEST_URI'])) {
			$location = $_SERVER['REQUEST_URI'];
		}
		else if(isset($_SERVER['PATH_INFO'])) {
			$location = $_SERVER['PATH_INFO'];
		}
		else if(isset($_ENV['PATH_INFO'])) {
			$location = $_ENV['PATH_INFO'];
		}
		else if(isset($_ENV['PHP_SELF'])) {
			$location = $_ENV['PHP_SELF'];
		}
		else {
			$location = $_SERVER['PHP_SELF'];
		}

		if($fileOnly) {
			$location = basename($location);
		}

		if (strpos($location, '?') === false) {
			if(!empty($_SERVER['QUERY_STRING'])) {
				$location .= '?'.$_SERVER['QUERY_STRING'];
			}
			else if(!empty($_ENV['QUERY_STRING'])) {
				$location .= '?'.$_ENV['QUERY_STRING'];
			}
		}

		return $location;
	}

	/**
	 * Get the current URL of the current visitor.
	 *
	 * @return string The current URL
	 */
	function GetCurrentURL()
	{	
		if(isset($_SERVER['HTTPS'])){
			if ($_SERVER['HTTPS'] == 'on') {
				$url = 'https://';
			}
			else {
				$url = 'http://';
			}
        } else {
			$url = 'http://';
		}

		$url .= $_SERVER['SERVER_NAME'];

		$url .= GetCurrentLocation();

		return $url;
	}

	/**
	 * Fetch the IP address of the current visitor.
	 *
	 * @return string The IP address.
	 */
	function GetIP()
	{
		static $ip;
		if($ip) {
			return $ip;
		}

		$ip = '';

		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			if(preg_match_all("#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#s", $_SERVER['HTTP_X_FORWARDED_FOR'], $addresses)) {
				foreach($addresses[0] as $key => $val) {
					if (isPublicIPv4($val)) {
						$ip = $val;
						break;
					}
				}
			}
		}

		if(!$ip) {
			if(isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
			else if(isset($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}
		$ip = preg_replace("#([^.0-9 ]*)#", "", $ip);

		return $ip;
	}
	
	
	/**
	 * Run stripslashes on every value of a multidimension array
	 *
	 * @param mixed $value The variable to run stripslashes on
	 * @return mixed
	 **/
	function stripslashes_deep($value)
	{
		if (is_array($value)) {
			$value = array_map('stripslashes_deep', $value);
		} else {
			$value = stripslashes($value);
		}

		return $value;
	}
	
	
	function strip_tag($value){ // remove html code from input 
	
		if (is_array($value)) {
			$value = array_map('strip_tags', $value);
		} else {
			$value = strip_tags($value);
		}
	
		if (is_array($value)) {
			$value = array_map('xss_clean', $value);
		} else {
			$value = xss_clean($value);
		}

		return $value;
	}
	
	function xss_clean($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
		
		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
		
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
		
		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		
		do{
		// Remove really unwanted tags
			$old_data = $data;
					$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
			}
		while ($old_data !== $data);
		
		// we are done...
		return $data;
	}

	
	/**
	 * Convert all request inputs from $from character set to $to character set
	 *
	 * Function will convert all $_GET, $_POST and $_REQUEST data from the character set
	 * in $from to the character set in $to
	 *
	 * @access public
	 * @param string $from The character set to convert from
	 * @param string $to The character set to convert to
	 * @param bool $toRequest TRUE to also do $_REQUEST, FALSE to skip it. Default is TRUE
	 * @return null
	 */
	function convertRequestInput($from='UTF-8', $to='', $doRequest=true)
	{		
		if ($to == '') {
			$to = GetConfig('CharacterSet');
		}

		if ($from == '' || $to == '' || $from === $to) {
			return;
		}

		$_GET = convert_charset($from, $to, $_GET);
		$_POST = convert_charset($from, $to, $_POST);

		if ($doRequest) {
			$_REQUEST = convert_charset($from, $to, $_REQUEST);
		}
	}
	
	/**
	 * Convert a string between 2 character sets.
	 *
	 * @param string Character set to convert from.
	 * @param string Character set to convert to.
	 * @param string String to convert.
	 * @return string The converted string.
	 */
	function convert_charset($in, $out, $str)
	{
		if ($in === $out) {
			return $str;
		} elseif (function_exists('mb_convert_encoding')) {
			if (is_array($str)) {
				foreach (array_keys($str) as $key) {
					$str[$key] = convert_charset($in, $out, $str[$key]);
				}
			} else {
				$str = mb_convert_encoding($str, $out, $in);
			}
			return $str;
		} else {
			return $str;
		}
	}
	/**
	* get randome number from given length
	* 
	**/
	function getrandnum($length)
	{
		$randstr=''; 
		srand((double)microtime()*1000000); 
		$chars = array ( 'a','b','C','D','e','f','G','h','i','J','k','L','m','N','P','Q','r','s','t','U','V','W','X','y','z','1','2','3','4','5','6','7','8','9'); 
		for ($rand = 0; $rand <= $length; $rand++) 
		{ 
			$random = rand(0, count($chars) -1); 
			$randstr .= $chars[$random]; 
		}
		return $randstr;
	}
	function validate_input($input,$dbcon=true,$content='all',$maxchars=0)
	{	
		if(get_magic_quotes_gpc()) 	
		{	
			if(ini_get('magic_quotes_sybase')) 	
			{	
				$input = str_replace("''", "'", $input);	
			}else {	
				$input = stripslashes($input);	
			}	
		}	
	
		if($content == 'alnum')
		{	
			$input = ereg_replace("[^a-zA-Z0-9]", '', $input);	
		}	
		elseif($content == 'num')	
		{	
			$input = ereg_replace("[^0-9]", '', $input);	
		}	
		elseif($content == 'alpha')	
		{
			$input = ereg_replace("[^a-zA-Z]", '', $input);	
		}
	
		if($maxchars)	
		{	
			$input = substr($input,0,$maxchars);	
		}	
	
		if($dbcon)	
		{
			$input = mysql_real_escape_string($input);	
		}else{	
			$input = mysql_escape_string($input);	
		}
	
		return $input;	
	}

	function text_wrap($text,$maxlength)
	{
		$txtarr=explode(" ",$text);
		$newtext=array();
		foreach($txtarr as $k=>$txt)
		{
			if (strlen($txt)>$maxlength)
			{
				$txt=wordwrap($txt, $maxlength, " ", 1);
			}
			$newtext[]=$txt;
		}
		return implode(" ",$newtext);
	}
	
	function convert2link($string)
	{
		$string = strtolower($string);
		$special_chars[] = 'ö';
		$special_chars[] = 'ü';
		$special_chars[] = 'Ö';
		$special_chars[] = 'Ä';
		$special_chars[] = 'Ü';
		$special_chars[] = 'ä';
		$special_chars[] = 'ü';
		$special_chars[] = 'ö';
		$special_chars[] = 'ß';
		$special_chars[] = 'Ž';
		$special_chars[] = '?';
		$special_chars[] = '.';
		$special_chars[] = ':';
		$special_chars[] = ',';
		$special_chars[] = '_';
		$special_chars[] = '-';
		$special_chars[] = '+';
		$special_chars[] = '&';
		$special_chars[] = '/';
		$special_chars[] = '\\';
		$special_chars[] = ' ';
		$special_chars[] = '"';
		$special_chars[] = '#';
		
		$special_chars2[] = 'oe';
		$special_chars2[] = 'ue';
		$special_chars2[] = 'Oe';
		$special_chars2[] = 'Ae';
		$special_chars2[] = 'Ue';
		$special_chars2[] = 'ae';
		$special_chars2[] = 'ue';
		$special_chars2[] = 'oe';
		$special_chars2[] = 'ss';
		$special_chars2[] = 'z';
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '_';
	
		$special_chars2[] = '';
		$special_chars2[] = '';
		$special_chars2[] = '_';
		$special_chars2[] = '_';
		$special_chars2[] = '';
		$special_chars2[] = '_';
		$special_chars2[] = '_';
		$special_chars2[] = '_';
		$special_chars2[] = '_';
		$special_chars2[] = '';		
		$string = str_replace($special_chars,$special_chars2,$string);	
		
		return $string;
	}
	/*
	* Get Prefix of site
	*
	* @return getting name of site eight character.
	**/
	function GetSitePrefix(){
		return substr(strtoupper(preg_replace(array('/\s/','/[^a-zA-z0-9]/'),'',GetConfig('SITE_TITLE'))),0,8);		
	}
	/**
	* Get Session name for create a dynamic session
	* 
	* @param session name
	* @return dynamic session name added title
	*/
	function GetSession($staticname){		
		if($staticname == 'cit_key')			
			return $staticname.session_id();
		else 
			return $staticname.session_name();	
	}
	/** 
	* Get Message Info create a message session for page
	* 
	* @return message sessoin
	**/
	function AddMessageInfo(){
		$GLOBALS['Message']  = '';
		if(isset($_SESSION['Success']) || isset($_SESSION['Error']) || isset($_SESSION[GetSession('Success')]) || isset($_SESSION[GetSession('Error')])){
			$GLOBALS['MessageStatus'] = 1;
		} else {
			$GLOBALS['MessageStatus'] = 0;
		} 
		if(isset($_SESSION['Success']) || isset($_SESSION[GetSession('Success')])){
			if($_SESSION['Success'] != ''){
				
				$GLOBALS['Message'] .= $_SESSION['Success'];					
				$GLOBALS['MessageClass'] = 'success';								   			
			}else{
				$GLOBALS['Message'] .= $_SESSION[GetSession('Success')];					
				$GLOBALS['MessageClass'] = 'success';
			}
		}
		if(isset($_SESSION['Error']) && $_SESSION['Error'] != ''){
			
			$GLOBALS['Message'] .= $_SESSION['Error'];					
			$GLOBALS['MessageClass'] = 'error';			   			
		}
		if(isset($_SESSION[GetSession('Error')]) && $_SESSION[GetSession('Error')] != ''){
			$GLOBALS['Message'] .= $_SESSION[GetSession('Error')];					
			$GLOBALS['MessageClass'] = 'error';		
		}

	}
	/**
	* Remove message info of created message 
	*
	* @return removed message status and exit from page
	**/
	function RemoveMessageInfo(){
		if($GLOBALS['MessageStatus'] == 1 ){
			if(isset($_SESSION['Success'])){
				$arrSession = array("Success");
				$GLOBALS['CLA_SESSION']->unsetSession($arrSession);
			}
			if(isset($_SESSION['Error'])){
				$arrSession = array("Error");
				$GLOBALS['CLA_SESSION']->unsetSession($arrSession);
			}	
			
			if(isset($_SESSION[GetSession('Success')])){
				unset($_SESSION[GetSession('Success')]);
			}
			if(isset($_SESSION[GetSession('Error')])){
				unset($_SESSION[GetSession('Error')]);
			}			
		}		
	}
	/**
	* Redirect page using javascript
	* @param page name for redirect	
	* 	
	* @retun javascript code for rediret
	**/
	function JavascriptHeader($redirectPage){
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml">';
		echo '<head>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo sprintf('<meta http-equiv="refresh" content="0; url=%s" />',$redirectPage);
		echo '<title>Loading.....</title>';
		echo '</head>';
		echo '<body>';
		echo '</body>';
		echo '</html>';				
	}
	/**
	* Url 
	*
	* @return url 
	**/
	function GetUrl($request){		
		$url = '';
		$countarr = 1;
		if(GetConfig('mod_rewrite')){			
			$url = $GLOBALS['ROOT_LINK'].'/';
			foreach($request as $requestvalue){
				$url .= $requestvalue;
				if(count($request) > $countarr){
					$url .= "/";
				}
				$countarr++;
			}	
		} else {
			$url = $GLOBALS['ROOT_LINK'].'/index.php?';
			foreach($request as $requestkey=>$requestvalue){
				$url .= $requestkey."=".$requestvalue;
				if(count($request) > $countarr){
					$url .= "&";
				}
				$countarr++;
			}	
		}
		return $url;
	}
	/**
	* Admin Url 
	*
	* @return admin url 
	**/
	function GetAdminUrl($request, $pagelink = 1){		
		$url = '';
		$countarr = 1;		
		if(GetConfig('mod_rewrite')){			
			$url = $GLOBALS['ADMIN_LINK'].'/';
			foreach($request as $requestvalue){
				$url .= $requestvalue;
				if(count($request) > $countarr){
					$url .= "/";
				}
				$countarr++;
			}	
			if($pagelink){
				if(isset($_REQUEST['page'])){
					$url .= '/'.$_REQUEST['page'];
				}
			}
		} else {
			$url = $GLOBALS['ADMIN_LINK'].'/index.php?';
			foreach($request as $requestkey=>$requestvalue){
				$url .= $requestkey."=".$requestvalue;
				if(count($request) > $countarr){
					$url .= "&";
				}
				$countarr++;
			}	
			if($pagelink){
				if(isset($_REQUEST['page'])){
					$url .= '&page='.$_REQUEST['page'];
				}
			}
		}
		return $url;
	}
	/**
	 * Parse an incoming shop path and turn it in to both a valid shop path and
	 * application path.
	 *
	 * @param string The URL to transform.
	 * @return array Array of shopPath and appPath
	 */
	function ParseShopPath($url)
	{
		$parts = parse_url($url);
		if(!isset($parts['scheme'])) {
			$parts['scheme'] = 'http';
		}

		if(!isset($parts['path'])) {
			$parts['path'] ='';
		}
		$parts['path'] = rtrim($parts['path'], '/');

		$shopPath = $parts['scheme'].'://'.$parts['host'];
		if(!empty($parts['port']) && $parts['port'] != 80) {
			$shopPath .= ':'.$parts['port'];
		}

		$shopPath .= $parts['path'];

		return array(
			'sitePath' => $shopPath,
			'folderPath' => $parts['path']
		);
	}
	
	
	/**
	 * Redirect to module page in admin side
	 *
	 * @param string The name of the class to load.
	 * @return object The instantiated version fo the class.
	 */
	function FrontRedirectUrl($currenturl = ''){				
		$redirectlink = '';
		if($currenturl == ''){			
			if($GLOBALS['SeoUnabled']){	
				$redirectlink = sprintf('Location: %s/%s',$GLOBALS['ROOT_LINK'],$_REQUEST['module']);					 
			} else {
				$redirectlink = sprintf('Location: %s/index.php?module=%s',$GLOBALS['ROOT_LINK'],$_REQUEST['module']);
			}			
		} else {				
			$redirectlink = sprintf('Location: %s',$currenturl);
		}
		return @header($redirectlink);
	}
	/**
	 * Redirect to module page in admin side
	 *
	 * @param string The name of the class to load.
	 * @return object The instantiated version fo the class.
	 */
	function GetFrontRedirectUrl($currenturl = ''){		
		if(!headers_sent()){	
			FrontRedirectUrl($currenturl);
		} else {
			if($currenturl == ''){				
				if($GLOBALS['SeoUnabled']){	
					JavascriptHeader(sprintf('%s/%s',$GLOBALS['ROOT_LINK'],$_REQUEST['module']));
				} else {
					JavascriptHeader(sprintf('%s/index.php?module=%s',$GLOBALS['ROOT_LINK'],$_REQUEST['module']));
				}				
			} else {
				JavascriptHeader(sprintf('%s',$currenturl));
			}
		}		
	}
	function GetRequestUrl($request, $pagelink = 1){		
		$url = '';
		$countarr = 1;		
		if(GetConfig('mod_rewrite')){						
			$url = '/';
			foreach($request as $requestvalue){
				$url .= $requestvalue;
				if(count($request) > $countarr){
					$url .= "/";
				}
				$countarr++;
			}				
		} else {
			$url = '&';
			foreach($request as $requestkey=>$requestvalue){
				$url .= $requestkey."=".$requestvalue;
				if(count($request) > $countarr){
					$url .= "&";
				}
				$countarr++;
			}				
		}
		return $url;
	}
	
	function date_dropdown($sday='',$smonth='',$syear=''){
		$year_limit = 0;
        $html_output = '<div class="row">';
        //$html_output .= '<label for="date_day">Date of birth:</label>'."\n";

        /*days*/
        $html_output .= '<div class="col-md-3"><select name="date_day" class="form-control" required>';
		$html_output .= '<option value="" >Day</option>';
            for ($day = 1; $day <= 31; $day++) {
				if($day == $sday){$dsel = 'selected="selected"';}else{$dsel='';}
                $html_output .= '<option '.$dsel.'>' . $day . '</option>';
            }
        $html_output .= '</select></div>';

        /*months*/
        $html_output .= '<div class="col-md-3"><select name="date_month"  class="form-control" required >';
        $months = array("", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
			$html_output .= '<option value="" >Month</option>';
            for ($month = 1; $month <= 12; $month++) {
				if($month == $smonth){$msel = 'selected="selected"';}else{$msel='';}
                $html_output .= '<option value="' . $month . '" '.$msel.'>' . $months[$month] . '</option>';
            }
        $html_output .= '</select></div>';

        /*years*/
        $html_output .= '<div class="col-md-3"><select name="date_year"  class="form-control" required>';
		$html_output .= '<option value="" >Year</option>';
            for ($year = 1900; $year <= (date("Y") - $year_limit); $year++) {
				if($year == $syear){$ysel = 'selected="selected"';}else{$ysel='';}
                $html_output .= '<option '.$ysel.'>' . $year . '</option>';
            }
        $html_output .= '</select></div>';
		 $html_output .= '</div>';

		
   		 return $html_output;
	}
	function GetPriceFormat($price){
		$currency_code = $GLOBALS['SITE_CURRENCYSYMBOL'];
		$price = $currency_code.number_format($price,2);
		return $price;
		
	}
	function GetDateFormat($date){
		
		return date_format(date_create($date),"d/m/Y h:i:s a");		
	}
	function GetOnlyDate($date){
		
		return date_format(date_create($date),"d/m/Y");		
	}
	function GetOnlyTime($date){
		
		return date_format(date_create($date),"h:i:s a");		
	}

	function GetDateTimeFormat($date){
		$dates = date_format(date_create($date),"d/m/Y");	
		$time = date_format(date_create($date),"h:i:s a");
		$datetime = $dates."<br>".$time;
		return $datetime;
	}
	
	function GetBpointCrn($site_id,$user_id){ 
	
		$siteid = str_pad($site_id, 3, '0', STR_PAD_LEFT);
		$userid = str_pad($user_id, 15, '0', STR_PAD_LEFT);
		$number = $siteid.$userid;
		$number = preg_replace("/\D/", "", $number);
		
		if(!is_numeric($number)) return false; 
		
		if($number <= 0) return false; 
	
		 $length = strlen($number); // Get the length of the seed number
		 $total = 0;
	
		// For each character in seed number, sum the character multiplied by its one based array position (instead of normal PHP zero based numbering)
		for($i = 0; $i < $length; $i++) $total += $number[$i] * ($i + 1);
	
		// The check digit is the result of the sum total from above mod 10
		$checkdigit = fmod($total, 10);
	
		// Return the original seed plus the check digit
		return $number . $checkdigit;
	}
	
	function GetLoginRedirect(){
		return $GLOBALS['DEFINE_ROOT_LINK'].$_SERVER['REQUEST_URI'];
	}
	function GetSubString($string,$lenth){
	    $string = strip_tags($string);
		return mb_strlen($string) > $lenth ? mb_substr($string, 0, $lenth) . "..." : $string;
	}
	
	function SeoFriendlyUrl($string){
		$string = str_replace(array('[\', \']'), '', $string);
		$string = preg_replace('/\[.*\]/U', '', $string);
		$string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
		$string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
		return strtolower(trim($string, '-'));
	}
	
	function GetAffiliateDir($company_id){
		$company_array = array(1=>'rakuten',2=>'apd',3=>'cf',4=>'other',5=>'ph',6=>'ir',7=>'optimise',8=>'cj');
		if($company_id){
			return $company_array[$company_id];
		}else{
			return $company_array;
		}
	}
	
	function GetCountry($country_code =''){
		$country_array = array('AU'=>'Australia','IN'=>'India','NZ'=>'New Zealand','GB'=>'United Kingdom','US'=>'United States');
		if($country_code){
			return $country_array[$country_code];
		}else{
			return $country_array;
		}
	}
	
	function AmazonTrakingId($site_id){
		$trakingids = array(3=>'ez07-22',25=>'eagles05-22',54=>'wm0cf-22',55=>'mnd-22',56=>'th068-22',57=>'ph0cd-22',60=>'lakeanalytic-22',61=>'realinnovation-22',62=>'sportclub-22',63=>'wtogeather-22',64=>'mbbc-22');
		return $trakingids[$site_id];
	}
	
	function encrypted_id($id){
		 $salt= 54;
		 return  $encuserid = base64_encode($id.$salt);
	}
	function decrypted_id($id){
		 $salt= 54;
		 $decrypted_id_raw = base64_decode($id);
		 return $decrypted_id = preg_replace(sprintf('/%s/', $salt), '', $decrypted_id_raw);
	}
	
	function GooglerecaptchaResponse(){
		
		if($GLOBALS['SITE_ID'] < 50){
			$secret = '6Ld9oScTAAAAAGurZIIygQimY9ti6GwAfbYZ2Vb4';
		}else{
			$secret = '6LetgFgUAAAAAIyLT8iLZSCxDTe1k_gpZI-GO9j5';
		}
		$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response'].'&remoteip='.$_SERVER['REMOTE_ADDR']);
        $responseData = json_decode($verifyResponse);
		return $responseData;
	}
	function GoogleDataSiteKey(){
		if($GLOBALS['SITE_ID'] < 50){
			$secret = '6Ld9oScTAAAAANQ9Lv6-FMzXvtkBTXvlNNo30Iny';
		}else{
			$secret = '6LetgFgUAAAAALwQcqWxk5TCpoHIm7NgO03zlg5x';
		}
		return $secret;
	}
	
	function addhttp($url) {
		$url = trim($url);
    	if (!preg_match("~^(?:f|ht)tps?://~i", $url) && $url !="") { $url = "http://" . $url; }
    	return $url;
	}

	function _SendSMS($number,$msg,$ref='') { // get started send sms 
		
		$username = 'gosgroup';
    	$password = 'Tusmore2011';
    	$destination = $number; //Multiple numbers can be entered, separated by a comma
    	$source    = $GLOBALS['SITE_SORTTITLE'];
    	$text = $msg;
    	$ref = $ref;
		
		$content =  'username='.rawurlencode($username).'&password='.rawurlencode($password).'&to='.rawurlencode($destination).'&from='.rawurlencode($source).'&message='.rawurlencode($text).'&ref='.rawurlencode($ref).'&maxsplit=5';
        $ch = curl_init('http://api.smsbroadcast.com.au/api-adv.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec ($ch);
        curl_close ($ch);
        return $output;       
    }
	
	function _SendMail($tomail,$send,$subject,$message){
		
		$SendMailType = GetConfig('mailer');
		$email = $GLOBALS['DB']->row("SELECT * FROM `emailsender` where site_id = ?",array($GLOBALS['SITE_ID']));
		$to = $tomail;
		$from = $email['emailsender_from'];
		$subject = $subject;
		$sender = $GLOBALS['SITE_TITLE'];
		$sender_name = $sender;
		
		$headers  = "From: $sender_name\n";
		$headers .= "Reply-To: <$from>\n";
		$headers .= "Return-Path: <$from>\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0 \n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
		
		if($SendMailType =='mail'){			
			return mail($to, $subject, $message, $headers);
		}else{ 
			require_once('mail/class.phpmailer.php');
			require_once('mail/class.smtp.php');
			$mail = new PHPMailer();
			$mail->SetFrom($from,$GLOBALS['SITE_TITLE']);
			$mail->Subject = $subject;
			$mail->MsgHTML($message);
			$mail->AddReplyTo($from);
			try {
					
				$mail->AddAddress($to);
				
				
				if ($mail->Send()) {
				return true;
				} else {
				//echo $mail->ErrorInfo; exit;
				//die();
				return false;
				}
				$mail->ClearAddresses();
			} catch (phpmailerException $e) {
				echo $e->errorMessage();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
			echo '123';
			exit;
		}
		
		
	}
	
	function _SendMailAdmin($from,$send,$subject,$message){
		
		$SendMailType = GetConfig('mailer');
		$email = $GLOBALS['DB']->row("SELECT * FROM `emailsender` where site_id = ?",array($GLOBALS['SITE_ID']));
		//if(!$from){ $from =  $email['emailsender_from'];}
		
		if($send==1){ 
			$send_name = $email['contact_name']; $send_email = $email['contact_email']; 
		}elseif($send==2){
			$send_name = $email['sales_name']; $send_email = $email['sales_email']; 
		}elseif($send==3){
			$send_name = $email['support_name']; $send_email = $email['support_email']; 
		}elseif($send ==4){
			$send_name = $email['custome_name']; $send_email = $email['custome_email']; 
		}else{
			$send_name = $email['contact_name']; $send_email = $email['contact_email']; 
		}
		
		
		$to = $send_email;
		$from = $email['emailsender_from'];
		$subject = $subject;
		$sender = $GLOBALS['SITE_TITLE'];
		$sender_name = $sender;
		
		$headers  = "From: $sender_name\r\n";
		$headers .= "Reply-To: <$from>\r\n";
		$headers .= "Return-Path: <$from>\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0 \r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		if($SendMailType =='mail'){
			if($to){
				$send = mail($to, $subject, $message, $headers);
			 	if($send){ return true; }else{ return false;}
			}				
		}else{ 
			require_once('mail/class.phpmailer.php');
			require_once('mail/class.smtp.php');
		
			$mail = new PHPMailer();
			$mail->SetFrom($from,$GLOBALS['SITE_TITLE']);
			$mail->Subject = $subject;
			$mail->MsgHTML($message);
			$mail->AddReplyTo($from);
			try {
				$emails = explode(",",$to);
				foreach($emails as $emailto){
					$mail->AddAddress($emailto);
				}
				
				if ($mail->Send()) {
				return true;
				} else {
				//echo $mail->ErrorInfo;
				//die();
				return false;
				}
				$mail->ClearAddresses();
			} catch (phpmailerException $e) {
				echo $e->errorMessage();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		
		
	}
	
	
	function _SendMailMasterAdmin($from,$send,$subject,$message){
		
		$SendMailType = GetConfig('mailer');
		$masteradminemail= $GLOBALS['DB']->row("SELECT * FROM `emailsender` where site_id = 0");
		
		$email = $GLOBALS['DB']->row("SELECT * FROM `emailsender` where site_id = ?",array($GLOBALS['SITE_ID']));
			
		if($send==1){ 
			$madmin_name = $masteradminemail['contact_name']; $madmin_email = $masteradminemail['contact_email']; 
		}elseif($send==2){
			$madmin_name = $masteradminemail['sales_name']; $madmin_email = $masteradminemail['sales_email']; 
		}elseif($send==3){
			$madmin_name = $masteradminemail['support_name']; $madmin_email = $masteradminemail['support_email']; 
		}elseif($send ==4){
			$madmin_name = $masteradminemail['custome_name']; $madmin_email = $masteradminemail['custome_email']; 
		}else{ 
			$madmin_name = false; $madmin_email = false; 
		}
		
		$to = $madmin_email;
		$from = $email['emailsender_from'];
		$subject = $subject;
		$sender = $GLOBALS['SITE_TITLE'];
		$sender_name = $sender;
		
		$headers  = "From: $sender_name\r\n";
		$headers .= "Reply-To: <$from>\r\n";
		$headers .= "Return-Path: <$from>\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0 \r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		if($SendMailType =='mail'){
			if($to){
				$send = mail($to, $subject, $message, $headers);
			 	if($send){ return true; }else{ return false;}
			}			
		}else{ 
			require_once('mail/class.phpmailer.php');
			require_once('mail/class.smtp.php');
			
			$mail = new PHPMailer();
			$mail->SetFrom($from,$GLOBALS['SITE_TITLE']);
			$mail->Subject = $subject;
			$mail->MsgHTML($message);
			$mail->AddReplyTo($from);
			try {
				$emails = explode(",",$to);
				foreach($emails as $emailto){
					$mail->AddAddress($emailto);
				}
				if ($mail->Send()) {
				return true;
				} else {
				//echo $mail->ErrorInfo;
				//die();
				return false;
				}
				$mail->ClearAddresses();
			} catch (phpmailerException $e) {
				echo $e->errorMessage();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}
	
	function _getEmailTemplate($temurl,$is_admin=''){
		// Get TEmplate Header Footer
		if($is_admin != 'admin'){
			$TemdetailRow = $GLOBALS['DB']->row("SELECT * FROM emailtemplate_detail WHERE site_id = ? LIMIT 0,1",array($GLOBALS['SITE_ID']));
			if($TemdetailRow['template_header'] !="" && $TemdetailRow['template_footer'] !=""){
				$emailHeader = $GLOBALS['CLA_HTML']->addContent($TemdetailRow['template_header']);
				$emailFooter = $GLOBALS['CLA_HTML']->addContent($TemdetailRow['template_footer']);
			}else{
				$TemdetailRow = $GLOBALS['DB']->row("SELECT * FROM emailtemplate_detail WHERE site_id = 0 LIMIT 0,1");
				$emailHeader = $GLOBALS['CLA_HTML']->addContent($TemdetailRow['template_header']);
				$emailFooter = $GLOBALS['CLA_HTML']->addContent($TemdetailRow['template_footer']);
			}
		 }else{
			$TemdetailRow = $GLOBALS['DB']->row("SELECT * FROM emailtemplate_detail WHERE site_id = 0 LIMIT 0,1");
			$emailHeader = $GLOBALS['CLA_HTML']->addContent($TemdetailRow['template_adminheader']);
			$emailFooter = $GLOBALS['CLA_HTML']->addContent($TemdetailRow['template_adminfooter']);
		 }
		
		// Get TEmplate
		$TemplateRow = $GLOBALS['DB']->row("SELECT * FROM `emailtemplate` WHERE template_url = ? AND site_id = ? LIMIT 0,1",array($temurl,$GLOBALS['SITE_ID']));
		
		if($TemplateRow['template_content'] !=""){
			$GLOBALS['EMAIL_SUBJECT'] = $GLOBALS['CLA_HTML']->addContent($TemplateRow['template_subject']);
			$emailContent = $GLOBALS['CLA_HTML']->addContent($TemplateRow['template_content']);
		}else{
		    $TemplateRow = $GLOBALS['DB']->row("SELECT * FROM `emailtemplate` WHERE template_url = ? AND site_id = 0 LIMIT 0,1",array($temurl));
			$GLOBALS['EMAIL_SUBJECT'] = $GLOBALS['CLA_HTML']->addContent($TemplateRow['template_subject']);
			$emailContent = $GLOBALS['CLA_HTML']->addContent($TemplateRow['template_content']);
		}
		
		if($TemplateRow['template_content'] == ""){ 
			echo "Please insert Email Template".$temurl; exit;
		}else{
			$emailContent='<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;"><tr>
    <td align="center" valign="top">'.$emailContent.'</td></tr></table>';
			$EmailTemplate = $emailHeader.''.$emailContent.''.$emailFooter;
		}
		
		return $EmailTemplate;
	}
	
	//Url Shorten
	function shortenURL($url){
		$apiv4 = 'https://api-ssl.bitly.com/v4/bitlinks';
		$genericAccessToken = '6c14a76c2b510d32c6d92ca2923f23a62c2075eb';
		$data = array('long_url' => $url);
		$payload = json_encode($data);
		$header = array('Authorization: Bearer ' . $genericAccessToken,
							'Content-Type: application/json',
							'Content-Length: ' . strlen($payload));	
		$ch = curl_init($apiv4);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		$resultToJson = json_decode($result);
		if (isset($resultToJson->link)) {
			return $resultToJson->link;
		}
		else{
			return false;
		}
	}
	
	function _Paginate($total_records='')
	{
		$pagination = '';
		$item_per_page = $GLOBALS['PerPage'];
		$current_page = $GLOBALS['PageStart']; 
		$total_pages = ceil($total_records/$item_per_page); 
		$page_url = GetUrl(array('module'=>$_REQUEST['module'],'page'=>'page'));
		
		if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
			$pagination .= ' <div class="paginate_main"><ul class="paginationul">';
			$right_links    = $current_page + 3; 
			$previous       = $current_page - 1; //previous link 
			$next           = $current_page + 1; //next link
			$first_link     = true; //boolean var to decide our first link
			
			if($current_page > 1){
				$previous_link = ($previous==0)?1:$previous;
				$pagination .= '<li class="first"><a href="'.$page_url.'/1" title="First">«</a></li>'; //first link
				$pagination .= '<li><a href="'.$page_url.'/'.$previous_link.'" title="Previous"><</a></li>'; //previous link
				for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
					if($i > 0){
						$pagination .= '<li><a href="'.$page_url.'/'.$i.'">'.$i.'</a></li>';
					}
				}   
				$first_link = false; //set first link to false
			}
			
			if($first_link){ //if current active page is first link
				$pagination .= '<li class="first active">'.$current_page.'</li>';
			}elseif($current_page == $total_pages){ //if it's the last active link
				$pagination .= '<li class="last active">'.$current_page.'</li>';
			}else{ //regular current link
				$pagination .= '<li class="active">'.$current_page.'</li>';
			}
			for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
				if($i<=$total_pages){
					$pagination .= '<li><a href="'.$page_url.'/'.$i.'">'.$i.'</a></li>';
				}
			}
			if($current_page < $total_pages){ 
				$next_link = ($i > $total_pages)? $total_pages : $i;
				$pagination .= '<li><a href="'.$page_url.'/'.$next_link.'" >></a></li>'; //next link
				$pagination .= '<li class="last"><a href="'.$page_url.'/'.$total_pages.'" title="Last">»</a></li>'; //last link
			}
			$pagination .= '</ul></div>'; 
		}
		return $pagination; //return pagination links
	}
	
	function _callwemadApi($url='', $fields=array()){
		if($url == ""){
			echo "please add app url"; return false;
		}else{
			$url = $GLOBALS['SITE_APPURL']."/".$url;
		}
	
		$auth = base64_encode('wemakeadifference:nHNfTwfu!)Sq7m06J73BNYHq');
		$headers = array('Authorization: Basic '.$auth);
		
		$fields_string='';
		foreach($fields as $key=>$value){ $fields_string .= $key.'='.$value.'&'; }
		
		rtrim($fields_string, '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);
		$result = json_decode($result, TRUE);
		
		return $result;
	}	