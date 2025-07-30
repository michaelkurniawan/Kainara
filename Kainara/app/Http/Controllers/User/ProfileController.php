<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Order; // Make sure to import the Order model
use App\Models\Address; // Make sure to import the Address model

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $userAddresses = $user->addresses()->get();

        // Fetch orders for the profile's Order History tab
        // These are orders that are considered "finished" or "archived"
        $userOrdersHistory = $user->orders()
                                  ->whereIn('status', ['Completed', 'Canceled', 'Returned', 'Refunded'])
                                  ->with('orderItems.product') // Eager load relationships needed for display
                                  ->orderByDesc('created_at')
                                  ->get();

        return view('profile', compact(['user', 'userAddresses', 'userOrdersHistory']));
    }

    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
            ]);

            // Delete old profile picture if it exists and is not the default
            if ($user->profile_picture && $user->profile_picture !== 'images/default-profile.png') {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
            }

            // Store the new image
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');

            // Update user's profile_picture path in database
            $user->profile_picture = 'storage/' . $path;
            $user->save();

            return redirect()->route('profile.index', ['#personal-info'])->with('notification', [
                'type' => 'success',
                'title' => 'Profile Picture Updated!',
                'message' => 'Your profile picture has been successfully updated.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Upload Failed!',
                'message' => 'Please check the image file. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->all())
              ->withFragment('personal-info'); // Stay on personal info tab
        } catch (\Exception $e) {
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'An Error Occurred!',
                'message' => 'Failed to update profile picture: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput()
              ->withFragment('personal-info'); // Stay on personal info tab
        }
    }

    public function updatePersonalInformation(Request $request)
    {
        $user = Auth::user();

        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'dob' => 'nullable|date',
            ]);

            $user->update($validatedData);

            return redirect()->route('profile.index', ['#personal-info'])->with('notification', [
                'type' => 'success',
                'title' => 'Personal Information Updated!',
                'message' => 'Your personal details have been successfully updated.',
                'hasActions' => false
            ]);

        } catch (ValidationException $e) {
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'Update Failed!',
                'message' => 'Please check your input. ' . $e->getMessage(),
                'hasActions' => false
            ])->withErrors($e->errors())->onlyInput($request->except(['_token', '_method']))
              ->withFragment('personal-info'); // Stay on personal info tab
        } catch (\Exception $e) {
            return redirect()->back()->with('notification', [
                'type' => 'error',
                'title' => 'An Error Occurred!',
                'message' => 'Failed to update personal information: ' . $e->getMessage(),
                'hasActions' => false
            ])->withInput()
              ->withFragment('personal-info'); // Stay on personal info tab
        }
    }
}