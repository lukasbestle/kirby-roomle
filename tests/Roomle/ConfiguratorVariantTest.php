<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Image\Image;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass LukasBestle\Roomle\ConfiguratorVariant
 */
class ConfiguratorVariantTest extends TestCase
{
	protected $app;

	public function setUp(): void
	{
		$this->app = new App([
			'site' => [
				'children' => [
					[
						'slug'  => 'test',
						'files' => [
							['filename' => 'image.png']
						]
					]
				]
			]
		]);
	}

	/**
	 * @coversNothing
	 */
	public function testConstruct()
	{
		$variant = new ConfiguratorVariant([
			'content' => [
				'productid' => $productId = 'some:variant',
				'title'     => $title = 'Some variant',
				'subtitle'  => $subtitle = 'for cool people',
				'image'     => $image = ['image.png']
			],
			'id'     => 0,
			'parent' => $this->app->page('test')
		]);

		$this->assertSame($productId, $variant->content()->productId()->value());
		$this->assertSame($title, $variant->content()->title()->value());
		$this->assertSame($subtitle, $variant->content()->subtitle()->value());
		$this->assertSame($image, $variant->content()->image()->value());

		$this->assertSame($productId, $variant->productId()->value());
		$this->assertSame($title, $variant->title()->value());
		$this->assertSame($subtitle, $variant->subtitle()->value());
	}

	/**
	 * @covers ::image
	 */
	public function testImage_Configuration()
	{
		$variant = new ConfiguratorVariant([
			'content' => [
				'productid' => 'some:variant:hash',
				'title'     => 'Some variant',
				'subtitle'  => 'for cool people',
				'image'     => []
			],
			'id'     => 0,
			'parent' => $this->app->page('test')
		]);

		$image = $variant->image();

		$this->assertInstanceOf(Image::class, $image);
		$this->assertSame('https://uploads.roomle.com/configurations/some:variant:hash/perspectiveImage.png', $image->url());
	}

	/**
	 * @covers ::image
	 */
	public function testImage_File()
	{
		$variant = new ConfiguratorVariant([
			'content' => [
				'productid' => 'some:variant',
				'title'     => 'Some variant',
				'subtitle'  => 'for cool people',
				'image'     => ['image.png']
			],
			'id'     => 0,
			'parent' => $this->app->page('test')
		]);

		$image = $variant->image();

		$this->assertInstanceOf(File::class, $image);
		$this->assertSame($this->app->file('test/image.png'), $image);
	}

	/**
	 * @covers ::image
	 */
	public function testImage_FileInvalid()
	{
		$variant = new ConfiguratorVariant([
			'content' => [
				'productid' => 'some:variant',
				'title'     => 'Some variant',
				'subtitle'  => 'for cool people',
				'image'     => ['does-not-exist.png']
			],
			'id'     => 0,
			'parent' => $this->app->page('test')
		]);

		$image = $variant->image();

		$this->assertInstanceOf(Image::class, $image);
		$this->assertSame('https://www.roomle.com/api/v2/items/some:variant/perspectiveImageHD', $image->url());
	}

	/**
	 * @covers ::image
	 */
	public function testImage_Item()
	{
		$variant = new ConfiguratorVariant([
			'content' => [
				'productid' => 'some:variant',
				'title'     => 'Some variant',
				'subtitle'  => 'for cool people',
				'image'     => []
			],
			'id'     => 0,
			'parent' => $this->app->page('test')
		]);

		$image = $variant->image();

		$this->assertInstanceOf(Image::class, $image);
		$this->assertSame('https://www.roomle.com/api/v2/items/some:variant/perspectiveImageHD', $image->url());
	}
}
