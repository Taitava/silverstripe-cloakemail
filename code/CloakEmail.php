<?php

class CloakEmailDatatypeExtension extends DataExtension
{
	public function Cloak() //This becomes a method in datatypes Text, Varchar and Enum. Can be called from templates, but also from PHP code.
	{
		return CloakEmail::CloakAll($this->owner->value, 'template');
	}
}

class CloakEmailPageExtension extends DataExtension
{
	public function Content()
	{
		if (!Config::inst()->get('CloakEmail', 'convert_page_content'))
		{
			//Page content conversion is disabled, so just return the content as is.
			return $this->owner->Content;
		}
		
		#return $this->owner->Content;
		return CloakEmail::CloakAll($this->owner->Content, 'page');
		
	}
	
	public function Cloak($value)  //This can be called from templates using $Cloak within the scope of a Page object
	{
		return CloakEmail::Cloak($value, 'template');
	}
}

class CloakEmail extends Object
{
	private static $mode					= 'simple';
	private static $convert_page_content	= false;
	private static $template_insert_links	= false;
	private static $page_insert_links		= false;
	private static $at						= ' at ';
	private static $dot						= ' dot ';
	private static $hard_noscript_error		= 'JavaScript must be turned on in order to see this email address.';
	
	/**
	 * Whether or not to remove any mailto links before converting email addresses. This prevents the conversion from breaking the link HTML, and if you set page_insert_links or template_insert_links to true, the purged links will be recreated - but their anchor text will be replaced with the email address itself even if it were something different before the conversion!
	 * @var bool
	 */
	private static $purge_mailto_links = false;
	
	public static function Cloak($value, $type)
	{
		//$type determines whether the call originates from a 'template' or from a 'page'. Some configuration values differ between pages and templates.
		
		$options= static::getOptions($type);
		$mode	= $options['mode'];
		return CloakEmailModes::$mode($value, $options);
	}
	
	public static function CloakAll($content, $type)
	{
		//Iterate all *****@*****.*** occurrences in the Page's Content field
		//and replace with the result of CloakEmail::Cloak(*EMAILADDRESS*)
		
		$email_detection_pattern = '/ [\w\d\-\.]+ @ [\w\d\-]+ \. [\w\d\-\.]+ /ix';
		/* Specification:
			1) Segment that contains a name: a-z, 0-9, . or - before an @ character.
			2) Segment that contains a domain: a-z, 0-9 or -.
			3) Segment that contains a tld (like .com) or multiple tld's (like .co.uk or .what.ever.com)
			Modifier 'i' makes it case insensitive, and 'x' makes spaces meaningless (= more readable)
		*/
		
		if (static::config()->get('purge_mailto_links'))
		{
			//Remove <a href="mailto: ... links in order to prevent the conversion from corrupting the HTML.
			$link_detection_pattern = '/ <a [^>].* href=["\']mailto: ([^"\']+) ["\'] [^>]* > (.*?) <\/a\s*> /ix';
			$content = preg_replace($link_detection_pattern, '$1', $content);
		}
		
		return preg_replace_callback(
			$email_detection_pattern,
			function ($matches) use ($type)
			{
				return CloakEmail::Cloak($matches[0], $type);
			},
			$content
		);
	}
	
	public static function RequireJavaScript($options)
	{
		static $done = false;
		if ($done) return;
		Requirements::customScript('var CloakEmailOptions = '.json_encode($options).';');
		Requirements::javascript('framework/thirdparty/jquery/jquery.js');
		Requirements::javascript('cloakemail/js/CloakEmail.js');
		$done = true;
	}
	
	public static function getOptions($type)
	{
		return array(
			'mode'					=> Config::inst()->get(__CLASS__, 'mode'),
			'dot'					=> Config::inst()->get(__CLASS__, 'dot'),
			'at'					=> Config::inst()->get(__CLASS__, 'at'),
			'insert_link'			=> Config::inst()->get(__CLASS__, ($type == 'page' ? 'page_insert_links' : 'template_insert_links')),
			'hard_noscript_error'	=> Config::inst()->get(__CLASS__, 'hard_noscript_error'),
		);
	}
}

class CloakEmailModes
{
	public static function none($value)
	{
		return $value;
	}
	
	public static function nojs($value, $options)
	{
		$address = str_replace(array('.','@'), array($options['dot'],$options['at']), $value);
		if ($options['insert_link']) return "<a href=\"mailto:$address\">$address</a>";
		return $address;
	}
	
	public static function simple($value, $options)
	{
		CloakEmail::RequireJavaScript($options);
		$address = str_replace(array('.','@'), array($options['dot'],$options['at']), $value);
		if ($options['insert_link']) $address = "<a href=\"mailto:$address\">$address</a>";
		return '<span class="simple-cloak">'.$address.'</span>';
	}
	
	public static function hard($value, $options)
	{
		CloakEmail::RequireJavaScript($options);
		$noscript = '<noscript>'.$options['hard_noscript_error'].'</noscript>';
		$chars = array();
		for ($i = 0; $i < strlen($value); $i++)
		{
			$char	= $value[$i];
			$chars[]= ord($char); //Convert the character to a decimal number
		}
		$insert_link = $options['insert_link'] ? ' insert-link' : '';
		return "$noscript<span class=\"hard-cloak$insert_link\" style=\"display: none;\">".implode('-',$chars)."</span>";
	}
}


