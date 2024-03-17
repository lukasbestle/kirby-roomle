<?php

namespace LukasBestle\Roomle;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\Parameters
 */
class ParametersTest extends TestCase
{
	public function setUp(): void
	{
		locale_set_default('en_US');
	}

	/**
	 * @coversNothing
	 */
	public function testConstruct()
	{
		$parameters = new Parameters([
			'height' => new Parameter([
				'key'        => 'height',
				'label'      => 'Height',
				'type'       => 'Decimal',
				'unitType'   => 'length',
				'value'      => '123.0'
			]),
			'width' => new Parameter([
				'key'        => 'width',
				'label'      => 'Width',
				'type'       => 'Decimal',
				'unitType'   => 'length',
				'value'      => '456.0'
			])
		]);

		$this->assertSame(2, $parameters->count());
		$this->assertSame(['height', 'width'], $parameters->keys());
		$this->assertInstanceOf(Parameter::class, $parameters->first());
		$this->assertSame('12.3 cm', $parameters->first()->valueLabel());
	}

	/**
	 * @covers ::rawData
	 */
	public function testRawData()
	{
		$parameters = new Parameters([
			'height' => new Parameter([
				'key'        => 'height',
				'label'      => 'Height',
				'type'       => 'Decimal',
				'unitType'   => 'length',
				'value'      => '123.0'
			]),
			'width' => new Parameter([
				'key'        => 'width',
				'label'      => 'Width',
				'type'       => 'Decimal',
				'unitType'   => 'length',
				'value'      => '456.0'
			])
		]);

		$this->assertSame([
			'height' => 123.0,
			'width'  => 456.0
		], $parameters->rawData());
	}
}
