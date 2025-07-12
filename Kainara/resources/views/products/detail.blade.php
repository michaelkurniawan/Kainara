<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Detail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
  <style>
    body {
      font-family: 'AncizarSerif', serif;
    }
    .btn-link {
      text-decoration: none !important;
    }
    .btn-size {
      width: 48px;
      height: 48px;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0;
    }
    .text-muted {
      font-size: 1.3rem;
    }
    .text-justify {
      text-align: justify;
    }
  </style>
</head>
<body class="bg-white text-dark d-flex flex-column min-vh-100">

<main class="container-fluid py-5 px-5 flex-grow-1">
  <div class="row g-5">
    <!-- Product Image -->
    <div class="col-lg-6 d-flex align-items-center justify-content-center">
      <img src="{{ $product->image }}" alt="{{ $product['name'] }}" class="img-fluid object-fit-contain" />

    </div>

    <!-- Product Info -->
    <div class="col-lg-6">
      <h1 class="fw-bold mb-3">{{ $product->name }}</h1>

      <div class="d-flex align-items-center text-secondary mb-4">
        <i class="fas fa-location-dot fs-6 me-3"></i>
        <span class="fs-5 d-flex align-items-center gap-2">
          <span>{{ $product->origin }}</span>

          @php
            $colors = $product->variants->pluck('color')->unique();
          @endphp

          @if ($colors->isNotEmpty())
            <div class="d-flex align-items-center gap-3 ms-2">
              <span class="ms-2 me-2">|</span>
              @foreach ($colors as $color)
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-circle d-inline-block" 
                        style="width: 16px; height: 16px; background-color: {{ strtolower($color) }}; border: 1px solid #999;">
                  </span>
                  <span class="text-capitalize ms-2">{{ $color }}</span>
                </div>
              @endforeach
            </div>
          @endif
        </span>
      </div>

      <p class="text-muted text-justify mb-4">
        {{ $product->description }}
      </p>

      <p class="fs-3 fw-bold mb-2">IDR {{ number_format($product->price, 0, ',', '.') }}</p>

      <div class="d-flex align-items-center mb-4">
        <div class="text-warning me-2 fs-4">★ ★ ★ ★ ★</div>
        <span class="text-muted fs-5">| 5 Reviews</span>
      </div>

      <!-- Size Chart Trigger -->
      <p class="mb-4 fs-6 d-flex align-items-center gap-2 text-secondary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sizeChartModal">
        <i class="fas fa-shirt fs-4"></i>
        <span class="text-decoration-underline fs-4 ms-2">Size Chart</span>
      </p>

      <!-- Size Buttons -->
      <div class="d-flex align-items-center mb-5 fw-semibold gap-3 fs-4">
        <span>Size</span>
        <div class="d-flex gap-3">
          @foreach (['XS','S','M','L','XL','XXL'] as $size)
            <button type="button" class="btn btn-outline-secondary rounded-0 btn-size">{{ $size }}</button>
          @endforeach
        </div>
      </div>

      <!-- Quantity & Actions -->
      <div class="mb-4" style="max-width: 60%;">
        <div class="d-flex gap-2 mb-3">
          <div class="d-flex border border-secondary px-3 py-2 justify-content-between align-items-center" style="width: 50%;">
            <button type="button" class="btn btn-link text-dark p-0 fw-bold rounded-0 btn-minus" style="font-size: 1.5rem;">-</button>
            <span id="quantity-display" class="fs-4">1</span>
            <button type="button" class="btn btn-link text-dark p-0 fw-bold rounded-0 btn-plus" style="font-size: 1.5rem;">+</button>
          </div>
          <button class="btn border-secondary rounded-0 btn-lg" style="width: 50%;">Add to Cart</button>
        </div>

        <button class="btn btn-dark rounded-0 btn-lg w-100">Buy it now</button>
      </div>
    </div>
  </div>
</main>

<!-- Size Chart Modal -->
<div class="modal fade" id="sizeChartModal" tabindex="-1" aria-labelledby="sizeChartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content px-5">
      <div class="modal-header mt-3 border-bottom border-3">
        <h4 class="modal-title fs-4" id="sizeChartModalLabel">Size Chart</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center align-items-center py-4 gap-5 flex-wrap text-center">
          <img src="{{ asset('images/KemejaPanjang.png') }}" alt="Kemeja Panjang" class="img-fluid me-3" style="max-width: 45%;">
          <img src="{{ asset('images/SizeKemejaPanjang.png') }}" alt="Size Kemeja Panjang" class="img-fluid" style="max-width: 45%;">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    let selectedSize = null;
    let quantity = 1;

    // Size selection
    const sizeButtons = document.querySelectorAll('.btn-size');
    sizeButtons.forEach(button => {
      button.addEventListener('click', function () {
        // reset all
        sizeButtons.forEach(btn => btn.classList.remove('bg-secondary', 'text-white'));
        // select this
        this.classList.add('bg-secondary', 'text-white');
        selectedSize = this.innerText;
      });
    });

    // Quantity control
    const minusBtn = document.querySelector('.btn-minus');
    const plusBtn = document.querySelector('.btn-plus');
    const quantityDisplay = document.querySelector('#quantity-display');

    minusBtn.addEventListener('click', function () {
      if (quantity > 1) {
        quantity--;
        quantityDisplay.innerText = quantity;
      }
    });

    plusBtn.addEventListener('click', function () {
      quantity++;
      quantityDisplay.innerText = quantity;
    });
  });
</script>

</body>
</html>
