<?php

namespace Taitava\CloakEmail;

use SilverStripe\ORM\DataExtension;

class DatatypeExtension extends DataExtension
{
	public function Cloak() //This becomes a method in datatypes Text, Varchar and Enum. Can be called from templates, but also from PHP code.
	{
		return CloakEmail::CloakAll($this->owner->value, 'template');
	}
}