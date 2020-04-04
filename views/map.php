<div id="map"></div>

<div class="modal fade mapModal" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body container">
                <div class="row userInfo mt-2">
                    <div class="col-12 name"></div>
                    <div class="col-12 mt-2 tel"></div>
                    <div class="col-12 mt-2 address"></div>
                    <div class="col-12 mt-2 comments"></div>
                    <div class="col-12 mt-4 bascinetMaterial bascinet">Gotowe przyłbice <span></span></div>
                    <div class="col-12 mt-4 bascinetMaterial material">Zapotrzebowanie na materiały <span></span></div>
                </div>
                <hr class="interaction">
                <div class="row mt-3 interaction">
                </div>
            </div>
            <div class="modal-footer container">
                <div class="row">
                    <div class="form-group col-12 mb-0 text-right">
                        <a href="" class="navigate mr-3" target="_blank">Nawiguj</a>
                        <button type="button" class="btn btn-red" id="driver-confirmation">Zapisz</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade mapModal" id="hosMagModal" tabindex="-1" role="dialog" aria-labelledby="hosMagLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hosMagLabel">Informacje</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body container">
                <div class="row mt-3">
                    <div class="col-12 name"></div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 description"></div>
                </div>
                <div class="row mt-5">
                    <div class="col-12 bascinetMaterial"></div>
                </div>
                <hr class="interaction">
                <div class="row mt-3">
                    <div class="form-group col-12 mb-3 interaction"></div>
                </div>
            </div>
            <div class="modal-footer container">
                <div class="row">
                    <div class="form-group col-12 mb-0 text-right">
                        <a href="" class="navigate mr-3" target="_blank">Nawiguj</a>
                        <button type="button" class="btn btn-red" id="hosMag" data-type=""></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="legend">
    <div class="element frozen">Nieaktywny <span class="circle"></span></div>
    <div class="element material">Potrzebuje materiałów <span class="circle"></span></div>
    <div class="element bascinet">Gotowe przyłbice <span class="circle"></span></div>
    <div class="element hospital">Szpitale <span class="circle"></span></div>
    <div class="element magazine">Magazyny <span class="circle"></span></div>
</div>
