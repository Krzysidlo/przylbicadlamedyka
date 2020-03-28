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
                                displayToast("Nieznany błąd", "danger");
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
                var $form = $settings.find(".form form");

                $form.on('submit', function (e) {
                    e.preventDefault();

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
                            displayToast(data.message, data.alert);
                        },
                        error: function () {
                            displayToast("Nieznany błąd", "danger");
                        },
                        complete: function () {
                            hideLoading();
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
                            displayToast("Nieznany błąd", "danger");
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
                            displayToast("Nieznany błąd", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                };

                // $(":input").inputmask();
            }
        })();

        (function addressMap() {
            var $body = $("body.register, body.settings");
            if ($body.length) {
                var $addressInput = $body.find("#address"),
                    $addressFinder = $body.find("#addressFinder"),
                    addMarker = setTimeout(function () {
                    });

                var center = new L.LatLng(50.0619474, 19.9368564);
                var map = L.map('addressMap').setView(center, 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                var marker = L.marker(center, {draggable: 'true'}).addTo(map);

                var bindMarker = function (latLng) {
                    map.removeLayer(marker);
                    marker.setLatLng(latLng);
                    marker.addTo(map);
                    map.panTo(latLng);

                    $addressInput.val(latLng.lat + "," + latLng.lng);
                };

                if ($addressInput.val() !== "") {
                    var latLng = $addressInput.val().split(",");
                    bindMarker(new L.LatLng(latLng[0], latLng[1]));
                }

                marker.on('dragend', function (e) {
                    bindMarker(e.target._latlng);
                });

                $addressFinder.on('keyup', function (e) {
                    var address = $addressFinder.val();

                    clearTimeout(addMarker);
                    addMarker = setTimeout(function () {
                        $.get(location.protocol + '//nominatim.openstreetmap.org/search?format=json&q=' + address, function (data) {
                            if (typeof data[0] !== "undefined") {
                                var lat = data[0].lat,
                                    lng = data[0].lon;

                                bindMarker(new L.LatLng(lat, lng));
                            }
                        });
                    }, 1E3);
                });
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
                            displayToast("Nieznany błąd", "danger");
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