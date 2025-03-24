<?php
/********************************************************
 * Template Engine
 * Class for templatating a script
********************************************************
 * @package Template Engine
********************************************************/
class CIT_Html
{
	// The template file being used
	private $template;
	// the email template file being used
	private $emailTemplate;
	// The html content of the template
	private $html;
	// The parameters to be replaced
	private $parameters = array();
	/*************************************************************************
	* function addMain ($template)
	* Reads the file into a string variable
	* USAGE example:
	* $template = 'html/index.html';
	* $page->CIT_Html($template);
	*************************************************************************
	* $template: The file you want to convert
	* ***********************************************************************/

	function addMain($template)
	{
		$this->template = $template;
		$this->html = implode ('', (file($this->template)));
	}
	/*************************************************************************
	* function SetLoop ($name, $values)
	* Outputs an array as an html loop
	* USAGE example:
	* $items['0']['title'] = 'A Book';
	* $items['0']['id'] = '1';
	* $page->SetLoop ("ITEMS", $items);
	*************************************************************************
	* $name: The name of the Loop
	* $values: The Array
	* ***********************************************************************/
	function SetLoop ($name, $values)
	{

		$return = '';
		if(preg_match('/{LOOP: ' . $name . '\}(.*)\{\/LOOP: ' . $name . '\}/s', $this->html, $parts))
		{
			if(is_array($values))
			{
				foreach ($values as $value)
				{
					$templatestuff = $parts['1'];
					foreach ($value as $key2 => $value2)
					{
						if(!is_array($value2))
						{
							$templatestuff = str_replace('{%%' . $name . '.' . $key2 . '%%}', $value2, $templatestuff);
						}else{
							foreach ($value2 as $key3 => $value3)
							{
								if(!is_array($value3))
								{
									$templatestuff = str_replace('{%%' . $name . '.' . $key2 . '/' . $key3 . '%%}', $value3, $templatestuff);
								}else{
									foreach ($value3 as $key4 => $value4)
									{
										if(!is_array($value4))
										{
											$templatestuff = str_replace('{%%' . $name . '.' . $key2 . '/' . $key3 . '/' . $key4 . '%%}', $value4, $templatestuff);
										}
									}
								}
							}
						}
					}
					$return.= $templatestuff;
				}
			}else{
				$return = $values;
			}
		}

		if(isset($parts['0']))
		{
			$this->html = stripslashes(str_replace ($parts['0'], $return, $this->html));
		}
	}

	/*************************************************************************
	* function assign ($variable, $value)
	* Sets a paramateter to later be replaced in the template
	* USAGE example:
	* $page->assign ("TITLE", 'The about page');
	*************************************************************************
	* $variable: The parameters name
	* $value: The paramaters value
	* ***********************************************************************/
	function assign ($variable, $value)
	{
		$this->parameters[$variable] = $value;
	}

	/*************************************************************************
	* function display ()
	* Echos the finished template
	* $page->display();
	* ***********************************************************************/
	function display()
	{
		foreach ($this->parameters as $key => $value)
		{
			$template_name = '{' . $key . '}';
			$this->html = str_replace ($template_name, $value, $this->html);
		}
		$this->html = $this->Parse("DEFINE_", $this->html, $GLOBALS);
		$ifmatches = array();
		preg_match_all('/IF\(\"(.*?)\"(.*?)\"(.*?)\"\)\{(.*?)\{:IF\}/s', $this->html, $ifmatches);
		if(count($ifmatches['0']) != 0)
		{
			foreach ($ifmatches['0'] as $key => $value)
			{
				if(trim($ifmatches['2'][$key]) == '!=')
				{
					if($ifmatches['1'][$key] != $ifmatches['3'][$key])
					{
						$this->html = str_replace($value, $ifmatches['4'][$key], $this->html);
					}
					else
					{
						$this->html = str_replace($value, '', $this->html);
					}
				}
				elseif(trim($ifmatches['2'][$key]) == '==')
				{
					if($ifmatches['1'][$key] == $ifmatches['3'][$key])
					{
						$this->html = str_replace($value, $ifmatches['4'][$key], $this->html);
					}else{
						$this->html = str_replace($value, '', $this->html);
					}
				}
				elseif(trim($ifmatches['2'][$key]) == '<')
				{
					if($ifmatches['1'][$key] < $ifmatches['3'][$key])
					{
						$this->html = str_replace($value, $ifmatches['4'][$key], $this->html);
					}else{
						$this->html = str_replace($value, '', $this->html);
					}
				}
				elseif(trim($ifmatches['2'][$key]) == '>')
				{
					if($ifmatches['1'][$key] > $ifmatches['3'][$key])
					{
						$this->html = str_replace($value, $ifmatches['4'][$key], $this->html);
					}else{
						$this->html = str_replace($value, '', $this->html);
					}
				}
				elseif(trim($ifmatches['2'][$key]) == '%')
				{
					$mod = $ifmatches['1'][$key]%$ifmatches['3'][$key];
					if($mod == 0)
					{
						$this->html = str_replace($value, $ifmatches['4'][$key], $this->html);
					}else{
						$this->html = str_replace($value, '', $this->html);
					}
				}
			}
		}
		echo $this->html;
	}
	/**
	* For add more content
	*
	***/
	/*public function addSub($getHTML){
		$getHTML = implode ('', (file($getHTML)));
		$getHTML = $this->Parse("DEFINE_", $getHTML, $GLOBALS);
		return $getHTML;
	}*/
	public function addSub($getHTML, $getLoop = array()){
$getHTML = implode ('', (file($getHTML)));
$getHTML = $this->Parse("DEFINE_", $getHTML, $GLOBALS);
if(is_array($getLoop) && count($getLoop) != 0){
if(count($getLoop)){
foreach($getLoop as $name=>$values){
$getHTML = $this->SetSubLoop( $name, $values, $getHTML);
}
}
}
return trim($getHTML);
}

	/**
	* content
	**/
	public function addContent($getContentHtml){
		$getContentHtml = $this->Parse("DEFINE_",$getContentHtml,$GLOBALS);
		return $getContentHtml;
	}
	/**
	* Parse
	* Generic parsing function
	*
	* @param the prefix to search for
	* @param the text to parse
	* @param the associative array or function with the replacement
	* values in/returned by it
	*
	* @return string the parsed text
	*/
	public function Parse($prefix, $text, $replace)
	{
		$matches = array();
		$output = $text;

		// Parse out the language pack variables in the template file
		preg_match_all('/(?siU)(%%'.preg_quote($prefix).'[a-zA-Z0-9_\.]+%%)/', $output, $matches);

		foreach ($matches[0] as $key => $k) {
			$pattern1 = $k;
			$pattern2 = str_replace('%', '', $pattern1);
			$pattern2 = str_replace($prefix.'', '', $pattern2);

			if (is_array($replace) && isset($replace[$pattern2])) {
				$output = str_replace($pattern1, $replace[$pattern2], $output);
			} elseif (is_string($replace) && method_exists($this, $replace)) {
				$result = $this->$replace($pattern2);
				$output = str_replace($pattern1, $result, $output);
			} else {
				$output = str_replace($pattern1, '', $output);
			}
		}
		return $output;
	}
	// sub loop function
public function SetSubLoop ($name, $values,$subcontent)
{
$return = '';
if(preg_match('/{LOOP: ' . $name . '\}(.*)\{\/LOOP: ' . $name . '\}/s', $subcontent, $parts))
{
if(is_array($values))
{
foreach ($values as $value)
{
$templatestuff = $parts['1'];
foreach ($value as $key2 => $value2)
{
if(!is_array($value2))
{
$templatestuff = str_replace('{%%' . $name . '.' . $key2 . '%%}', $value2, $templatestuff);
}else{
foreach ($value2 as $key3 => $value3)
{
if(!is_array($value3))
{
$templatestuff = str_replace('{%%' . $name . '.' . $key2 . '/' . $key3 . '%%}', $value3, $templatestuff);
}else{
foreach ($value3 as $key4 => $value4)
{
if(!is_array($value4))
{
$templatestuff = str_replace('{%%' . $name . '.' . $key2 . '/' . $key3 . '/' . $key4 . '%%}', $value4, $templatestuff);
}
}
}
}
}
}
$return.= $templatestuff;
}
}else{
$return = $values;
}
}

if(isset($parts['0']))
{
$subcontent = stripslashes(str_replace ($parts['0'], $return, $subcontent));
}
return $subcontent;
}

}

?>
