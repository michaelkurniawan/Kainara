<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'AncizarSerif', serif;
    }

    .product-card {
      background-color: #e9dfcf;
      text-align: center;
      padding: 1rem;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center; /* Center vertically */
      align-items: center; /* Center horizontally */
    }

    .product-card .product-title {
      margin: 0.5rem 0;
    }

    .product-title {
      font-weight: bold;
      font-size: 1.5rem;
    }

    .product-price {
      font-size: 1.3rem;
    }

    .product-container {
      height: 100%;
    }

    h1.display-5 {
      font-size: 4.5rem;
      font-weight: bold;
    }

    p.text-muted {
      font-size: 1.5rem;
    }

    .filter-line {
      border-top: 1px solid #000;
    }


    .filter-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
    }

    .dropdown-toggle::after {
      display: none;
    }

    .filter-label {
      font-size: 1.5rem;
    }

    .custom-caret {
      font-size: 0.9rem;
      position: relative;
      top: 3px;
    }

    .btn.border {
      background-color: #fff;
      font-size: 1rem;
    }

    .btn.border:hover {
      background-color: #f8f9fa;
    }

    .product-card .card-body {
      padding: 1rem;
    }
  </style>
</head>

<body class="bg-white">
  <main class="container-fluid py-5 px-5 flex-grow-1">
    <div class="container text-center">
      <div class="header">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-3 d-none d-md-block">
            <img src="{{ asset('images/awan.png') }}" alt="cloud left" class="img-fluid cloud-image" />
          </div>
          <div class="col-md-6">
            <h1 class="display-5">Our Products</h1>
            <p class="text-muted lead">Bangga Pakai Karya UMKM</p>
          </div>
          <div class="col-md-3 d-none d-md-block">
            <img src="{{ asset('images/awankanan.png') }}" alt="cloud right" class="img-fluid cloud-image" />
          </div>
        </div>
      </div>
    </div>

    <div class="filter-line"></div>

    {{-- Selected Filters --}}
    @if (!empty($originFilter) || !empty($colorFilter) || request()->has('sort'))
      <div class="d-flex flex-wrap align-items-center gap-3">
        <h4 class="me-1" style="font-size: 1.25rem;">Filtered by:</h4>

        {{-- Origin Filters --}}
        @foreach ((array) $originFilter as $selectedOrigin)
          <form method="GET" action="{{ route('products.index') }}" class="d-inline">
            @foreach ((array) $originFilter as $origin)
              @if ($origin !== $selectedOrigin)
                <input type="hidden" name="origins[]" value="{{ $origin }}">
              @endif
            @endforeach
            @if (request()->has('sort'))
              <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            <button type="submit" class="btn border d-flex align-items-center px-3 py-2" style="font-size: 1.1rem;">
              <span class="me-3">{{ $selectedOrigin }}</span>
              <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
            </button>
          </form>
        @endforeach

        {{-- Color Filters --}}
        @foreach ((array) $colorFilter as $selectedColor)
          <form method="GET" action="{{ route('products.index') }}" class="d-inline">
            @foreach ((array) $colorFilter as $color)
              @if ($color !== $selectedColor)
                <input type="hidden" name="colors[]" value="{{ $color }}">
              @endif
            @endforeach

            @foreach ((array) $originFilter as $origin)
              <input type="hidden" name="origins[]" value="{{ $origin }}">
            @endforeach

            @if (request()->has('sort'))
              <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <button type="submit" class="btn border d-flex align-items-center px-3 py-2" style="font-size: 1.1rem;">
              <span class="me-2 d-flex align-items-center">
                <span class="rounded-circle d-inline-block" style="width: 16px; height: 16px; background-color: {{ strtolower($selectedColor) }}; border: 1px solid #999;"></span>
                <span class="ms-2 text-capitalize">{{ $selectedColor }}</span>
              </span>
              <i class="bi bi-x-lg ms-2" style="font-size: 0.7rem;"></i>
            </button>
          </form>
        @endforeach

        {{-- Sort Filter --}}
        @php
          $sort = request('sort');
          $sortLabel = [
            'az' => 'Alphabetically, A-Z',
            'za' => 'Alphabetically, Z-A',
            'price_low' => 'Price: Low to High',
            'price_high' => 'Price: High to Low',
          ];
        @endphp
        @if ($sort && isset($sortLabel[$sort]))
          <form method="GET" action="{{ route('products.index') }}" class="d-inline">
            @foreach ((array) $originFilter as $origin)
              <input type="hidden" name="origins[]" value="{{ $origin }}">
            @endforeach
            <button type="submit" class="btn border d-flex align-items-center px-3 py-2" style="font-size: 1.1rem;">
              <span class="me-3">{{ $sortLabel[$sort] }}</span>
              <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
            </button>
          </form>
        @endif

        {{-- Clear All (paling kanan) --}}
        <div class="ms-auto">
          <a href="{{ route('products.index') }}" class="btn btn-outline-danger px-4 py-1" style="font-size: 1.25rem;">Clear All</a>
        </div>
      </div>
    @endif

    <!-- Product Cards -->
    <div class="row g-5 ">
      @forelse ($products as $product)
        <div class="col-12 col-sm-6 col-md-4">
          <a href="{{ url('products/' . $product->id) }}" class="text-decoration-none text-dark">
            <div class="product-container card h-100">
              <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid" />
              <div class="product-card">
                <div class="product-title">{{ $product->name }}</div>
                <div class="product-price">IDR {{ number_format($product->price, 0, ',', '.') }}</div>
              </div>
            </div>
          </a>
        </div>
      @empty
        <div class="col-12 text-center">
          <p class="text-muted fs-4">Produk belum tersedia.</p>
        </div>
      @endforelse
    </div>
  </main>

  <!-- Hidden filter form -->
  <form id="filterForm" method="GET" class="d-none">
    @foreach (['Central Java', 'Yogyakarta', 'East Nusa Tenggara', 'Papua', 'West Java'] as $origin)
      <input type="hidden" name="origins[]" value="{{ $origin }}" disabled>
    @endforeach
    @foreach (['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White'] as $color)
      <input type="hidden" name="colors[]" value="{{ $color }}" disabled>
    @endforeach
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const form = document.getElementById('filterForm');
    const inputs = form.querySelectorAll('input[type="hidden"]');
    const colorInputs = Array.from(form.querySelectorAll('input[name="colors[]"]'));

    function syncColorsToForm(selectedColors) {
      colorInputs.forEach(input => {
        input.disabled = !selectedColors.includes(input.value);
      });
    }

    document.querySelectorAll('.color-option').forEach(el => {
      el.addEventListener('click', () => {
        const color = el.dataset.color;
        el.classList.toggle('bg-light');

        const activeColors = Array.from(document.querySelectorAll('.color-option.bg-light'))
          .map(el => el.dataset.color);

        syncColorsToForm(activeColors);
        form.submit();
      });
    });

    // Toggle dropdown Origin1
    document.getElementById('originToggle1').addEventListener('click', function (e) {
      e.preventDefault();
      const box = document.getElementById('originBox1');
      box.style.display = box.style.display === 'none' ? 'block' : 'none';
    });

    // Toggle dropdown Origin2
    document.getElementById('originToggle2').addEventListener('click', function (e) {
      e.preventDefault();
      const box = document.getElementById('originBox2');
      box.style.display = box.style.display === 'none' ? 'block' : 'none';
    });

    // Klik di luar menutup dropdown Origin1 dan Origin2
    document.addEventListener('click', function (e) {
      const toggle1 = document.getElementById('originToggle1');
      const box1 = document.getElementById('originBox1');
      const toggle2 = document.getElementById('originToggle2');
      const box2 = document.getElementById('originBox2');

      if (!toggle1.contains(e.target) && !box1.contains(e.target)) {
        box1.style.display = 'none';
      }
      if (!toggle2.contains(e.target) && !box2.contains(e.target)) {
        box2.style.display = 'none';
      }
    });

    // Sinkronisasi checkbox Origin1 ke form
    document.querySelectorAll('.origin-checkbox1').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        const inputs = form.querySelectorAll('input[name="origins[]"]');
        inputs.forEach(i => i.disabled = true);
        document.querySelectorAll('.origin-checkbox1:checked').forEach(cb => {
          const match = Array.from(inputs).find(i => i.value === cb.dataset.origin);
          if (match) match.disabled = false;
        });
        form.submit();
      });
    });

    // Sinkronisasi checkbox Origin2 ke form
    document.querySelectorAll('.origin-checkbox2').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        const inputs = form.querySelectorAll('input[name="origins[]"]');
        inputs.forEach(i => i.disabled = true);
        document.querySelectorAll('.origin-checkbox2:checked').forEach(cb => {
          const match = Array.from(inputs).find(i => i.value === cb.dataset.origin);
          if (match) match.disabled = false;
        });
        form.submit();
      });
    });
  </script>
</body>

</html>
