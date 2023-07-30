<?php /** @var LukasBestle\Roomle\Plan $plan */ ?>
<?= $plan->label() ?> (<?= $plan->id() ?>)
<?= $plan->configuratorUrl() . "\n" ?>

<?php foreach ($plan->groupedItems() as $item): ?>
<?= $item . "\n" ?>
<?php endforeach ?>
