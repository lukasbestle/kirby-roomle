<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\Size
 */
class SizeTest extends TestCase
{
	public function setUp(): void
	{
		new App();

		locale_set_default('en_US');
	}

	/**
	 * @covers ::formatLength
	 */
	public function testFormatLength()
	{
		$this->assertSame('0.1 cm', Size::formatLength(1));
		$this->assertSame('1 cm', Size::formatLength(10));
		$this->assertSame('1.3 cm', Size::formatLength(13));
		$this->assertSame('123.4 cm', Size::formatLength(1234));
		$this->assertSame('1,234.5 cm', Size::formatLength(12345));
	}

	/**
	 * @covers ::depthLabel
	 * @covers ::heightLabel
	 * @covers ::widthLabel
	 */
	public function testLabels()
	{
		$size = new Size([
			'depth'  => 12345,
			'height' => 34567,
			'width'  => 56789
		]);

		$this->assertSame('1,234.5 cm', $size->depthLabel());
		$this->assertSame('3,456.7 cm', $size->heightLabel());
		$this->assertSame('5,678.9 cm', $size->widthLabel());
	}

	/**
	 * @covers ::__toString
	 */
	public function testToString()
	{
		$size = new Size([
			'depth'  => 12345,
			'height' => 34567,
			'width'  => 56789
		]);

		$this->assertSame('W 5,678.9 cm / H 3,456.7 cm / D 1,234.5 cm', (string)$size);
	}
}
