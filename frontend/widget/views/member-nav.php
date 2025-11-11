<?php

use backend\widget\InlineScript;

/* @var $index int */
?>
    <!--側欄_開始-->
    <div class="col-md-3 mc-sidebar">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">會員中心</h3>
            </div>
            <div class="panel-body">
                <div class="mc-nav">
                    <ul>
                        <li data-index="0" class="js-index">
                            <a href="/member/center"><i class="fa fa-pencil mr5" aria-hidden="true"></i>會員資料修改</a>
                        </li>
                        <li data-index="1" class="js-index">
                            <a href="/member/service"><i class="fa fa-paper-plane mr5" aria-hidden="true"></i>聯絡客服
                            </a>
                        </li>
                        <li data-index="2" class="js-index">
                            <a href="/member/reply">
                                <i class="fa fa-reply mr5" aria-hidden="true"></i>最新客服回覆
                            </a>
                        </li>
                        <li data-index="3" class="js-index">
                            <a href="/order/index">
                                <i class="fa fa-search mr5" aria-hidden="true"></i>歷史訂單查詢
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--側欄_結束-->
<?php InlineScript::begin() ?>
    <script>
        (function () {
            var index = <?=$index?>;
            $('.js-index').each(function () {
                var i = parseInt(this.getAttribute('data-index'), 10);
                if (i === index) {
                    this.classList.add('selected');
                    return false;
                }
            })
        })();
    </script>
<?php InlineScript::end() ?>