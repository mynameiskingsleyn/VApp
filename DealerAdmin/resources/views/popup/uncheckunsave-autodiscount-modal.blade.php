<!-- Discount Warning Popup Modal -->
    <div class="modal delete_popup auto_discount_warning fade" id="unsaved_uncheck_auto_discount_warning_modal" tabindex="-1" role="dialog" aria-labelledby="auto_discount_warning_modalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <input type="hidden" id="DeleteSingleDiscountPopupUUid" name="discount_uuid" value="">
                </div>
                <div class="modal-body">
                    <h6>Warning!</h6>
                    <p>Unsaved changes will be lost. Are you sure?</p>
                    <!-- <p><span>These discounts are over and above the FCA incentives.</span></p> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rift-soft gcss-button btn-secondary btn-grey" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn rift-soft gcss-button confirm-discount-delete-btn" data-dismiss="modal" onclick="uncheckdeleteEmptyDiscount()">OK</button>
                </div>
            </div>
        </div>
    </div>