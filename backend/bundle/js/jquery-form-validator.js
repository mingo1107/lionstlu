// Requires bootstrap.css
(function (window, $, undefined) {
    $.fn.formValidate = function (jsonRequiredFields) {
        var params = buildParams();
        if (typeof jsonRequiredFields === 'object') {
            for (var k in params) {
                jsonRequiredFields[k] = params[k];
            }
        } else {
            jsonRequiredFields = params;
        }
        var required = [];
        for (var obj in jsonRequiredFields) {
            var field = this.find('[name="' + obj + '"]'),
                inputType = this.find('[name="' + obj + '"]').not(':input[type="hidden"]').attr('type'),
                checkedCount = 0;

            if (typeof inputType === 'undefined') {
                inputType = this.find('[name="' + obj + '"]').not(':input[type="hidden"]').get(0).tagName;
            }
            inputType = inputType.toLowerCase();
            switch (inputType) {
                case 'number':
                case 'text':
                case 'password':
                case 'textarea':
                case 'select':
                case 'select-one':
                case 'select-multiple':
                    if (jsonRequiredFields[obj][0] == '') {
                        if ($.trim(field.val()) == '')
                            required.push(obj);
                    } else if (!isNaN(parseFloat(jsonRequiredFields[obj][0])) && isFinite(jsonRequiredFields[obj][0])) {
                        if ($.trim(field.val()).length < jsonRequiredFields[obj][0])
                            required.push(obj);
                    } else {
                        if (typeof jsonRequiredFields[obj][0] === 'function') {
                            if (!jsonRequiredFields[obj][0](arguments))
                                required.push(obj);
                        } else {
                            if (!new RegExp(jsonRequiredFields[obj][0]).test($.trim(field.val())))
                                required.push(obj);
                        }
                    }


                    break;
                case 'radio':
                    if (this.find('[name="' + obj + '"]:checked').length == 0) {
                        required.push(obj);
                    }
                    break;
                case 'checkbox':
                    field.each(function () {
                        if (this.checked) {
                            ++checkedCount;
                        }
                    });
                    if (jsonRequiredFields[obj][0] == '') {
                        if (checkedCount == 0) {
                            required.push(obj);
                        }
                    } else {
                        if (checkedCount < jsonRequiredFields[obj][0]) {
                            required.push(obj);
                        }
                    }
                    break;
            }
        }
        if (required.length > 0) {
            this.formMessage(jsonRequiredFields, required);
            return false;
        } else {
            this.formMessage(jsonRequiredFields, required);
            return true;
        }
    };


    $.fn.formMessage = function (jsonField, arrayReqField, displayHandler) {
        var errorDivClassName = 'fv-err',
            fieldStyle = 'js-fv-required',
            msgStyle = 'js-fv-required-message';

        this.find('[name]').removeClass(fieldStyle);
        var focus = false;
        if (typeof displayHandler === 'function') {
            for (var i = 0, len = arrayReqField.length; i != len; ++i) {
                var field = this.find('[name="' + arrayReqField[i] + '"]'),
                    inputType = this.find('[name="' + arrayReqField[i] + '"]').attr('type');
                if (typeof inputType === 'undefined') {
                    inputType = this.find('[name="' + arrayReqField[i] + '"]').get(0).tagName;
                }
                inputType = inputType.toLowerCase();
                field.addClass(fieldStyle);
                if (!focus) {
                    field.focus();
                    focus = true;
                }
            }
            displayHandler.apply(this.get(0), arguments);
        } else {
            this.find('.' + errorDivClassName).remove();

            for (var i = 0, len = arrayReqField.length; i != len; ++i) {
                var field = this.find('[name="' + arrayReqField[i] + '"]:last'),
                    inputType = this.find('[name="' + arrayReqField[i] + '"]').attr('type');
                if (typeof inputType === 'undefined') {
                    inputType = this.find('[name="' + arrayReqField[i] + '"]').get(0).tagName;
                }
                inputType = inputType.toLowerCase();
                field.addClass(fieldStyle);
                if (!focus) {
                    field.focus();
                    focus = true;
                }

                var message = jsonField[arrayReqField[i]][1];
                if (inputType === 'text' ||
                    inputType === 'number' ||
                    inputType === 'password' ||
                    inputType === 'textarea' ||
                    inputType === 'select' ||
                    inputType === 'select-one' ||
                    inputType === 'select-multiple' ||
                    inputType === 'input') {
                    field.after(
                        ['<span class="' + errorDivClassName + '">',
                            message,
                            '</span>'].join(''));
                } else {
                    field.parent().append(
                        ['<span class="' + errorDivClassName + '">',
                            message,
                            '</span>'].join(''));
                }
            }
            this.find('.' + errorDivClassName).addClass(msgStyle);
        }
    };
    var regex = {
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
    };

    var buildParams = function () {
        var validateParams = {};
        $('[data-v-rule]').each(function () {
            var inputName = $(this).attr("name");
            var validateValue = $(this).attr("data-v-rule");
            var validateMsg = $(this).attr("data-v-msg");
            switch (validateValue) {
                case 'email':
                case 'url':
                case 'digit':
                case 'number':
                case 'positiveNumber':
                case 'date':
                case 'datetime':
                case 'id':
                    validateValue = regex[validateValue];

            }
            validateParams[inputName] = [validateValue, validateMsg];
        });
        return validateParams;
    }
})(window, jQuery);