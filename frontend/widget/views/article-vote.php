<?php

use backend\widget\InlineScript;
use ball\api\ResponseCode;
use common\models\VoteModel;
use common\models\VoteOptionModel;
use frontend\assets\FormValidateAsset;

/* @var $vote \common\models\VoteModel */
/* @var $optionList \common\models\VoteOptionModel[] */
FormValidateAsset::register($this);
$colorIndex = 0;
?>
<?php if (!empty($vote) && !empty($optionList)): ?>
    <!--側欄_開始-->
    <div id="vote-scrollspy-mb-none" class="col-md-4 col-sm-4 col-xs-12 scrollspy">
        <div id="fixed-row" class="sidebar" data-spy="affix">
            <div class="vote-card-row">
                <div class="header-card">
                    <h2>投票</h2>
                </div>
                <form class="vote-form" id="d-form" name="m-form" method="post">
                    <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                           value="<?= yii::$app->request->csrfToken ?>"/>
                    <input type="hidden" name="vote_id" value="<?= $vote->id ?>"/>
                    <div class="p1020">
                        <div class="ddd">

                            <div class="row">
                                <div class="col-xs-12">
                                    <h2 class="vote-title"><?= $vote->name ?></h2>
                                    <?php if ($vote->deadline == VoteModel::DEADLINE_ON): ?>
                                        <time class="vote_attach_time">
                                            <i class="fa fa-clock-o mr5" aria-hidden="true"></i>
                                            距離投票結束，剩餘<span class="getting-started js-clock"></span>
                                        </time>
                                    <?php endif ?>
                                </div>
                                <?php foreach ($optionList as $o): ?>
                                    <div class="col-xs-12 vote-item">
                                        <input type="radio" id="radio-s<?= $colorIndex + 1 ?>" name="option"
                                               value="<?= $o->id ?>"
                                               data-v-rule="" data-v-msg="尚未選擇投票項目"/>
                                        <label for="radio-s<?= $colorIndex + 1 ?>" class="default">
                                            <span class="vote-icon"></span>
                                            <span class="vote-option"><?= $o->name ?></span>
                                            <span class="vote-amount"><?= $o->count ?>票</span>
                                            <span class="progress-bar-bg <?= VoteOptionModel::$cssColor[$colorIndex++ % 4] ?>"></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-lg" style="padding:15px;">我要投票
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--側欄_結束-->
    <?php $colorIndex = 0; ?>
    <!--側欄_開始-->
    <div id="vote-scrollspy-desktop-none" class="col-md-4 col-sm-12 col-xs-12">
        <div class="sidebar">
            <div class="vote-card-row">
                <div class="header-card">
                    <h2>投票</h2>
                </div>
                <form class="vote-form" id="m-form" name="m-form" method="post">
                    <input type="hidden" name="<?= yii::$app->request->csrfParam ?>"
                           value="<?= yii::$app->request->csrfToken ?>"/>
                    <input type="hidden" name="vote_id" value="<?= $vote->id ?>"/>
                    <div class="p1020">
                        <div class="ddd">
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2 class="vote-title"><?= $vote->name ?></h2>
                                    <?php if ($vote->deadline == VoteModel::DEADLINE_ON): ?>
                                        <time class="vote_attach_time">
                                            <i class="fa fa-clock-o mr5" aria-hidden="true"></i>
                                            距離投票結束，剩餘<span class="getting-started js-clock"></span>
                                        </time>
                                    <?php endif ?>
                                </div>
                                <?php foreach ($optionList as $o): ?>
                                    <div class="col-xs-12 vote-item">
                                        <input type="radio" id="radio-mb<?= $colorIndex + 1 ?>" name="option"
                                               value="<?= $o->id ?>"
                                               data-v-rule="" data-v-msg="尚未選擇投票項目"/>
                                        <label for="radio-mb<?= $colorIndex + 1 ?>" class="default">
                                            <span class="vote-icon"></span>
                                            <span class="vote-option"><?= $o->name ?></span>
                                            <span class="vote-amount"><?= $o->count ?>票</span>
                                            <span class="progress-bar-bg <?= VoteOptionModel::$cssColor[$colorIndex++ % 4] ?>"></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-lg" style="padding:15px;">我要投票
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
            <?php if ($vote->deadline == VoteModel::DEADLINE_ON): ?>
            var $clock = $('.js-clock');
            // Set the date we're counting down to
            var countDownDate = new Date('<?=$vote->end_time?>').getTime();

            // Update the count down every 1 second
            var x = setInterval(function () {

                // Get todays date and time
                var now = new Date().getTime();

                // Find the distance between now an the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result in the element with id="demo"
                $clock.html(" " + days + "天 " + hours + "時 "
                    + minutes + "分 " + seconds + "秒 ");

                // If the count down is finished, write some text
                if (distance < 0) {
                    clearInterval(x);
                    $clock.html("投票已結束");
                }
            }, 1000);
            <?php endif ?>

            var $buttons = $('button[type="submit"]');
            $('form').submit(function () {
                if ($(this).formValidate()) {
                    $buttons.attr('disabled', 'disabled');

                    $.post('xhr-vote', $(this).serialize(), function (data) {
                        if (!data.error && data.code === '<?=ResponseCode::SUCCESS?>') {
                            alert('投票成功，感謝您的參與。')
                            window.location.reload();
                        }
                        else if (data.error.code === '<?=ResponseCode::ERROR_NEED_LOGIN?>') {
                            alert('請先登入會員。');
                        }
                        else if (data.error.code === '<?=ResponseCode::ERROR_VOTE_EXISTS_DAILY?>') {
                            alert('每個會員每天只能投票一次。');
                        }
                        else if (data.error.code === '<?=ResponseCode::ERROR_VOTE_EXISTS?>') {
                            alert('每個會員只能投票一次。');
                        }
                        else {
                            alert('投票失敗，請稍後再試');
                        }

                    }, 'json');
                }
                return false;
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
