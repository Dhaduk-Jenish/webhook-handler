<?php
class CIT_INDEX
{
	private $action = '';
	private $id = '';
	public function __construct(){

	} 

	public function displayPage(){

		if(isset($_REQUEST['module']) && is_string($_REQUEST['module'])){
			if($_REQUEST['module'] == 'webhook'){
				require_once($GLOBALS['CLASSES'].'/'.$_REQUEST['module'].'.php');		
				$GLOBALS['CLA_INDEX'] = GetClass('CIT_WEBHOOK');	
				$GLOBALS['CLA_INDEX']->displayPage();
			}else{
				$GLOBALS['CLA_HTML']->addMain($GLOBALS['WWW_TPL'].'/dashboard.html');	
				$GLOBALS['CLA_HTML']->display();
			}
		}else{
			$GLOBALS['CLA_HTML']->addMain($GLOBALS['WWW_TPL'].'/dashboard.html');	
			$GLOBALS['CLA_HTML']->display();			
		}
				
			
	}



}