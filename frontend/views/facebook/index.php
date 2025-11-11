<?php

use backend\widget\InlineScript;

?>
    <style>
        /*#like-btn {*/
            /*position: relative;*/
            /*float: left;*/
        /*}*/

        /*.overlay {*/
            /*top: 0;*/
            /*left: 0;*/
            /*width: 2000px;*/
            /*height: 200px;*/
            /*position: absolute;*/
            /*background-color: black*/
        /*}*/
    </style>
    <div class="m-2">
        <div class="overlay">
            <div class="fb-like" data-href="https://www.facebook.com/porsche" data-layout="standard"
                 data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
        </div>
    </div>

    <div class="m-2">
        <div class="overlay">
            <div class="fb-like" data-href="https://www.facebook.com/bmw" data-layout="standard"
                 data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
        </div>
    </div>

    <div class="m-2">
        <div class="overlay">
            <div class="fb-like" data-href="https://www.facebook.com/MercedesBenz/" data-layout="standard"
                 data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
        </div>
    </div>

<?php InlineScript::begin() ?>
    <script>


        var page_like_or_unlike_callback = function (url, html_element) {
            console.log(url);
            console.log(html_element);
            alert('你按讚了, url: ' + url);
        };
        // In your onload handler
        FB.Event.subscribe('edge.create', page_like_or_unlike_callback);
        FB.Event.subscribe('edge.remove', page_like_or_unlike_callback);
        $('.overlay').click(function () {
            alert('hello');
        });
        // $(document).find('iframe').each(function () {
        //     if (!this.id) {
        //         console.log(this.name);
        //         $(this.contentWindow.document).click(function () {
        //             alert('test');
        //         });
        //     }
        // });
        var finished_rendering = function () {
            console.log("finished rendering plugins");
        }

        // In your onload handler
        FB.Event.subscribe('xfbml.render', finished_rendering);
    </script>
<?php InlineScript::end() ?>