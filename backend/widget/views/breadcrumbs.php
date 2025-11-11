<?php
/* @var $homeLink array */
/* @var $links array */
?>
<ol class="breadcrumb">
    <li>
        <a href="<?= $homeLink['url'] ?>"><?= $homeLink['label'] ?></a>
    </li>
    <?php foreach ($links as $link):
        if (isset($link['url'])): ?>
            <li>
                <a href="<?= $link['url'][0] ?>"><?= $link['label'] ?></a>
            </li>
        <?php else: ?>
            <li class="active"><strong><?= $link['label'] ?></strong></li>
        <?php endif;
    endforeach; ?>
</ol>
