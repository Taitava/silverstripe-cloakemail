<?php


namespace Taitava\CloakEmail;


use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;

class PageExtensionTest extends SapphireTest
{
	public function testContent()
	{
		$this->apply_options(static::options());
		/** @var SiteTree $page */
		$page = Injector::inst()->create(SiteTree::class);
		$page->Content = $this->dummy_content();
		$actual_cloak_result = $page->Content();
		$this->assertEquals($this->expected_cloak_result(), $actual_cloak_result);
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
		return '<div><span class="simple-cloak"><a href="mailto:' . $simple_part . '">' . $simple_part . '</a></span></div>';
	}
	
	/**
	 * @return string
	 */
	private static function dummy_content()
	{
		return '<div>email.address@domain.tld</div>';
	}
}