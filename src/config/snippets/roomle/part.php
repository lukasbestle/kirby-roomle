<?php /** @var LukasBestle\Roomle\Part $part */ ?>
<?= $part->count() ?>x <?= $part->label() ?> (<?= $part->articleNr() ?>)
<?php foreach ($part->parameters() as $parameter): ?>
<?= $parameter . "\n" ?>
<?php endforeach ?>
