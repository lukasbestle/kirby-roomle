<?php

use Kirby\Toolkit\Escape;

/** @var LukasBestle\Roomle\ConfiguratorBlock $block */
?>
<figure id="<?= Escape::attr($block->htmlId()) ?>" class="roomle-configurator">
	<?php if ($block->hasVariants() === true): ?>
	<nav>
		<ul role="listbox" class="roomle-configurator-variants">
			<?php foreach ($block->variants() as $id => $variant): ?>
			<li role="presentation">
				<input
					id="<?= Escape::attr($block->htmlId() . '-variant-' . $id) ?>"
					name="<?= Escape::attr($block->htmlId() . '-variant') ?>"
					value="<?= Escape::attr($variant->productId()) ?>"
					type="radio"
				>
				<label for="<?= Escape::attr($block->htmlId() . '-variant-' . $id) ?>">
					<?= $variant->image()->html() ?>

					<strong><?= Escape::html($variant->title()) ?></strong>
					<span><?= Escape::html($variant->subtitle()) ?></span>
				</label>
				<?php endforeach ?>
			</li>
		</ul>
	</nav>
	<?php endif ?>

	<div id="<?= Escape::attr($block->htmlId()) ?>-container"></div>

	<script type="module">
	import Configurator from '<?= Escape::js($block->jsUrl()) ?>';

	new Configurator(<?= $block->frontendJson() ?>).init();
	</script>
</figure>
