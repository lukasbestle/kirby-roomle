<?php

namespace LukasBestle\Roomle;

use Kirby\Toolkit\Collection;

/**
 * Parameters
 * List of configured parameters of a configuration part/component
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Parameters extends Collection
{
	/**
	 * Returns a key-value array with the
	 * machine-readable parameter data
	 */
	public function rawData(): array
	{
		return array_map(
			fn (Parameter $parameter): string|float => $parameter->value(),
			$this->data
		);
	}
}
