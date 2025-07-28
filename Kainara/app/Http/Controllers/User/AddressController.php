<?php

namespace App\Http\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator; // Tambahkan ini

class AddressController extends Controller
{
    /**
     * Display a listing of the addresses (for API).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Untuk demo, kita tambahkan URL edit dan delete langsung ke data alamat
        $userAddresses = Session::get('user_addresses', []);
        $addressesWithUrls = collect($userAddresses)->map(function($address) {
            $address['edit_url'] = route('addresses.edit', ['address' => $address['id']]); // Placeholder, will be used by JS
            $address['delete_url'] = route('addresses.destroy', ['address' => $address['id']]);
            return $address;
        })->toArray();

        return response()->json($addressesWithUrls);
    }

    /**
     * Store a newly created address in storage.
     * Mengembalikan JSON untuk modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
            $request->validate([
            'label' => 'required|string|max:255', // Changed from 'type'
            'recipient_name' => 'required|string|max:255', // Changed from 'name'
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500', // Changed from 'street', now a text area
            'country' => 'required|string|max:255', // New field
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean', // Changed from 'is_primary'
        ]);

        $user = Auth::user();

        // If the new address is set as default, unmark existing default addresses for this user
        if ($request->has('is_default')) {
            $user->addresses()->where('is_default', true)->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'label' => $request->label, // Changed
            'recipient_name' => $request->recipient_name, // Changed
            'phone' => $request->phone,
            'address' => $request->address, // Changed
            'country' => $request->country, // New
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'is_default' => $request->has('is_default'), // Changed
        ]);

        // Redirect back to the profile page, specifically to the addresses tab
        return redirect()->route('profile')->with('success', 'Address added successfully!')->fragment('addresses');
    }

    /**
     * Update the specified address in storage.
     * Mengembalikan JSON untuk modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'is_primary' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userAddresses = Session::get('user_addresses', []);
        $updated = false;
        $updatedAddress = null;

        foreach ($userAddresses as $key => &$address) {
            if ($address['id'] == (int)$id) {
                $address['type'] = $request->type;
                $address['name'] = $request->name;
                $address['phone'] = $request->phone;
                $address['street'] = $request->street;
                $address['sub_district'] = $request->sub_district;
                $address['district'] = $request->district;
                $address['city'] = $request->city;
                $address['province'] = $request->province;
                $address['postal_code'] = $request->postal_code;
                $address['is_primary'] = (bool)$request->is_primary;
                $updated = true;
                $updatedAddress = $address;
                break;
            }
        }

        if ($updatedAddress && $updatedAddress['is_primary']) {
            foreach ($userAddresses as &$address) {
                if ($address['id'] != (int)$id) {
                    $address['is_primary'] = false;
                }
            }
        } elseif ($updatedAddress && !$updatedAddress['is_primary'] && count($userAddresses) > 0 && !collect($userAddresses)->contains('is_primary', true)) {
            // If the updated address is no longer primary and no other primary exists, set the first one as primary
            if (!empty($userAddresses)) { // Ensure there's at least one address
                $userAddresses[0]['is_primary'] = true;
            }
        }

        Session::put('user_addresses', $userAddresses);

        if ($updated) {
            return response()->json([
                'message' => 'Address updated successfully!',
                'address' => $updatedAddress,
                'selected_address_id' => $updatedAddress['id']
            ], 200);
        }

        return response()->json(['message' => 'Address not found for update.'], 404);
    }

    /**
     * Remove the specified address from storage.
     * Mengembalikan JSON untuk modal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $userAddresses = Session::get('user_addresses', []);
        $addressWasPrimary = false;
        $deletedAddressExists = false;

        // Check if the address to be deleted exists and if it was primary
        foreach ($userAddresses as $address) {
            if ($address['id'] == (int)$id) {
                $deletedAddressExists = true;
                if ($address['is_primary']) {
                    $addressWasPrimary = true;
                }
                break;
            }
        }
        
        $userAddresses = array_filter($userAddresses, fn($address) => $address['id'] != (int)$id);
        $userAddresses = array_values($userAddresses); // Re-index array

        // If the deleted address was primary, set a new primary if addresses remain
        if ($deletedAddressExists && $addressWasPrimary && !empty($userAddresses)) {
            $userAddresses[0]['is_primary'] = true;
        }

        Session::put('user_addresses', $userAddresses);

        if ($deletedAddressExists) {
            return response()->json([
                'message' => 'Address deleted successfully!',
                'selected_address_id' => !empty($userAddresses) ? collect($userAddresses)->firstWhere('is_primary')['id'] ?? $userAddresses[0]['id'] : null
            ], 200);
        }

        return response()->json(['message' => 'Address not found for deletion.'], 404);
    }
}