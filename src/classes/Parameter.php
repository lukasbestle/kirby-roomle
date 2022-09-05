<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\App;
use Kirby\Toolkit\Obj;

/**
 * Parameter
 * A configured parameter of a configuration part/component
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Parameter extends Obj
{
	/**
	 * Machine-readable key that
	 * identifies this parameter
	 */
	public string $key;

	/**
	 * Human-readable label that
	 * identifies this parameter
	 */
	public string|null $label;

	/**
	 * Data type of the `value`
	 *
	 * @var string 'Decimal'|'Integral'|'Material'|'String'|'Unknown'
	 */
	public string $type;

	/**
	 * Unit type for number values
	 *
	 * @var string|null 'angle'|'area'|'count'|'length'|null
	 */
	public string|null $unitType;

	/**
	 * Machine-readable value
	 */
	public string $value;

	/**
	 * Human-readable value
	 */
	public string|null $valueLabel;

	/**
	 * Returns a plain text representation of the parameter
	 * e.g. for use in contact forms
	 */
	public function __toString(): string
	{
		// TODO: Remove suppression comment when support for Kirby 3.7.x is dropped
		/** @psalm-suppress InternalMethod */
		return App::instance()->snippet('roomle/parameter', ['parameter' => $this], true);
	}

	/**
	 * Returns the human-readable label
	 * that identifies this parameter
	 *
	 * @return string
	 */
	public function label(): string
	{
		return $this->label ?? $this->key;
	}

	/**
	 * Returns the machine-readable value
	 * depending on the `type`
	 */
	public function value(): string|float
	{
		if ($this->type === 'Decimal') {
			return (float)$this->value;
		}

		return $this->value;
	}

	/**
	 * Returns the human-readable value
	 */
	public function valueLabel(): string
	{
		if ($this->type === 'Decimal' && $this->unitType === 'length') {
			/** @var float $value */
			$value = $this->value();

			return Configuration::formatLength((int)$value);
		}

		return $this->valueLabel ?? $this->value;
	}
}
