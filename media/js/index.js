var index = function () {
    var $preLoader = $("#preloader");

    $(window).on('load', function () {
        hidePreloader();
    });

    $(function () {

        var $body = $("body"),
            settingsMap,
            mapMarker;

        (function serviceWorker() {
            if ($body.data('logged')) {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('/sw.js').then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, error => {
                        console.error('ServiceWorker registration failed: ', error);
                    });
                }
            }
        })();

        $(document).trigger('scroll');

        if (!mobileAndTabletCheck() && $body.height() > $(window).height()) {
            $body.addClass('desktop');
        }

        (function driverConfirmation() {
            let $confirmBtn = $("body .activityBox.trip button.confirm");

            $confirmBtn.on('click', function (e) {
                e.preventDefault();

                let $btn = $(this),
                    $box = $btn.parents(".activityBox.trip"),
                    frozenID = $btn.data('frozen'),
                    requestsID = $btn.data('requests'),
                    method = "both";

                if (frozenID === 0) {
                    method = "bascinet";
                } else if (requestsID === 0) {
                    method = "material";
                }
                console.log(method, frozenID, requestsID);

                $.ajax({
                    url: `/ajax/map/${method}?ajax=true`,
                    type: "POST",
                    data: {frozenID: frozenID, requestsID: requestsID},
                    dataType: "JSON",
                    beforeSend: function () {
                        showLoading();
                    },
                    success: function (data) {
                        if (data.success) {
                            $box.fadeOut(function () {
                                $(this).remove();
                            });
                        }

                        if (data.alert) {
                            displayToast(data.message, data.alert);
                        }
                    },
                    error: function () {
                        displayToast("Wystąpił nieznany błąd", "danger");
                    },
                    complete: function () {
                        hideLoading();
                    }
                });
            });
        })();

        (function index() {
            var $index = $("body.index, body.trips");
            if ($index.length) {
                var $cancelBtn = $index.find(".activityBox .cancel");

                $cancelBtn.on('click', function (e) {
                    e.preventDefault();

                    if (confirm("Czy na pewno chcesz anulować tę akcję?")) {
                        var $btn = $(this),
                            id = $btn.data('id'),
                            type = $btn.data('type'),
                            url = $btn.attr('href');

                        $.ajax({
                            url: url + "?ajax=true",
                            type: "POST",
                            data: {id: id, type: type},
                            dataType: "JSON",
                            beforeSend: function () {
                                showLoading();
                            },
                            success: function (data) {
                                if (data.success) {
                                    $btn.parents(".activityBox").fadeOut(function () {
                                        $(this).remove();
                                    });
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
                    }
                });
            }
        })();

        (function modals() {
            var $modals = $(".modal.functionModal");
            $modals.find("form").on('submit', function (e) {
                e.preventDefault();

                var $form = $(this),
                    formData = new FormData($form.get(0)),
                    $modal = $form.parents(".modal");

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
                            $modal.modal('hide');
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
            });

            $modals.find("input[type='range']").on('input', function () {
                var $input = $(this);

                $input.parents('.modal').find("label[for='" + $input.attr('id') + "'] span").html($input.val());
            });

            $modals.find("input").on('keydown', function (e) {
                if (!isNumberKey(e)) {
                    e.preventDefault();
                }
            });

            function isNumberKey(e) {
                if (e.shiftKey) {
                    return false;
                }

                var keyCode = (e.which) ? e.which : e.keyCode;

                if (keyCode === 8 || keyCode === 13 || ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105))) {
                    return true;
                }

                return false;
            }
        })();

        (function menu() {
            var $navbarTop = $("nav.navbar.fixed-top");
            if ($navbarTop.length) {
                var $navbarLeft = $("nav.navbar.fixed-left"),
                    $toggler = $navbarTop.find(".navbar-toggler");

                $toggler.on('click', function () {
                    $navbarLeft.toggleClass('compact');
                    if (!mobileAndTabletCheck()) {
                        if ($navbarLeft.hasClass('compact')) {
                            setCookie("leftMenu", true, 365);
                        } else {
                            setCookie("leftMenu", null);
                        }
                    }
                });

                if (mobileAndTabletCheck()) {
                    $(document).on('click', function (e) {
                        if (!$navbarLeft.hasClass("compact")) {
                            var $target = $(e.target);
                            if (!$target.hasClass("fixed-left") && !$target.parents(".fixed-left").length && !$target.hasClass("navbar-toggler") && $target.parents(".navbar-toggler").length <= 0) {
                                $navbarLeft.addClass("compact");
                            }
                        }
                    });
                }
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
                var $form = $settings.find("form"),
                    $editBtn = $form.find(".edit"),
                    $cancelBtn = $form.find(".cancel"),
                    $noConfirmBtn = $form.find(".no-confirm"),
                    $saveBtn = $form.find("[type='submit']"),
                    originalValues = [];

                setTimeout(function () {
                    toggleMapInteraction(false, settingsMap, mapMarker);
                }, 1E2);

                $noConfirmBtn.on('click', function (e) {
                    e.preventDefault();

                    displayToast("Aby móc edytować informacje, proszę potwierdzić adres e-mail");
                });

                $editBtn.on('click', function (e) {
                    e.preventDefault();

                    $.each($form.find('input:not(.locked)'), function () {
                        var $input = $(this),
                            name = $input.attr('name'),
                            value = $input.val();

                        $input.attr('readonly', false);

                        originalValues.push({
                            name: name,
                            value: value
                        });
                    });

                    toggleMapInteraction(true, settingsMap, mapMarker);

                    $(this).fadeOut(function () {
                        $cancelBtn.fadeIn();
                        $saveBtn.fadeIn();
                    });
                });

                $cancelBtn.on('click', function (e) {
                    e.preventDefault();

                    $.each(originalValues, function (i, e) {
                        var $input = $form.find("input[name='" + e.name + "']");

                        $input.val(e.value);
                        $input.attr('readonly', true);
                    });

                    toggleMapInteraction(false, settingsMap, mapMarker);

                    $saveBtn.fadeOut();
                    $(this).fadeOut(function () {
                        $editBtn.fadeIn();
                    });
                });

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
                            if (data.success) {
                                $form.find('input:not(.locked)').attr('readonly', true);
                                toggleMapInteraction(false, settingsMap, mapMarker);
                                $cancelBtn.fadeOut();
                                $saveBtn.fadeOut(function () {
                                    $editBtn.fadeIn();
                                });
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
                });

                var $sendConfirm = $settings.find("#sendConfirm");
                if ($sendConfirm.length) {
                    $sendConfirm.on('click', function (e) {
                        e.preventDefault();

                        $.ajax({
                            url: $sendConfirm.attr('href') + "?ajax=true",
                            type: "POST",
                            data: {},
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
                                displayToast("Nieznany błąd", "danger");
                            },
                            complete: function () {
                                hideLoading();
                            }
                        });
                    });
                }
            }
        })();

        (function register() {
            var $register = $("body.register, body.reset");
            if ($register.length) {
                var $chngView = $register.find("a.chngView"),
                    $forms = $register.find(".leftContainer form");

                var changeView = function (view, pushState) {
                    pushState = pushState | false;

                    if (view === "null") {
                        view = "register";
                    }

                    var $loginView = $register.find(".leftContainer.login"),
                        $forgotView = $register.find(".leftContainer.forgot"),
                        $currentView = $register.find(".leftContainer:visible"),
                        $targetView = $register.find(".leftContainer." + view),
                        pageName = document.title.split(" - ")[1];

                    if (pushState) {
                        window.history.pushState(view, '', '/' + view);
                    }

                    switch (view) {
                        case 'login':
                            document.title = "Zaloguj się - " + pageName;
                            break;
                        case 'register':
                            document.title = "Zarejestruj się - " + pageName;
                            break;
                        case 'forgot':
                            document.title = "Zapomniałem hasła - " + pageName;
                            $forgotView.find('input[type="email"]').val($loginView.find('input[type="email"]').val());
                            $currentView.fadeOut(function () {
                                $forgotView.fadeIn();
                            });
                            break;
                    }

                    if (view !== "forgot") {
                        $currentView.fadeOut(function () {
                            $targetView.fadeIn();
                        });
                    }
                };

                $chngView.on('click', function (e) {
                    e.preventDefault();

                    changeView($(this).data('view'), true);
                });

                window.onpopstate = function (event) {
                    changeView(JSON.stringify(event.state).replace(/['"]+/g, ''));
                };

                $forms.on('submit', function (e) {
                    e.preventDefault();

                    var $form = $(this),
                        formData = new FormData($form.get(0)),
                        parts = $form.attr('action').split("/"),
                        method = parts[3];

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
                                if (method === "resetPassword") {
                                    showPreloader();
                                    location.href = "/login";
                                } else if (method !== "forgot") {
                                    showPreloader();
                                    location.href = "/";
                                }
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
                });

            }
        })();

        (function map() {
            var $map = $("body.map");
            if ($map.length) {
                let myMap = L.map('map').setView([50.0647, 19.9450], 25),
                    today = new Date(Date.now()),
                    clickedElement,
                    usersID;

                //Fix for datepicker to hide on blur
                $(document).on('mousedown', function (e) {
                    clickedElement = $(e.target);
                })
                    .on('mouseup', function (e) {
                        clickedElement = null;
                    });

                //Default date for date picker (current day)
                today = ("0" + today.getDate()).slice(-2) + "." + ("0" + (today.getMonth() + 1)).slice(-2) + "." + today.getFullYear();

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(myMap);

                $.ajax({
                    url: "/ajax/map/getInfo?ajax=true",
                    type: "POST",
                    data: "",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.success) {
                            let mapInfo = JSON.parse(JSON.stringify(data));
                            mapInfo.requests = {};
                            for (let userID in data.requests) {
                                let requests = data.requests[userID];
                                for (let i in requests) {
                                    let request = requests[i];

                                    if (typeof mapInfo.requests[request.user_id] === "undefined") {
                                        if (request.frozen) {
                                            request.bascinet = 0;
                                            request.material = 0;

                                            mapInfo.requests[request.user_id] = request;
                                        } else {
                                            mapInfo.requests[request.user_id] = request;
                                        }
                                    } else {
                                        if (!request.frozen) {
                                            mapInfo.requests[request.user_id].bascinet += request.bascinet;
                                            mapInfo.requests[request.user_id].material += request.material;
                                        }
                                        mapInfo.requests[request.user_id].frozen &= request.frozen;
                                    }
                                }
                            }
                            onMapLoad(mapInfo);
                        }
                    },
                    error: function () {
                        displayToast("Wystąpił problem podczas ładowania pinezek", "danger");
                    }
                });

                function createMyIcon(iconUrl) {
                    return L.icon({
                        iconUrl: iconUrl,
                        iconSize: [43, 59],
                        iconAnchor: [21, 59],
                        popupAnchor: [0, -58],
                    });
                }

                function createBindPopup(info) {
                    var bascinetInfo = "",
                        materialInfo = "",
                        googleMapsLink = generateGoogleMapsLink(info.lat, info.lng);

                    googleMapsLink = `<a href="${googleMapsLink}" class="btn btn-white" target="_blank" data-target="#googlemaps">Nawiguj</a>`;
                    if (info.type === "hospital" || info.type === "magazine") {
                        if (info.bascinet) {
                            bascinetInfo = "<div class='col-12 mt-4 bascinetMaterial'>Potrzebne przyłbice <span>" + info.bascinet + "</span></div>";
                        }

                        if (info.material) {
                            materialInfo = "<div class='col-12 mt-4 bascinetMaterial'>Dostępne materiały <span>" + info.material + "</span></div>";
                        }
                        if (USER_PRV === 2) {
                            var type = "odbiór",
                                longType = "Potwierdź odbiór materiału z magazynu",
                                max = 600,
                                ineract = true;

                            if (!info.material) {
                                ineract = false;
                                longType = "Informacje";
                            }

                            let input = `
                                <label for="quantity">Ilość: <span class="quantity">50</span></label>
                                <input type="range" name="quantity" id="quantity" class="custom-range form-control" value="50" min="50" max="${max}" step="50">
                                `;

                            if (info.type === "hospital") {
                                if (info.bascinetOwn) {
                                    ineract = true;
                                    longType = "Potwierdź dostarczenie przyłbic do szpitala";
                                    input = `
                                    <label for="quantity" class="md-form mb-1">Ilość</label>
                                    <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Ilość" value="${info.bascinetOwn}" readonly>
                                    `;
                                }
                                type = "dostarczenie";
                                longType = "Informacje";
                            } else if (info.type === "hospital") {
                                ineract = false;
                            }

                            let interaction = "";
                            if (ineract) {
                                interaction = `
                                <hr>
                                <div class="row">
                                    <div class="form-group col-12 mb-3">
                                        <input type="hidden" name="pinID" value="${info.pinID}">
                                        ${input}
                                    </div>
                                    <div class="form-group col-12 mb-0 text-right">
                                        ${googleMapsLink}
                                        <button type="button" class="btn btn-red" id="hosMag" data-type="${info.type}">Potwierdź ${type}</button>
                                    </div>
                                </div>
                                `;
                            }

                            return `
                            <div class="popup container pin pb-4">
                                <div class="row">
                                    <div class="col-12 title">${longType}</div>
                                </div>
                                <div class="row userInfo mt-3">
                                    <div class="col-12 bascinetMaterial">${info.name}</div>
                                </div>
                                <div class="row userInfo mt-3">
                                    <div class="col-12 description">${info.description}</div>
                                </div>
                                <div class="row userInfo mt-3">
                                    ${bascinetInfo}
                                    ${materialInfo}
                                </div>
                                ${interaction}
                            </div>
                            `;
                        }
                        return `
                        <div class="popup container pin pb-4">
                            <div class="row">
                                <div class="col-12 title">Informacje</div>
                            </div>
                            <div class="row userInfo mt-3">
                                <div class="col-12 bascinetMaterial">${info.name}</div>
                            </div>
                            <div class="row userInfo mt-3">
                                <div class="col-12 description">${info.description}</div>
                            </div>
                            <div class="row userInfo mt-3">
                                ${bascinetInfo}
                                ${materialInfo}
                            </div>
                        </div>
                        `;
                    } else {
                        var comments = "",
                            action = "";

                        if (info.bascinet) {
                            bascinetInfo = '<div class="col-12 mt-4 bascinetMaterial">Gotowe przyłbice <span>' + info.bascinet + '</span></div>';
                        }

                        if (info.material) {
                            materialInfo = '<div class="col-12 mt-4 bascinetMaterial">Zapotrzebowanie na materiały <span>' + info.material + '</span></div>';
                        }

                        if (info.comments) {
                            comments = '<div class="col-12 comments">Uwagi <span>' + info.comments + '</span></div>';
                        }

                        if (bascinetInfo === "") {
                            action = `
                            <input type='hidden' name='action' value='material'>
                            <label class="md-form mb-1">
                                Akcja
                            </label>
                            <input type='text' value='Dostarczenie' class="form-control" readonly>
                            `;
                        } else if (materialInfo === "") {
                            action = `
                            <input type='hidden' name='action' value='bascinet'>
                            <label class="md-form mb-1">
                                Akcja
                            </label>
                            <input type='text' value='Odbiór' class="form-control" readonly>
                            `;
                        } else {
                            action = `
                            <label for="driverSelect" class="md-form mb-1">Wybierz akcję</label>
                            <select class="form-control" name="action" id="driverSelect">
                                <option value="bascinet">Odbiór</option>
                                <option value="material">Dostarczenie</option>
                                <option value="both" selected>Odbiór i dostarczenie</option>
                            </select>
                            `;
                        }

                        if (USER_PRV === 2) {
                            return `
                            <div class="popup container">
                                <div class="row">
                                    <div class="col-12 title">Zaplanuj transport</div>
                                </div>
                                <div class="row userInfo mt-3">
                                    <div class="col-12 name">${info.name}</div>
                                    <div class="col-12 mt-2 tel">${info.tel}</div>
                                    <div class="col-12 mt-2 address">${info.address}</div>
                                    ${bascinetInfo}
                                    ${materialInfo}
                                    ${comments}
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-12 mb-2">
                                        ${action}
                                    </div>
                                    <div class="form-group col-12 mb-2">
                                        <label for="driverDate" class="md-form mb-1">Dzień</label>
                                        <input type="text" id="driverDate" class="form-control" placeholder="Dzień" value="${today}" readonly>
                                    </div>
                                    <div class="form-group col-12 mb-3">
                                        <label for="driverTime" class="md-form mb-1">Godzina</label>
                                        <input type="time" id="driverTime" class="form-control" placeholder="Godzina" value="">
                                    </div>
                                    <div class="form-group col-12 mb-4 text-right">
                                        ${googleMapsLink}
                                        <button type="button" class="btn btn-red" id="driver-confirmation">Zapisz</button>
                                    </div>
                                </div>
                            </div>
                            `;
                        } else {
                            return `
                            <div class="popup container pb-4">
                                <div class="row">
                                    <div class="col-12 title">Informacje</div>
                                </div>
                                <div class="row userInfo mt-3">
                                    <div class="col-12 name">${info.name}</div>
                                    <div class="col-12 mt-2 tel">${info.tel}</div>
                                    <div class="col-12 mt-2 address">${info.address}</div>
                                    ${bascinetInfo}
                                    ${materialInfo}
                                    ${comments}
                                </div>
                            </div>
                            `;
                        }
                    }
                }

                //On request, hospital or magazine freeze
                $(document).on('click', "#driver-confirmation", function (e) {
                    e.preventDefault();
                    let action = $map.find('#requestModal [name="action"]').val(),
                        date = $map.find('#requestModal #driverDate').val(),
                        time = $map.find('#requestModal #driverTime').val();

                    if (time === "") {
                        displayToast("Proszę podać godzinę odbioru/dostarczenia", "warning");
                        return false;
                    }

                    $.ajax({
                        url: "/ajax/map/freezeRequest?ajax=true",
                        type: "POST",
                        data: {usersID: usersID, action: action, date: date, time: time},
                        dataType: "JSON",
                        beforeSend: function () {
                            showLoading();
                        },
                        success: function (data) {
                            if (data.success) {
                                setTimeout(function () {
                                    location.reload();
                                }, 1E3);
                            }

                            if (data.alert) {
                                displayToast(data.message, data.alert);
                            }
                        },
                        error: function () {
                            displayToast("Wystąpił nieznany błąd", "danger");
                        },
                        complete: function () {
                            hideLoading();
                        }
                    });
                })
                    .on('click', "#hosMag", function (e) {
                        e.preventDefault();

                        var $btn = $(this),
                            $modal = $btn.parents(".modal"),
                            type = $modal.find("input[name='type']").val(),
                            quantity = $modal.find("input[name='quantity']").val(),
                            pinsID = $modal.find("input[name='pinsID']").val();

                        if (isNaN(quantity) || quantity === "") {
                            displayToast("Proszę podać poprawną liczbę", "warning");
                            return false;
                        }

                        $.ajax({
                            url: "/ajax/map/hosMag?ajax=true",
                            type: "POST",
                            data: {type: type, quantity: quantity, pinsID: pinsID},
                            dataType: "JSON",
                            beforeSend: function () {
                                showLoading();
                            },
                            success: function (data) {
                                if (data.success) {
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1E3);
                                }

                                if (data.alert) {
                                    displayToast(data.message, data.alert);
                                }
                            },
                            error: function () {
                                displayToast("Wystąpił nieznany błąd", "danger");
                            },
                            complete: function () {
                                hideLoading();
                            }
                        });
                    })
                    .on('input', "#quantity[type='range']", function () {
                        var $input = $(this);

                        $input.parents('.modal').find("label[for='" + $input.attr('id') + "'] span").html($input.val());
                    })
                    .on('click', "#quantity[type='text']", function () {
                        displayToast("Obecnie nie można zmieniać ilości dostarczanej do szpitali", "warning");
                    });

                function generateGoogleMapsLink(lat, lng) {
                    //  https://maps.google.com/maps?q=50.0647,19.9450
                    // return "https://maps.google.com/maps?q=" + lat + "," + lng;
                    if ((navigator.platform.indexOf("iPhone") !== -1) || (navigator.platform.indexOf("iPod") !== -1) || (navigator.platform.indexOf("iPad") !== -1)) {
                        return "maps://www.google.com/maps/dir/?api=1&travelmode=driving&layer=traffic&destination=" + lat + "," + lng;
                    } else {
                        return "https://www.google.com/maps/dir/?api=1&travelmode=driving&layer=traffic&destination=" + lat + "," + lng;
                    }
                }

                function onMapLoad(data) {
                    let pins = data.pins,
                        userData = data.requests,
                        lat, lng, type, icon;

                    for (let usersID in userData) {
                        if (USER_PRV === 1 && USER_NAME !== userData[usersID].name) {
                            continue
                        }
                        [lat, lng] = userData[usersID].latLng.split(',');
                        let frozen = userData[usersID].frozen,
                            iconUrl = defineIconColor(userData[usersID].bascinet, userData[usersID].material, frozen),
                            info = {
                                type: "request",
                                name: userData[usersID].name,
                                tel: userData[usersID].tel,
                                address: userData[usersID].address,
                                bascinet: parseInt(userData[usersID].bascinet),
                                material: parseInt(userData[usersID].material),
                                comments: userData[usersID].comments,
                                usersID: usersID,
                                lat: lat,
                                lng: lng
                            },
                            userMarker = L.marker([lat, lng], {icon: createMyIcon(iconUrl)}).addTo(myMap);

                        if (!frozen) {
                            userMarker.on('click', () => {
                                openModal(info);
                            });
                        }
                    }

                    for (let pinsID in pins) {
                        [lat, lng] = pins[pinsID].latLng.split(',');
                        type = pins[pinsID].type;

                        switch (type) {
                            case "hospital":
                                icon = createMyIcon(IMG_URL + "/pin_hospital.png");
                                break;
                            case "magazine":
                                icon = createMyIcon(IMG_URL + "/pin_magazine.png");
                                break;
                            default:
                                icon = createMyIcon(IMG_URL + "/pin_frozen.png");
                                break;
                        }

                        let info = {
                            type: type,
                            name: pins[pinsID].name,
                            description: pins[pinsID].description,
                            bascinet: parseInt(pins[pinsID].bascinet),
                            material: parseInt(pins[pinsID].material),
                            bascinetOwn: parseInt(data.bascinet),
                            pinsID: pinsID,
                            lat: lat,
                            lng: lng
                        };
                        let hosMagMarker = L.marker([lat, lng], {icon: icon}).addTo(myMap);
                        hosMagMarker.on('click', () => {
                            openModal(info);
                        });
                    }
                }

                // function onMapLoad(data) {
                //     var userData = data.requests,
                //         latLng, htmlElement, popup, marker, icon;
                //
                //     for (var userId in userData) {
                //         if (USER_PRV === 1 && USER_NAME !== userData[userId].name) {
                //             continue
                //         }
                //         latLng = userData[userId].latLng.split(',');
                //         var additionalComments = userData[userId].comments,
                //             frozen = userData[userId].frozen,
                //             iconUrl = defineIconColor(userData[userId].bascinet, userData[userId].material, frozen),
                //             myIcon = createMyIcon(iconUrl);
                //
                //         htmlElement = createBindPopup({
                //             type: "request",
                //             name: userData[userId].name,
                //             tel: userData[userId].tel,
                //             address: userData[userId].address,
                //             lat: latLng[0],
                //             lng: latLng[1],
                //             bascinet: userData[userId].bascinet,
                //             material: userData[userId].material,
                //             comments: additionalComments,
                //         });
                //
                //         popup = L.popup({
                //             minWidth: 400,
                //             className: 'customPopup',
                //         }).setContent(htmlElement);
                //         marker = L.marker(latLng, {icon: myIcon}).addTo(myMap);
                //
                //         if (iconUrl !== IMG_URL + "/pin_frozen.png") {
                //             marker.bindPopup(popup);
                //         }
                //
                //         marker._myId = userId;
                //     }
                //
                //     var pins = data.pins;
                //
                //     for (var pinID in pins) {
                //         latLng = pins[pinID].latLng.split(',');
                //         var type = pins[pinID].type;
                //         if (type === "hospital") {
                //             icon = createMyIcon(IMG_URL + "/pin_hospital.png");
                //         } else if (type === "magazine") {
                //             icon = createMyIcon(IMG_URL + "/pin_magazine.png");
                //         }
                //
                //         htmlElement = createBindPopup({
                //             type: type,
                //             name: pins[pinID].name,
                //             description: pins[pinID].description,
                //             lat: latLng[0],
                //             lng: latLng[1],
                //             bascinet: pins[pinID].bascinet,
                //             material: pins[pinID].material,
                //             bascinetOwn: data.bascinet,
                //             pinID: pinID,
                //         });
                //
                //         var minWidth = 0,
                //             maxWidth = 300;
                //         if (USER_PRV === 2) {
                //             minWidth = 400;
                //             maxWidth = 0;
                //         }
                //
                //         popup = L.popup({
                //             minWidth: minWidth,
                //             maxWidth: maxWidth,
                //             className: 'customPopup',
                //         }).setContent(htmlElement);
                //
                //         marker = L.marker(latLng, {icon: icon}).bindPopup(popup).addTo(myMap);
                //         marker._myId = pinID;
                //     }
                // }

                function openModal(data) {
                    let type = data.type,
                        maxMatFromMag = 600,
                        $modal, $confirmBtn, $hrInteraction, $interaction, interaction;

                    switch (type) {
                        case 'hospital':
                        case 'magazine':
                            $modal = $map.find("#hosMagModal");

                            $confirmBtn = $modal.find(".modal-footer #hosMag");
                            $hrInteraction = $modal.find(".modal-body hr.interaction");
                            $interaction = $modal.find(".modal-body div.interaction");

                            $confirmBtn.hide();
                            $hrInteraction.hide();
                            $interaction.hide();

                            $modal.find(".modal-body .name").html(data.name);
                            $modal.find(".modal-body .description").html(data.description);
                            let $bascinetMaterial = $modal.find(".modal-body .bascinetMaterial");

                            interaction = "";

                            if (data.bascinet < 0) {
                                data.bascinet = 0;
                            }

                            if (data.material < 0) {
                                data.material = 0;
                            }

                            if (type === 'hospital') {
                                $bascinetMaterial.html(`Potrzebne przyłbice <span>${data.bascinet}</span>`);

                                if (data.bascinetOwn > 0) {
                                    interaction = `
                                    <label for="quantity" class="md-form mb-1">Ilość</label>
                                    <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Ilość" value="${data.bascinetOwn}" readonly>
                                    `;
                                    $confirmBtn.html(`Potwierdź dostarczenie`);
                                }
                            } else {
                                $bascinetMaterial.html(`Posiadane materiały <span>${data.material}</span>`);

                                if (data.material > 0) {
                                    interaction = `
                                    <label for="quantity">Ilość: <span class="quantity">50</span></label>
                                    <input type="range" name="quantity" id="quantity" class="custom-range form-control" value="50" min="50" max="${maxMatFromMag}" step="50">
                                    `;
                                    $confirmBtn.html(`Potwierdź dostarczenie`);
                                }
                            }
                            if ((type === "hospital" && data.bascinetOwn > 0) || (type === "magazine" && data.material > 0)) {
                                $interaction.html(`
                                <input type="hidden" name="type" value="${type}">
                                <input type="hidden" name="pinsID" value="${data.pinsID}">
                                ${interaction}
                                `);
                                $hrInteraction.show();
                                $interaction.show();
                                $confirmBtn.show();
                            }
                            break;
                        case 'request':
                            usersID = data.usersID;
                            $modal = $map.find("#requestModal");

                            $modal.find(".modal-header .modal-title").html("Zaplanuj transport");

                            $confirmBtn = $modal.find(".modal-footer #hosMag"),
                                $hrInteraction = $modal.find(".modal-body hr.interaction"),
                                $interaction = $modal.find(".modal-body div.interaction");

                            $confirmBtn.hide();
                            $hrInteraction.hide();
                            $interaction.hide();

                            $modal.find(".modal-body .name").html(data.name);
                            $modal.find(".modal-body .tel").html(`<a href="tel:${data.tel}">${data.tel}</a>`);
                            $modal.find(".modal-body .address").html(data.address);
                            $modal.find(".modal-body .bascinet span").html(data.bascinet);
                            $modal.find(".modal-body .material span").html(data.material);
                            if (data.bascinet <= 0) {
                                $modal.find(".modal-body .bascinet");
                            }
                            if (data.material <= 0) {
                                $modal.find(".modal-body .material");
                            }

                            if (USER_PRV === 1) {
                                $modal.find(".modal-header .modal-title").html("Informacje");
                            } else {
                                let action;

                                if (data.bascinet === 0) {
                                    action = `
                                    <input type='hidden' name='action' value='material'>
                                    <label class="md-form mb-1">
                                        Akcja
                                    </label>
                                    <input type='text' value='Dostarczenie' class="form-control" readonly>
                                    `;
                                } else if (data.material === 0) {
                                    action = `
                                    <input type='hidden' name='action' value='bascinet'>
                                    <label class="md-form mb-1">
                                        Akcja
                                    </label>
                                    <input type='text' value='Odbiór' class="form-control" readonly>
                                    `;
                                } else {
                                    action = `
                                    <label for="driverSelect" class="md-form mb-1">Wybierz akcję</label>
                                    <select class="form-control" name="action" id="driverSelect">
                                        <option value="bascinet">Odbiór</option>
                                        <option value="material">Dostarczenie</option>
                                        <option value="both" selected>Odbiór i dostarczenie</option>
                                    </select>
                                    `;
                                }
                                $interaction.html(`
                                <div class="form-group col-12 mb-2">
                                    ${action}
                                </div>
                                <div class="form-group col-12 mb-2">
                                    <label for="driverDate" class="md-form mb-1">Dzień</label>
                                    <input type="text" id="driverDate" class="form-control" placeholder="Dzień" value="${today}" readonly>
                                </div>
                                <div class="form-group col-12 mb-3">
                                    <label for="driverTime" class="md-form mb-1">Godzina</label>
                                    <input type="time" id="driverTime" class="form-control" placeholder="Godzina" value="">
                                </div>
                                `);
                                $hrInteraction.show();
                                $interaction.show();
                                $confirmBtn.show();
                                $map.find('.modal #driverDate').datepicker({
                                    autoclose: true,
                                    language: "pl",
                                    todayHighlight: true,
                                    todayBtn: "linked",
                                    maxViewMode: 2,
                                    startDate: today,
                                })
                                    .on('blur', function (e) {
                                        if (clickedElement === null) {
                                            $(this).datepicker('hide');
                                        }
                                    });
                            }
                            break;
                        default:
                            console.log(type);
                            break;
                    }

                    let $navigate = $modal.find(".modal-footer .navigate");

                    $navigate.attr('href', generateGoogleMapsLink(data.lat, data.lng));

                    if (USER_PRV === 1) {
                        $navigate.hide();
                    }

                    $modal.modal('show');
                }

                function defineIconColor(readyBascinetsNo, MaterialsNeededNo, frozen) {
                    if (frozen) {
                        //    GREY
                        return IMG_URL + '/pin_frozen.png';
                    } else if (readyBascinetsNo && MaterialsNeededNo) {
                        //    GREEN / RED
                        return IMG_URL + '/pin_both.png';
                    } else if (readyBascinetsNo && !MaterialsNeededNo) {
                        //    GREEN
                        return IMG_URL + '/pin_bascinet.png';
                    } else if (!readyBascinetsNo && MaterialsNeededNo) {
                        //    RED
                        return IMG_URL + '/pin_material.png';
                    }
                }
            }
        })();

        (function address() {
            var $body = $("body.address");
            if ($body.length) {
                var $pinNameInput = $body.find("#pinName"),
                    $form = $body.find("form");

                $("input[type='radio']").on("change", function () {
                    var num = parseInt($("input[type='radio']:checked").val());
                    switch (num) {
                        case 1:
                            $pinNameInput.attr('required', true);
                            $pinNameInput.parent().fadeIn();
                            break;
                        case 2:
                            $pinNameInput.attr('required', false);
                            $pinNameInput.parent().fadeOut();
                            break;
                    }
                });

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
                });
            }
        })();

        (function addressMap() {
            var $body = $("body.address, body.settings");
            if ($body.length) {
                var $locationInput = $body.find("[name='location']"),
                    $addressInputs = $body.find("input.address"),
                    addMarker = setTimeout(function () {
                    }),
                    $mapContainer = $body.find("#addressMap"),
                    lat, lng;

                var center = new L.LatLng(50.0619474, 19.9368564);
                settingsMap = L.map('addressMap').setView(center, 15);
                if (mobileAndTabletCheck()) {
                    settingsMap.dragging.disable();
                    settingsMap.tap.disable();
                }

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(settingsMap);

                mapMarker = L.marker(center, {draggable: 'true'}).addTo(settingsMap);

                var bindMarker = function (latLng) {
                    settingsMap.removeLayer(mapMarker);
                    mapMarker.setLatLng(latLng);
                    mapMarker.addTo(settingsMap);
                    settingsMap.panTo(latLng);

                    $locationInput.val(latLng.lat + "," + latLng.lng);
                };

                if ($locationInput.val() !== "") {
                    [lat, lng] = $locationInput.val().split(",");
                    bindMarker(new L.LatLng(lat, lng));
                }

                $locationInput.on('change', function () {
                    [lat, lng] = $locationInput.val().split(",");
                    bindMarker(new L.LatLng(lat, lng));
                });

                mapMarker.on('dragend', function (e) {
                    bindMarker(e.target._latlng);
                });

                $addressInputs.on('keyup', function (e) {
                    var address = [],
                        stringAddress = "";

                    $addressInputs.each(function (i, e) {
                        var addrPart = $(e).val();
                        if (addrPart !== "") {
                            address.push(addrPart);
                        }
                    });

                    stringAddress = address.join(" ");
                    $mapContainer.addClass("loading");

                    clearTimeout(addMarker);
                    addMarker = setTimeout(function () {
                        $.get(location.protocol + '//nominatim.openstreetmap.org/search?format=json&q=' + stringAddress, function (data) {
                            if (typeof data[0] !== "undefined") {
                                var lat = data[0].lat,
                                    lng = data[0].lon;

                                bindMarker(new L.LatLng(lat, lng));
                                $mapContainer.removeClass("loading");
                            } else {
                                displayToast("Nie znaleziono adresu, proszę wybrać odpowiedni punkt na mapie", "warning");
                                $mapContainer.removeClass("loading");
                            }
                        });
                    }, 1E3);
                });
            }
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

    function setCookie(name, val, days) {
        var expires;

        if (days) {
            var data = new Date();
            data.setTime(data.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + data.toGMTString();
        } else {
            expires = "";
        }

        document.cookie = name + "=" + val + expires + "; path=/";
    }

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

    function toggleMapInteraction(enable, map, marker) {
        enable = enable | false;
        if (enable) {
            if (!mobileAndTabletCheck()) {
                map.dragging.enable();
            }
            if (map.tap) {
                map.tap.enable();
            }
            map.touchZoom.enable();
            map.doubleClickZoom.enable();
            map.scrollWheelZoom.enable();
            map.boxZoom.enable();
            map.keyboard.enable();
            if (typeof marker !== "undefined") {
                marker.dragging.enable()
            }
        } else {
            map.dragging.disable();
            if (map.tap) {
                map.tap.disable();
            }
            map.touchZoom.disable();
            map.doubleClickZoom.disable();
            map.scrollWheelZoom.disable();
            map.boxZoom.disable();
            map.keyboard.disable();
            if (typeof marker !== "undefined") {
                marker.dragging.disable()
            }
            map.panTo(marker.getLatLng());
        }
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