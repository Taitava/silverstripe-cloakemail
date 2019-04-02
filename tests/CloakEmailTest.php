<?php

namespace Taitava\CloakEmail;

use SilverStripe\Dev\SapphireTest;

class CloakEmailTest extends SapphireTest
{
	
	public function testCloak()
	{
		$options = CloakEmailTest::options();
		foreach (self::expected_cloak_results() as $mode => $expected_cloak_result)
		{
			$options['mode'] = $mode;
			$this->apply_options($options);
			$actual_cloak_result = CloakEmail::Cloak(
				static::dummy_email(),
				'template' // type does not matter, because both 'template' and 'page' have similar options set in $this->options().
			);
			$this->assertEquals($expected_cloak_result, $actual_cloak_result);
		}
	}
	
	public function testCloakAll()
	{
		$options = CloakEmailTest::options();
		$test_content = $this->dummy_content($this->dummy_email());
		foreach (self::expected_cloak_results() as $mode => $expected_cloaked_email)
		{
			$expected_cloak_result = $this->dummy_content($expected_cloaked_email);
			$options['mode'] = $mode;
			$this->apply_options($options);
			$actual_cloak_result = CloakEmail::CloakAll(
				$test_content,
				'template' // type does not matter, because both 'template' and 'page' have similar options set in $this->options().
			);
			$this->assertEquals($expected_cloak_result, $actual_cloak_result);
		}
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
	
	/**
	 * @return array
	 */
	private static function expected_cloak_results()
	{
		$simple_part = 'email[DOT]address[AT]domain[DOT]tld'; // This is used repeatedly, so keep it in a separate variable
		return [
			'none' => static::dummy_email(),
			'nojs' => '<a href="mailto:' . $simple_part . '">' . $simple_part . '</a>',
			'simple' => '<span class="simple-cloak"><a href="mailto:' . $simple_part . '">' . $simple_part . '</a></span>',
			'hard' => '<noscript>[ERROR]</noscript><span class="hard-cloak insert-link" style="display: none;">101-109-97-105-108-46-97-100-100-114-101-115-115-64-100-111-109-97-105-110-46-116-108-100</span>',
		];
	}
	
	/**
	 * @return string
	 */
	private static function dummy_email()
	{
		return 'email.address@domain.tld';
	}
	
	/**
	 * @param string $cloaked_or_plain_email
	 * @return string
	 */
	private static function dummy_content($cloaked_or_plain_email)
	{
		return str_replace('[email]', $cloaked_or_plain_email, '<div>[email]</div><p>[email]</p>');
	}
}