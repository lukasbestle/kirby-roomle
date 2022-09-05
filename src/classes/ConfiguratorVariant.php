<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\File;
use Kirby\Cms\StructureObject;
use Kirby\Image\Image;

/**
 * ConfiguratorVariant
 * Configured variant from the block content
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConfiguratorVariant extends StructureObject
{
	/**
	 * Returns an image object for the configured variant image
	 */
	public function image(): File|Image
	{
		$image = $this->content()->image()->toFile();
		if ($image !== null) {
			return $image;
		}

		// more than two colons = configuration, else item
		$productId = $this->content()->productId()->value();
		if (count(explode(':', $productId)) > 2) {
			$url = 'https://uploads.roomle.com/configurations/' . $productId . '/perspectiveImage.png';
		} else {
			$url = 'https://www.roomle.com/api/v2/items/' . $productId . '/perspectiveImageHD';
		}

		return new Image(compact('url'));
	}
}
