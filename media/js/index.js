var index = function () {
    var $preLoader = $("#preloader");

    $(window).on('load', function () {
        hidePreloader();
    });

    $(function () {
        var $body = $("body"),
            winFocus = true,
            online = true;

        $(document).trigger('scroll');

        if (!mobileAndTabletCheck() && $body.height() > $(window).height()) {
            $body.addClass('desktop');
        }

        (function menu() {
            var $body = $("body"),
                $navbar = $("nav.navbar"),
                $toggler = $navbar.find(".navbar-toggler"),
                $collapse = $navbar.find(".navbar-collapse"),
                $notifications = $navbar.find(".notifications"),
                clearNew;

            $toggler.on('click', function () {
                if ($collapse.hasClass('show')) {
                    $body.removeClass("collapsed");
                } else {
                    $body.addClass("collapsed");
                }
            });

            $notifications.find("#notifications").on('click', function () {
                clearNew = setTimeout(function () {
                    $.ajax({
                        url: "/ajax/notifications.php?ajax=true",
                        data: {action: "read"},
                        type: "POST",
                        dataType: "JSON",
                        success: function (data) {
                            if (data.success) {
                                $notifications.find("#notifications .num").html("0").removeClass('new');
                            }
                        }
                    });
                }, 15E2);
            })
                .on('blur', function () {
                    clearTimeout(clearNew);
                });
            $notifications.find('.dropdown-menu a.dropdown-item').on('click', function (e) {
                var href = $(this).attr('href'),
                    path = window.location.pathname,
                    parts = href.split("#");

                shouldPrevent = (href === "#");
                if (shouldPrevent) {
                    e.preventDefault();
                }
                shouldPrevent |= (href.indexOf("#") >= 0 && parts[0] == path);
                if (shouldPrevent) {
                    e.stopPropagation();
                    if ($body.hasClass('collapsed')) {
                        $toggler.trigger('click');
                    }
                }
            });
        })();

        (function chooseCompetition() {
            $(".dropdown #change + .dropdown-menu .dropdown-item").on('click', function () {
                $(this).closest("form").submit();
            });
        })();

        (function hiddenSurprise() {
            if ($("section.loginRegister").length <= 0) {
                var $modal = $("#surpriseModal"),
                    $cheatingModal = $("#cheatingModal"),
                    $modalImg = $modal.find("img.img-responsive"),
                    $srp = $("[class^=srp]"),
                    allowChange = false;

                $cheatingModal.on('show.bs.modal', function () {
                    var $modalImage = $cheatingModal.find(".modal-content .modal-body .img-responsive"),
                        min = 1,
                        max = 2,
                        random = Math.floor(Math.random() * (max - min + 1)) + min;

                    $modalImage.attr('src', $modalImage.data('src') + random + $modalImage.data('ext'));
                });

                $srp.on('click', function (e) {
                    e.preventDefault();

                    var $this = $(this);

                    if ($this.hasClass("active")) {
                        var className = $this.attr('class').split(" ")[0],
                            number = className.substr(3);

                        allowChange = true;
                        $modalImg.attr("src", $modalImg.data("srp") + number + $modalImg.data("ext"));
                        $modal.modal('show');

                        $.ajax({
                            url: "/ajax/picture_found.php?ajax=true",
                            data: {number: number},
                            type: "POST",
                            dataType: "JSON",
                            success: function (data) {
                                if (data.success) {
                                    if (data.modal && data.modalId) {
                                        var $newModal = $("#" + data.modalId);

                                        $modal.on('hidden.bs.modal', function () {
                                            if ($newModal.length <= 0) {
                                                $body.append($(data.modal));
                                                $newModal = $("#" + data.modalId);
                                            }
                                            $newModal.modal('show');
                                            $newModal.on('shown.bs.modal', function () {
                                                $modal.off('hidden.bs.modal');
                                            });
                                        });
                                    }
                                }
                                switch (data.success) {
                                    case 'foundAll':
                                        allowChange = true;
                                        $(".pagin .pagination .page-item [class^=srp]").addClass('active');
                                        break;
                                }
                            },
                            error: function () {
                                displayToast("Ajax error", "danger");
                            }
                        });
                    }
                });

                var srpLinkObserver = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.attributeName === "class") {
                                var $element = $(mutation.target);

                                if (allowChange) {
                                    allowChange = false;
                                } else {
                                    $element.removeClass("active");

                                    $cheatingModal.modal('show');
                                }
                            }
                        });
                    }),
                    srpImageObserver = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.attributeName === "src") {
                                if (allowChange) {
                                    allowChange = false;
                                } else {
                                    allowChange = true;
                                    $modalImg.attr('src', "");
                                    $modal.modal('hide');
                                    setTimeout(function () {
                                        $cheatingModal.modal('show');
                                    }, 6E2);
                                }
                            }
                        });
                    });

                if (typeof $modalImg[0] === 'Node') {
                    srpImageObserver.observe($modalImg[0], {
                        attributes: true
                    });
                }

                $srp.each(function (i, e) {
                    srpLinkObserver.observe(e, {
                        attributes: true
                    });
                });
            }
        })();

        (function showPreload() {
            $(document).on('click', '.preload', function (e) {
                var $this = $(e);
                showPreloader();
                setTimeout(function () {
                    hidePreloader();
                }, 5E3);
            })
                .on('keydown', function (e) {
                    if (e.which === 116) {
                        showPreloader();
                    }
                });
        })();

        (function settings() {
            var $settings = $("body.settings");
            if ($settings.length) {
                var $form = $settings.find(".form form"),
                    $darkTheme = $form.find("input[name='darkTheme']"),
                    $pushNotifications = $form.find("input[name='pushNotifications']"),
                    $parallax = $form.find("input[name='parallax']"),
                    $parallaxUrl = $form.find("input[name='parallaxUrl']"),
                    $avatar = $form.find("input[name='avatar']"),
                    $avatarLabel = $form.find("label[for='avatar']"),
                    $gravatar = $form.find("input[name='gravatar']"),
                    $avatarParent = $avatar.parent(),
                    $profile = $avatarParent.find("#profile"),
                    $sourceModal = $settings.find("#source");

                $form.on('submit', function (e) {
                    e.preventDefault();
                    saveSettings($form);
                });

                $darkTheme.on('change', function () {
                    var $link = $("link.theme");
                    if ($(this).prop('checked')) {
                        $link.attr('href', $link.data('dark'));
                        $("nav.navbar").removeClass("navbar-light bg-light").addClass("navbar-dark bg-dark");
                    } else {
                        $link.attr('href', $link.data('light'));
                        $("nav.navbar").removeClass("navbar-dark bg-dark").addClass("navbar-light bg-light");
                    }
                });

                $pushNotifications.on('change', function () {
                    var $this = $(this);
                    if ($this.prop('checked')) {
                        if (!Push.Permission.has() && Push.Permission.get() !== 'denied') {
                            Push.Permission.request(subscribePushManager(), notifyDenied());
                        } else if (!Push.Permission.has()) {
                            $this.prop('checked', false);
                            notifyDenied();
                        }
                    }
                });

                $parallax.on('change', function () {
                    if ($(this).prop('checked')) {
                        $parallaxUrl.parent().fadeIn();
                    } else {
                        $parallaxUrl.parent().fadeOut();
                    }
                });

                var $iTag = $parallaxUrl.parent().find("i.fa");

                if ($iTag.hasClass("active")) {
                    $iTag.removeClass("active");
                }

                $parallaxUrl.on('blur', function () {
                    $iTag.removeClass("active");
                });

                $gravatar.on('change', function () {
                    var $avatarParent = $avatar.parents(".avatarParent");
                    if ($(this).prop('checked')) {
                        $avatarParent.fadeOut();
                    } else {
                        $avatarParent.fadeIn();
                    }
                });

                $avatarLabel.on('click', function (e) {
                    e.preventDefault();

                    $sourceModal.modal('show');
                });

                var openCameraModal = false;
                $sourceModal.on('click', '.option.capture', function (e) {
                    e.preventDefault();

                    openCameraModal = true;
                    $sourceModal.modal('hide');
                })
                    .on('click', '.option.upload', function (e) {
                        e.preventDefault();

                        $sourceModal.modal('hide');
                        $avatar.trigger('click');
                    })
                    .on('hidden.bs.modal', function () {
                        if (openCameraModal) {
                            $cameraModal.modal("show");

                            //navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia);

                            navigator.mediaDevices.getUserMedia({
                                video: true, audio: false
                            })
                                .then(function (stream) {
                                    cameraStream = stream;
                                    video.srcObject = stream;
                                    video.play();
                                })
                                .catch(function (error) {
                                    console.log('error', error);
                                });

                            openCameraModal = false;
                        }
                    });

                var uploadedFile = "";
                $avatar.on('change', function () {
                    var input = this,
                        reader = new FileReader();

                    if (input.files && input.files[0]) {
                        uploadedFile = input.files;
                        reader.onload = function (e) {
                            $profile.attr('src', e.target.result);

                            $avatarParent.find('.removePic').removeClass("hidden");
                            $avatarParent.find("input[name='save_avatar']").val("true");
                        };
                        reader.readAsDataURL(uploadedFile[0]);
                    } else {
                        $avatar.prop('files', uploadedFile);
                    }
                });

                var droppedFiles = false;
                $avatarParent.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                })
                    .on('dragover dragenter', function () {
                        $avatarParent.addClass('drag');
                    })
                    .on('dragleave dragend drop', function () {
                        $avatarParent.removeClass('drag');
                    })
                    .on('drop', function (e) {
                        droppedFiles = e.originalEvent.dataTransfer.files;
                        if (droppedFiles.length >= 2) {
                            removePic($avatarParent.find('.removePic'));
                            displayToast($avatar.data('tmf'), 'warning');
                        } else {
                            $avatar.prop('files', droppedFiles);
                            $avatar.trigger('change');
                        }
                    });

                $avatarParent.find('.removePic').on('click', function () {
                    removePic($(this));
                });

                var removePic = function ($removeBtn) {
                    $removeBtn.addClass("hidden");

                    $avatar.val("");
                    $avatarParent.find("input[name='save_avatar']").val("");
                    $profile.attr('src', $avatar.data('default'));
                    uploadedFile = "";
                    $avatar.prop('files', uploadedFile);
                };

                $settings.find("#recountPoints").on('click', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: "/ajax/recalculate_points.php?ajax=true",
                        type: "POST",
                        dataType: "JSON",
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.alert) {
                                displayToast(data.message, data.alert);
                            }
                        },
                        error: function () {
                            displayToast("Ajax error", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                });

                var $cameraModal = $settings.find("#camera"),
                    $takenPicture = $cameraModal.find("img.preview"),
                    video = $cameraModal.find('video.camera_stream')[0],
                    $videoContainer = $(video).parents(".box"),
                    $pictureContainer = $takenPicture.parents(".box"),
                    $takePictureBtn = $cameraModal.find(".capture"),
                    $tryAgainBtn = $cameraModal.find(".again"),
                    $saveBtn = $cameraModal.find(".save"),
                    imageDataURL,
                    cameraStream;

                $saveBtn.hide();
                $tryAgainBtn.hide();

                $takePictureBtn.on('click', function (e) {
                    e.preventDefault();

                    var $takePictureBtn = $(this),
                        hidden_canvas = $cameraModal.find('canvas.snapshot')[0],
                        width = video.videoWidth,
                        height = video.videoHeight,
                        context = hidden_canvas.getContext('2d');

                    hidden_canvas.width = width;
                    hidden_canvas.height = height;

                    context.drawImage(video, 0, 0, width, height);

                    imageDataURL = hidden_canvas.toDataURL('image/jpg');

                    $takenPicture.attr('src', imageDataURL);
                    $videoContainer.hide();
                    $pictureContainer.show();
                    $takePictureBtn.hide();
                    $tryAgainBtn.show();
                    $saveBtn.show();
                });

                $tryAgainBtn.on('click', function (e) {
                    e.preventDefault();

                    $videoContainer.show();
                    $pictureContainer.hide();
                    $takePictureBtn.show();
                    $tryAgainBtn.hide();
                    $saveBtn.hide();
                });

                $cameraModal.on('hidden.bs.modal', function () {
                    var track = cameraStream.getTracks()[0];
                    $takenPicture.attr('src', "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=");
                    track.stop();
                    $tryAgainBtn.trigger('click');
                });

                $saveBtn.on('click', function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: "/ajax/save_picture.php?ajax=true",
                        type: "POST",
                        data: {file: imageDataURL},
                        dataType: "JSON",
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.success) {
                                $profile.attr('src', data.picture + "?date=" + Date.now());
                                $avatarParent.find('.removePic').removeClass("hidden");
                                $avatarParent.find("input[name='save_avatar']").val("capture");
                            } else {
                                if (data.alert) {
                                    displayToast(data.message, data.alert);
                                }
                            }
                            $cameraModal.modal('hide');
                        },
                        error: function () {
                            displayToast("Wystąpił błąd podczas przetwarzania zdjęcia, przepraszamy.", "danger");
                            $cameraModal.modal('hide');
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                });

                var $changePswdModal = $settings.find("#changePasswordModal"),
                    $changePswdBtn = $settings.find("#changePassword"),
                    $changePswdForm = $changePswdModal.find('form'),
                    $saveChngPswdBtn = $changePswdModal.find("button[type='submit']");

                $changePswdBtn.on('click', function (e) {
                    e.preventDefault();

                    $changePswdModal.modal('show');
                });

                $saveChngPswdBtn.on('click', function (e) {
                    e.preventDefault();
                    var form = $changePswdForm.get(0),
                        formData = new FormData(form);

                    formData.append('changePassword', true);

                    $.ajax({
                        url: $changePswdForm.attr('action') + "?ajax=true",
                        type: "POST",
                        data: formData,
                        dataType: "JSON",
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.success) {
                                $changePswdModal.modal('hide');
                            } else {
                                var fields = data.field.split(",");
                                $(fields).each(function (i, e) {
                                    $changePswdForm.find('input[name="' + e + '"]').removeClass('valid').addClass('invalid').trigger('focus');
                                });
                            }
                            displayToast(data.message, data.alert);
                        },
                        error: function () {
                            displayToast("Wystąpił błąd podczas próby zmiany hasła. Proszę odświeżyć stronę i spróbować ponownie", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                });

                $changePswdModal.on('hidden.bs.modal', function (e) {
                    $changePswdForm.find('input').val("").trigger("blur");
                });

                var $groupsModal = $settings.find("#groupsModal"),
                    $groupNameInput = $groupsModal.find("input#groupName"),
                    $groupCodeInput = $groupsModal.find("input#groupCode"),
                    $joinCreateBtn = $groupsModal.find("button#createJoin");

                $groupCodeInput.on('keyup', function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $joinCreateBtn.trigger('click');
                        return false;
                    }
                });

                $groupNameInput.on('keyup', function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $joinCreateBtn.trigger('click');
                        return false;
                    }
                    if ($groupNameInput.val() === "") {
                        $joinCreateBtn.removeClass("create");
                    } else {
                        $joinCreateBtn.addClass("create");
                    }
                });

                $joinCreateBtn.on('click', function (e) {
                    e.preventDefault();
                    var name = $groupNameInput.val(),
                        code = $groupCodeInput.val();

                    if (name === "") {
                        if (code === "") {
                            displayToast("Proszę podać hasło do grupy, aby móc do niej dołączyć", "warning");
                            return false;
                        }
                        $.ajax({
                            url: "/ajax/groups.php?ajax=true",
                            type: "POST",
                            data: {action: "join", code: code},
                            dataType: "JSON",
                            beforeSend: function () {
                                $joinCreateBtn.addClass("loading");
                            },
                            success: function (data) {
                                if (data.success) {
                                    var $newRow = $(data.newRow).insertBefore($groupsModal.find("table tr.form"));
                                    $newRow.fadeIn();
                                    $groupNameInput.val("").trigger('blur');
                                    $groupCodeInput.val("").trigger('blur');
                                }
                                if (data.alert) {
                                    displayToast(data.message, data.alert);
                                }
                            },
                            error: function () {
                                displayToast("Ajax error", "danger");
                            },
                            complete: function () {
                                $joinCreateBtn.removeClass("loading");
                            }
                        });
                    } else {
                        if (code === "") {
                            displayToast("Proszę podać hasło dla nowej grupy o nazwie '" + name + "'", "warning");
                            return false;
                        }
                        $.ajax({
                            url: "/ajax/groups.php?ajax=true",
                            type: "POST",
                            data: {action: "new", name: name, code: code},
                            dataType: "JSON",
                            success: function (data) {
                                if (data.success) {
                                    if (data.success) {
                                        var $newRow = $(data.newRow).insertBefore($groupsModal.find("table tr.form"));
                                        $newRow.fadeIn();
                                        $groupNameInput.val("").trigger('blur');
                                        $groupCodeInput.val("").trigger('blur');
                                    }
                                }
                                if (data.alert) {
                                    displayToast(data.message, data.alert);
                                }
                            },
                            error: function () {
                                displayToast("Ajax error", "danger");
                            }
                        });
                    }
                });


                $groupsModal.find("table").on('click', 'button.leave', function (e) {
                    e.preventDefault();
                    var $row = $(this).parents('tr'),
                        id = $row.data('id');

                    $.ajax({
                        url: "/ajax/groups.php?ajax=true",
                        type: "POST",
                        data: {action: "leave", id: id},
                        dataType: "JSON",
                        success: function (data) {
                            if (data.success) {
                                $row.fadeOut(function () {
                                    $row.remove();
                                })
                            }
                            if (data.alert) {
                                displayToast(data.message, data.alert);
                            }
                        },
                        error: function () {
                            displayToast("Ajax error", "danger");
                        }
                    });
                });

                $groupsModal.find("table").on('click', 'button.delete', function (e) {
                    e.preventDefault();

                    if (!confirm("UWAGA! Usunięcie grupy jest nieodwracalne. Spoowoduje to, że wszyscy użytkownicy, którzy są do niej przypisani zostaną z niej usunięci. Czy na pewno chcesz to zrobić?")) {
                        return false;
                    }
                    var $row = $(this).parents('tr'),
                        id = $row.data('id');

                    $.ajax({
                        url: "/ajax/groups.php?ajax=true",
                        type: "POST",
                        data: {action: "delete", id: id},
                        dataType: "JSON",
                        success: function (data) {
                            if (data.success) {
                                $row.fadeOut(function () {
                                    $row.remove();
                                })
                            }
                            if (data.alert) {
                                displayToast(data.message, data.alert);
                            }
                        },
                        error: function () {
                            displayToast("Ajax error", "danger");
                        }
                    });
                });
            }
        })();

        (function register() {
            var $register = $("body.register");
            if ($register.length) {
                var $forgotPasswordModal = $("#forgotPassword"),
                    $resetPasswordForm = $forgotPasswordModal.find("form"),
                    $logRegForm = $register.find(".logRegForm form");

                $logRegForm.on('submit', function (e) {
                    e.preventDefault();
                    logRegAjax($(this));
                });

                $forgotPasswordModal.on('show.bs.modal', function () {
                    var email = $register.find("input[name='lemail']").val();
                    if (email.length) {
                        $forgotPasswordModal.find("#femail").val(email);
                    }
                })
                    .on('shown.bs.modal', function () {
                        $forgotPasswordModal.find("#femail").trigger('focus');
                    });

                $resetPasswordForm.on('submit', function (e) {
                    e.preventDefault();
                    var $form = $(this),
                        formData = new FormData($form.get(0));

                    formData.append('forgotPassword', true);

                    $.ajax({
                        url: $form.attr('action') + "?ajax=true",
                        type: "POST",
                        data: formData,
                        dataType: "JSON",
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.success) {
                                $forgotPasswordModal.modal('hide');
                            }
                            displayToast(data.message, data.alert);
                        },
                        error: function () {
                            displayToast("Ajax error", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                });

                var logRegAjax = function ($form) {
                    var formData = new FormData($form.get(0));

                    $.ajax({
                        url: $form.attr('action') + "?ajax=true",
                        type: "POST",
                        data: formData,
                        dataType: "JSON",
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.success) {
                                location.href = "/";
                            }

                            if (data.alert) {
                                displayToast(data.message, data.alert);
                            }
                        },
                        error: function () {
                            displayToast("Ajax error", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                };

                // $(":input").inputmask();

                var map = L.map('addressMap').setView([50.0619474, 19.9368564], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                var marker = L.marker([50.0619474, 19.9368564], {draggable:'true'}).addTo(map);

                var $addressFinder = $register.find("#addressFinder"),
                    addMarker = setTimeout(function(){});
                $addressFinder.on('keyup', function (e) {
                    var address = $addressFinder.val();

                    clearTimeout(addMarker);
                    addMarker = setTimeout(function () {
                        $.get(location.protocol + '//nominatim.openstreetmap.org/search?format=json&q='+address, function(data){
                            if (typeof data[0] !== "undefined") {
                                var lat = data[0].lat,
                                    lng = data[0].lon;

                                bindMarker(lat, lng);
                            }
                        });
                    }, 1E3);
                });

                var bindMarker = function(lat, lng) {
                    var location = new L.LatLng(lat, lng);
                    map.removeLayer(marker);
                    marker.setLatLng(location);
                    marker.addTo(map);
                    // marker.trigger('dragging');
                    map.panTo(location);
                };
            }
        })();

        (function notifications() {
            setInterval(function () {
                $.ajax({
                    url: "/ajax/notifications.php?ajax=true",
                    type: "POST",
                    data: {action: 'get'},
                    dataType: "JSON",
                    success: function (data) {
                        if (data.success) {
                            var notifications = data.data;
                            for (var i in notifications) {
                                var $newNotification = $("<a>");
                                $newNotification.addClass("dropdown-item waves-effect waves-light active");
                                if (notifications[i].href != null) {
                                    $newNotification.attr('href', notifications[i].href);
                                    $newNotification.addClass("preload");
                                } else {
                                    $newNotification.attr('href', "#");
                                }
                                $newNotification.html(notifications[i].content);
                                $newNotification.prependTo("nav.navbar .notifications .dropdown-menu");
                                var $newNotificationsNum = $("nav.navbar #notifications span.num");
                                $newNotificationsNum.html(parseInt($newNotificationsNum.html()) + 1);
                                $newNotificationsNum.addClass("new");
                            }
                        }
                    }
                });
            }, 1E4);
        })();

        (function adminPrivileges() {
            var $body = $("body.privileges");
            if ($body.length) {
                var $form = $body.find("form.setPrivileges"),
                    $usersSelect = $form.find("#usersID"),
                    $privilegesSelect = $form.find("#level"),
                    usersSelectOptions = {
                        placeholder: "Wybierz użytkownika",
                        allowClear: true
                    },
                    privSelectOptions = {
                        placeholder: "Wybierz uprawnienie",
                        minimumResultsForSearch: -1,
                        allowClear: true
                    };

                $usersSelect.select2(usersSelectOptions);
                $privilegesSelect.select2(privSelectOptions);

                $usersSelect.on('change', function () {
                    var $option = $usersSelect.find("option:selected")
                    priv = $option.data('priv');

                    $privilegesSelect.val(priv);
                    $privilegesSelect.select2("destroy");
                    $privilegesSelect.select2(privSelectOptions);
                });

                $form.on('submit', function (e) {
                    e.preventDefault();

                    var form = $form.get(0),
                        formData = new FormData(form);

                    formData.append('savePrivileges', true);

                    $.ajax({
                        url: $form.attr('action') + "?ajax=true",
                        type: "POST",
                        data: formData,
                        dataType: "JSON",
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.success) {
                                var $option = $usersSelect.find("option:selected"),
                                    priv = $privilegesSelect.val(),
                                    text = $option.text(),
                                    pos = text.lastIndexOf("("),
                                    newText = text.substr(0, pos) + "(" + priv + ")";

                                $option.text(newText);
                                $option.data('priv', priv);
                                $option.attr('data-priv', priv);
                                $usersSelect.select2("destroy");
                                $usersSelect.select2(usersSelectOptions);
                            }
                            displayToast(data.message, data.alert);
                        },
                        error: function () {
                            displayToast("Ajax error", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                });
            }
        })();
    });

    function displayToast(message, alert, offset) {
        alert = alert || "danger";
        offset = (typeof offset != "undefined") ? offset : 3E3;

        var $toast = $("<div>").addClass("kjtoast hidden alert alert-" + alert);
        $toast.html(message);
        $toast.appendTo($("body .toastContainer"));

        $toast.fadeIn();

        if (offset > 0) {
            var hideToast = setTimeout(function () {
                $toast.fadeOut(function () {
                    $toast.remove();
                });
            }, offset);

            $toast.on('mouseenter', function () {
                clearTimeout(hideToast);
            })
                .on('mouseleave', function () {
                    hideToast = setTimeout(function () {
                        $toast.fadeOut(function () {
                            $toast.remove();
                        });
                    }, 3E3);
                })
                .on('click', function () {
                    clearTimeout(hideToast);
                    $toast.fadeOut(function () {
                        $toast.remove();
                    });
                });
        }

        return $toast;
    }

    function saveSettings($form) {
        var formData = new FormData($form.get(0));

        formData.append('saveSettings', true);

        $.ajax({
            url: $form.attr('action') + "?ajax=true",
            type: "POST",
            data: formData,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                showLoading();
            },
            success: function (data) {
                displayToast(data.message, data.alert);
            },
            error: function () {
                displayToast("Ajax error", "danger");
            },
            complete: function () {
                hideLoading();
            }
        });
    }

    function mobileAndTabletCheck() {
        var check = false;
        (function (a) {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    }

    function showLoading() {
        $("#loading").fadeIn();
    }

    function hideLoading() {
        $("#loading").fadeOut();
    }

    function showPreloader() {
        $preLoader.fadeIn();

        $("body").addClass('no_scroll');

        setTimeout(function () {
            hidePreloader();
        }, 7E3);
    }

    function hidePreloader() {
        $preLoader.fadeOut();

        var $body = $("body");
        $body.removeClass('no_scroll');
    }

    $.fn.isInViewport = function () {
        var $element = $(this),
            elementTop = $element.offset().top,
            elementBottom = elementTop + $element.outerHeight(),
            viewportTop = $(window).scrollTop() - 55,
            viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom && elementBottom < viewportBottom && elementTop > viewportTop;
    };
};

function loadScript(url, callback) {
    var head = document.getElementsByTagName('head')[0],
        script = document.createElement('script');

    script.type = 'text/javascript';
    script.src = JS_URL + "/" + url;

    script.onreadystatechange = callback;
    script.onload = callback;

    head.appendChild(script);
}

loadScript("external.min.js", index);