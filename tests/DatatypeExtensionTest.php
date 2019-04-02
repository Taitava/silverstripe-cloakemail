<?php


namespace Taitava\CloakEmail;


use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBEnum;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\FieldType\DBVarchar;

class DatatypeExtensionTest extends SapphireTest
{
	public function testCloak()
	{
		$this->apply_options(static::options());
		foreach ($this->datatypes() as $datatype)
		{
			/** @var DBField $field */
			$field = Injector::inst()->create($datatype);
			$field->setValue($this->dummy_email());
			$actual_cloak_result = $field->Cloak();
			$this->assertEquals($this->expected_cloak_result(), $actual_cloak_result);
		}
	}
	
	private function datatypes()
	{
		return [
			DBText::class,
			DBVarchar::class,
			DBEnum::class,
		];
	}
	
	private function apply_options(array $options)
	{
		foreach ($options as $option => $value)
		{
			CloakEmail::config()->merge($option, $value);
		}
	}
	
	private static function options()
	{
		return [
			'mode' => 'simple',
			'dot' => '[DOT]',
			'at' => '[AT]',
			'convert_page_content' => true,
			'page_insert_links' => true,
			'template_insert_links' => true,
			'purge_mailto_links' => true,
			'hard_noscript_error' => '[ERROR]',
		];
	}
	
	private static function expected_cloak_result()
	{
		$simple_part = 'email[DOT]address[AT]domain[DOT]tld'; // This is used repeatedly, so keep it in a separate variable
		return '<span class="simple-cloak"><a href="mailto:' . $simple_part . '">' . $simple_part . '</a></span>';
	}
	
	/**
	 * @return string
	 */
	private static function dummy_email()
	{
		return 'email.address@domain.tld';
	}
}