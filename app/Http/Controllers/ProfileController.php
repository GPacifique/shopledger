<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const PHOTO_DIR = 'images/users';

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->except(['photo', 'remove_photo']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->boolean('remove_photo')) {
            $this->deletePhoto($user);
        }

        if ($request->hasFile('photo')) {
            $this->deletePhoto($user);

            $file = $request->file('photo');

            File::ensureDirectoryExists(public_path(self::PHOTO_DIR));

            // Saved as images/users/{user id}.jpg or .png (type detected from the real file)
            $file->move(public_path(self::PHOTO_DIR), $user->id . '.' . $file->extension());
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $this->deletePhoto($user);
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function deletePhoto(User $user): void
    {
        foreach (['jpg', 'jpeg', 'png'] as $ext) {
            File::delete(public_path(self::PHOTO_DIR . "/{$user->id}.{$ext}"));
        }
    }
}