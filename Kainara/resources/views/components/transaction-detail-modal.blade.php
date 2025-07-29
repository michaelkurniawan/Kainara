{{-- resources/views/components/transaction-detail-modal.blade.php --}}

<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bg-accent-primary text-white rounded-top-4">
                <h5 class="modal-title font-serif-semibold" id="transactionDetailModalLabel">
                    <i class="bi bi-receipt me-2"></i>Transaction Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="mb-4 p-3 rounded-3 bg-white shadow-sm">
                    <h6 class="text-accent-gold font-serif-medium mb-3">
                        <i class="bi bi-info-circle me-1"></i>Order Information
                    </h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order ID:</strong> <span id="modalOrderId"></span></p>
                            <p class="mb-1"><strong>Invoice:</strong> <span id="modalInvoice"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Date:</strong> <span id="modalOrderDate"></span></p>
                            <p class="mb-1"><strong>Status:</strong> <span id="modalOrderStatus" class="badge"></span></p>
                        </div>
                    </div>
                </div>

                <div class="mb-4 p-3 rounded-3 bg-white shadow-sm">
                    <h6 class="text-accent-gold font-serif-medium mb-3">
                        <i class="bi bi-geo-alt me-1"></i>Shipping Address
                    </h6>
                    <p class="mb-1" id="modalShippingNamePhone"></p>
                    <p class="mb-1" id="modalShippingAddress"></p>
                    <p class="mb-1" id="modalShippingCityProvince"></p>
                    <p class="mb-1" id="modalShippingCountryPostal"></p>
                </div>

                <div class="mb-4 p-3 rounded-3 bg-white shadow-sm">
                    <h6 class="text-accent-gold font-serif-medium mb-3">
                        <i class="bi bi-box-seam me-1"></i>Order Items
                    </h6>
                    <div id="modalOrderItems" class="list-group">
                        {{-- Items will be dynamically loaded here by JavaScript --}}
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 p-3 rounded-3 bg-white shadow-sm">
                    <h6 class="mb-0 font-serif-semibold">Total Amount:</h6>
                    <h5 class="mb-0 font-serif-semibold text-accent-gold" id="modalTotalAmount"></h5>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>