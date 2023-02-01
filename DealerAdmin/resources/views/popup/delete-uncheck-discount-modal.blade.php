<div class="modal delete_popup fade" id="uncheck_rule_discount_delete_popup_modal" tabindex="-1" role="dialog" aria-labelledby="discount_delete_ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <input type="hidden" id="uncheckDeleteSingleDiscountPopupUUid" name="discount_uuid" value="">
            </div>
            <div class="modal-body">
                <p>“<span id="uncheckDeleteDiscountPopupName"></span>” will be deleted. Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn rift-soft gcss-button btn-secondary btn-grey" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn rift-soft gcss-button" onclick="uncheckruleDeleteDiscountforVIN()">Continue</button>
            </div>
        </div>
    </div>
</div>