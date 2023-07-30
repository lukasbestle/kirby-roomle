<?php /** @var LukasBestle\Roomle\Configuration $configuration */ ?>
<?php if ($configuration->count()): ?><?= $configuration->count() ?>x <?php endif ?>
<?= $configuration->label() ?> (<?= $configuration->id() ?>)
<?= $configuration->size() . "\n" ?>
<?= $configuration->configuratorUrl() . "\n" ?>

<?php foreach ($configuration->parts() as $part): ?>
<?= $part . "\n" ?>
<?php endforeach ?>
