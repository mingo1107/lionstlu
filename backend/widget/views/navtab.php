<?php
/* @var $links array */
?>
<ul class="nav nav-tabs margin-bottom-2">
    <?php foreach ($links as $link): ?>
        <li role="presentation" <?= isset($link['active']) && $link['active'] === true ? 'class="active"' : '' ?>>
            <a href="<?= $link['url'] ?>"><?= $link['label'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>
