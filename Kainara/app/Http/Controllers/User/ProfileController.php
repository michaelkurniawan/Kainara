<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Order;
use App\Models\UserAddress;

class ProfileController extends Controller
{
    /**
     * Display the user profile page with personal information, addresses, and order history.
     */
    public function index()
    {
        $user = Auth::user();
        $userAddresses = $user->addresses()->get();

        // Fetch orders for the profile's Order History tab
        // These are orders that are considered "finished" or "archived"
        $userOrdersHistory = $user->orders()
                                        // Include 'Refund Rejected' in the history filter
                                        ->whereIn('status', ['Completed', 'Canceled', 'Returned', 'Refunded', 'Refund Pending', 'Refund Failed', 'Refund Rejected'])
                                        ->with(['orderItems.product', 'payment.refunds'])
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
            if ($user->profile_picture && !str_contains($user->profile_picture, 'default-profile.png')) {
                $oldPath = str_replace('storage/', '', $user->profile_picture);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
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
            return redirect()->back()->withErrors($e->errors())->withInput()
                ->with('notification', [
                    'type' => 'error',
                    'title' => 'Upload Failed!',
                    'message' => 'Please check the image file. ' . $e->getMessage(),
                    'hasActions' => false
                ])->withFragment('personal-info');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('notification', [
                    'type' => 'error',
                    'title' => 'An Error Occurred!',
                    'message' => 'Failed to update profile picture: ' . $e->getMessage(),
                    'hasActions' => false
                ])->withFragment('personal-info');
        }
    }

    /**
     * Update the user's personal information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            return redirect()->back()->withErrors($e->errors())->withInput($request->except(['_token', '_method']))
                ->with('notification', [
                    'type' => 'error',
                    'title' => 'Update Failed!',
                    'message' => 'Please check your input. ' . $e->getMessage(),
                    'hasActions' => false
                ])->withFragment('personal-info');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('notification', [
                    'type' => 'error',
                    'title' => 'An Error Occurred!',
                    'message' => 'Failed to update personal information: ' . $e->getMessage(),
                    'hasActions' => false
                ])->withFragment('personal-info');
        }
    }
}