<?php
/** @var $oid int * */
?>
<div class="container-700 mt30">

    <div class="row">

        <!--購買完成_開始-->
        <div class="col-md-12 pay-col">

            <div class="panel panel-deepblue" style="border-top:0;">
                <div class="panel-body">
                    <div class="row">


                        <!--xxx_開始-->
                        <div class="col-md-12">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                        </div>
                        <!--xxx_結束-->

                        <div class="col-md-12">
                            <h3 class="text-success text-center">您已完成訂購</h3>
                            <p class="form-control-static text-center">
                                本商品由【王小明3c】委託刊登,
                                <br>「商品寄送」等一切後續服務,將由【王小明3c】全數負責。
                            </p>
                        </div>


                    </div>

                </div>
            </div>

        </div>
        <!--購買完成_結束-->


        <!--xxx_開始-->
        <div class="col-sm-12 col-xs-12 mb20">
            <button type="button" class="btn btn-block btn-xlg btn-success"
                    onclick="window.location.href='/order/detail?id=<?= $oid ?>'">檢視「訂單」紀錄
            </button>
        </div>
        <!--xxx_結束-->


    </div><!--row-end-->

</div><!--container-end-->
