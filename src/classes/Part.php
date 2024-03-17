<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Toolkit\Obj;

/**
 * Part
 * A single part/component of a configuration
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Part extends Obj
{
	/**
	 * Article number of the part
	 */
	public string $articleNr;

	/**
	 * ID of the component
	 */
	public string $componentId;

	/**
	 * Number of units of this part
	 * in the configuration
	 */
	public int $count;

	/**
	 * Currency symbol for `price` and `retailerPrice`
	 */
	public string|null $currencySymbol;

	/**
	 * Human-readable part name
	 */
	public string $label;

	/**
	 * How many parts are contained
	 * in one item of the article number
	 */
	public int $packageSize;

	/**
	 * Configured parameters of this part
	 *
	 * @var array
	 */
	public array $parameters;

	/**
	 * Price of the part
	 */
	public float|null $price;

	/**
	 * Retailer price of the part
	 */
	public float|null $retailerPrice;

	/**
	 * Returns a plain text representation of the part
	 * e.g. for use in contact forms
	 */
	public function __toString(): string
	{
		// TODO: Remove suppression comment when support for Kirby 3.7.x is dropped
		/** @psalm-suppress InternalMethod */
		return App::instance()->snippet('roomle/part', ['part' => $this], true);
	}

	/**
	 * Returns the configured parameters
	 * of this part as an object structure
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException if a parameter in the raw data is not an array
	 */
	public function parameters(): Parameters
	{
		$parameters = [];
		foreach ($this->parameters as $num => $parameter) {
			if (is_array($parameter) !== true) {
				throw new InvalidArgumentException('Invalid parameter ' . $num);
			}

			// skip dummy parameters
			if (is_string($parameter['value'] ?? null) !== true) {
				continue;
			}

			$parameter = new Parameter($parameter);

			// double-check uninitialized property before access (normally should not happen);
			// the Psalm error is suppressed because Psalm assumes all props to be
			// initialized in the `Parameter` constructor (which `Obj` does not ensure)
			/** @psalm-suppress TypeDoesNotContainType */
			if (isset($parameter->key) !== true) {
				throw new InvalidArgumentException('Parameter ' . $num . ' does not have a key');
			}

			$parameters[$parameter->key] = $parameter;
		}

		return new Parameters($parameters);
	}

	/**
	 * Returns a size object for the part
	 */
	public function size(): Size
	{
		$parameters = $this->parameters();

		$data = [];
		foreach (['depth', 'height', 'width'] as $parameter) {
			$value = $parameters->get($parameter)?->value();

			if (is_float($value) !== true) {
				throw new InvalidArgumentException('Invalid ' . $parameter . ' value');
			}

			$data[$parameter] = (int)$value;
		}

		return new Size($data);
	}
}
