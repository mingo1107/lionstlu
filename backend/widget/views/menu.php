<?php
/* @var $roleList \common\models\AccessRoleModel[] */
$menuParentList = Yii::$app->view->params['menuParentList'];
/* @var $menuList \common\models\AccessModel[] */
$menuList = Yii::$app->view->params['menuList'];
/* @var $menuList array */
$accessList = Yii::$app->view->params['accessList'];
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header"><a href="/"><img src="/image/logo.png" width="142" height="48"></a></li>
            <?php foreach ($menuParentList as $parentMenu): ?>
                <?php if (!empty($accessList[$parentMenu->id])): ?>
                    <li>
                        <?php if (!empty($menuList[$parentMenu->id])): ?>
                            <a href="#" class="js-void">
                                <span class="nav-label"><?= $parentMenu->name ?></span>
                                <span class="fa arrow"></span>
                            </a>
                            <ul class="nav nav-second-level">
                                <?php foreach ($menuList[$parentMenu->id] as $menu): ?>
                                    <?php if (!empty($accessList[$menu->id])): ?>
                                        <li><a href="/<?= $menu->link ?>"><?= $menu->name ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <a href="/<?= $parentMenu->link ?>">
                                <span class="nav-label"><?= $parentMenu->name ?></span>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>