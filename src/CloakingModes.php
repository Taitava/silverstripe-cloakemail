<?php

namespace Taitava\CloakEmail;

class CloakingModes
{
	public static function none($value)
	{
		return $value;
	}
	
	public static function nojs($value, $options)
	{
		$address = str_replace(array('.', '@'), array($options['dot'], $options['at']), $value);
		if ($options['insert_link']) return "<a href=\"mailto:$address\">$address</a>";
		return $address;
	}
	
	public static function simple($value, $options)
	{
		CloakEmail::RequireJavaScript($options);
		$address = str_replace(array('.', '@'), array($options['dot'], $options['at']), $value);
		if ($options['insert_link']) $address = "<a href=\"mailto:$address\">$address</a>";
		return '<span class="simple-cloak">' . $address . '</span>';
	}
	
	public static function hard($value, $options)
	{
		CloakEmail::RequireJavaScript($options);
		$noscript = '<noscript>' . $options['hard_noscript_error'] . '</noscript>';
		$chars = array();
		for ($i = 0; $i < strlen($value); $i++)
		{
			$char = $value[$i];
			$chars[] = ord($char); //Convert the character to a decimal number
		}
		$insert_link = $options['insert_link'] ? ' insert-link' : '';
		return "$noscript<span class=\"hard-cloak$insert_link\" style=\"display: none;\">" . implode('-', $chars) . "</span>";
	}
}