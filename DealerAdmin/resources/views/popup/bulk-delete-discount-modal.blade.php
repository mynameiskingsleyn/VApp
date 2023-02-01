    <div class="modal delete_popup" id="bulk_delete_popup_modal" tabindex="-1" role="dialog" aria-labelledby="delete_row_ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <input type="hidden" id="BulkDeleteDiscountPopupName" name="discount_name" value="">
                </div>
                <div class="modal-body">
                    <p>“<span id="BulkDeleteDiscountPopupText"></span>” will be deleted. Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rift-soft gcss-button btn-secondary btn-grey" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn rift-soft gcss-button confirm-delete-btn" onclick="deleteBulkDiscount()" data-dismiss="modal">Continue</button>
                </div>
            </div>
        </div>
    </div>