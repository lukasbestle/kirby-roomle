<?php

use Kirby\Cms\App;
use Kirby\Exception\Exception;
use LukasBestle\Roomle\Configuration;
use LukasBestle\Roomle\Plan;

/**
 * Kirby Roomle Plugin
 * Block to embed the [Roomle 3D Configurator](https://www.roomle.com/en/configurator)
 * into your Kirby site
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */

// validate the Kirby version; the supported versions are
// updated manually when verified to work with the plugin
$kirbyVersion = App::version();
if (
	$kirbyVersion !== null &&
	(
		version_compare($kirbyVersion, '3.7.0-rc.1', '<') === true ||
		version_compare($kirbyVersion, '6.0.0-alpha', '>=') === true
	)
) {
	throw new Exception(
		'The installed version of the Kirby Roomle plugin ' .
		'is not compatible with Kirby ' . $kirbyVersion
	);
}

// autoload classes
require_once __DIR__ . '/autoload.php';

// register the plugin
App::plugin('lukasbestle/roomle', [
	'blockModels'  => require __DIR__ . '/src/config/blockModels.php',
	'blueprints'   => require __DIR__ . '/src/config/blueprints.php',
	'options'      => require __DIR__ . '/src/config/options.php',
	'snippets'     => require __DIR__ . '/src/config/snippets.php',
	'translations' => require __DIR__ . '/src/config/translations.php',
]);

/**
 * Returns the object for a product configuration
 *
 * @param array|string|null $data `null` to get the data from the request (`roomle-configuration` param)
 */
function roomleConfiguration(array|string|null $data = null): Configuration|null
{
	return Configuration::lazyInstance($data);
}

/**
 * Returns the object for a room configuration
 *
 * @param array|string|null $data `null` to get the data from the request (`roomle-configuration` param)
 */
function roomlePlan(array|string|null $data = null): Plan|null
{
	return Plan::lazyInstance($data);
}
