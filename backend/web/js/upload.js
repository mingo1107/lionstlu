(function ($) {
    const EXT_VIDEO = ['mp4', 'webm', 'ogg', 'MP4'];
    const EXT_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'PNG'];
    const CROP_PIXEL_BASIS = '400';
    const DEFAULT_CROP_OPTION = {
        boundary: {
            width: '80%',
            height: '80%',
        },
        customClass: 'upload'
    };

    let $dropZones = $('.js-drop');
    let uploadCount = 0;
    let uploadComplete = 0;
    let cropCount = 0;
    let cropComplete = 0;
    let submitComplete = 0;
    let reloadTimeout = null;
    let cropper = null;
    let reader = new FileReader();

    let convertBase64 = function (file, callback) {
        reader.readAsDataURL(file);
        reader.onload = function () {
            callback(reader.result);
        };
    };

    let doCrop = function (fileId, src, cropOption, next) {
        return function () {
            let $crop = $('#' + fileId + '-crop');
            $crop.removeClass('hide');
            console.log(cropOption);
            cropper = $crop.croppie(cropOption);

            cropper.croppie('bind', {
                url: src,
            }).then(function () {
                $crop.croppie('setZoom', 0)
            });

            $.fancybox.open($crop, {
                afterClose: function (instance, current) {
                    cropper.croppie('destroy');
                    next();
                },
                touch: false,
            });
        };
    };

    let cropEveryVersion = function (input, f) {
        return function (blobSrc) {
            let wFileId = input.getAttribute('data-file-window');

            let $originW = $('#' + wFileId + '-origin');

            let window = input.getAttribute('data-crop-window');
            let ratioW = input.getAttribute('data-crop-ratio-window');

            let cropOptionW = $.extend(DEFAULT_CROP_OPTION, {
                viewport: {
                    width: CROP_PIXEL_BASIS,
                    height: CROP_PIXEL_BASIS / ratioW,
                },
            });


            $originW.attr('src', f.url).attr('data-img', blobSrc).attr('data-file-name', f.uploadName);


            if (window === '1' && blobSrc !== "") {
                doCrop(wFileId, blobSrc, cropOptionW, function () {
                })();
            }
        };
    };

    let reload = function () {
        window.location.reload();
    };

    let block = function () {
        $(window).bind('beforeunload', function () {
            return '上傳未完成，請問是否需要離開？';
        });
    };

    let unblock = function () {
        $(window).unbind('beforeunload');
    };

    $('.js-upload').each(function () {
        let el = this;

        $('#' + el.id).fileupload({
            dropZone: $('#' + el.id + '-drop'),
            submit: function (el, data) {
                block();

                let auto = this.getAttribute("data-auto");
                let csrfName = $('meta[name=csrf-param]').prop('content');
                let csrfToken = $('meta[name=csrf-token]').prop('content');
                let fileName = data.files[0].name;
                let current = null;

                ++uploadCount;

                if (auto === '1') {
                    fileName = fileName.replace(/[^.]+/i, Date.now().toString() + Math.random().toString(12).substring(2, 12));
                    data.files[0].uploadName = fileName;
                    current = this.getAttribute('data-current');
                } else {
                    current = "";
                }
                this.setAttribute('data-current', fileName);

                data.formData = {
                    'param_name': this.getAttribute('name'),
                    'auto': '0',
                    'category': this.getAttribute('data-category'),
                    'current': current
                };
                data.formData[csrfName] = csrfToken;
                data.jqXHR = $(this).fileupload('send', data);
                return false;
            },
            progress: function (e, data) {
                let input = this;
                let $inputW = $('#' + input.getAttribute('data-id-window'));
                let $bar = $('#' + input.getAttribute('name') + '-bar');
                let f = null;

                $bar.removeClass("hide").text('上傳進度：' + Math.round(100. * uploadComplete / uploadCount).toString() + "%");
                window.clearTimeout(reloadTimeout);

                if (!data.error) {
                    f = data.files[0];

                    $inputW.attr('data-current', f.uploadName).attr('data-first', 1);

                    convertBase64(f, cropEveryVersion(input, f));
                }
            },
            done: function (e, data) {
                let input = this;
                let name = input.getAttribute('name');
                let field = input.getAttribute('data-field');
                let autoSubmit = input.getAttribute('data-auto-submit');
                let autoRefresh = input.getAttribute('data-auto-refresh');
                let originText = input.getAttribute('data-origin-text');

                let $form = $($(input).closest('form'));
                let $bar = $('#' + name + '-bar');
                let $a = $('#' + name + '-display-a');
                let $displayTool = $('#' + name + '-display-tool');
                let $displayText = $('#' + name + '-display-text');
                let $preview = $('#' + name + '-preview');
                let $uploadInfo = $('#' + field + "UploadInfo");

                let f = null;
                let ext = null;

                $.each(data.result, function (index, file) {
                    f = file[0];

                    if (!file.error) {
                        ++uploadComplete;
                        $bar.text('上傳進度：' + Math.round(100. * uploadComplete / uploadCount).toString() + "%");

                        $('#' + field).val(f.name);
                        $(input).attr('data-current', f.name);
                        $uploadInfo.attr('value', JSON.stringify(f));

                        if (autoSubmit === "1") {
                            $.post($form.attr("action"), $form.serialize(), function (err, data) {
                                ++submitComplete;

                                if (submitComplete === uploadComplete) {
                                    unblock();
                                    if (autoRefresh === "1") {
                                        window.clearTimeout(reloadTimeout);
                                        reloadTimeout = window.setTimeout(function () {
                                            window.setTimeout(reload, 1000);
                                            $bar.text('上傳完成，頁面將重新載入。');
                                        }, 1000);
                                    } else {
                                        window.setTimeout(function () {
                                            $bar.addClass("hide");
                                            $bar.text("");
                                        }, 1000);
                                        $bar.text("上傳完成");
                                    }
                                }
                            });
                        } else {
                            ext = f.name.split('.').pop();

                            if (uploadCount === uploadComplete) {
                                unblock();
                                if (autoRefresh === "1") {
                                    window.clearTimeout(reloadTimeout);
                                    window.setTimeout(function () {
                                        window.setTimeout(reload, 1000);
                                        $bar.text('上傳完成，頁面將重新載入。');
                                    }, 1000);
                                } else {
                                    window.setTimeout(function () {
                                        $bar.addClass("hide");
                                        $bar.text("");
                                    }, 1000);
                                    $bar.text("上傳完成");
                                }
                            }

                            $a.empty();
                            ext = ext.toLowerCase();
                            if ($.inArray(ext, EXT_IMAGE) !== -1) {
                                $a.attr('data-fancybox', '1');
                                $a.attr('target', '_blank');
                                $a.attr('href', f.url);
                                $a.html('<img class="img-responsive" id="' + name + '-display" src="' + f.url + '" />');
                                $displayTool.removeClass('hide');
                                $displayText.html(originText)
                            } else if ($.inArray(ext, EXT_VIDEO) !== -1) {
                                $a.attr('data-fancybox', '1');
                                $a.attr('target', '_blank');
                                $a.attr('href', f.url);
                                $a.html('<video width="400" controls>' +
                                    '<source id="' + name + '-display" src="' + f.url + '" type="video/' + ext + '">' +
                                    'Your browser does not support HTML5 video.' +
                                    '</video>');
                                $displayTool.addClass('hide');
                                $displayText.html('');
                            } else {
                                $a.unbind('data-fancybox');
                                $a.unbind('target');
                                $a.attr('href', f.url);
                                $a.html('<div class="oi oi-file">' + f.name + '</div>');
                                $displayTool.addClass('hide');
                                $displayText.html('');
                            }

                            $preview.removeClass('hide');
                            $preview.addClass('card');
                        }
                    } else {
                        $bar.text('上傳失敗');
                        alert(f.error);
                    }
                });
            },
            fail: function (e, data) {
                let $bar = $('#' + this.getAttribute('name') + '-bar');

                ++uploadComplete;

                window.setTimeout(function () {
                    $bar.addClass('hide').text('');
                }, 1000);
                $bar.text(data.errorThrown);
            },
        });
    });

    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });

    $(document).bind('dragover', function (e) {
        let timeout = window.dropZoneTimeout;
        let $hoveredDropZone = $(e.target).closest($dropZones);

        if (timeout) {
            clearTimeout(timeout);
        } else {
            $dropZones.addClass('bgc-light-gray');
        }
        $dropZones.not($hoveredDropZone).removeClass('bgc-light-gray');
        $hoveredDropZone.addClass('bgc-light-gray');
        window.dropZoneTimeout = setTimeout(function () {
            window.dropZoneTimeout = null;
            $dropZones.removeClass('bgc-light-gray');
        }, 100);
    });

    $dropZones.click(function () {
        $('#' + this.getAttribute('data-target')).trigger('click');
    });

    $('.js-crop-confirm').click(function () {
        let fileId = this.getAttribute('data-target');
        let input = document.getElementById(fileId);

        let field = input.getAttribute('data-field');
        let first = input.getAttribute('data-first');
        let auto = input.getAttribute('data-auto');
        let url = input.getAttribute('data-url');
        let category = input.getAttribute('data-category');
        let current = input.getAttribute('data-current');
        let subtype = input.getAttribute('data-subtype');

        let $a = $('#' + fileId + '-display-a');
        let $preview = $('#' + fileId + '-preview');
        let $origin = $('#' + fileId + '-origin');

        let formData = new FormData();
        let subName = null;

        cropper.croppie('result', {
            type: 'blob',
            size: 'original'
        }).then(function (blob) {
            formData.append('param_name', fileId);
            formData.append($('meta[name=csrf-param]').prop('content'), $('meta[name=csrf-token]').prop('content'));
            formData.append('category', category);

            if (first === "true" || auto !== "1") {
                formData.append('current', '');
            } else {
                formData.append('current', current);
            }

            if (auto === '1') {
                formData.append('auto', auto);
                formData.append(fileId, blob);
            } else {
                subName = $origin.attr('data-file-name');
                formData.append(fileId, blob, subName.replace(/\..*/, "") + '_' + subtype);
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                submit: function () {
                    ++cropCount;
                    block();
                },
                success: function (data) {
                    ++cropComplete;

                    if (cropCount === cropComplete) {
                        unblock();
                    }

                    $a.empty();
                    $a.attr('href', data[fileId][0].s3Url + '?t=' + new Date().getTime());
                    $a.html('<img id="' + fileId + '-display" class="img-responsive" src="' + data[fileId][0].s3Url + '?t=' + new Date().getTime() + '" />');
                    $(input).attr('data-current', data[fileId][0].name);
                    $("#" + field).val(data[fileId][0].name);

                    if ($preview.hasClass("hide")) {
                        $preview.removeClass("hide");
                        $preview.addClass("card");
                    }
                    if (first === "1") {
                        $(input).removeAttr("data-first");
                    }
                },
                error: function (e) {
                    alert('上傳失敗，請稍後再試');
                    console.error(e);
                    formData.destroy();
                }
            });
            $.fancybox.close();
        });
    });

    $('.js-crop-cancel').click(function () {
        $.fancybox.close();
    });

    $('.js-img-delete').click(function () {
        if (confirm('確定刪除?')) {
            let name = this.getAttribute('data-target');
            let csrfName = $('meta[name=csrf-param]').prop('content');
            let csrfToken = $('meta[name=csrf-token]').prop('content');

            let $input = $('#' + name);
            let $preview = $('#' + name + '-preview');
            let $field = $('#' + $input.attr('data-field'));

            let params = {
                category: $input.attr('data-category'),
                name: $field.val()
            };

            params[csrfName] = csrfToken;
            $.post('/upload/delete', params, function (data) {
                if (data.code === '000') {
                    $field.val('');
                    $preview.addClass('hide');
                    $preview.removeClass('card');
                } else {
                    alert('Delete failed, please try again later');
                }
            }, 'json');
        }
    });

    $('.js-img-crop').click(function () {
        let name = this.getAttribute('data-target');
        let crop = this.getAttribute('data-crop');

        let $crop = $('#' + name + '-crop');
        let $e = $('#' + name + '-origin');
        let $input = $('#' + $e.attr('data-target'));
        let src = $e.attr('data-img');

        let thisCropOption = null;

        let ratioW = $input.attr('data-crop-ratio-window');

        if (src) {
            switch (crop) {
                case 'w' :
                    thisCropOption = $.extend(DEFAULT_CROP_OPTION, {
                        viewport: {
                            width: CROP_PIXEL_BASIS,
                            height: CROP_PIXEL_BASIS / ratioW,
                        },
                    });
                    break;
                default :
                    thisCropOption = DEFAULT_CROP_OPTION;
                    break;
            }

            cropper = $crop.croppie(thisCropOption);
            cropper.croppie('bind', {
                url: src,
            }).then(function () {
                $crop.croppie('setZoom', 0)
            });

            $.fancybox.open($crop, {
                afterClose: function (instance, current) {
                    cropper.croppie('destroy');
                },
                touch: false,
            });
        }
    });

    $('.js-crop-ratio').change(function () {
        let cropType = this.getAttribute("data-crop-type");
        let fileId = this.getAttribute("data-target");
        let $upload = $('#' + fileId + '.js-upload');

        $upload.attr('data-crop-ratio-' + cropType, this.value);
    });
})(jQuery);