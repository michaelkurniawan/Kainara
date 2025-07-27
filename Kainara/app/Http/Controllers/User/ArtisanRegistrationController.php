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
        // 1. Validasi semua data dari semua step
        $validatedData = $request->validate([
            // --- Validasi Step 1: Owner Profile ---
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'home_address' => 'required|string',
            'home_province' => 'required|string',
            'home_city' => 'required|string',
            'home_postal_code' => 'required|string|max:10',
            
            // --- Validasi Step 2: Business Information ---
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

            // --- Validasi Step 3: Portfolio ---
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
            // 2. Buat record baru di artisan_profiles
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
                'other_business_type' => $validatedData['other_business_type'],
                'business_description' => $validatedData['business_description'],
                'business_phone_number' => $validatedData['business_phone_number'],
                'business_email' => $validatedData['business_email'],
                'business_address' => $validatedData['business_address'],
                'business_province' => $validatedData['business_province'],
                'business_city' => $validatedData['business_city'],
                'business_postal_code' => $validatedData['business_postal_code'],
                // Status defaultnya adalah 'pending' dari migrasi
            ]);

             // 3. LOGIKA PENYIMPANAN PORTFOLIO DIPERBAIKI
            
            // Siapkan array untuk menampung semua path foto yang berhasil di-upload
            $uploadedPhotoPaths = [];

            if ($request->hasFile('upload_photo')) {
                foreach ($request->file('upload_photo') as $file) {
                    $path = $file->store('portfolios', 'public');
                    // Tambahkan path yang berhasil ke dalam array
                    $uploadedPhotoPaths[] = $path;
                }
            }

            // Simpan SATU record portfolio HANYA JIKA ada foto yang di-upload
            if (!empty($uploadedPhotoPaths)) {
                $profile->portfolios()->create([
                    'project_title' => $validatedData['project_title'],
                    'project_description' => $validatedData['project_description'],
                    'fabric_type' => $validatedData['fabric_type'],
                    'other_fabric_type' => $validatedData['other_fabric_type'],
                    'year_created' => $validatedData['year_created'],
                    'photo_paths' => $uploadedPhotoPaths, // <-- NAMA KOLOM & NILAI SUDAH BENAR (ARRAY)
                    'video_link' => $validatedData['video_link'],
                ]);
            }

            // (Opsional) Kirim email notifikasi ke admin dan pendaftar
            // Mail::to('admin@kainara.com')->send(...);
            // Mail::to($profile->email)->send(new ArtisanSubmissionConfirmation($profile));

        } catch (\Exception $e) {
            // Jika terjadi error saat menyimpan, log error dan redirect kembali
            Log::error('Gagal menyimpan pendaftaran artisan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }

        // 4. Redirect ke halaman form dengan pesan sukses untuk menampilkan Step 4
        return redirect()->route('artisan.register')->with('registration_complete', true);
    }

}

