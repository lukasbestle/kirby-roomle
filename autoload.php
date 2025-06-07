<?php

use Kirby\Filesystem\F;

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
