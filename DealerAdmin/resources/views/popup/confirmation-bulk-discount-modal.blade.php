    <div class="modal delete_popup fade" id="conforamtion_bulk-discount_popup_modal" tabindex="-1" role="dialog" aria-labelledby="discount_delete_ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <input type="hidden" id="DeleteSingleDiscountPopupUUid" name="discount_uuid" value="">
                </div>
                <div class="modal-body">
                    <p><span id="DeleteDiscountPopupName"></span>Your selected discount exceeds the maximum allowable limit of $5,000 and has been adjusted to meet program requirements</p>
                    <p><span ></span>Do you want continue ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rift-soft gcss-button btn-secondary btn-grey" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-bulk-discount-btn" class="btn rift-soft gcss-button confirm-discount-btn" data-dismiss="modal">Continue</button>
                </div>
            </div>
        </div>
    </div>