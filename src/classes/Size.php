<?php

namespace LukasBestle\Roomle;

use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Obj;
use NumberFormatter;

/**
 * Size
 * Width, height and depth of a configuration or part
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Size extends Obj
{
	/**
	 * Depth in mm
	 */
	public int $depth;

	/**
	 * Height in mm
	 */
	public int $height;

	/**
	 * Width in mm
	 */
	public int $width;

	/**
	 * Returns a human-readable string of the size
	 */
	public function __toString(): string
	{
		return I18n::template('roomle.size', null, [
			'depth'  => $this->depthLabel(),
			'height' => $this->heightLabel(),
			'width'  => $this->widthLabel(),
		]);
	}

	/**
	 * Returns the depth of the configured product
	 * as a human-readable string in cm
	 */
	public function depthLabel(): string
	{
		return static::formatLength($this->depth);
	}

	/**
	 * Formats a length in millimeters as a
	 * human-readable string in cm
	 * @internal
	 */
	public static function formatLength(int $millimeters): string
	{
		$centimeters = $millimeters / 10;

		if (class_exists(NumberFormatter::class) === true) {
			$formatter = new NumberFormatter(locale_get_default(), NumberFormatter::DECIMAL);
			return $formatter->format($centimeters) . ' cm';
		}

		return $centimeters . ' cm'; // @codeCoverageIgnore
	}

	/**
	 * Returns the height of the configured product
	 * as a human-readable string in cm
	 */
	public function heightLabel(): string
	{
		return static::formatLength($this->height);
	}

	/**
	 * Returns the width of the configured product
	 * as a human-readable string in cm
	 */
	public function widthLabel(): string
	{
		return static::formatLength($this->width);
	}
}
