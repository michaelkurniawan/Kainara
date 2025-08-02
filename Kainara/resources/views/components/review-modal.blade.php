<style>
    /* Kode CSS Anda tidak ada perubahan */
    #reviewModal .modal-content { overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); background-color: #ffffff; }
    #reviewModal .modal-header { background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 30px 25px; }
    #reviewModal .modal-title { font-family: 'Ancizar Serif', serif; font-weight: bold; color: #343a40; font-size: 1.4rem; }
    #reviewModal .modal-body { padding: 30px 25px; }
    #stars { font-size: 2.2rem; color: #ccc; margin-top: 10px; display: flex; justify-content: center; gap: 5px; }
    #stars .fa-star { cursor: pointer; transition: color 0.3s ease-in-out, transform 0.2s ease-in-out; }
    #stars .fa-star:hover { transform: translateY(-2px); }
    #stars .fa-star.fas { color: #ffc107; }
    #rating-text { display: block; margin-top: 10px; font-size: 0.9rem; color: #6c757d; }
    #reviewModal #comment { padding: 12px 15px; min-height: 100px; resize: vertical; font-size: 1rem; border: 1px solid #ced4da; margin-bottom: 15px; }
    #reviewModal #comment::placeholder { color: #adb5bd; }
    #reviewModal #comment:focus { border-color: #B6B09F; box-shadow: 0 0 0 0.25rem rgba(182, 176, 159, 0.25); }
    .btn-custom-gold { background-color: #B39C59; border-color: #AD9D6D; transition: all 0.2s ease-in-out; border-radius: 0 !important; }
    .btn-custom-gold:hover { background-color: #c9b071; border-color: #B39C59; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
    .btn-outline-secondary { border-color: #ced4da; color: #6c757d; transition: all 0.2s ease-in-out; border-radius: 0 !important; }
    .btn-outline-secondary:hover { background-color: #e9ecef; border-color: #adb5bd; color: #495057; }
    #reviewModal .modal-footer-epi { padding: 15px 25px; }
</style>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Submit Review & Complete Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewForm" method="POST" action="{{ route('reviews.store') }}">
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
                <div class="modal-footer-epi d-flex justify-content-end align-items-center border-top-0 py-3 px-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary font-serif-regular px-4 py-2 me-3" id="skipReviewButton">Skip</button>
                    <button type="submit" class="btn btn-primary font-serif-medium px-4 py-2 btn-custom-gold me-2">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewModal = document.getElementById('reviewModal');
        const reviewForm = document.getElementById('reviewForm');
        const starsContainer = document.getElementById('stars');
        const ratingInput = document.getElementById('review_rating_input');
        const ratingText = document.getElementById('rating-text');
        const skipReviewButton = document.getElementById('skipReviewButton');
        let selectedRating = 0;

        // --- Star Rating Functionality ---
        const updateStars = (rating) => {
            const stars = starsContainer.querySelectorAll('.fa-star');
            stars.forEach(star => {
                const starRating = parseInt(star.dataset.rating);
                if (starRating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
            ratingInput.value = rating;
            if (rating > 0) {
                ratingText.textContent = `You rated ${rating} out of 5 stars.`;
            } else {
                ratingText.textContent = 'Click on stars to rate';
            }
        };

        starsContainer.addEventListener('mouseover', function(e) {
            if (e.target.classList.contains('fa-star')) {
                const hoveredRating = parseInt(e.target.dataset.rating);
                updateStars(hoveredRating);
            }
        });

        starsContainer.addEventListener('mouseout', function() {
            updateStars(selectedRating);
        });

        starsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('fa-star')) {
                selectedRating = parseInt(e.target.dataset.rating);
                updateStars(selectedRating);
            }
        });

        // --- Modal Initialization and Reset ---
        reviewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button ? button.getAttribute('data-bs-order-id') : null;
            const reviewOrderIdInput = document.getElementById('review_order_id');
            if (reviewOrderIdInput) {
                reviewOrderIdInput.value = orderId;
            }
            reviewForm.reset();
            selectedRating = 0;
            updateStars(0);
        });

        skipReviewButton.addEventListener('click', function(e) {
            e.preventDefault();
            const skipInput = document.createElement('input');
            skipInput.type = 'hidden';
            skipInput.name = 'skip_review';
            skipInput.value = '1';
            reviewForm.appendChild(skipInput);
            
            reviewForm.submit();
        });

        reviewForm.addEventListener('submit', function(e) {
            const isSubmitReviewButton = e.submitter && e.submitter.id !== 'skipReviewButton';

            if (isSubmitReviewButton && selectedRating === 0) {
                e.preventDefault(); 
                alert('Please provide a rating (1-5 stars) or click "Skip".');
            }
        });
    });
</script>