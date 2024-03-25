<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\Plan
 */
class PlanTest extends TestCase
{
	public function setUp(): void
	{
		new App([
			'request' => [
				'body' => [
					'roomle-configuration' => '{"id": "some-plan-id"}'
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
		$plan = new Plan([
			'configuratorUrl' => $url = 'https://example.com/configurator?roomle-id=some:product#roomle-id'
		]);

		$this->assertSame($url, $plan->configuratorUrl());
	}

	/**
	 * @covers ::configuratorUrl
	 */
	public function testConfiguratorUrl_Invalid()
	{
		$plan = new Plan([
			'configuratorUrl' => 'https://malicious.com/configurator?roomle-id=some:product#roomle-id'
		]);

		$this->assertNull($plan->configuratorUrl());
	}

	/**
	 * @covers ::__construct
	 * @covers ::lazyInstance
	 */
	public function testConstruct_Array()
	{
		$plan = new Plan([
			'id' => 'some-custom-id'
		]);
		$this->assertSame('some-custom-id', $plan->id());

		$plan = Plan::lazyInstance([
			'id' => 'some-custom-id'
		]);
		$this->assertSame('some-custom-id', $plan->id());
	}

	/**
	 * @covers ::__construct
	 * @covers ::lazyInstance
	 */
	public function testConstruct_Request()
	{
		$plan = new Plan();
		$this->assertSame('some-plan-id', $plan->id());

		$plan = Plan::lazyInstance();
		$this->assertSame('some-plan-id', $plan->id());
	}

	/**
	 * @covers ::__construct
	 * @covers ::lazyInstance
	 */
	public function testConstruct_RequestInvalid()
	{
		// CMS instance without request data
		new App();

		$this->assertNull(Plan::lazyInstance());

		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('No configuration data passed or available in request');

		new Plan();
	}

	/**
	 * @covers ::__construct
	 * @covers ::lazyInstance
	 */
	public function testConstruct_String()
	{
		$plan = new Plan('{"id": "some-id"}');
		$this->assertSame('some-id', $plan->id());

		$plan = Plan::lazyInstance('{"id": "some-id"}');
		$this->assertSame('some-id', $plan->id());
	}

	/**
	 * @covers ::__construct
	 * @covers ::lazyInstance
	 */
	public function testConstruct_StringInvalid()
	{
		$this->assertNull(Plan::lazyInstance('Definitely not JSON'));

		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('JSON string is invalid');

		new Plan('Definitely not JSON');
	}

	/**
	 * @covers ::groupedItems
	 */
	public function testGroupedItems()
	{
		$plan = new Plan([
			'items' => [
				[
					'id'    => 'some:item1',
					'depth' => 123
				],
				[
					'id'    => 'some:item2',
					'depth' => 234
				],
				[
					'id'    => 'some:item1',
					'depth' => 123
				],
				[
					'id'    => null,
					'label' => 'Radiator'
				]
			]
		]);

		$items = $plan->groupedItems();

		$this->assertSame(2, $items->count());
		$this->assertSame(['some:item1', 'some:item2'], $items->pluck('id'));
		$this->assertInstanceOf(Configuration::class, $items->first());
		$this->assertSame(123, $items->first()->depth());
	}

	/**
	 * @covers ::groupedItems
	 */
	public function testGroupedItems_Invalid()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Invalid item 0');

		$plan = new Plan([
			'items' => [
				'not an array'
			]
		]);

		$plan->groupedItems();
	}

	/**
	 * @covers ::hasId
	 */
	public function testHasId_Missing()
	{
		$plan = new Plan(['id' => null]);
		$this->assertFalse($plan->hasId());
	}

	/**
	 * @covers ::hasId
	 */
	public function testHasId_Set()
	{
		$plan = new Plan(['id' => 'some-id']);
		$this->assertTrue($plan->hasId());
	}

	/**
	 * @covers ::items
	 */
	public function testItems()
	{
		$plan = new Plan([
			'items' => [
				[
					'id'    => 'some:item1',
					'depth' => 123
				],
				[
					'id'    => 'some:item2',
					'depth' => 234
				],
				[
					'id'    => 'some:item1',
					'depth' => 123
				],
				[
					'id'    => null,
					'label' => 'Radiator'
				]
			]
		]);

		$items = $plan->items();

		$this->assertSame(3, $items->count());
		$this->assertSame(['some:item1', 'some:item2', 'some:item1'], $items->pluck('id'));
		$this->assertInstanceOf(Configuration::class, $items->first());
		$this->assertSame(123, $items->first()->depth());
	}

	/**
	 * @covers ::items
	 */
	public function testItems_Invalid()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Invalid item 0');

		$plan = new Plan([
			'items' => [
				'not an array'
			]
		]);

		$plan->items();
	}

	/**
	 * @covers ::label
	 */
	public function testLabel()
	{
		$plan = new Plan();

		$this->assertSame('Plan', $plan->label());
	}

	/**
	 * @covers ::thumbnail
	 */
	public function testThumbnail_Missing()
	{
		$plan = new Plan(['thumbnail' => null]);

		$this->assertNull($plan->thumbnail());
	}

	/**
	 * @covers ::thumbnail
	 */
	public function testThumbnail_Set()
	{
		$plan = new Plan([
			'thumbnail' => $url = 'https://example.com/path/to/image.jpg'
		]);

		$image = $plan->thumbnail();
		$this->assertNull($image->root());
		$this->assertSame($url, $image->url());
		$this->assertSame('<img alt="" src="' . $url . '">', $image->html());
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString_Configuration()
	{
		$plan = new Plan([
			'configuratorUrl' => 'https://example.com/configurator',
			'id'              => null,
			'items'           => [
				[
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
					]
				]
			]
		]);

		$this->assertStringEqualsFile(__DIR__ . '/fixtures/configuration.txt', (string)$plan);
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString_Plan()
	{
		$plan = new Plan([
			'configuratorUrl' => 'https://example.com/configurator/plan',
			'id'              => 'abcdefghi',
			'items'           => [
				[
					'configuratorUrl' => 'https://example.com/configurator/item1',
					'depth'           => 12,
					'height'          => 34,
					'id'              => 'some:id1',
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
					]
				],
				[
					'configuratorUrl' => 'https://example.com/configurator/item2',
					'depth'           => 12,
					'height'          => 34,
					'id'              => 'some:id2',
					'label'           => 'Some other product',
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
					]
				],
				[
					'configuratorUrl' => 'https://example.com/configurator/item1',
					'depth'           => 12,
					'height'          => 34,
					'id'              => 'some:id1',
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
					]
				],
			]
		]);

		$this->assertStringEqualsFile(__DIR__ . '/fixtures/plan.txt', (string)$plan);
	}
}
