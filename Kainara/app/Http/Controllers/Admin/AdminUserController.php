<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Admin\AdminUserStoreRequest;
use App\Http\Requests\Admin\AdminUserEditRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query(); // Mulai query

        // Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->input('search');
            $searchLower = strtolower($search); // <--- Konversi input pencarian ke lowercase

            $query->where(function($q) use ($searchLower) {
                $q->where(DB::raw("LOWER(CONCAT(first_name, ' ', last_name))"), 'like', '%' . $searchLower . '%');
                
                $q->orWhere(DB::raw('LOWER(first_name)'), 'like', '%' . $searchLower . '%');
                $q->orWhere(DB::raw('LOWER(last_name)'), 'like', '%' . $searchLower . '%');
                $q->orWhere(DB::raw('LOWER(email)'), 'like', '%' . $searchLower . '%');
            });
        }

        $users = $query->paginate(10);

        $users->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminUserStoreRequest $request)
    {
        // if (!$request->hasFile('profile_picture')) {
        //     // Ini akan menampilkan detail jika file tidak terdeteksi sama sekali
        //     // (misal: masalah enctype form atau php.ini upload_max_filesize)
        //     dd("File 'profile_picture' was not detected.", $request->all(), $_FILES);
        // } else {
        //     // Ini akan menampilkan MIME type yang terdeteksi
        //     // Jika Anda melihat MIME type di sini, masalahnya ada di aturan 'mimes' di FormRequest
        //     dd($request->file('profile_picture')->getMimeType());
        // }

        $validatedData = $request->validated();

        $ProfilePic = '/asset/default.png';

        if ($request->hasFile('profile_picture')) {
            $ProfilePic = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'profile_picture' => $ProfilePic,
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.update', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUserEditRequest $request, User $user)
    {
        $validatedData = $request->validated();

        // Path gambar profil yang sedang aktif di database
        $currentProfilePictureInDb = $user->profile_picture;
        $profilePicturePathToSave = $currentProfilePictureInDb; // Default: pertahankan gambar lama

        // Cek apakah ada file profile_picture baru yang diunggah
        if ($request->hasFile('profile_picture')) {

            // Hapus gambar profil lama JIKA ada dan BUKAN default.png
            // Hati-hati: ada dua skema default.png di sini
            if ($currentProfilePictureInDb && $currentProfilePictureInDb !== '/asset/default.png') {
                 // Karena gambar yang di-upload ada di storage/app/public/profile_pictures/,
                 // kita hanya perlu cek dan hapus dari sana.
                 // Jika path-nya adalah '/asset/default.png', berarti itu gambar di public/asset, tidak perlu dihapus.
                if (str_starts_with($currentProfilePictureInDb, 'profile_pictures/')) {
                    if (Storage::disk('public')->exists($currentProfilePictureInDb)) {
                        Storage::disk('public')->delete($currentProfilePictureInDb);
                    }
                }
            }

            // Simpan gambar baru ke 'profile_pictures' subfolder di dalam disk 'public'
            $profilePicturePathToSave = $request->file('profile_picture')->store('profile_pictures', 'public');
        }


        // Siapkan data untuk update
        $userData = [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'profile_picture' => $profilePicturePathToSave,
            'role' => $validatedData['role'],
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Jangan biarkan admin menghapus dirinya sendiri
        if (auth()->check() && auth()->user()->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        if ($user->profile_picture &&
            $user->profile_picture !== '/asset/default.png' &&
            str_starts_with($user->profile_picture, 'profile_pictures/'))
        {
            if (Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
