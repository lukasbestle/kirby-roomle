<?php

namespace LukasBestle\Roomle;

use Kirby\Cms\Block;
use Kirby\Cms\Page;
use Kirby\Cms\Structure;
use Kirby\Cms\Url;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Toolkit\Str;

/**
 * ConfiguratorBlock
 * Model class for the `roomle-configurator` block
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */
class ConfiguratorBlock extends Block
{
	/**
	 * Returns the Roomle configurator ID
	 *
	 * @throws \Kirby\Exception\InvalidArgumentException if no configurator ID was configured
	 */
	public function configuratorId(): string
	{
		$id = match ($this->content()->useConfiguratorId()->value()) {
			'default' => $this->option('configuratorId'),
			'custom'  => $this->content()->configuratorId()->value(),
			default   => null
		};

		if (is_string($id) !== true || !$id) {
			throw new InvalidArgumentException('Missing or invalid configurator ID setting');
		}

		return $id;
	}

	/**
	 * Returns the data that is necessary for the JS logic
	 * as JSON string
	 */
	public function frontendJson(): string
	{
		return json_encode($this->frontendProps());
	}

	/**
	 * Returns the data that is necessary for the JS logic
	 */
	public function frontendProps(): array
	{
		return [
			'configuratorId' => $this->configuratorId(),
			'htmlId'         => $this->htmlId(),
			'options'        => $this->options(),
			'targetUrl'      => $this->targetUrl(),
		];
	}

	/**
	 * Returns whether variants have been configured
	 */
	public function hasVariants(): bool
	{
		return $this->variants()->isNotEmpty();
	}

	/**
	 * Returns the block ID used for matching the
	 * JS logic to the HTML code and query string
	 */
	public function htmlId(): string
	{
		return 'roomle-' . $this->id();
	}

	/**
	 * Returns the URL to the `configurator.js` file
	 */
	public function jsUrl(): string
	{
		/** @var \Kirby\Cms\Plugin $plugin */
		$plugin   = $this->kirby()->plugin('lukasbestle/roomle');
		$mediaUrl = $plugin->mediaUrl();

		return $mediaUrl . '/configurator.js';
	}

	/**
	 * Returns the Roomle item or configuration ID of the
	 * product to display by default
	 */
	public function mainProductId(): string
	{
		$id = $this->content()->mainProductId()->value();

		if (!$id) {
			throw new InvalidArgumentException('Missing main product ID');
		}

		return $id;
	}

	/**
	 * Returns the custom options for the Roomle configurator
	 */
	public function options(): array
	{
		$defaults = [];

		// locale settings from the site's language code
		$language = $this->kirby()->language();
		if ($language !== null) {
			$code = $language->code();

			if (Str::contains($code, '-') === true) {
				$defaults['locale'] = Str::before($code, '-');
				$defaults['overrideCountry'] = Str::after($code, '-');
			} else {
				$defaults['locale'] = $code;
			}
		}

		// assemble the options from the dynamic defaults with
		// overrides from the config and the block settings
		$overrides = $this->content()->options()->yaml();
		$options   = array_merge($defaults, $this->option('options'), $overrides);

		// no point displaying the "request product" button
		// if no target page was configured
		if ($this->targetUrl() === null) {
			$options['buttons']['add_to_basket']  = false;
			$options['buttons']['requestproduct'] = false;
		}

		// always set the `id` property from block data
		$options['id'] = $this->mainProductId();

		return $options;
	}

	/**
	 * Returns the page to redirect to when
	 * "request product" is clicked
	 */
	public function targetUrl(): string|null
	{
		$defaultTarget = $this->option('target');
		if ($defaultTarget !== null) {
			$defaultTarget = Url::to($defaultTarget);
		}

		return match ($this->content()->useTarget()->value()) {
			'default' => $defaultTarget,
			'custom'  => $this->content()->target()->toPage()?->url(),
			default   => null
		};
	}

	/**
	 * Returns the variants the visitor can switch between
	 */
	public function variants(): Structure
	{
		$field     = $this->content()->variants();
		$structure = new Structure([], $field->parent());

		foreach ($field->value() as $id => $props) {
			$variant = new ConfiguratorVariant([
				'content'    => $props,
				'id'         => $props['id'] ?? $id,
				'parent'     => $field->parent(),
				'structure'  => $structure
			]);

			$structure->set($variant->id(), $variant);
		}

		return $structure;
	}

	/**
	 * Returns a configured plugin option value
	 */
	protected function option(string $option): mixed
	{
		return $this->kirby()->option('lukasbestle.roomle.' . $option);
	}
}
