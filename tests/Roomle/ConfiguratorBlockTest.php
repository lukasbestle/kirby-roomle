<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\ConfiguratorBlock
 */
class ConfiguratorBlockTest extends TestCase
{
	protected $app;

	public function setUp(): void
	{
		$this->app = new App([
			'options' => [
				'lukasbestle.roomle' => [
					'configuratorId' => 'defaultConfigurator',
					'target'         => 'default-target',
					'options'        => ['some' => 'default', 'other' => 'default']
				]
			],
			'site' => [
				'children' => [
					[
						'slug'  => 'test',
						'files' => [
							['filename' => 'image.png']
						]
					],
					[
						'slug' => 'custom-target'
					],
					[
						'slug' => 'default-target'
					]
				]
			],
			'urls' => [
				'index' => 'https://example.com',
				'media' => 'https://media.example.com'
			]
		]);
	}

	/**
	 * @covers ::configuratorId
	 */
	public function testConfiguratorId_Custom()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'configuratorid'    => 'demoConfigurator',
				'useconfiguratorid' => 'custom',
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('demoConfigurator', $block->configuratorId());
	}

	/**
	 * @covers ::configuratorId
	 * @covers ::option
	 */
	public function testConfiguratorId_Default()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'configuratorid'    => 'demoConfigurator',
				'useconfiguratorid' => 'default',
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('defaultConfigurator', $block->configuratorId());
	}

	/**
	 * @covers ::configuratorId
	 */
	public function testConfiguratorId_Invalid1()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Missing or invalid configurator ID setting');

		$block = new ConfiguratorBlock([
			'content' => [
				'configuratorid'    => '',
				'useconfiguratorid' => 'custom',
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$block->configuratorId();
	}

	/**
	 * @covers ::configuratorId
	 * @covers ::option
	 */
	public function testConfiguratorId_Invalid2()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Missing or invalid configurator ID setting');

		$app = $this->app->clone([
			'options' => [
				'lukasbestle.roomle' => [
					'configuratorId' => null
				]
			]
		]);

		$block = new ConfiguratorBlock([
			'content' => [
				'configuratorid'    => 'demoConfigurator',
				'useconfiguratorid' => 'default',
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$block->configuratorId();
	}

	/**
	 * @covers ::configuratorId
	 */
	public function testConfiguratorId_Invalid3()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Missing or invalid configurator ID setting');

		$block = new ConfiguratorBlock([
			'content' => [
				'configuratorid'    => 'demoConfigurator',
				'useconfiguratorid' => 'invalid',
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$block->configuratorId();
	}

	/**
	 * @coversNothing
	 */
	public function testFields()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'configuratorid'    => $configuratorId = 'demoConfigurator',
				'mainproductid'     => $mainProductId = 'some:product',
				'options'           => $options = 'some: option',
				'target'            => $target = ['default-target'],
				'useconfiguratorid' => $useConfiguratorId = 'custom',
				'usetarget'         => $useTarget = 'custom',
				'variants' => $variants = [
					[
						'productid' => 'some:variant',
						'title'     => 'Some variant',
						'subtitle'  => 'for cool people',
						'image'     => ['image.png']
					]
				]
			],
			'id'       => $id = '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame($configuratorId, $block->content()->configuratorId()->value());
		$this->assertSame($mainProductId, $block->content()->mainProductId()->value());
		$this->assertSame($options, $block->content()->options()->value());
		$this->assertSame($target, $block->content()->target()->value());
		$this->assertSame($useConfiguratorId, $block->content()->useConfiguratorId()->value());
		$this->assertSame($useTarget, $block->content()->useTarget()->value());
		$this->assertSame($variants, $block->content()->variants()->value());

		$this->assertSame($id, $block->id());
	}

	/**
	 * @covers ::frontendJson
	 */
	public function testFrontendJson()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid'     => 'some:product',
				'options'           => '',
				'useconfiguratorid' => 'default',
				'usetarget'         => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame(json_encode([
			'configuratorId' => 'defaultConfigurator',
			'htmlId' => 'roomle-12345678-90ab-cdef-1234-567890abcdef',
			'options' => [
				'some' => 'default',
				'other' => 'default',
				'id' => 'some:product',
			],
			'targetUrl' => 'https://example.com/default-target'
		]), $block->frontendJson());
	}

	/**
	 * @covers ::frontendProps
	 */
	public function testFrontendProps()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid'     => 'some:product',
				'options'           => '',
				'useconfiguratorid' => 'default',
				'usetarget'         => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame([
			'configuratorId' => 'defaultConfigurator',
			'htmlId' => 'roomle-12345678-90ab-cdef-1234-567890abcdef',
			'options' => [
				'some' => 'default',
				'other' => 'default',
				'id' => 'some:product',
			],
			'targetUrl' => 'https://example.com/default-target'
		], $block->frontendProps());
	}

	/**
	 * @covers ::hasVariants
	 */
	public function testHasVariants_No()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'variants' => []
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertFalse($block->hasVariants());
	}

	/**
	 * @covers ::hasVariants
	 */
	public function testHasVariants_Yes()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'variants' => [
					[
						'productid' => 'some:variant',
						'title'     => 'Some variant',
						'subtitle'  => 'for cool people',
						'image'     => ['image.png']
					]
				]
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertTrue($block->hasVariants());
	}

	/**
	 * @covers ::htmlId
	 */
	public function testHtmlId()
	{
		$block = new ConfiguratorBlock([
			'content' => [],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('roomle-12345678-90ab-cdef-1234-567890abcdef', $block->htmlId());
	}

	/**
	 * @covers ::jsUrl
	 */
	public function testJsUrl()
	{
		$block = new ConfiguratorBlock([
			'content' => [],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('https://media.example.com/plugins/lukasbestle/roomle/configurator.js', $block->jsUrl());
	}

	/**
	 * @covers ::mainProductId
	 */
	public function testMainProductId()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => 'some:product'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('some:product', $block->mainProductId());
	}

	/**
	 * @covers ::mainProductId
	 */
	public function testMainProductId_Invalid()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Missing main product ID');

		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => ''
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$block->mainProductId();
	}

	/**
	 * @covers ::options
	 * @covers ::option
	 */
	public function testOptions_Defaults()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => 'some:product',
				'options'       => '',
				'usetarget'     => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame([
			'some' => 'default',
			'other' => 'default',
			'id' => 'some:product',
		], $block->options());
	}

	/**
	 * @covers ::options
	 * @covers ::option
	 */
	public function testOptions_Locale1()
	{
		$app = $this->app->clone([
			'languages' => [
				['code' => 'en'],
				['code' => 'de']
			]
		]);
		$app->setCurrentLanguage('de');

		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => 'some:product',
				'options'       => '',
				'usetarget'     => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame([
			'locale' => 'de',
			'some' => 'default',
			'other' => 'default',
			'id' => 'some:product',
		], $block->options());
	}

	/**
	 * @covers ::options
	 * @covers ::option
	 */
	public function testOptions_Locale2()
	{
		$app = $this->app->clone([
			'languages' => [
				['code' => 'en-us'],
				['code' => 'de-at']
			]
		]);
		$app->setCurrentLanguage('de-at');

		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => 'some:product',
				'options'       => '',
				'usetarget'     => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame([
			'locale' => 'de',
			'overrideCountry' => 'at',
			'some' => 'default',
			'other' => 'default',
			'id' => 'some:product',
		], $block->options());
	}

	/**
	 * @covers ::options
	 * @covers ::option
	 */
	public function testOptions_MissingProduct()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Missing main product ID');

		$block = new ConfiguratorBlock([
			'content' => [
				'options'   => '',
				'usetarget' => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$block->options();
	}

	/**
	 * @covers ::options
	 * @covers ::option
	 */
	public function testOptions_NoTarget()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => 'some:product',
				'options'       => '',
				'usetarget'     => 'none'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame([
			'buttons' => [
				'add_to_basket' => false,
				'requestproduct' => false,
			],
			'some' => 'default',
			'other' => 'default',
			'id' => 'some:product',
		], $block->options());
	}

	/**
	 * @covers ::options
	 * @covers ::option
	 */
	public function testOptions_Overrides()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'mainproductid' => 'some:product',
				'options'       => "some: custom value\nskin:\n  primary-color: '#123456'\nid: should not be used",
				'usetarget'     => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame([
			'some' => 'custom value',
			'other' => 'default',
			'skin' => [
				'primary-color' => '#123456'
			],
			'id' => 'some:product',
		], $block->options());
	}

	/**
	 * @covers ::targetUrl
	 */
	public function testTargetUrl_Custom()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'target'    => ['custom-target'],
				'usetarget' => 'custom'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('https://example.com/custom-target', $block->targetUrl());
	}

	/**
	 * @covers ::targetUrl
	 */
	public function testTargetUrl_CustomInvalid()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'target'    => ['does-not-exist'],
				'usetarget' => 'custom'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertNull($block->targetUrl());
	}

	/**
	 * @covers ::targetUrl
	 * @covers ::option
	 */
	public function testTargetUrl_Default1()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'target'    => ['custom-target'],
				'usetarget' => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('https://example.com/default-target', $block->targetUrl());
	}

	/**
	 * @covers ::targetUrl
	 * @covers ::option
	 */
	public function testTargetUrl_Default2()
	{
		$app = $this->app->clone([
			'options' => [
				'lukasbestle.roomle' => [
					'target' => 'not-a-page'
				]
			]
		]);

		$block = new ConfiguratorBlock([
			'content' => [
				'target'    => ['custom-target'],
				'usetarget' => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('https://example.com/not-a-page', $block->targetUrl());
	}

	/**
	 * @covers ::targetUrl
	 * @covers ::option
	 */
	public function testTargetUrl_Default3()
	{
		$app = $this->app->clone([
			'options' => [
				'lukasbestle.roomle' => [
					'target' => 'https://external.com/target'
				]
			]
		]);

		$block = new ConfiguratorBlock([
			'content' => [
				'target'    => ['custom-target'],
				'usetarget' => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertSame('https://external.com/target', $block->targetUrl());
	}

	/**
	 * @covers ::targetUrl
	 * @covers ::option
	 */
	public function testTargetUrl_Default4()
	{
		$app = $this->app->clone([
			'options' => [
				'lukasbestle.roomle' => [
					'target' => null
				]
			]
		]);

		$block = new ConfiguratorBlock([
			'content' => [
				'target'    => ['custom-target'],
				'usetarget' => 'default'
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$this->assertNull($block->targetUrl());
	}

	/**
	 * @covers ::Variants
	 */
	public function testVariants()
	{
		$block = new ConfiguratorBlock([
			'content' => [
				'variants' => [
					[
						'productid' => 'some:variant',
						'title'     => 'Some variant',
						'subtitle'  => 'for cool people',
						'image'     => ['image.png']
					],
					[
						'productid' => 'another:variant',
						'title'     => 'Another variant',
						'subtitle'  => 'for even cooler people',
						'image'     => ['image.png']
					]
				]
			],
			'id'       => '12345678-90ab-cdef-1234-567890abcdef',
			'isHidden' => false,
			'parent'   => $this->app->page('test'),
			'type'     => 'roomle-configurator'
		]);

		$variants = $block->variants();

		$this->assertSame(2, $variants->count());
		$this->assertInstanceOf(Page::class, $variants->parent());
		$this->assertSame($this->app->page('test'), $variants->parent());

		$firstVariant = $variants->first();

		$this->assertInstanceOf(ConfiguratorVariant::class, $firstVariant);
		$this->assertSame('some:variant', $firstVariant->productId()->value());
		$this->assertInstanceOf(Page::class, $firstVariant->parent());
		$this->assertSame($this->app->page('test'), $firstVariant->parent());
		$this->assertInstanceOf(File::class, $firstVariant->image());
		$this->assertSame($this->app->file('test/image.png'), $firstVariant->image());
	}
}
