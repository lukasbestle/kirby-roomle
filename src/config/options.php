<?php

return [
	// Roomle configurator ID (from your Roomle Contact Person);
	// you can test the plugin with the ID `demoConfigurator`;
	// REQUIRED (either globally or in the block settings)
	'configuratorId' => null,

	// page to redirect to when "request product" is clicked;
	// defaults to none (request product feature is disabled)
	'target' => null,

	// custom options for the Roomle configurator;
	// see https://docs.roomle.com/web/embedding/api/interfaces/types.UiInitData.html
	'options' => [],
];
