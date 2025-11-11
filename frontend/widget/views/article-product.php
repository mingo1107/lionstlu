<?php

use backend\widget\InlineScript;
use ball\helper\File;
use common\models\MediaTrait;
use common\models\ProductModel;
use frontend\assets\FormValidateAsset;

/* @var $product \common\models\ProductModel */
/* @var $standardList \common\models\StandardModel[] */
/* @var $standardInfo array */
FormValidateAsset::register($this);
?>
<?php if (!empty($product) && !empty($standardList)): ?>
    <!--側欄_開始-->
    <div class="col-md-4 col-sm-5 col-xs-12 scrollspy mb-none">
        <div id="fixed-row" class="sidebar" data-spy="affix">
            <div class="buy-card-row">
                <div class="header-card">
                    <h2>立即購買</h2>
                </div>
                <img src="<?= File::img(File::CATEGORY_PRODUCT,
                    MediaTrait::serialize($product, 'media')->src) ?>"
                     alt="<?= $product->name ?>" title="<?= $product->name ?>" width="626" class="img-responsive"/>
                <form id="d-form" name="d-form" method="get" action="/checkout/index">
                    <div class="p1020">
                        <h2><?= $product->name ?></h2>
                        <div class="ddd">
                            <div class="form-group">
                                <label class="control-label">請選擇規格</label>
                                <select class="form-control js-standard" id="standard" name="sid">
                                    <?php foreach ($standardList as $s): ?>
                                        <option value="<?= $s->id ?>">
                                            <?= $product->standard_type == ProductModel::STANDARD_TYPE_DOUBLE ?
                                                $s->name . ' - ' . $s->name2 : $s->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">請選擇數量</label>
                                <select class="form-control js-qty" id="quantity" name="quantity" data-v-rule=""
                                        data-v-msg="請選擇數量">
                                    <?php
                                    $qty = $standardList[0]->stock < 10 ? $standardList[0]->stock : 10;
                                    for ($i = 1; $i <= $qty; ++$i): ?>
                                        <option><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>

                        </div>
                        <div class="card-row">
                            <div class="price">原價：NT$
                                <span class="js-original-price"><?= $standardList[0]->original_price ?></span>元
                            </div>
                            <div class="sale">優惠價：NT$
                                <span class="js-price"><?= $standardList[0]->price ?></span>元
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-lg" style="padding:15px;">我要購買
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!--側欄_結束-->

    <!--側欄_開始-->
    <div class="col-md-4 col-sm-5 col-xs-12 desktop-none">
        <div class="sidebar">
            <div class="buy-card-row">
                <div class="header-card">
                    <h2>立即購買</h2>
                </div>
                <img src="<?= File::img(File::CATEGORY_PRODUCT,
                    MediaTrait::serialize($product, 'media')->src) ?>"
                     alt="<?= $product->name ?>" title="<?= $product->name ?>" width="626" class="img-responsive"/>
                <form id="m-form" name="m-form" method="get" action="/checkout/index">
                    <div class="p1020">
                        <h2><?= $product->name ?></h2>
                        <div class="ddd">
                            <div class="form-group">
                                <label class="control-label" for="m_standard">請選擇規格</label>
                                <select class="form-control js-standard" id="m_standard" name="sid">
                                    <?php foreach ($standardList as $s): ?>
                                        <option value="<?= $s->id ?>">
                                            <?= $product->standard_type == ProductModel::STANDARD_TYPE_DOUBLE ?
                                                $s->name . ' - ' . $s->name2 : $s->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">請選擇數量</label>
                                <select class="form-control js-qty" id="m_quantity" name="quantity" data-v-rule=""
                                        data-v-msg="請選擇數量">
                                    <?php
                                    $qty = $standardList[0]->stock < 10 ? $standardList[0]->stock : 10;
                                    for ($i = 1; $i <= $qty; ++$i): ?>
                                        <option><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>

                        </div>
                        <div class="card-row">
                            <div class="price">原價：NT$
                                <span class="js-original-price"><?= $standardList[0]->original_price ?></span>元
                            </div>
                            <div class="sale">優惠價：NT$
                                <span class="js-price"><?= $standardList[0]->price ?></span>元
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-lg" style="padding:15px;">我要購買
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!--側欄_結束-->
    <?php InlineScript::begin() ?>
    <script>
        (function () {
            var standardList = <?=json_encode($standardInfo)?>;
            var qty = 1;
            var oPrice = <?=$standardList[0]->original_price?>;
            var price = <?=$standardList[0]->price?>;

            $('.js-qty').change(function () {
                qty = this.value === '' ? 0 : parseInt(this.value, 10);
                $('.js-original-price').text(oPrice * qty);
                $('.js-price').text(price * qty);
            });

            $('.js-standard').change(function () {
                oPrice = standardList[this.value].original_price;
                price = standardList[this.value].price;
                $('.js-original-price').text(oPrice * qty);
                $('.js-price').text(price * qty);
            });

            $('form').submit(function () {
                return $(this).formValidate();
            });
            $('#fixed-row').affix({
                offset: {
                    top: $('#fixed-row').offset().top,
                    bottom: ($('footer').outerHeight(true)) + 40/*捲到底後,和下方的距離*/
                }
            });
        })();
    </script>
    <?php InlineScript::end() ?>
<?php endif ?>