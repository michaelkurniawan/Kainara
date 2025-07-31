@props(['title', 'subtitle'])

<div class="container text-center">
    <div class="header">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-3 d-none d-md-block">
                <img src="{{ asset('images/awan.png') }}" alt="cloud left" class="img-fluid cloud-image" />
            </div>
            <div class="col-md-6">
                <h1 class="display-5" style="letter-spacing: 5.5px">{{ $title }}</h1>
                <p class="text-muted lead">{{ $subtitle }}</p>
            </div>
            <div class="col-md-3 d-none d-md-block">
                <img src="{{ asset('images/awankanan.png') }}" alt="cloud right" class="img-fluid cloud-image" />
            </div>
        </div>
    </div>
</div>