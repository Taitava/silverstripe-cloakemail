<?php

class CloakEmailDatatypeExtension extends DataExtension
{
	public function Cloak() //This becomes a method in datatypes Text, Varchar and Enum. Can be called from templates, but also from PHP code.
	{
		return CloakEmail::CloakAll($this->owner->value, 'template');
	}
}