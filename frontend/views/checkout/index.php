<?php


use backend\widget\InlineScript;
use ball\helper\File;
use ball\helper\HtmlHelper;
use common\assets\TwCityAsset;
use common\models\MediaTrait;
use common\models\ProductModel;
use frontend\assets\FormValidateAsset;
use frontend\assets\PayAsset;
use yii\helpers\Html;

/* @var $product \common\models\ProductModel */
/* @var $standard \common\models\StandardModel */
/* @var $quantity int */
/* @var $form \frontend\models\CheckoutForm */

FormValidateAsset::register($this);
TwCityAsset::register($this);
PayAsset::register($this);
?>
<div class="container-700 mt30">

    <div class="row">
        <?= HtmlHelper::displayFlash() ?>
        <form id="main-form" class="form-horizontal" method="post">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                   value="<?= Yii::$app->request->csrfToken ?>"/>
            <!--產品資訊_開始-->
            <div class="col-md-12 pay-col">
                <div class="panel panel-deepblue">
                    <div class="panel-heading">
                        <h3 class="panel-title">購買商品</h3>
                    </div>
                    <div class="panel-body-pa0">
                        <div class="event-img">
                            <img src="<?= File::img(File::CATEGORY_PRODUCT,
                                MediaTrait::serialize($product, 'media')->src) ?>"
                                 alt="<?= $product->name ?>" title="<?= $product->name ?>" width="626"
                                 class="img-responsive"/>
                        </div>
                        <div class="event-price"><strong>優惠價：$<?= $quantity * $standard->price ?>元</strong><span
                                    class="price line-through">原價: $<?= $quantity * $standard->original_price ?>元</span>
                        </div>
                        <div class="event-info">
                            <h2><?= $product->name ?></h2>
                            <div class="event-style">
                                <div>規格：<?= $product->standard_type == ProductModel::STANDARD_TYPE_DOUBLE ?
                                        $standard->name . ' - ' . $standard->name2 : $standard->name ?></div>
                                <div>數量：<?= $quantity ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--產品資訊_結束-->

            <!--付款方式_開始-->
            <div class="col-md-12 pay-col">
                <div class="panel panel-deepblue">
                    <div class="panel-heading">
                        <h3 class="panel-title">付款方式</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 mb15">
                                <input type="radio" id="radio-s1" name="pay-select" checked />
                                <label for="radio-s1" class="default"><span></span><span class="title"><i class="icon-credit-card mr10"></i>貨到付款</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--付款方式_結束-->


            <!--會員資訊_開始-->
            <div class="col-md-12 pay-col">

                <div class="panel panel-deepblue">
                    <div class="panel-heading">
                        <h3 class="panel-title">購買人資訊</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?php if (Yii::$app->user->isGuest): ?>
                                <div class="col-xs-12 mb15">
                                    <label for="">E-Mail (會員登入帳號，查訂單用)</label>
                                    <?= Html::activeTextInput($form, 'email',
                                        ['class' => 'form-control input-lg', 'placeholder' => '請輸入E-Mail',
                                            'data-v-rule' => 'email', 'data-v-msg' => 'E-Mail格式錯誤']) ?>
                                </div>

                                <div class="col-xs-12 mb15">
                                    <label for="">請輸入登入密碼</label>
                                    <?= Html::activePasswordInput($form, 'password',
                                        ['class' => 'form-control input-lg', 'placeholder' => '請輸入密碼']) ?>
                                </div>

                                <div class="col-xs-12 mb15">
                                    <label for="">請再次輸入密碼</label>
                                    <?= Html::activePasswordInput($form, 'password2',
                                        ['class' => 'form-control input-lg', 'placeholder' => '請再次輸入密碼']) ?>
                                </div>
                            <?php else: ?>
                                <div class="col-xs-12 mb15">
                                    <label for="">E-Mail</label>
                                    <?= Html::activeTextInput($form, 'email',
                                        ['class' => 'form-control input-lg', 'placeholder' => '請輸入E-Mail',
                                            'data-v-rule' => 'email', 'data-v-msg' => 'E-Mail格式錯誤']) ?>
                                </div>
                            <?php endif ?>
                            <div class="col-xs-12 mb15">
                                <label for="">姓名</label>
                                <?= Html::activeTextInput($form, 'name',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入購買人姓名',
                                        'data-v-rule' => '', 'data-v-msg' => '輸入購買人姓名']) ?>
                            </div>
                            <div class="col-xs-12 mb15">
                                <label for="">聯絡手機</label>
                                <?= Html::activeTextInput($form, 'mobile',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入手機',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入手機']) ?>
                            </div>
                            <div id="city-selector">
                                <div class="col-xs-6 mb15">
                                    <label>所在城市</label>
                                    <?= Html::activeDropDownList($form, 'city', [],
                                        ['class' => 'form-control input-lg', 'data-v-rule' => '', 'data-v-msg' => '請輸入所在城市']) ?>
                                </div>
                                <div class="col-xs-6 mb15">
                                    <label>所在地區</label>
                                    <?= Html::activeDropDownList($form, 'district', [],
                                        ['class' => 'form-control input-lg', 'data-v-rule' => '', 'data-v-msg' => '請輸入所在地區']) ?>
                                    <?= Html::activeHiddenInput($form, 'zip') ?>
                                </div>
                            </div>
                            <div class="col-xs-12 mb15">
                                <label>地址</label>
                                <?= Html::activeTextInput($form, 'address',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入地址',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入地址']) ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <!--會員資訊_結束-->


            <!--收件人資訊_開始-->
            <div class="col-md-12 pay-col">

                <div class="panel panel-deepblue">
                    <div class="panel-heading">
                        <h3 class="panel-title">收件人資訊</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 mb15">
                                <input type="checkbox" id="copy" name="copy" value="1"/>
                                <label for="copy" class="" style="font-size:16px;">同購買人資訊</label>
                            </div>

                            <div class="col-xs-12 mb15">
                                <label for="">收件人E-Mail</label>
                                <?= Html::activeTextInput($form, 'receiver_email',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入E-Mail',
                                        'data-v-rule' => 'email', 'data-v-msg' => '收件人E-Mail格式錯誤']) ?>
                            </div>
                            <div class="col-xs-12 mb15">
                                <label for="">收件人姓名</label>
                                <?= Html::activeTextInput($form, 'receiver_name',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入購買人姓名',
                                        'data-v-rule' => '', 'data-v-msg' => '輸入收件人姓名']) ?>
                            </div>
                            <div class="col-xs-12 mb15">
                                <label for="">收件人聯絡手機</label>
                                <?= Html::activeTextInput($form, 'receiver_mobile',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入手機',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入收件人手機']) ?>
                            </div>
                            <div id="receiver-city-selector">
                                <div class="col-xs-6 mb15">
                                    <label>收件人所在城市</label>
                                    <?= Html::activeDropDownList($form, 'receiver_city', [],
                                        ['class' => 'form-control input-lg', 'data-v-rule' => '', 'data-v-msg' => '請輸入收件人所在城市']) ?>
                                </div>
                                <div class="col-xs-6 mb15">
                                    <label>收件人所在地區</label>
                                    <?= Html::activeDropDownList($form, 'receiver_district', [],
                                        ['class' => 'form-control input-lg', 'data-v-rule' => '', 'data-v-msg' => '請輸入收件人所在地區']) ?>
                                    <?= Html::activeHiddenInput($form, 'receiver_zip') ?>
                                </div>
                            </div>
                            <div class="col-xs-12 mb15">
                                <label>地址</label>
                                <?= Html::activeTextInput($form, 'receiver_address',
                                    ['class' => 'form-control input-lg', 'placeholder' => '請輸入收件人地址',
                                        'data-v-rule' => '', 'data-v-msg' => '請輸入收件人地址']) ?>
                            </div>


                        </div>

                    </div>
                </div>

            </div>
            <!--收件人資訊_結束-->


            <!--xxx_開始-->
            <div class="col-sm-6 col-xs-12">
                <button id="back" type="button" class="btn btn-block btn-xlg btn-default" onclick="history.back()"><i
                            class="fa fa-angle-left mr5" aria-hidden="true"></i>回上頁
                </button>
            </div>
            <div class="col-sm-6 col-xs-12 mb20">
                <button type="submit" class="btn btn-block btn-xlg btn-success">確定購買
                    <i class="fa fa-angle-right ml5" aria-hidden="true"></i></button>
            </div>
            <!--xxx_結束-->

        </form>


    </div><!--row-end-->

</div><!--container-end-->
<?php InlineScript::begin() ?>
<script>
    (function () {
        <?php if (Yii::$app->user->isGuest): ?>
        var formParams = {
            '<?= Html::getInputName($form, 'password') ?>': [function () {
                var password = document.getElementById('<?= Html::getInputId($form, 'password') ?>');
                var password2 = document.getElementById('<?= Html::getInputId($form, 'password2') ?>');
                if ($.trim(password.value).length !== 0 && password.value === password2.value) {
                    return true;
                } else {
                    return false;
                }
            }, '請輸入密碼並且密碼必須一致']
        };
        <?php else:?>
        var formParams = {};
        <?php endif?>

        $('#main-form').submit(function () {
            return $(this).formValidate(formParams);
        });

        $('#copy').change(function () {
            var mapping = {
                '<?= Html::getInputId($form, 'email') ?>': '<?= Html::getInputId($form, 'receiver_email') ?>',
                '<?= Html::getInputId($form, 'name') ?>': '<?= Html::getInputId($form, 'receiver_name') ?>',
                '<?= Html::getInputId($form, 'mobile') ?>': '<?= Html::getInputId($form, 'receiver_mobile') ?>',
                '<?= Html::getInputId($form, 'address') ?>': '<?= Html::getInputId($form, 'receiver_address') ?>'

            };
            if (this.checked) {
                for (var k in mapping) {
                    document.getElementById(mapping[k]).value = document.getElementById(k).value;
                }
                new TwCitySelector({
                    el: "#receiver-city-selector",
                    elCounty: "#<?=Html::getInputId($form, 'receiver_city')?>", // 在 el 裡查找 dom
                    elDistrict: "#<?=Html::getInputId($form, 'receiver_district')?>", // 在 el 裡查找 dom
                    elZipcode: "#<?=Html::getInputId($form, 'receiver_zip')?>", // 在 el 裡查找 dom
                    selectedCounty: document.getElementById('<?= Html::getInputId($form, 'city') ?>').value,
                    selectedDistrict: document.getElementById('<?= Html::getInputId($form, 'district') ?>').value,
                    countyClassName: "form-control input-lg",
                    countyFiledName: "<?=Html::getInputName($form, 'receiver_city')?>",
                    districtClassName: "form-control input-lg",
                    districtFieldName: "<?=Html::getInputName($form, 'receiver_district')?>",
                    zipcodeFiledName: "<?=Html::getInputName($form, 'receiver_zip')?>",
                });
            }
        });

        new TwCitySelector({
            el: "#city-selector",
            elCounty: "#<?=Html::getInputId($form, 'city')?>", // 在 el 裡查找 dom
            elDistrict: "#<?=Html::getInputId($form, 'district')?>", // 在 el 裡查找 dom
            elZipcode: "#<?=Html::getInputId($form, 'zip')?>", // 在 el 裡查找 dom
            selectedCounty: '<?=$form->city?>',
            selectedDistrict: '<?=$form->district?>',
            countyClassName: "form-control input-lg",
            countyFiledName: "<?=Html::getInputName($form, 'city')?>",
            districtClassName: "form-control input-lg",
            districtFieldName: "<?=Html::getInputName($form, 'district')?>",
            zipcodeFiledName: "<?=Html::getInputName($form, 'zip')?>",
        });

        new TwCitySelector({
            el: "#receiver-city-selector",
            elCounty: "#<?=Html::getInputId($form, 'receiver_city')?>", // 在 el 裡查找 dom
            elDistrict: "#<?=Html::getInputId($form, 'receiver_district')?>", // 在 el 裡查找 dom
            elZipcode: "#<?=Html::getInputId($form, 'receiver_zip')?>", // 在 el 裡查找 dom
            selectedCounty: '<?=$form->city?>',
            selectedDistrict: '<?=$form->district?>',
            countyClassName: "form-control input-lg",
            countyFiledName: "<?=Html::getInputName($form, 'receiver_city')?>",
            districtClassName: "form-control input-lg",
            districtFieldName: "<?=Html::getInputName($form, 'receiver_district')?>",
            zipcodeFiledName: "<?=Html::getInputName($form, 'receiver_zip')?>",
        });
    })();
</script>
<?php InlineScript::end() ?>
