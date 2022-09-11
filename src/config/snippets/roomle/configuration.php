<?php /** @var LukasBestle\Roomle\Configuration $configuration */ ?>
<?= $configuration->label() ?> (<?= $configuration->id() ?>)
<?= $configuration->size() . "\n" ?>
<?= $configuration->configuratorUrl() . "\n" ?>

<?php foreach ($configuration->parts() as $part): ?>
<?= $part . "\n" ?>
<?php endforeach ?>
