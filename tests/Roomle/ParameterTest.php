<?php

namespace LukasBestle\Roomle;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\Parameter
 */
class ParameterTest extends TestCase
{
	public function setUp(): void
	{
		locale_set_default('en_US');
	}

	/**
	 * @coversNothing
	 */
	public function testFields()
	{
		$parameter = new Parameter([
			'key'        => $key = 'height',
			'type'       => $type = 'Decimal',
			'unitType'   => $unitType = 'length',

			// additional fields without documented properties
			'sort'       => $sort = 0,
		]);

		$this->assertSame($key, $parameter->key());
		$this->assertSame($type, $parameter->type());
		$this->assertSame($unitType, $parameter->unitType());
		$this->assertSame($sort, $parameter->sort());
	}

	/**
	 * @covers ::label
	 */
	public function testLabel()
	{
		$parameter = new Parameter([
			'key'   => 'height',
			'label' => 'Height'
		]);

		$this->assertSame('Height', $parameter->label());
	}

	/**
	 * @covers ::label
	 */
	public function testLabel_Fallback()
	{
		$parameter = new Parameter([
			'key'   => 'height',
			'label' => null
		]);

		$this->assertSame('height', $parameter->label());
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString()
	{
		$parameter = new Parameter([
			'key'        => 'height',
			'label'      => 'Height',
			'type'       => 'Decimal',
			'unitType'   => 'length',
			'value'      => '123.0'
		]);

		$this->assertStringEqualsFile(__DIR__ . '/fixtures/parameter.txt', (string)$parameter);
	}

	/**
	 * @covers ::value
	 */
	public function testValue()
	{
		$parameter = new Parameter([
			'type'  => 'String',
			'value' => '123.0'
		]);

		$this->assertSame('123.0', $parameter->value());
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Decimal()
	{
		$parameter = new Parameter([
			'type'  => 'Decimal',
			'value' => '123.0'
		]);

		$this->assertSame(123.0, $parameter->value());
	}

	/**
	 * @covers ::valueLabel
	 */
	public function testValueLabel()
	{
		$parameter = new Parameter([
			'type'       => 'String',
			'value'      => '123.0',
			'valueLabel' => 'Some string'
		]);

		$this->assertSame('Some string', $parameter->valueLabel());
	}

	/**
	 * @covers ::valueLabel
	 */
	public function testValueLabel_Length()
	{
		$parameter = new Parameter([
			'type'       => 'Decimal',
			'unitType'   => 'length',
			'value'      => '123.0',
			'valueLabel' => 'should not be used'
		]);

		$this->assertSame('12.3 cm', $parameter->valueLabel());
	}
}
