<div id="map"></div>

<div class="modal fade" id="modalPopup" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Siema! Co dostarczysz?</h5>
            </div>
            <div class="modal-body">

                <p class="md-form mb-1">Wybierz akcję</p>
                    <select name="pets" id="driverAction-select">
                        <option value="">--Odbiór / Dostarczenie--</option>
                        <option value="collect">Odbiór</option>
                        <option value="deliver">Dostarczenie</option>
                        <option value="collectDeliver">Odbiór i dostarczenie</option>
                    </select>

                <p class="md-form mb-1">Ile przyłbic?</p>
                    <input type="text" id="readyBascinetsNo">

                <p class="md-form mb-1">Ile materiałów?</p>
                    <input type="text" id="MaterialsNeededNo">

                <p class="md-form mb-1">Termin</p>
                    <input type="text" id="driverDate">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                <button type="button" class="btn btn-primary" id="driver-confirmation">Potwierdź</button>
            </div>
        </div>
    </div>
</div>
