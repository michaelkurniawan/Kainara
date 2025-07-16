@extends('layouts.app') {{-- Atau layout admin jika Anda punya --}}

@section('title', 'Detail Submission: ' . $profile->name)

@section('content')
<div class="container my-5">
    <a href="{{ route('admin.test.submissions') }}" class="btn btn-outline-secondary mb-4">← Back to List</a>

    {{-- Detail Pendaftar --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3>Registrant Profile</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Full Name:</dt>
                <dd class="col-sm-9">{{ $profile->name }}</dd>

                <dt class="col-sm-3">Email:</dt>
                <dd class="col-sm-9">{{ $profile->email }}</dd>

                <dt class="col-sm-3">Phone Number:</dt>
                <dd class="col-sm-9">{{ $profile->phone_number }}</dd>

                <dt class="col-sm-3">Date of Birth:</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($profile->date_of_birth)->format('d F Y') }}</dd>

                <dt class="col-sm-3">Gender:</dt>
                <dd class="col-sm-9">{{ ucfirst($profile->gender) }}</dd>

                <dt class="col-sm-3">Home Address:</dt>
                <dd class="col-sm-9">{{ $profile->home_address }}, {{ $profile->home_city }}, {{ $profile->home_province }} {{ $profile->home_postal_code }}</dd>
            </dl>
        </div>
    </div>

    {{-- Informasi Bisnis --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3>Business Information</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Business Name:</dt>
                <dd class="col-sm-9">{{ $profile->business_name }}</dd>

                <dt class="col-sm-3">Business Type:</dt>
                <dd class="col-sm-9">{{ $profile->business_type === 'others' ? $profile->other_business_type : $profile->business_type }}</dd>
                
                <dt class="col-sm-3">Description:</dt>
                <dd class="col-sm-9">{{ $profile->business_description }}</dd>
                
                {{-- Tampilkan info kontak & alamat bisnis jika ada --}}
                @if($profile->business_phone_number || $profile->business_email)
                    <dt class="col-sm-3">Business Contact:</dt>
                    <dd class="col-sm-9">{{ $profile->business_phone_number }} / {{ $profile->business_email }}</dd>
                @endif
                @if($profile->business_address)
                    <dt class="col-sm-3">Business Address:</dt>
                    <dd class="col-sm-9">{{ $profile->business_address }}, {{ $profile->business_city }}, {{ $profile->business_province }} {{ $profile->business_postal_code }}</dd>
                @endif
            </dl>
        </div>
    </div>

    {{-- Portfolio --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3>Portfolio</h3>
        </div>
        <div class="card-body">
            @if($profile->portfolios->isNotEmpty())
                @foreach($profile->portfolios as $portfolio)
                    <div class="mb-4 border-bottom pb-3">
                        <h5>{{ $portfolio->project_title }} ({{ $portfolio->year_created }})</h5>
                        <p><strong>Fabric Type:</strong> {{ $portfolio->fabric_type === 'others' ? $portfolio->other_fabric_type : $portfolio->fabric_type }}</p>
                        <p>{{ $portfolio->project_description }}</p>
                        
                        @if($portfolio->video_link)
                            <p><a href="{{ $portfolio->video_link }}" target="_blank">View Video</a></p>
                        @endif

                        <div class="d-flex flex-wrap gap-2">
                            @foreach((array) $portfolio->photo_paths as $photo)
                                <div>
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Portfolio Image" style="height: 150px; width: 150px; object-fit: cover; border-radius: 5px;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <p>No portfolio submitted.</p>
            @endif
        </div>
    </div>
</div>
@endsection