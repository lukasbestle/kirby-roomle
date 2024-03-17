<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\Configuration
 */
class ConfigurationTest extends TestCase
{
	public function setUp(): void
	{
		new App([
			'request' => [
				'body' => [
					'roomle-configuration' => '{"id": null, "items": [{"depth": 123}]}'
				]
			],
			'urls' => [
				'index' => 'https://example.com'
			]
		]);

		locale_set_default('en_US');
	}

	/**
	 * @covers ::configuratorUrl
	 */
	public function testConfiguratorUrl()
	{
		$configuration = new Configuration([
			'configuratorUrl' => $url = 'https://example.com/configurator?roomle-id=some:product#roomle-id'
		]);

		$this->assertSame($url, $configuration->configuratorUrl());
	}

	/**
	 * @covers ::configuratorUrl
	 */
	public function testConfiguratorUrl_Invalid()
	{
		$configuration = new Configuration([
			'configuratorUrl' => 'https://malicious.com/configurator?roomle-id=some:product#roomle-id'
		]);

		$this->assertNull($configuration->configuratorUrl());
	}

	/**
	 * @covers ::lazyInstance
	 */
	public function testConstruct_Array()
	{
		$configuration = new Configuration([
			'depth' => 1337
		]);
		$this->assertSame(1337, $configuration->depth());

		$configuration = Configuration::lazyInstance([
			'depth' => 1337
		]);
		$this->assertSame(1337, $configuration->depth());
	}

	/**
	 * @covers ::lazyInstance
	 */
	public function testConstruct_Request()
	{
		$configuration = Configuration::lazyInstance();
		$this->assertSame(123, $configuration->depth());
	}

	/**
	 * @covers ::lazyInstance
	 */
	public function testConstruct_RequestInvalid()
	{
		// CMS instance without request data
		new App();

		$this->assertNull(Configuration::lazyInstance());
	}

	/**
	 * @covers ::lazyInstance
	 */
	public function testConstruct_String()
	{
		$configuration = Configuration::lazyInstance('{"items": [{"depth": 1337}]}');
		$this->assertSame(1337, $configuration->depth());
	}

	/**
	 * @covers ::lazyInstance
	 */
	public function testConstruct_StringInvalid()
	{
		$this->assertNull(Configuration::lazyInstance('Definitely not JSON'));
	}

	/**
	 * @covers ::parts
	 */
	public function testParts()
	{
		$configuration = new Configuration([
			'parts' => [
				[
					'articleNr'   => '12345',
					'componentId' => 'some:component1',
					'label'       => 'Some component 1'
				],
				[
					'articleNr'   => '12345',
					'componentId' => 'some:component1',
					'label'       => 'Some component 1 with different properties'
				],
				[
					'articleNr'   => '23456',
					'componentId' => 'some:component2',
					'label'       => 'Some component 2'
				],
				[
					'componentId' => 'some:component3',
					'label'       => 'Some component 3 without article number'
				]
			]
		]);

		$parts = $configuration->parts();

		$this->assertSame(3, $parts->count());
		$this->assertSame(['some:component1', 'some:component1', 'some:component2'], $parts->pluck('componentId'));
		$this->assertInstanceOf(Part::class, $parts->first());
		$this->assertSame('Some component 1', $parts->first()->label());
	}

	/**
	 * @covers ::parts
	 */
	public function testParts_Invalid()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Invalid part 0');

		$configuration = new Configuration([
			'parts' => [
				'not an array'
			]
		]);

		$configuration->parts();
	}

	/**
	 * @covers ::perspectiveImage
	 */
	public function testPerspectiveImage()
	{
		$configuration = new Configuration([
			'perspectiveImage' => $url = 'https://example.com/path/to/image.jpg'
		]);

		$image = $configuration->perspectiveImage();
		$this->assertNull($image->root());
		$this->assertSame($url, $image->url());
		$this->assertSame('<img alt="" src="' . $url . '">', $image->html());
	}

	/**
	 * @covers ::size
	 */
	public function testSize()
	{
		$configuration = new Configuration([
			'depth'  => 12345,
			'height' => 34567,
			'width'  => 56789
		]);

		$this->assertSame('W 5,678.9 cm / H 3,456.7 cm / D 1,234.5 cm', (string)$configuration->size());
		$this->assertSame('5,678.9 cm', $configuration->size()->widthLabel());
	}

	/**
	 * @covers ::topImage
	 */
	public function testTopImage()
	{
		$configuration = new Configuration([
			'topImage' => $url = 'https://example.com/path/to/image.jpg'
		]);

		$image = $configuration->topImage();
		$this->assertNull($image->root());
		$this->assertSame($url, $image->url());
		$this->assertSame('<img alt="" src="' . $url . '">', $image->html());
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString()
	{
		$configuration = new Configuration([
			'configuratorUrl' => 'https://example.com/configurator',
			'depth'           => 12,
			'height'          => 34,
			'id'              => 'some:id',
			'label'           => 'Some product',
			'width'           => 56,
			'parts'           => [
				[
					'articleNr'   => '123.456.789',
					'componentId' => 'some:component1',
					'count'       => 2,
					'label'       => 'Some part',
					'parameters'  => [
						[
							'key'        => 'height',
							'label'      => 'Height',
							'type'       => 'Decimal',
							'unitType'   => 'length',
							'value'      => '123.0'
						],
						[
							'key'        => 'width',
							'label'      => 'Width',
							'type'       => 'Decimal',
							'unitType'   => 'length',
							'value'      => '456.0'
						]
					]
				],
				[
					'articleNr'   => '987.654.321',
					'componentId' => 'some:component2',
					'count'       => 1,
					'label'       => 'Some other part',
					'parameters'  => [
						[
							'key'        => 'height',
							'label'      => 'Height',
							'type'       => 'Decimal',
							'unitType'   => 'length',
							'value'      => '789.0'
						]
					]
				]
			],
		]);

		$this->assertStringEqualsFile(__DIR__ . '/fixtures/configuration.txt', (string)$configuration);
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString_WithCount()
	{
		$configuration = new Configuration([
			'configuratorUrl' => 'https://example.com/configurator',
			'count'           => 3,
			'depth'           => 12,
			'height'          => 34,
			'id'              => 'some:id',
			'label'           => 'Some product',
			'width'           => 56,
			'parts'           => [
				[
					'articleNr'   => '123.456.789',
					'componentId' => 'some:component1',
					'count'       => 2,
					'label'       => 'Some part',
					'parameters'  => [
						[
							'key'        => 'height',
							'label'      => 'Height',
							'type'       => 'Decimal',
							'unitType'   => 'length',
							'value'      => '123.0'
						],
						[
							'key'        => 'width',
							'label'      => 'Width',
							'type'       => 'Decimal',
							'unitType'   => 'length',
							'value'      => '456.0'
						]
					]
				],
				[
					'articleNr'   => '987.654.321',
					'componentId' => 'some:component2',
					'count'       => 1,
					'label'       => 'Some other part',
					'parameters'  => [
						[
							'key'        => 'height',
							'label'      => 'Height',
							'type'       => 'Decimal',
							'unitType'   => 'length',
							'value'      => '789.0'
						]
					]
				]
			],
		]);

		$this->assertStringEqualsFile(__DIR__ . '/fixtures/configuration_count.txt', (string)$configuration);
	}
}
