<?php

// TODO: Remove stub when support for Kirby 3.7.x is dropped

namespace Kirby\Cms;

class App
{
	/**
	 * @psalm-return ($lazy is false ? static : static|null)
	 */
	public static function instance(self $instance = null, bool $lazy = false)
	{
	}

	/**
	 * @psalm-return ($return is true ? string : null)
	 */
	public function snippet($name, $data = [], bool $return = true): string|null
	{
	}
}
