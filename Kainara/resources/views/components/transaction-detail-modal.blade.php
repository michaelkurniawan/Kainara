<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title font-serif-semibold" id="transactionDetailModalLabel">
                    <i class="bi bi-receipt me-2"></i>Transaction Details
                </h5>
                <button type="button" class="btn-close me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="info-section mb-4">
                    <h6 class="info-section-title">
                        <i class="bi bi-info-circle me-2"></i>Order Information
                    </h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <p class="detail-label">Order ID:</p>
                            <p class="detail-value" id="modalOrderId"></p>
                        </div>
                        <div class="col-md-6">
                            <p class="detail-label">Invoice:</p>
                            <p class="detail-value" id="modalInvoice"></p>
                        </div>
                        <div class="col-md-6">
                            <p class="detail-label">Order Date:</p>
                            <p class="detail-value" id="modalOrderDate"></p>
                        </div>
                        <div class="col-md-6">
                            <p class="detail-label">Status:</p>
                            <p class="detail-value"><span id="modalOrderStatus" class="badge"></span></p>
                        </div>
                    </div>
                </div>

                <div class="info-section mb-4">
                    <h6 class="info-section-title">
                        <i class="bi bi-geo-alt me-2"></i>Shipping Address
                    </h6>
                    <p class="detail-value" id="modalShippingNamePhone"></p>
                    <p class="detail-value" id="modalShippingAddress"></p>
                    <p class="detail-value" id="modalShippingCityProvince"></p>
                    <p class="detail-value" id="modalShippingCountryPostal"></p>
                </div>

                <div class="info-section mb-4">
                    <h6 class="info-section-title">
                        <i class="bi bi-box-seam me-2"></i>Order Items
                    </h6>
                    <div id="modalOrderItems" class="list-group list-group-flush">
                    </div>
                </div>

                <div class="summary-section d-flex justify-content-between align-items-center mt-4">
                    <h6 class="font-serif-semibold mb-0">Total Amount:</h6>
                    <h6 class="font-serif-bold text-accent-gold mb-0 fs-4" id="modalTotalAmount"></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --color-brand: #AD9D6C;
        --color-text-dark: #212529;
        --color-white: #ffffff;
        --color-border-light: #e9ecef;
        --color-subtle-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        
    }

    .modal-content {
        border-radius: 0 !important;
        overflow: hidden;
        border: none;
        box-shadow: var(--color-subtle-shadow);
        background-color: var(--color-white);
    }

    .modal-header {
        background-color: var(--color-white) !important;
        color: var(--color-text-dark);
        border-bottom: 1px solid var(--color-border-light);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* Added padding to modal header */
        padding: 1.5rem 2rem; 
    }

    .modal-header .modal-title {
        font-size: 1.8rem;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        color: var(--color-text-dark);
        line-height: 1;
    }

    .modal-header .modal-title i {
        color: var(--color-brand);
        font-size: 1.5rem;
        margin-right: 0.75rem;
    }

    .modal-header .btn-close {
        filter: none;
        opacity: 0.7;
        transition: opacity 0.2s ease;
        margin-top: 0;
        margin-right: -0.5rem;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        /* Added padding to modal body */
        padding: 2rem; 
    }

    .info-section {
        background-color: var(--color-white);
        padding: 2rem;
        border-radius: 0;
        border: 1px solid var(--color-border-light);
        box-shadow: none;
    }

    .info-section:not(:last-child) {
        margin-bottom: 1.5rem !important;
    }

    .info-section-title {
        font-family: var(--font-primary);
        font-weight: 600;
        font-size: 1.35rem;
        color: var(--color-text-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--color-border-light);
        display: flex;
        align-items: center;
        letter-spacing: 0.03em;
    }

    .info-section-title i {
        color: var(--color-brand);
        font-size: 1.25rem;
        margin-right: 0.75rem;
    }

    .detail-label {
        font-family: var(--font-secondary);
        font-weight: 500;
        color: var(--color-text-dark);
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
        opacity: 0.8;
    }

    .detail-value {
        font-family: var(--font-secondary);
        font-weight: 400;
        color: var(--color-text-dark);
        font-size: 1.05rem;
        margin-bottom: 1rem;
        word-wrap: break-word;
    }

    .text-accent-gold {
        color: var(--color-brand) !important;
    }

    #modalOrderStatus {
        padding: 0.4em 0.8em;
        font-size: 0.88em;
        font-weight: 600;
        vertical-align: middle;
        border-radius: 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-success { background-color: #28a745; color: white; }
    .badge-info { background-color: #17a2b8; color: white; }
    .badge-warning { background-color: #ffc107; color: #343a40; }
    .badge-danger { background-color: #dc3545; color: white; }
    .badge-secondary { background-color: #6c757d; color: white; }
    .badge-primary { background-color: var(--color-brand); color: white; }

    .list-group-flush .list-group-item {
        padding: 0.75rem 0;
        border-color: var(--color-border-light);
        color: var(--color-text-dark);
        font-family: var(--font-secondary);
        font-size: 1.05rem;
    }

    .summary-section {
        background-color: var(--color-white);
        padding: 1.75rem 2rem;
        border-radius: 0;
        border: 1px solid var(--color-border-light);
        box-shadow: none;
        margin-top: 2rem !important;
    }

    .summary-section h6 {
        font-size: 1.3rem;
        color: var(--color-text-dark);
    }

    .summary-section h5 {
        font-size: 2.2rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .modal-footer {
        display: none;
    }
</style>