<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public string $heroBadge = '';
    public string $heroTitle = '';
    public string $heroDescription = '';

    public $photoUpload = null;
    public $heroBackgroundUpload = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user?->name ?? '';
        $this->email = $user?->email ?? '';

        $profile = $user?->profile;

        $this->heroBadge = $profile?->hero_badge ?? 'MY PORTFOLIO';
        $this->heroTitle = $profile?->hero_title ?? 'Creative portfolio system for refined digital projects.';
        $this->heroDescription = $profile?->hero_description ?? 'Explore selected works, project progress, design systems, and portfolio activity from one clean public page.';
    }

    public function updateAccount(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $validated['name'],
                'role' => $user->profile?->role ?? 'Portfolio Manager',
                'bio' => $user->profile?->bio,
                'avatar' => $user->profile?->avatar,
                'hero_badge' => $this->heroBadge,
                'hero_title' => $this->heroTitle,
                'hero_description' => $this->heroDescription,
                'hero_background' => $user->profile?->hero_background,
            ]
        );

        session()->flash('account_success', 'Nama dan email berhasil diperbarui.');
    }

    public function updateHeroContent(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'heroBadge' => ['required', 'string', 'max:80'],
            'heroTitle' => ['required', 'string', 'max:180'],
            'heroDescription' => ['required', 'string', 'max:500'],
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'role' => $user->profile?->role ?? 'Portfolio Manager',
                'bio' => $user->profile?->bio,
                'avatar' => $user->profile?->avatar,
                'hero_badge' => $validated['heroBadge'],
                'hero_title' => $validated['heroTitle'],
                'hero_description' => $validated['heroDescription'],
                'hero_background' => $user->profile?->hero_background,
            ]
        );

        session()->flash('hero_success', 'Konten halaman public berhasil diperbarui.');
    }

    public function updateHeroBackground(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->validate([
            'heroBackgroundUpload' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp,bmp', 'max:8192'],
        ]);

        $backgroundPath = $this->storeUploadedImageAsWebp(
            $this->heroBackgroundUpload,
            'hero-backgrounds',
            'heroBackgroundUpload'
        );

        $oldBackground = $user->profile?->hero_background;

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'role' => $user->profile?->role ?? 'Portfolio Manager',
                'bio' => $user->profile?->bio,
                'avatar' => $user->profile?->avatar,
                'hero_badge' => $this->heroBadge,
                'hero_title' => $this->heroTitle,
                'hero_description' => $this->heroDescription,
                'hero_background' => $backgroundPath,
            ]
        );

        if ($oldBackground && Str::startsWith($oldBackground, 'storage/hero-backgrounds/')) {
        }

        $this->reset('heroBackgroundUpload');

        session()->flash('hero_background_success', 'Background halaman public berhasil diperbarui.');
    }

    public function updateProfilePhoto(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->validate([
            'photoUpload' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp,bmp', 'max:4096'],
        ]);

        $avatarPath = $this->storeUploadedImageAsWebp(
            $this->photoUpload,
            'profile-photos',
            'photoUpload'
        );

        $oldAvatar = $user->profile?->avatar;

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'role' => $user->profile?->role ?? 'Portfolio Manager',
                'bio' => $user->profile?->bio,
                'avatar' => $avatarPath,
                'hero_badge' => $this->heroBadge,
                'hero_title' => $this->heroTitle,
                'hero_description' => $this->heroDescription,
                'hero_background' => $user->profile?->hero_background,
            ]
        );

        if ($oldAvatar && Str::startsWith($oldAvatar, 'storage/profile-photos/')) {
        }

        $this->reset('photoUpload');

        session()->flash('photo_success', 'Foto profil berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->validate([
            'currentPassword' => ['required'],
            'newPassword' => ['required', Password::defaults()],
            'newPasswordConfirmation' => ['required', 'same:newPassword'],
        ], [
            'newPasswordConfirmation.same' => 'Konfirmasi password baru tidak sama.',
        ]);

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Password lama tidak sesuai.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset([
            'currentPassword',
            'newPassword',
            'newPasswordConfirmation',
        ]);

        session()->flash('password_success', 'Password berhasil diperbarui.');
    }

    private function storeUploadedImageAsWebp($uploadedFile, string $folder, string $errorKey): string
{
    $mimeType = $uploadedFile->getMimeType() ?: 'application/octet-stream';
    $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: $uploadedFile->extension() ?: 'jpg');

    return $this->uploadLocalFileToSupabase(
        $uploadedFile->getRealPath(),
        $folder,
        $mimeType,
        $extension,
        $errorKey
    );
}

    public function render()
    {
        return view('livewire.settings.index');
    }
}