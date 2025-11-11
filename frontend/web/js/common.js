(function (window, undefined) {
    var common = function () {

    };

    common.prototype = (function () {
        // 使用者的facebook id
        var facebookUID = 'fb_uid';
        // 使用者的facebook email
        var facebookEmail = 'fb_email';
        var facebookAccessToken = 'fb_token';
        // facebook存取權限
        var facebookScope = 'email';
        var escapeRegExp = function (str) {
            return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        }
        return {
            init: function () {
                return this
            },
            // 判斷是否為數字
            isNumeric: function (n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            },
            tryParseInt: function (string, radix) {
                var result;
                try {
                    result = parseInt(string, 10)
                } catch (err) {
                    result = 0;
                }
                return result;
            },
            popUp: function (url, width, height) {
                window.open(url, '_blank', 'height=' + height + ', width=' + width + ', resizable=0, status=0, toolbar=0 ,scrollbars=1, location=0,menubar=0,directories=0');
            },
            setCookie: function (c_name, value, exdays) {
                var exdate = new Date();
                exdate.setDate(exdate.getDate() + exdays);
                var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString()) + ";domain=.ishafun.com";
                document.cookie = c_name + "=" + c_value;
            },
            getCookie: function (c_name) {
                var i, x, y, ARRcookies = document.cookie.split(";");
                for (i = 0; i < ARRcookies.length; i++) {
                    x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
                    y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
                    x = x.replace(/^\s+|\s+$/g, "");
                    if (x == c_name) {
                        return unescape(y);
                    }
                }
            },
            toHalfWidth: function (fullWidthText) {
                var tmp = [];
                var a = fullWidthText;
                var b = "";
                for (var i = 0; i < a.length; i++) {
                    if (a.charCodeAt(i) == 12288) {
                        tmp[i] = 32; // 半形 空格 ASCII 為 32 , 全形 空格 ASCII 為12288
                    } else if (a.charCodeAt(i) >= 65281 && a.charCodeAt(i) <= 65374) {
                        tmp[i] = a.charCodeAt(i) - 65248;//其他字元 半形ASCII 33~126 與 全形ASCII 65281~65374 對應之 ASCII 皆相差 65248
                    } else {
                        tmp[i] = a.charCodeAt(i);
                    }

                    b += String.fromCharCode(tmp[i]);
                }
                return b;
            },
            // 正規表示式
            regex: {
                email: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,

                url: /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,

                digit: /^\d+$/,

                number: /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/,
                positiveNumber: /^[+]?\d+([.]\d+)?$/,
                // yyyy-mm-dd 1900~2099
                date: /^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/,
                // yyyy-mm-dd 1900~2099 (HH:MM:SS or HH:MM:SS.mmm)
                datetime: /^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])\s([0-1]?\d|2[0-3]):([0-5]?\d):([0-5]?\d(\.\d{1,3})?)$/,
                // 身分證字號
                id: /^[a-zA-Z]\d{9}$/
            },
            facebook: {
                login: function (appId, scope, callback) {
                    var checkLogin = function () {
                        FB.AppEvents.logPageView();
                        FB.getLoginStatus(function (response) {
                            if (response.status === 'connected') {
                                callback(response.authResponse);
                            } else {
                                FB.login(function (response) {
                                    /**
                                     {
                                            status: 'connected',
                                            authResponse: {
                                                accessToken: '...',
                                                expiresIn:'...',
                                                reauthorize_required_in:'...'
                                                signedRequest:'...',
                                                userID:'...'
                                            }
                                        }
                                     */
                                    if (response.status === 'connected') {
                                        callback(response.authResponse);
                                    } else {
                                        console.log("Login failed");
                                        console.log(response);
                                    }
                                }, {scope: scope});
                            }
                        }, true);
                    };

                    window.fbAsyncInit = function () {
                        FB.init({
                            appId: appId,
                            cookie: true,
                            xfbml: true,
                            version: 'v4.0',
                            status: true // set this status to true, this will fixed popup blocker issue
                        });
                        checkLogin();
                    };

                    (function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];

                        if (d.getElementById(id)) {
                            return;
                        }
                        js = d.createElement(s);
                        js.id = id;
                        js.src = "https://connect.facebook.net/en_US/sdk.js";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));

                    // Workaround of "After initializing of Facebook SDK, login function not execute."
                    if (typeof FB !== 'undefined') {
                        checkLogin();
                    }
                }
            },
            isMobile: {
                android: function () {
                    return navigator.userAgent.match(/Android/i);
                },
                blackBerry: function () {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function () {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                opera: function () {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                windows: function () {
                    return navigator.userAgent.match(/IEMobile/i);
                },
                any: function () {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            },
            parseDateFromMySQL: function (dateFromMySQL) {
                var dateStr = dateFromMySQL; // returned from mysql timestamp/datetime field
                var a = dateStr.split(" ");
                var d = a[0].split("-");
                var t = a[1].split(":");
                return new Date(d[0], (d[1] - 1), d[2], t[0], t[1], t[2]);
            },
            replaceAll: function (str, find, replace) {
                return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
            }
        }
    })();

    window.common = new common();
})(window);