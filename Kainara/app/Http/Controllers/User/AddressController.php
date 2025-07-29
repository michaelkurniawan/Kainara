<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
{
    /**
     * Menyimpan alamat baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:255',
                'recipient_name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'is_default' => 'boolean', // Ini sekarang bekerja dengan input hidden
            ]);

            $user = Auth::user();

            // Jika alamat ini diatur sebagai default, set semua alamat lain user menjadi non-default
            if (isset($validatedData['is_default']) && $validatedData['is_default']) {
                $user->addresses()->update(['is_default' => false]);
            } elseif ($user->addresses()->doesntExist()) {
                // Jika ini alamat pertama user, otomatis jadikan default
                $validatedData['is_default'] = true;
            }

            // Buat alamat baru
            $user->addresses()->create($validatedData);

            // Redirect sukses kembali ke tab 'Addresses'
            return redirect()->route('profile.index', ['#addresses'])->with('notification', [
                'type' => 'success',
                'title' => 'Alamat Berhasil Ditambahkan!',
                'message' => 'Alamat baru Anda telah berhasil disimpan.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            // Redirect kembali dengan error validasi, tetap di tab 'Addresses'
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Gagal Menambahkan Alamat!',
                'message' => 'Mohon periksa kembali input Anda. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token']))
              ->withFragment('addresses'); // Pertahankan tab aktif
        } catch (\Exception $e) {
            // Redirect kembali dengan error umum, tetap di tab 'Addresses'
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan!',
                'message' => 'Gagal menambahkan alamat: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput()
              ->withFragment('addresses'); // Pertahankan tab aktif
        }
    }

    /**
     * Mengambil data alamat untuk mengisi modal edit.
     * Ini diasumsikan sebagai endpoint API yang dipanggil oleh AJAX.
     *
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UserAddress $address)
    {
        // Pastikan pengguna memiliki akses ke alamat ini
        if ($address->user_id !== Auth::id()) {
            // Jika akses ditolak, kembalikan response JSON error
            return response()->json([
                'type' => 'error',
                'title' => 'Akses Ditolak!',
                'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.'
            ], 403); // Status code 403 Forbidden
        }

        // Kembalikan data alamat dalam format JSON
        return response()->json($address);
    }

    /**
     * Memperbarui alamat yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, UserAddress $address)
    {
        // Pastikan pengguna memiliki akses ke alamat ini
        if ($address->user_id !== Auth::id()) {
            // Redirect kembali ke tab 'Addresses' jika akses ditolak
            return redirect()->route('profile.index', ['#addresses'])->with('notification', [
                'type' => 'error',
                'title' => 'Akses Ditolak!',
                'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.',
                'hasActions' => false
            ]);
        }

        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:255',
                'recipient_name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'is_default' => 'boolean', // Ini sekarang bekerja dengan input hidden
            ]);

            $user = Auth::user();

            // Logika untuk mengatur alamat default
            if (isset($validatedData['is_default']) && $validatedData['is_default']) {
                // Set semua alamat lain user menjadi non-default
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            } else {
                // Jika alamat yang sedang diedit adalah default dan di-uncheck,
                // dan ada alamat lain, set alamat pertama yang ditemukan sebagai default baru.
                if ($address->is_default && $user->addresses()->where('id', '!=', $address->id)->exists()) {
                    $user->addresses()->where('id', '!=', $address->id)->first()->update(['is_default' => true]);
                }
            }

            // Perbarui alamat
            $address->update($validatedData);

            // Redirect sukses kembali ke tab 'Addresses'
            return redirect()->route('profile.index', ['#addresses'])->with('notification', [
                'type' => 'success',
                'title' => 'Alamat Berhasil Diperbarui!',
                'message' => 'Perubahan alamat Anda telah berhasil disimpan.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            // Redirect kembali dengan error validasi, tetap di tab 'Addresses'
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Gagal Memperbarui Alamat!',
                'message' => 'Mohon periksa kembali input Anda. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token', '_method']))
              ->withFragment('addresses'); // Pertahankan tab aktif
        } catch (\Exception $e) {
            // Redirect kembali dengan error umum, tetap di tab 'Addresses'
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan!',
                'message' => 'Gagal memperbarui alamat: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput()
              ->withFragment('addresses'); // Pertahankan tab aktif
        }
    }

    /**
     * Menghapus alamat dari database.
     *
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(UserAddress $address)
    {
        // Pastikan pengguna memiliki akses ke alamat ini
        if ($address->user_id !== Auth::id()) {
            // Redirect kembali ke tab 'Addresses' jika akses ditolak
            return redirect()->route('profile.index', ['#addresses'])->with('notification', [
                'type' => 'error',
                'title' => 'Akses Ditolak!',
                'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.',
                'hasActions' => false
            ]);
        }

        try {
            // Jika alamat yang dihapus adalah default, cari alamat lain dan jadikan default baru
            if ($address->is_default) {
                $user = Auth::user();
                $newDefaultAddress = $user->addresses()->where('id', '!=', $address->id)->first();
                if ($newDefaultAddress) {
                    $newDefaultAddress->update(['is_default' => true]);
                }
            }

            // Hapus alamat
            $address->delete();

            // Redirect sukses kembali ke tab 'Addresses'
            return redirect()->route('profile.index', ['#addresses'])->with('notification', [
                'type' => 'success',
                'title' => 'Alamat Berhasil Dihapus!',
                'message' => 'Alamat telah berhasil dihapus dari daftar Anda.',
                'hasActions' => false
            ]);
        } catch (\Exception $e) {
            // Redirect kembali dengan error umum, tetap di tab 'Addresses'
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Gagal Menghapus Alamat!',
                'message' => 'Terjadi kesalahan saat menghapus alamat: ' . $e->getMessage(),
                'hasActions' => false
            ])->withFragment('addresses'); // Pertahankan tab aktif
        }
    }
}
