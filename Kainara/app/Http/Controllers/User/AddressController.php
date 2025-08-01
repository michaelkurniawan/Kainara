<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddress as Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
{
    /**
     * Stores a new address in the database.
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
                'is_default' => 'boolean',
            ]);

            $user = Auth::user();

            if (isset($validatedData['is_default']) && $validatedData['is_default']) {
                $user->addresses()->update(['is_default' => false]);
            } elseif ($user->addresses()->doesntExist()) {
                $validatedData['is_default'] = true;
            }

            $user->addresses()->create($validatedData);

            $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'success',
                'title' => 'Address Added Successfully!',
                'message' => 'Your new address has been saved.',
                'hasActions' => false // No actions needed for a simple success notification
            ]);

        } catch (ValidationException $e) {
            $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'Failed to Add Address!',
                'message' => 'Please check your input. ' . $e->getMessage(), // You might want to refine this message for production
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token']));
        } catch (\Exception $e) {
            $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'An Error Occurred!',
                'message' => 'Failed to add address: ' . $e->getMessage(), // Consider a more generic message for production
                'hasActions' => false
            ])->withInput();
        }
    }

    /**
     * Fetches address data for editing.
     * This is assumed to be an API endpoint called via AJAX.
     *
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json([
                'type' => 'error',
                'title' => 'Access Denied!',
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        return response()->json($address);
    }

    /**
     * Updates an existing address in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserAddress  $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Address $address)
    {
        $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';

        if ($address->user_id !== Auth::id()) {
            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'error',
                'title' => 'Access Denied!',
                'message' => 'You do not have permission to perform this action.',
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
                'is_default' => 'boolean',
            ]);

            $user = Auth::user();

            if (isset($validatedData['is_default']) && $validatedData['is_default']) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            } else {
                if ($address->is_default && $user->addresses()->where('id', '!=', $address->id)->exists()) {
                    $user->addresses()->where('id', '!=', $address->id)->first()->update(['is_default' => true]);
                }
            }

            $address->update($validatedData);

            $redirectTarget = $fromCheckout ?
                                route('checkout.show') :
                                route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'success',
                'title' => 'Address Updated Successfully!',
                'message' => 'Your address changes have been saved.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'Failed to Update Address!',
                'message' => 'Please check your input. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token', '_method']));
        } catch (\Exception $e) {
            $redirectBack = $fromCheckout ?
                                redirect()->back() :
                                redirect()->back()->withFragment('addresses');

            return $redirectBack->with('notification', [
                'type' => 'error',
                'title' => 'An Error Occurred!',
                'message' => 'Failed to update address: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput();
        }
    }

    /**
     * Deletes an address from the database.
     *
     * @param \App\Models\UserAddress $address
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Address $address, Request $request)
    {
        // The previous code had a request() helper call, but passing it as an argument is a cleaner practice.
        // Let's stick with that for clarity.
        $fromCheckout = $request->has('from_checkout') && $request->input('from_checkout') == '1';

        if ($address->user_id !== Auth::id()) {
            $redirectTarget = $fromCheckout ? route('checkout.show') : route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'error',
                'title' => 'Access Denied!',
                'message' => 'You do not have permission to perform this action.',
                'hasActions' => false
            ]);
        }

        try {
            if ($address->is_default) {
                $user = Auth::user();
                $newDefaultAddress = $user->addresses()->where('id', '!=', $address->id)->first();
                if ($newDefaultAddress) {
                    $newDefaultAddress->update(['is_default' => true]);
                }
            }

            $address->delete();

            $redirectTarget = $fromCheckout ? route('checkout.show') : route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'success',
                'title' => 'Address Deleted Successfully!',
                'message' => 'The address has been removed from your list.',
                'hasActions' => false
            ]);
        } catch (\Exception $e) {
            $redirectTarget = $fromCheckout ? route('checkout.show') : route('profile.index', ['#addresses']);

            return redirect($redirectTarget)->with('notification', [
                'type' => 'error',
                'title' => 'Failed to Delete Address!',
                'message' => 'An error occurred while deleting the address: ' . $e->getMessage(),
                'hasActions' => false
            ]);
        }
    }
}