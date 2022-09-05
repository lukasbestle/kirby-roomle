<?php /** @var LukasBestle\Roomle\Configuration $configuration */ ?>
<?= $configuration->label() ?> (<?= $configuration->id() ?>)
W <?= $configuration->widthLabel() ?> / H <?= $configuration->heightLabel() ?> / D <?= $configuration->depthLabel() . "\n" ?>
<?= $configuration->configuratorUrl() . "\n" ?>

<?php foreach ($configuration->parts() as $part): ?>
<?= $part . "\n" ?>
<?php endforeach ?>
