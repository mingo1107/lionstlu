<?php
/* @var $homeLink array */
/* @var $links array */
?>
<!--麵包屑_開始-->
<div class="row mt20">
    <div class="col-md-12">
        <ol class="breadcrumb">
            <li>
                <a href="<?= $homeLink['url'] ?>"><?= $homeLink['label'] ?></a>
            </li>
            <?php foreach ($links as $link):
                if (isset($link['url'])): ?>
                    <li>
                        <a href="<?= $link['url'] ?>"><?= $link['label'] ?></a>
                    </li>
                <?php else: ?>
                    <li class="active"><strong><?= $link['label'] ?></strong></li>
                <?php endif;
            endforeach; ?>
        </ol>

    </div>
</div>
<!--麵包屑_結束-->
