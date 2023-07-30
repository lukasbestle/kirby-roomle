<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Data\Json;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Image\Image;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Str;

/**
 * Plan
 * Parent object for a room configuration
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Plan extends Obj
{
	/**
	 * Deeplink to the configurator where
	 * this configuration was created
	 */
	public string $configuratorUrl;

	/**
	 * ID of the configuration
	 */
	public string|null $id;

	/**
	 * List of individual items of the configuration
	 */
	public array $items;

	/**
	 * Image URL of a perspective view of the configuration
	 */
	public string|null $thumbnail;

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
		// use the configuration snippet for product configurations
		if ($this->hasId() === false) {
			return (string)$this->items()->first();
		}

		// TODO: Remove suppression comment when support for Kirby 3.7.x is dropped
		/** @psalm-suppress InternalMethod */
		return App::instance()->snippet('roomle/plan', ['plan' => $this], true);
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
	 * Returns the list of items with duplicates
	 * merged to a single item including the count
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException if a part in the raw data is not an array
	 */
	public function groupedItems(): Collection
	{
		$groupedItems = [];
		foreach ($this->items() as $item) {
			if (isset($groupedItems[$item->id]) === true) {
				$groupedItems[$item->id]->count++;
			} else {
				$groupedItems[$item->id] = $item;
				$groupedItems[$item->id]->count = 1;
			}
		}

		return new Collection($groupedItems);
	}

	/**
	 * Checks if the configuration has a plan ID
	 */
	public function hasId(): bool
	{
		return $this->id !== null;
	}

	/**
	 * Returns the list of individual items
	 * of the configuration as an object structure
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException if a part in the raw data is not an array
	 */
	public function items(): Collection
	{
		$items = [];
		foreach ($this->items as $num => $item) {
			if (is_array($item) !== true) {
				throw new InvalidArgumentException('Invalid item ' . $num);
			}

			$items[] = new Configuration($item);
		}

		return new Collection($items);
	}

	/**
	 * Returns a generic label for a plan
	 */
	public function label(): string
	{
		/** @var string $label */
		$label = I18n::translate('roomle.plan');

		return $label;
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
	 * Returns the image of a perspective view of the configuration
	 */
	public function thumbnail(): Image|null
	{
		if ($this->thumbnail === null) {
			return null;
		}

		return new Image(['url' => $this->thumbnail]);
	}
}
