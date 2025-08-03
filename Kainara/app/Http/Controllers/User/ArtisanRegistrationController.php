<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ArtisanProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ArtisanRegistrationController extends Controller
{
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
        /**
     * Menyimpan data pendaftaran artisan yang baru.
     */
    public function store(Request $request)
    {
        // DEBUG #1: Tulis log bahwa method ini telah dieksekusi.
        Log::info('--- Artisan Registration Store Method Executed ---');
        Log::info('Raw Request Data:', $request->all());

        // 1. Validasi semua data dari semua step
        $validatedData = $request->validate([
            // --- Aturan validasi Anda (tidak perlu diubah) ---
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255|unique:artisan_profiles,email',
            'home_address' => 'required|string',
            'home_province' => 'required|string',
            'home_city' => 'required|string',
            'home_postal_code' => 'required|string|max:10',
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'other_business_type' => 'nullable|string|required_if:business_type,others|max:255',
            'business_description' => 'required|string',
            'business_phone_number' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'business_address' => 'required_without:same_as_home_address|string|nullable',
            'business_province' => 'required_without:same_as_home_address|string|nullable',
            'business_city' => 'required_without:same_as_home_address|string|nullable',
            'business_postal_code' => 'required_without:same_as_home_address|string|nullable|max:10',
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'fabric_type' => 'required|string',
            'other_fabric_type' => 'nullable|string|required_if:fabric_type,others|max:255',
            'year_created' => 'required|integer|min:1900|max:'.date('Y'),
            'upload_photo' => 'nullable|array',
            'upload_photo.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'video_link' => 'nullable|url',
        ]);

        // DEBUG #2: Tulis log bahwa validasi berhasil dan tampilkan datanya.
        Log::info('Validation Passed. Validated Data:', $validatedData);

        try {
            // 2. Buat record baru di artisan_profiles
            $profileData = [
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
                'business_type' => $request->business_type === 'others' ? $validatedData['other_business_type'] : $validatedData['business_type'],
                'business_description' => $validatedData['business_description'],
                'business_phone_number' => $validatedData['business_phone_number'],
                'business_email' => $validatedData['business_email'],
                'business_address' => $request->has('same_as_home_address') ? $validatedData['home_address'] : $validatedData['business_address'],
                'business_province' => $request->has('same_as_home_address') ? $validatedData['home_province'] : $validatedData['business_province'],
                'business_city' => $request->has('same_as_home_address') ? $validatedData['home_city'] : $validatedData['business_city'],
                'business_postal_code' => $request->has('same_as_home_address') ? $validatedData['home_postal_code'] : $validatedData['business_postal_code'],
            ];
            
            // DEBUG #3: Tampilkan data yang akan dimasukkan ke ArtisanProfile
            Log::info('Data to be created for ArtisanProfile:', $profileData);
            
            $profile = ArtisanProfile::create($profileData);

            // DEBUG #4: Konfirmasi bahwa ArtisanProfile berhasil dibuat
            Log::info('ArtisanProfile created successfully. ID: ' . $profile->id);

            // 3. Simpan portfolio yang berelasi
            $paths = [];
            if ($request->hasFile('upload_photo')) {
                Log::info('Request has files for upload_photo.');
                foreach ($request->file('upload_photo') as $file) {
                    $path = $file->store('portfolios', 'public');
                    $paths[] = $path;
                    Log::info('Stored file: ' . $path);
                }
            } else {
                Log::warning('No files found for upload_photo in the request.');
            }

            $portfolioData = [
                'project_title' => $validatedData['project_title'],
                'project_description' => $validatedData['project_description'],
                'fabric_type' => $request->fabric_type === 'others' ? $validatedData['other_fabric_type'] : $validatedData['fabric_type'],
                'year_created' => $validatedData['year_created'],
                'photo_paths' => json_encode($paths), 
                'video_link' => $validatedData['video_link'],
            ];
            
            // DEBUG #5: Tampilkan data yang akan dimasukkan ke Portfolio
            Log::info('Data to be created for Portfolio:', $portfolioData);


            // DEBUG #6 (PALING PENTING): HENTIKAN EKSEKUSI DI SINI
            // Ini akan menghentikan kode TEPAT SEBELUM baris yang error.
            // dd(
            //     '--- SCRIPT PAUSED ---',
            //     'Profile Object:', $profile->toArray(),
            //     'Does "portfolio()" method exist on ArtisanProfile?', method_exists($profile, 'portfolio'), // <-- INI AKAN true/false
            //     'Does "portfolios()" method exist on ArtisanProfile?', method_exists($profile, 'portfolios') // <-- INI AKAN true/false
            // );


            // Baris ini adalah sumber error Anda. Kode `dd()` di atas akan menghentikan eksekusi sebelum sampai ke sini.
            $profile->portfolio()->create($portfolioData);


        } catch (\Exception $e) {
            // DEBUG #7: Jika terjadi error, catat dengan lebih detail
            Log::error('Gagal menyimpan pendaftaran artisan: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString()); // Catat seluruh stack trace
            return back()->withInput()->with('error', 'Something went wrong. Please check the logs.');
        }

        // Jika berhasil (setelah Anda menghapus dd())
        Log::info('Registration completed successfully for Artisan ID: ' . $profile->id);
        return redirect()->route('artisan.register')->with('registration_complete', true);
    }
}