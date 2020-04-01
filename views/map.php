<div id="map"></div>

<div class="modal fade" id="modalPopup" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <p class="md-form mb-1">Wybierz akcję</p>
                    <select name="pets" id="driverAction-select">
                        <option value="collect">Odbiór</option>
                        <option value="deliver">Dostarczenie</option>
                        <option value="collectDeliver">Odbiór i dostarczenie</option>
                    </select>

                <p class="readyBascinetsNo-form">Potwierdź odebranie <span></span> przyłbic</p>

                <p class="md-form mb-1">Ile materiałów?</p>
                    <input type="text" id="MaterialsNeededNo">

                <p class="md-form mb-1">Termin</p>
                    <input type="text" id="driverDate">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="driver-confirmation">Potwierdź</button>
            </div>
        </div>
    </div>
</div>

<div class="legend">
    <div class="element frozen">Niekatywny <span class="circle"></span></div>
    <div class="element material">Potrzebuje materiałów <span class="circle"></span></div>
    <div class="element bascinet">Gotowe przyłbice <span class="circle"></span></div>
    <div class="element both">Gotowe przyłbice i potrzebuje materiałów <span class="circle"></span></div>
    <div class="element hospital">Szpitale <span class="circle"></span></div>
</div>
