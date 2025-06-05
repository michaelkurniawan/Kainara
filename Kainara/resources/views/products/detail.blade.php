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
      width: 50px;
      height: 50px;
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
<body class="bg-white">
    <main class="container-fluid py-5 px-5 flex-grow-1">
        <div class="row g-5"> <!-- Add g-5 to increase spacing between columns -->
            <!-- Left: Product Image -->
            <div class="col-lg-5">
                <div class="bg-light d-flex align-items-center justify-content-center ratio ratio-1x1">
                    <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}" class="img-fluid object-fit-contain" />
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="col-lg-7">
                <h1 class="fw-bold mb-4">{{ $product['name'] }}</h1>

                <p class="text-muted text-justify" style="margin-bottom: 35px;">
                    Troso Weaving or Tenun Ikat Troso is a Jepara weaving craft precisely from Troso Village. Troso ikat weaving is a fabric woven from strands of weft or warp threads that were previously tied and dipped in natural dyes. The loom used is a non-machine loom.
                </p>

                <p class="fs-3 fw-bold mb-2">IDR {{ number_format($product['price'], 0, ',', '.') }}</p>

                <div class="d-flex align-items-center" style="margin-bottom: 35px;">
                    <div class="text-warning me-2 fs-4">★ ★ ★ ★ ★</div>
                    <span class="text-muted fs-5">| 5 Reviews</span>
                </div>

                <!-- Size Chart Trigger -->
                <p class="mb-3 fs-4 d-flex align-items-center gap-4 text-secondary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sizeChartModal">
                    <i class="fas fa-shirt fs-5"></i>
                    <span class="text-decoration-underline">Size Chart</span>
                </p>

                <!-- Size Buttons -->
                <div class="size-buttons">
                    <div class="d-flex gap-2">
                        <div class="d-flex align-items-center mb-5 fw-semibold gap-3 fs-5">
                            <span>Size</span>
                            <div class="d-flex py-2 w-55 gap-3 justify-content-between align-items-center">

                            @foreach (['S','M','L','XL'] as $size)
                                <button class="btn btn-outline-secondary rounded-0 btn-size">{{ $size }}</button>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>  

                <!-- Quantity & Buttons -->
                <div class="mb-4">
                    <div class="d-flex gap-2 mb-3">
                        <div class="d-flex border border-secondary px-3 py-2 w-50 justify-content-between align-items-center">
                            <button class="btn btn-link text-dark p-0 fw-bold rounded-0">-</button>
                            <span>1</span>
                            <button class="btn btn-link text-dark p-0 fw-bold rounded-0">+</button>
                        </div>
                        <button class="btn btn-outline-dark w-50 rounded-0 btn-lg">Add to Cart</button>
                    </div>
                    <button class="btn btn-dark w-100 rounded-0 btn-lg">Buy it now</button>
                </div>
            </div>
        </div>
    </main>

<!-- Size Chart Modal -->
<div class="modal fade" id="sizeChartModal" tabindex="-1" aria-labelledby="sizeChartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header px-5 py-4 border-bottom border-3">
        <h4 class="modal-title fs-4" id="sizeChartModalLabel">Size Chart</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap text-center">
          <img src="{{ asset('images/KemejaPanjang.png') }}" alt="Kemeja Panjang" class="img-fluid" style="max-width: 45%;">
          <img src="{{ asset('images/SizeKemejaPanjang.png') }}" alt="Size Kemeja Panjang" class="img-fluid" style="max-width: 45%;">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
