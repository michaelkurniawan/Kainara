<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArtisanProfile;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArtisanApprovedNotification;
use App\Mail\ArtisanRejectedNotification;

class AdminAffiliateReqController extends Controller
{
    public function index(){
        $pendingSubmissions = ArtisanProfile::where('status', 'pending')->get();
        return view('admin.affiliations.index', ['submissions' => $pendingSubmissions]);
    }

    public function show(ArtisanProfile $profile){
        $profile->load('portfolios');
        return view('admin.affiliations.show', ['profile' => $profile]);
    }

    public function approve(ArtisanProfile $profile){
        $profile->update(['status' => 'approved']);
        Mail::to($profile->email)->send(new ArtisanApprovedNotification($profile));
        return redirect()->route('admin.affiliations.index')->with('message', "Profile {$profile->name} approved and email sent!");
    }

    public function reject(ArtisanProfile $profile){
        $profile->update(['status' => 'rejected']);
        Mail::to($profile->email)->send(new ArtisanRejectedNotification($profile));
        return redirect()->route('admin.affiliations.index')->with('message', "Profile {$profile->name} rejected and email sent!");
    }
}
