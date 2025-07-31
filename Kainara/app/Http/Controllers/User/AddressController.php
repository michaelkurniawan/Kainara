<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddress as Address; // Alias UserAddress to Address for type hinting
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

            // Determine redirect target based on 'from_checkout' flag
            // Ensure to maintain the #addresses fragment for the profile page
            $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            // Redirect sukses kembali ke target yang ditentukan
            return redirect($redirectTarget)->with('notification', [
                'type' => 'success',
                'title' => 'Alamat Berhasil Ditambahkan!',
                'message' => 'Alamat baru Anda telah berhasil disimpan.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            // Determine redirect target for validation errors
            // For checkout, we redirect back to the page itself so old inputs and errors persist.
            // For profile, we redirect back with the fragment.
            $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            // Redirect kembali dengan error validasi
            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'Gagal Menambahkan Alamat!',
                'message' => 'Mohon periksa kembali input Anda. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token']));
        } catch (\Exception $e) {
            // Determine redirect target for general errors
            $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            // Redirect kembali dengan error umum
            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan!',
                'message' => 'Gagal menambahkan alamat: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput();
        }
    }

    /**
     * Mengambil data alamat untuk mengisi modal edit.
     * Ini diasumsikan sebagai endpoint API yang dipanggil oleh AJAX.
     *
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Address $address)
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
    public function update(Request $request, Address $address)
    {
        // Determine if the request originated from checkout
        $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';

        // Authorization check
        if ($address->user_id !== Auth::id()) {
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
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
                'is_default' => 'boolean', // This now works with hidden input
            ]);

            $user = Auth::user();

            // Logic to set default address
            if (isset($validatedData['is_default']) && $validatedData['is_default']) {
                // Set all other user addresses to non-default
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            } else {
                // If the address being edited was default and unchecked,
                // and there are other addresses, set the first found address as the new default.
                if ($address->is_default && $user->addresses()->where('id', '!=', $address->id)->exists()) {
                    $user->addresses()->where('id', '!=', $address->id)->first()->update(['is_default' => true]);
                }
            }

            // Update the address
            $address->update($validatedData);

            // Determine redirect target
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            // Redirect success back to the determined target
            return redirect($redirectTarget)->with('notification', [
                'type' => 'success',
                'title' => 'Alamat Berhasil Diperbarui!',
                'message' => 'Perubahan alamat Anda telah berhasil disimpan.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            // Determine redirect target for validation errors
            $redirectBack = $fromCheckout ?
                                redirect()->back() : // Go back to the form if validation fails on checkout
                                redirect()->back()->withFragment('addresses'); // Go back to profile#addresses

            // Redirect back with validation errors
            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'Gagal Memperbarui Alamat!',
                'message' => 'Mohon periksa kembali input Anda. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token', '_method']));
        } catch (\Exception $e) {
            // Determine redirect target for general errors
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            // Redirect back with general error
            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan!',
                'message' => 'Gagal memperbarui alamat: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput();
        }
    }

    /**
     * Menghapus alamat dari database.
     *
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Address $address)
    {
        // Determine if the request originated from checkout
        $fromCheckout = request()->has('from_checkout') && request()->input('from_checkout') == '1';

        if ($address->user_id !== Auth::id()) {
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'error',
                'title' => 'Akses Ditolak!',
                'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.',
                'hasActions' => false
            ]);
        }

        try {
            // If the address being deleted is the default, find another and make it the new default
            if ($address->is_default) {
                $user = Auth::user();
                $newDefaultAddress = $user->addresses()->where('id', '!=', $address->id)->first();
                if ($newDefaultAddress) {
                    $newDefaultAddress->update(['is_default' => true]);
                }
            }

            // Delete the address
            $address->delete();

            // Determine redirect target
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            // Redirect with success message
            return redirect($redirectTarget)->with('notification', [
                'type' => 'success',
                'title' => 'Alamat Berhasil Dihapus!',
                'message' => 'Alamat telah berhasil dihapus dari daftar Anda.',
                'hasActions' => false
            ]);
        } catch (\Exception $e) {
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            // Redirect with error message
            return redirect($redirectTarget)->with('notification', [
                'type' => 'error',
                'title' => 'Gagal Menghapus Alamat!',
                'message' => 'Terjadi kesalahan saat menghapus alamat: ' . $e->getMessage(),
                'hasActions' => false
            ]);
        }
    }
}