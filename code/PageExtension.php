<?php

namespace Taitava\CloakEmail;

use SilverStripe\ORM\DataExtension;

class PageExtension extends DataExtension
{
	public function Content()
	{
		if (!$this->page_content_converting_allowed())
		{
			//Page content conversion is disabled, so just return the content as is.
			return $this->owner->Content;
		}
		
		return CloakEmail::CloakAll($this->owner->Content, 'page');
		
	}
	
	public function Cloak($value)  //This can be called from templates using $Cloak within the scope of a Page object
	{
		return CloakEmail::Cloak($value, 'template');
	}
	
	/**
	 * @return bool
	 */
	private function page_content_converting_allowed()
	{
		return (bool) CloakEmail::config()->convert_page_content;
	}
}