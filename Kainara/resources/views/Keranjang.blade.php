<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <title>Keranjang Page</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #F8F5F0;
      color: #333;
    }

    .btn-gold {
      background-color:rgb(0, 0, 0); /* warna emas */
      color: white;
      border: none;
    }

    .btn-gold:hover {
      background-color:#d4af37; /* warna hover */
      color: white;
    }

    .img-shoppingcart-title {
      width: 100%;
      margin-bottom: 2rem;
    }

    .product-card {
      border: 1px solid #ddd;
      background-color: #fff;
    }

    .btn-outline-secondary {
      width: 32px;
      height: 32px;
      padding: 0;
    }

    .btn-link {
      font-size: 1.25rem;
    }

    .subtotal-box {
      background-color: #fff;
      border: 1px solid #ddd;
      padding: 1.5rem;
    }

    @media (max-width: 992px) {
      .row.flex-lg-row {
        flex-direction: column !important;
      }
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

  <section>
    <img src="css/shoppingcart.png" alt="Shopping Cart Title" class="img-shoppingcart-title">
  </section>
      
  <section>
    <div class="container mt-2">
      <div class="row">
        <!-- Kolom kiri (produk) -->
        <div class="col-md-8">
          <!-- isi produk -->
          <div class=" mb-2 p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div style="margin-left: 6rem; color:rgb(136, 139, 142);">Product name</div>
              <div style="margin-left: 18rem; color:rgb(136, 139, 142);">Price</div>
              <div style="margin-left: 1rem; color:rgb(136, 139, 142);">Quantity</div>
              <div style="margin-right: 2rem; color:rgb(136, 139, 142);">Total price</div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="row d-flex flex-lg-row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
          <!-- Product Card -->
          <div class="card product-card mb-3">
            <div class="card-body d-flex align-items-center">
              <div class="me-3">
                <div class="bg-dark" style="width: 80px; height: 80px;"></div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">Nama Product</h6>
                <small class="text-muted">Size / Color</small>
              </div>
              <div class="text-end me-3">
                <p class="mb-1">IDR 500.000</p>
              </div>
              <div class="d-flex align-items-center me-3">
                <button class="btn btn-outline-secondary btn-sm">−</button>
                <span class="mx-2">1</span>
                <button class="btn btn-outline-secondary btn-sm">+</button>
              </div>
              <div class="text-end me-3">
                <p class="mb-0">IDR 500.000</p>
              </div>
              <i class="bi bi-trash" style ="cursor: pointer;"></i>
            </div>
          </div>

          <div class="card product-card mb-3">
            <div class="card-body d-flex align-items-center">
              <div class="me-3">
                <div class="bg-dark" style="width: 80px; height: 80px;"></div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">Nama Product</h6>
                <small class="text-muted">Size / Color</small>
              </div>
              <div class="text-end me-3">
                <p class="mb-1">IDR 500.000</p>
              </div>
              <div class="d-flex align-items-center me-3">
                <button class="btn btn-outline-secondary btn-sm">−</button>
                <span class="mx-2">1</span>
                <button class="btn btn-outline-secondary btn-sm">+</button>
              </div>
              <div class="text-end me-3">
                <p class="mb-0">IDR 500.000</p>
              </div>
              <i class="bi bi-trash" style ="cursor: pointer;"></i>
            </div>
          </div>

          <div class="card product-card mb-3">
            <div class="card-body d-flex align-items-center">
              <div class="me-3">
                <div class="bg-dark" style="width: 80px; height: 80px;"></div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">Nama Product</h6>
                <small class="text-muted">Size / Color</small>
              </div>
              <div class="text-end me-3">
                <p class="mb-1">IDR 500.000</p>
              </div>
              <div class="d-flex align-items-center me-3">
                <button class="btn btn-outline-secondary btn-sm">−</button>
                <span class="mx-2">1</span>
                <button class="btn btn-outline-secondary btn-sm">+</button>
              </div>
              <div class="text-end me-3">
                <p class="mb-0">IDR 500.000</p>
              </div>
              <i class="bi bi-trash" style ="cursor: pointer;"></i>
            </div>
          </div>

          <!-- Duplikat kartu produk lainnya di sini jika perlu -->
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
          <div class="subtotal-box">
            <h5>Subtotal</h5>
            <p class="text-muted small mb-1">Includes Taxes</p>
            <hr>
            <h5 class="fw-bold">IDR 1.500.000</h5>
            <button class="btn btn-gold w-100 mt-3">Checkout</button>
            <button class="btn btn-outline-dark w-100 mt-2">Continue Shopping</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
