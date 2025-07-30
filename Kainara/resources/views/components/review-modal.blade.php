<style>
    /* Base Modal Styling */
    #reviewModal .modal-content {
        overflow: hidden; /* Ensures no content overflows rounded corners */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        background-color: #ffffff; /* Clean white background */
    }

    #reviewModal .modal-header {
        background-color: #f8f9fa; /* Light grey header */
        border-bottom: 1px solid #e9ecef;
        padding: 30px 25px;
    }

    #reviewModal .modal-title {
        font-family: 'Ancizar Serif', serif; /* Consistent with main page font */
        font-weight: bold;
        color: #343a40;
        font-size: 1.4rem;
    }

    #reviewModal .modal-body {
        padding: 30px 25px;
    }

    #stars {
        font-size: 2.2rem; /* Slightly larger stars */
        color: #ccc; /* Default empty star color */
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 5px; /* Space between stars */
    }

    #stars .fa-star {
        cursor: pointer;
        transition: color 0.3s ease-in-out, transform 0.2s ease-in-out;
    }

    #stars .fa-star:hover {
        transform: translateY(-2px); /* Slight lift on hover */
    }

    #stars .fa-star.fas {
        color: #ffc107; /* Filled star color - warm yellow */
    }

    #rating-text {
        display: block; /* Ensure it takes its own line */
        margin-top: 10px;
        font-size: 0.9rem;
        color: #6c757d;
    }

    #reviewModal #comment {
        padding: 12px 15px;
        min-height: 100px;
        resize: vertical; /* Allow vertical resizing */
        font-size: 1rem;
        border: 1px solid #ced4da;
        margin-bottom: 15px; /* Reduced space below comment box */
    }

    #reviewModal #comment::placeholder {
        color: #adb5bd;
    }

    #reviewModal #comment:focus {
        border-color: #B6B09F; /* Highlight color on focus */
        box-shadow: 0 0 0 0.25rem rgba(182, 176, 159, 0.25);
    }

    #reviewModal .btn {
        border-radius: 0px;
        font-weight: 600;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    #reviewModal .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }

    #reviewModal .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    #reviewModal .btn-submit-review {
        background-color: #B6B09F; /* Custom primary color */
        border-color: #B6B09F;
        color: #fff;
    }

    #reviewModal .btn-submit-review:hover {
        background-color: #9c9685; /* Darker shade on hover */
        border-color: #9c9685;
    }

    #reviewModal .btn-submit-review:disabled {
        background-color: #a7a7a7; /* Gray when disabled */
        border-color: #a7a7a7;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Adjusted modal-footer padding */
    #reviewModal .modal-footer {
        padding: 15px 25px; /* Reduced padding */
    }
</style>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Added modal-lg class here --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Submit Review & Complete Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewForm">
                @csrf
                <input type="hidden" name="order_id" id="review_order_id">
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <label for="rating" class="form-label fs-5 fw-bold text-dark mb-2">Your Rating</label>
                        <div id="stars">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="review_rating_input" value="0">
                        <small class="form-text" id="rating-text">Click on stars to rate</small>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label fw-bold text-dark">Your Comment (Optional)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Share your experience and thoughts about the product and service..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-review">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>