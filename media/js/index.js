var index = function () {
    var $preLoader = $("#preloader");

    $(window).on('load', function () {
        hidePreloader();
    });

    $(function () {

        var $body = $("body"),
            settingsMap,
            mapMarker;

        $(document).trigger('scroll');

        if (!mobileAndTabletCheck() && $body.height() > $(window).height()) {
            $body.addClass('desktop');
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

        (function index() {
            var $index = $("body.index");
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

                window.onpopstate = function(event) {
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
                var mymap = L.map('map').setView([50.0647, 19.9450], 25);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(mymap);

                $.ajax({
                    url: "/ajax/map/getInfo?ajax=true",
                    type: "POST",
                    data: "",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.success) {
                            onMapClick(data)
                        }
                    },
                    error: function () {
                        displayToast("Problem z załadowaniem pinezek", "danger");

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

                var modalBody = `
                <div class>
                    <p class="md-form mb-1">Wybierz akcję</p>
                        <select name="driverSelect" id="driverAction-select">
                            <option value="bascinet">Odbiór</option>
                            <option value="material">Dostarczenie</option>
                            <option value="bascinet,material">Odbiór i dostarczenie</option>
                        </select>

                    <p class="md-form mb-1">Dzień</p>
                        <input type="text" id="driverDateDay">

                    <p class="md-form mb-1">Godzina</p>
                        <input type="text" id="driverDateHour">
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="driver-confirmation">Potwierdź</button>
                </div>
                `

                function createBindPopup(
                                lat,
                                lng,
                                readyBascinetsNo,
                                MaterialsNeededNo,
                                additionalComments,
                                userName,
                                userTelNo,
                                userAddress,
                                frozen) {
                    var htmlElement = `<div>${userName}</div>
                                       <div>${userTelNo}</div>
                                       <div>${userAddress}</div>
                    `
                    if (readyBascinetsNo) {
                        htmlElement += '<div><b>Gotowe przyłbice:</b><br>' + readyBascinetsNo + '<br></div>';
                    }
                    if (MaterialsNeededNo) {
                        htmlElement += '<div><b>Zapotrzebowanie na materiały</b><br>' + MaterialsNeededNo + '<br></div>';
                    }
                    if (additionalComments) {
                        htmlElement += '<div><b>Komentarz</b><br>' + additionalComments + '<br></div>';
                    }
                    if (!frozen) {
                        htmlElement += modalBody
                    }
                    var googleMapsLink = generateGoogleMapsLink(lat, lng);
                    htmlElement += '<div><button type="button" class="btn btn-secondary" data-target="#googlemaps" onclick="location.href=\'' + googleMapsLink + '\';">' +
                        'MAPS LINK' +
                        '</button></div>';
                    return htmlElement
                }

                var openPopupUserId;

                mymap.on('popupopen', function (e) {
                    openPopupUserId = e.popup._source._myId;
                });


                $(document).on('click', "#driver-confirmation", function(e) {
                    e.preventDefault();
                    var actionType = $('#driverAction-select').val();
                        driverDateDay = $('#driverDateDay').val();
                        driverDateHour = $('#driverDateHour').val();
                    //    TODO HANDLE WITH RESPONSE - what if success wht if error
//                    sendConfirmedDriverData(openPopupUserId, actionType, driverDate)
                    displayToast(`Potwierdziłeś: ${actionType} w dniu ${driverDateDay} o godzinie ${driverDateHour}
                    i wysyłam do ${openPopupUserId}`, "success");
                });


                function generateGoogleMapsLink(lat, lng) {
                    //  https://maps.google.com/maps?q=50.0647,19.9450
                    return "https://maps.google.com/maps?q=" + lat + "," + lng;
                }

                function onMapClick(data) {
                    userData = data.requests;
                    for (var userId in userData) {
                        var latLng = userData[userId].latLng.split(','),
                            userName = userData[userId].name,
                            userTelNo = userData[userId].tel,
                            userAddress = userData[userId].address,
                            readyBascinetsNo = userData[userId].bascinet,
                            MaterialsNeededNo = userData[userId].material,
                            additionalComments = userData[userId].comments,
                            frozen = userData[userId].frozen,
                            iconUrl = defineIconColor(readyBascinetsNo, MaterialsNeededNo, frozen),
                            myIcon = createMyIcon(iconUrl),
                            htmlElement = createBindPopup(
                                                    latLng[0],
                                                    latLng[1],
                                                    readyBascinetsNo,
                                                    MaterialsNeededNo,
                                                    additionalComments,
                                                    userName,
                                                    userTelNo,
                                                    userAddress,
                                                    frozen),
                            marker = L.marker(latLng, {icon: myIcon}).bindPopup(htmlElement).addTo(mymap);
                         marker._myId = userId;
                    }
                    hospitalData = data.hospitals;
                    for (var hospitalId in hospitalData) {
                        var latLng = hospitalData[hospitalId].latLng.split(','),
                            hospitalName = hospitalData[hospitalId].name;
                            hospitalIcon = createMyIcon(IMG_URL + "/pin_hospital.png"),
                            htmlElement = `<div>${hospitalName}</div>`;
                            marker = L.marker(latLng, {icon: hospitalIcon}).bindPopup(htmlElement).addTo(mymap);
                        marker._myId = hospitalId;
                    }


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

                function sendConfirmedDriverData(userId, actionType, driverDate) {
                    $.ajax({
                        url: "/ajax/map/driverConfirmation?ajax=true",
                        type: "POST",
                        data: {userId: userId, actionType: actionType, date:driverDate},
                        dataType: "JSON",
                        success: function (data) {
                            console.log(data);
                            if (data.success) {

                            } else {
                            }
                        },
                        error: function () {
                        console.log('error!!!!!')
                        }
                    });
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
                    }, 15E2);
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