<?php

use Kirby\Cms\App;
use Kirby\Exception\Exception;
use Kirby\Filesystem\F;
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
		version_compare($kirbyVersion, '5.0.0-alpha', '>=') === true
	)
) {
	throw new Exception(
		'The installed version of the Kirby Roomle plugin ' .
		'is not compatible with Kirby ' . $kirbyVersion
	);
}

// autoload classes
F::loadClasses([
	'LukasBestle\Roomle\Configuration'       => __DIR__ . '/src/classes/Configuration.php',
	'LukasBestle\Roomle\ConfiguratorBlock'   => __DIR__ . '/src/classes/ConfiguratorBlock.php',
	'LukasBestle\Roomle\ConfiguratorVariant' => __DIR__ . '/src/classes/ConfiguratorVariant.php',
	'LukasBestle\Roomle\Parameter'           => __DIR__ . '/src/classes/Parameter.php',
	'LukasBestle\Roomle\Parameters'          => __DIR__ . '/src/classes/Parameters.php',
	'LukasBestle\Roomle\Part'                => __DIR__ . '/src/classes/Part.php',
	'LukasBestle\Roomle\Plan'                => __DIR__ . '/src/classes/Plan.php',
	'LukasBestle\Roomle\Size'                => __DIR__ . '/src/classes/Size.php',
]);

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
