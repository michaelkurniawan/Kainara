<div class="modal fade" id="sizeChartModal" tabindex="-1" aria-labelledby="sizeChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content px-5">
            <div class="modal-header mt-3">
                <h4 class="modal-title fs-4" id="sizeChartModalLabel">
                    @if(isset($product) && isset($product->gender))
                        Women's Dress Size Chart
                    @else
                        Women's Dress Size Chart
                    @endif
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center py-4 gap-5 flex-wrap text-center">
                    <img src="{{ asset('images/sizechart/Dress.png') }}" alt="Gambar Gaun Wanita" class="img-fluid me-5" style="max-width: 45%;">
                    <img src="{{ asset('images/sizechart/SizeDress.png') }}" alt="Tabel Ukuran Gaun Wanita" class="img-fluid" style="max-width: 45%;">
                </div>
            </div>
        </div>
    </div>
</div>