<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Models\ArtisanProfile;
    
class ArtisanRegistrationController extends Controller
{
    //
    public function showForm()
    {
        $provinces = [
            'Aceh', 'Bali', 'Banten', 'Bengkulu', 'Gorontalo', 'DKI Jakarta', 'Jambi',
            'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Kalimantan Barat', 'Kalimantan Selatan',
            'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara', 'Kepulauan Bangka Belitung',
            'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara', 'Nusa Tenggara Barat',
            'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Papua Barat Daya', 'Papua Pegunungan',
            'Papua Selatan', 'Papua Tengah', 'Riau', 'Sulawesi Barat', 'Sulawesi Selatan',
            'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara', 'Sumatera Barat',
            'Sumatera Selatan', 'Sumatera Utara', 'DI Yogyakarta'
        ];
        sort($provinces); // Urutkan berdasarkan abjad

        return view('artisan.form', [
            'provinces' => $provinces
        ]);
    }

     /**
     * Menyimpan data pendaftaran artisan yang baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Step 1: Owner Profile
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'home_address' => 'required|string',
            'home_province' => 'required|string',
            'home_city' => 'required|string',
            'home_postal_code' => 'required|string|max:10',

            // Step 2: Business Information
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'other_business_type' => 'nullable|string|required_if:business_type,others|max:255',
            'business_description' => 'required|string',
            'business_phone_number' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'business_address' => 'required_if:same_as_home_address,null|string|nullable',
            'business_province' => 'required_if:same_as_home_address,null|string|nullable',
            'business_city' => 'required_if:same_as_home_address,null|string|nullable',
            'business_postal_code' => 'required_if:same_as_home_address,null|string|nullable|max:10',

            // Step 3: Portfolio (single)
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'fabric_type' => 'required|string',
            'other_fabric_type' => 'nullable|string|required_if:fabric_type,others|max:255',
            'year_created' => 'required|integer|min:1900|max:'.date('Y'),
            'upload_photo' => 'sometimes|array',
            'upload_photo.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'video_link' => 'nullable|url',
        ]);

        try {
            // Buat profile
            $profile = ArtisanProfile::create([
                'name' => $validatedData['full_name'],
                'email' => $validatedData['email_address'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'gender' => $validatedData['gender'],
                'phone_number' => $validatedData['phone_number'],
                'home_address' => $validatedData['home_address'],
                'home_province' => $validatedData['home_province'],
                'home_city' => $validatedData['home_city'],
                'home_postal_code' => $validatedData['home_postal_code'],
                'business_name' => $validatedData['business_name'],
                'business_type' => $validatedData['business_type'],
                'other_business_type' => $validatedData['other_business_type'] ?? null,
                'business_description' => $validatedData['business_description'],
                'business_phone_number' => $validatedData['business_phone_number'] ?? null,
                'business_email' => $validatedData['business_email'] ?? null,
                'business_address' => $validatedData['business_address'] ?? null,
                'business_province' => $validatedData['business_province'] ?? null,
                'business_city' => $validatedData['business_city'] ?? null,
                'business_postal_code' => $validatedData['business_postal_code'] ?? null,
            ]);

            // Upload foto (optional, bisa lebih dari 1)
            $uploadedPhotoPaths = [];
            if ($request->hasFile('upload_photo')) {
                foreach ($request->file('upload_photo') as $file) {
                    $uploadedPhotoPaths[] = $file->store('portfolios', 'public');
                }
            }

            // Buat/isi satu portfolio milik artisan
            $profile->portfolio()->create([
                'project_title' => $validatedData['project_title'],
                'project_description' => $validatedData['project_description'],
                'fabric_type' => $validatedData['fabric_type'],
                'other_fabric_type' => $validatedData['other_fabric_type'] ?? null,
                'year_created' => $validatedData['year_created'],
                'photo_paths' => $uploadedPhotoPaths, // disimpan sebagai JSON
                'video_link' => $validatedData['video_link'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pendaftaran artisan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }

        return redirect()->route('artisan.register')->with('registration_complete', true);
    }

}

