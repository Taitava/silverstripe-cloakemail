<?php

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