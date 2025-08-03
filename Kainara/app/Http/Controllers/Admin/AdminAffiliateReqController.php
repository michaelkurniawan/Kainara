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
    public function index(Request $request){
        $status = $request->input('status', 'pending');

        if ($status === 'all') {
            $submissions = ArtisanProfile::all();
        } else {
            $submissions = ArtisanProfile::where('status', $status)->get();
        }

        return view('admin.affiliations.index', ['submissions' => $submissions]);
    }

    public function show(ArtisanProfile $profile){
        $profile->load('portfolio');
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