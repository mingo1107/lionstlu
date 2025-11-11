<?php
?>
<!--忘記密碼-開始-->
<div class="modal-body step step-2 container" data-step="2">
    <div class="signin-row">
        <a href="#" data-dismiss="modal" id="close"><i
                class="icon-cross-out"></i></a>
        <a href="#" id="back" class="step step-2"
           data-step="1"
           onClick="sendEvent('#demo-modal-1', 1)"><i
                class="fa fa-angle-left mr5" aria-hidden="true"></i>返回登入</a>
        <h3>忘記密碼</h3>
        <fieldset>
            <div class="form-group">
                <input class="form-control input-lg" id="inputEmail" placeholder="請輸入註冊時的Email"
                       name="email" type="text" autofocus>
            </div>
            <div class="alert alert-danger"><i class="fa fa-exclamation-triangle mr3"
                                               aria-hidden="true"></i>請輸入Email
            </div>
            <div class="alert alert-success"><i class="fa fa-check mr3" aria-hidden="true"></i>新密碼已寄到「xxx@gmail.com.tw」中
            </div>
            <input class="btn btn-success btn-block mt25 mb10 btn-xlg" type="submit" value="送出">
        </fieldset>
    </div>
</div>
<!--忘記密碼-結束-->
