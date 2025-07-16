<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtisanProfile;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArtisanApprovedNotification;
use App\Mail\ArtisanRejectedNotification;

class AdminTestController extends Controller
{
    // Menampilkan daftar pendaftar
    public function index()
    {
        $pendingSubmissions = ArtisanProfile::where('status', 'pending')->get();
        return view('admin-test.index', ['submissions' => $pendingSubmissions]);
    }

      public function show(ArtisanProfile $profile)
    {
        // Laravel's Route Model Binding akan otomatis menemukan ArtisanProfile
        // berdasarkan ID yang ada di URL.

        // Kita juga perlu mengambil data portfolio yang berelasi.
        $profile->load('portfolios');

        return view('admin-test.show', ['profile' => $profile]);
    }

    // Menyetujui pendaftaran
    public function approve(ArtisanProfile $profile)
    {
        $profile->update(['status' => 'approved']);
        Mail::to($profile->email)->send(new ArtisanApprovedNotification($profile));
        return redirect()->route('admin.test.submissions')->with('message', "Profile {$profile->name} approved and email sent!");
    }

    // Menolak pendaftaran
    public function reject(ArtisanProfile $profile)
    {
        $profile->update(['status' => 'rejected']);
        Mail::to($profile->email)->send(new ArtisanRejectedNotification($profile));
        return redirect()->route('admin.test.submissions')->with('message', "Profile {$profile->name} rejected and email sent!");
    }
}