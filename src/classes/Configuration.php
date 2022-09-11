<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Data\Json;
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
	 * Constructor
	 *
	 * @param array|string|null $data `null` to get the data from the request (`roomle-configuration` param)
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException if no configuration data was passed as argument or in the request
	 * @throws \Kirby\Exception\InvalidArgumentException if the JSON data from the request could not be parsed
	 */
	public function __construct(array|string|null $data = null)
	{
		if ($data === null) {
			$data = App::instance()->request()->get('roomle-configuration');

			if (!$data) {
				throw new InvalidArgumentException('No configuration data passed or available in request');
			}
		}

		if (is_string($data) === true) {
			$data = Json::decode($data);
		}

		parent::__construct($data);
	}

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
		try {
			return new self($data);
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

			$part = new Part($part);

			// double-check uninitialized property before access (normally should not happen);
			// the Psalm error is suppressed because Psalm assumes all props to be
			// initialized in the `Part` constructor (which `Obj` does not ensure)
			/** @psalm-suppress TypeDoesNotContainType */
			if (isset($part->componentId) !== true) {
				throw new InvalidArgumentException('Part ' . $num . ' does not have a component ID');
			}

			$parts[$part->componentId] = $part;
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
