<?php

namespace LukasBestle\Roomle;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\Part
 */
class PartTest extends TestCase
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
		$part = new Part([
			'articleNr'      => $articleNr = '123.456.789',
			'componentId'    => $componentId = 'some:component',
			'count'          => $count = 2,
			'currencySymbol' => $currencySymbol = '€',
			'label'          => $label = 'Some component',
			'packageSize'    => $packageSize = 1,
			'price'          => $price = 123.45,
			'retailerPrice'  => $retailerPrice = 234.56,

			// additional fields without documented properties
			'subpartId'      => $subpartId = 12,
			'hasGeometry'    => $hasGeometry = false,
		]);

		$this->assertSame($articleNr, $part->articleNr());
		$this->assertSame($componentId, $part->componentId());
		$this->assertSame($count, $part->count());
		$this->assertSame($currencySymbol, $part->currencySymbol());
		$this->assertSame($label, $part->label());
		$this->assertSame($packageSize, $part->packageSize());
		$this->assertSame($price, $part->price());
		$this->assertSame($retailerPrice, $part->retailerPrice());
		$this->assertSame($subpartId, $part->subpartId());
		$this->assertSame($hasGeometry, $part->hasGeometry());
	}

	/**
	 * @covers ::parameters
	 */
	public function testParameters()
	{
		$part = new Part([
			'parameters' => [
				[
					'key'      => 'height',
					'label'    => 'Height',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '123.0'
				],
				[
					'key'      => 'width',
					'label'    => 'Width',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '456.0'
				],
				[
					'key'      => 'internal',
					'label'    => 'Internal parameter without value'
				]
			]
		]);

		$parameters = $part->parameters();

		$this->assertSame(2, $parameters->count());
		$this->assertSame(['height', 'width'], $parameters->keys());
		$this->assertInstanceOf(Parameter::class, $parameters->first());
		$this->assertSame('12.3 cm', $parameters->first()->valueLabel());
	}

	/**
	 * @covers ::parameters
	 */
	public function testParameters_Invalid1()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Invalid parameter 0');

		$part = new Part([
			'parameters' => [
				'not an array'
			]
		]);

		$part->parameters();
	}

	/**
	 * @covers ::parameters
	 */
	public function testParameters_Invalid2()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Parameter 0 does not have a key');

		$part = new Part([
			'parameters' => [
				[
					'label' => 'Some parameter without key',
					'value' => '123'
				]
			]
		]);

		$part->parameters();
	}

	/**
	 * @covers ::size
	 */
	public function testSize()
	{
		$configuration = new Part([
			'parameters' => [
				[
					'key'      => 'depth',
					'label'    => 'Depth',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '123.0'
				],
				[
					'key'      => 'height',
					'label'    => 'Height',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '456.0'
				],
				[
					'key'      => 'width',
					'label'    => 'Width',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '789.0'
				]
			]
		]);

		$this->assertSame('W 78.9 cm / H 45.6 cm / D 12.3 cm', (string)$configuration->size());
		$this->assertSame('78.9 cm', $configuration->size()->widthLabel());
	}

	/**
	 * @covers ::size
	 */
	public function testSize_Invalid()
	{
		$this->expectException('Kirby\Exception\InvalidArgumentException');
		$this->expectExceptionMessage('Invalid width value');

		$part = new Part([
			'parameters' => [
				[
					'key'      => 'depth',
					'label'    => 'Depth',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '123.0'
				],
				[
					'key'      => 'height',
					'label'    => 'Height',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '456.0'
				]
			]
		]);

		$part->size();
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString()
	{
		$part = new Part([
			'articleNr'   => '123.456.789',
			'componentId' => 'some:component1',
			'count'       => 2,
			'label'       => 'Some part',
			'parameters'  => [
				[
					'key'      => 'height',
					'label'    => 'Height',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '123.0'
				],
				[
					'key'      => 'width',
					'label'    => 'Width',
					'type'     => 'Decimal',
					'unitType' => 'length',
					'value'    => '456.0'
				]
			]
		]);

		$this->assertStringEqualsFile(__DIR__ . '/fixtures/part.txt', (string)$part);
	}
}
