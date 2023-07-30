<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Image\Image;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Str;

/**
 * Configuration
 * Parent object for a product configuration
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Configuration extends Obj
{
	/**
	 * Identifier of the Roomle Catalog
	 * this configuration is part of
	 */
	public string $catalog;

	/**
	 * Deeplink to the configurator where
	 * this configuration was created
	 */
	public string $configuratorUrl;

	/**
	 * Count of the same item in a plan
	 * (only set by `$plan->groupedItems()`)
	 */
	public int|null $count = null;

	/**
	 * Depth of the configured product in mm
	 */
	public int $depth;

	/**
	 * Height of the configured product in mm
	 */
	public int $height;

	/**
	 * ID of the configuration
	 */
	public string $id;

	/**
	 * Product label
	 */
	public string $label;

	/**
	 * List of individual parts of the configuration
	 */
	public array $parts;

	/**
	 * Image URL of a perspective view of the configured product
	 */
	public string $perspectiveImage;

	/**
	 * Root component ID of the configuration
	 */
	public string $rootComponentId;

	/**
	 * Image URL of a top view of the configured product
	 */
	public string $topImage;

	/**
	 * Width of the configured product in mm
	 */
	public int $width;

	/**
	 * Returns a plain text representation of the configuration
	 * e.g. for use in contact forms
	 */
	public function __toString(): string
	{
		// TODO: Remove suppression comment when support for Kirby 3.7.x is dropped
		/** @psalm-suppress InternalMethod */
		return App::instance()->snippet('roomle/configuration', ['configuration' => $this], true);
	}

	/**
	 * Returns the deeplink to the configurator
	 * where this configuration was created
	 * if the URL is valid
	 */
	public function configuratorUrl(): string|null
	{
		$homeUrl = App::instance()->url('index');

		if ($homeUrl && Str::startsWith($this->configuratorUrl, $homeUrl) === true) {
			return $this->configuratorUrl;
		}

		return null;
	}

	/**
	 * Creates an instance if data is available
	 * (either from the argument or the request)
	 *
	 * @param array|string|null $data `null` to get the data from the request (`roomle-configuration` param)
	 */
	public static function lazyInstance(array|string|null $data = null): self|null
	{
		if (is_array($data) === true) {
			return new self($data);
		}

		try {
			$plan = new Plan($data);
			return $plan->items()->first();
		} catch (InvalidArgumentException) {
			return null;
		}
	}

	/**
	 * Returns the list of individual parts
	 * of the configuration as an object structure
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException if a part in the raw data is not an array
	 */
	public function parts(): Collection
	{
		$parts = [];
		foreach ($this->parts as $num => $part) {
			if (is_array($part) !== true) {
				throw new InvalidArgumentException('Invalid part ' . $num);
			}

			$parts[] = new Part($part);
		}

		return new Collection($parts);
	}

	/**
	 * Returns the image of a perspective view of the configured product
	 */
	public function perspectiveImage(): Image
	{
		return new Image(['url' => $this->perspectiveImage]);
	}

	/**
	 * Returns a size object for the configuration
	 */
	public function size(): Size
	{
		return new Size([
			'depth'  => $this->depth,
			'height' => $this->height,
			'width'  => $this->width,
		]);
	}

	/**
	 * Returns the image of a top view of the configured product
	 */
	public function topImage(): Image
	{
		return new Image(['url' => $this->topImage]);
	}
}
