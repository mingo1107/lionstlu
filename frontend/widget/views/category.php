<?php
/* @var $categoryList common\models\ArticleCategoryModel[] */
?>
<?php if (!empty($categoryList)): ?>
    <!--分類選單_開始-->
    <div class="menu-fluid">
        <div class="container">
            <div class="row">
                <!--xxx_開始-->
                <div class="col-md-12">
                    <div id="carousel-row" class="carousel">
                        <?php foreach ($categoryList as $model): ?>
                            <!--item-開始-->
                            <div class="carousel-cell"><a href="/article/category?id=<?= $model->id ?>"
                                                          class="selected"><?= $model->name ?></a></div>
                            <!--item-結束-->
                        <?php endforeach; ?>
                    </div>
                    <!--carousel-row-end-->
                </div>
                <!--xxx_結束-->
            </div>
            <!--row-end-->
        </div>
        <!--container-end-->
    </div>
    <!--分類選單_結束-->
<?php endif ?>