<?php

namespace App\Livewire\Profile;

use App\Models\Education;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('About Me')]
class AboutEditor extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Profile Properties
    |--------------------------------------------------------------------------
    */

    public int $profileId;

    public string $name = '';

    public string $role = '';

    public string $bio = '';

    public string $birthDate = '';

    public string $domicile = '';

    public string $publicEmail = '';

    public string $professionalStatus = '';

    public string $workPreference = '';

    public string $aboutTitle = '';

    public string $aboutDescription = '';

    public string $linkedinUrl = '';

    public string $githubUrl = '';

    public string $cvUrl = '';

    public string $languagesText = '';

    public string $currentFocusText = '';

    public bool $isPublic = true;

    /*
    |--------------------------------------------------------------------------
    | Education Properties
    |--------------------------------------------------------------------------
    */

    public ?int $editingEducationId = null;

    public string $educationLevel = '';

    public string $educationInstitution = '';

    public string $educationMajor = '';

    public string $educationGpa = '';

    public string $educationStartYear = '';

    public string $educationEndYear = '';

    public string $educationStatus = '';

    public string $educationDescription = '';

    public int $educationSortOrder = 0;

    public bool $educationIsVisible = true;

    /*
    |--------------------------------------------------------------------------
    | Component Lifecycle
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $user = Auth::user();

        /*
         * Auth::user() dapat menghasilkan User atau null.
         * Pemeriksaan instanceof sekaligus membantu Intelephense
         * mengenali tipe object secara tepat.
         */
        if (! $user instanceof User) {
            abort(403);
        }

        /*
         * Gunakan method relasi agar hasilnya dapat dikenali
         * sebagai Profile|null oleh IDE.
         */
        $profile = $user
            ->profile()
            ->first();

        /*
         * Membuat profil jika user belum mempunyai profil.
         */
        if (! $profile instanceof Profile) {
            $hasPublicProfile = Profile::query()
                ->where('is_public', true)
                ->exists();

            $profile = $user
                ->profile()
                ->create([
                    'name' => $user->name,
                    'role' => 'Web Developer',
                    'is_public' => ! $hasPublicProfile,
                ]);
        }

        /*
         * Menjamin terdapat setidaknya satu profil publik.
         */
        $hasPublicProfile = Profile::query()
            ->where('is_public', true)
            ->exists();

        if (! $hasPublicProfile && ! $profile->is_public) {
            $profile->update([
                'is_public' => true,
            ]);

            $profile->refresh();
        }

        $this->profileId = $profile->id;

        $this->fillProfileForm($profile);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Actions
    |--------------------------------------------------------------------------
    */

    public function saveProfile(): void
    {
        $this->validate($this->profileRules());

        DB::transaction(function (): void {
            $profile = $this->ownedProfile();

            /*
             * Sistem hanya menggunakan satu profil publik.
             * Ketika profil ini diaktifkan, profil lain dinonaktifkan.
             */
            if ($this->isPublic) {
                Profile::query()
                    ->where('id', '!=', $profile->id)
                    ->update([
                        'is_public' => false,
                    ]);
            }

            $profile->update([
                'name' => trim($this->name),

                'role' => $this->emptyToNull(
                    $this->role
                ),

                'bio' => $this->emptyToNull(
                    $this->bio
                ),

                'birth_date' => $this->emptyToNull(
                    $this->birthDate
                ),

                'domicile' => $this->emptyToNull(
                    $this->domicile
                ),

                'public_email' => $this->emptyToNull(
                    $this->publicEmail
                ),

                'professional_status' => $this->emptyToNull(
                    $this->professionalStatus
                ),

                'work_preference' => $this->emptyToNull(
                    $this->workPreference
                ),

                'about_title' => $this->emptyToNull(
                    $this->aboutTitle
                ),

                'about_description' => $this->emptyToNull(
                    $this->aboutDescription
                ),

                'linkedin_url' => $this->emptyToNull(
                    $this->linkedinUrl
                ),

                'github_url' => $this->emptyToNull(
                    $this->githubUrl
                ),

                'cv_url' => $this->emptyToNull(
                    $this->cvUrl
                ),

                'languages' => $this->parseLines(
                    $this->languagesText
                ),

                'current_focus' => $this->parseLines(
                    $this->currentFocusText
                ),

                'is_public' => $this->isPublic,
            ]);
        });

        session()->flash(
            'profile_success',
            'Informasi About Me berhasil disimpan.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Education Actions
    |--------------------------------------------------------------------------
    */

    public function saveEducation(): void
    {
        $this->resetValidation();

        $maximumYear = now()->year + 10;

        $this->validate([
            'educationLevel' => [
                'required',
                'string',
                'max:50',
            ],

            'educationInstitution' => [
                'required',
                'string',
                'max:255',
            ],

            'educationMajor' => [
                'nullable',
                'string',
                'max:255',
            ],

            'educationGpa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:4',
            ],

            'educationStartYear' => [
                'nullable',
                'integer',
                'digits:4',
                'min:1900',
                'max:' . $maximumYear,
            ],

            'educationEndYear' => [
                'nullable',
                'integer',
                'digits:4',
                'min:1900',
                'max:' . $maximumYear,
            ],

            'educationStatus' => [
                'nullable',
                'string',
                'max:100',
            ],

            'educationDescription' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'educationSortOrder' => [
                'required',
                'integer',
                'min:0',
                'max:9999',
            ],

            'educationIsVisible' => [
                'boolean',
            ],
        ]);

        $startYear = $this->emptyToNull(
            $this->educationStartYear
        );

        $endYear = $this->emptyToNull(
            $this->educationEndYear
        );

        /*
         * Validasi manual agar tahun selesai tidak lebih kecil
         * daripada tahun mulai.
         */
        if (
            $startYear !== null
            && $endYear !== null
            && (int) $endYear < (int) $startYear
        ) {
            $this->addError(
                'educationEndYear',
                'Tahun selesai tidak boleh lebih kecil dari tahun mulai.'
            );

            return;
        }

        $profile = $this->ownedProfile();

        $data = [
            'level' => trim(
                $this->educationLevel
            ),

            'institution' => trim(
                $this->educationInstitution
            ),

            'major' => $this->emptyToNull(
                $this->educationMajor
            ),

            'gpa' => $this->emptyToNull(
                $this->educationGpa
            ),

            'start_year' => $startYear !== null
                ? (int) $startYear
                : null,

            'end_year' => $endYear !== null
                ? (int) $endYear
                : null,

            'status' => $this->emptyToNull(
                $this->educationStatus
            ),

            'description' => $this->emptyToNull(
                $this->educationDescription
            ),

            'sort_order' => $this->educationSortOrder,

            'is_visible' => $this->educationIsVisible,
        ];

        if ($this->editingEducationId !== null) {
            /*
             * ownedEducation() memiliki return type Education.
             * Intelephense akan mengenali update() sebagai method Eloquent.
             */
            $education = $this->ownedEducation(
                $this->editingEducationId
            );

            $education->update($data);

            $message = 'Riwayat pendidikan berhasil diperbarui.';
        } else {
            Education::query()->create(
                array_merge(
                    [
                        'profile_id' => $profile->id,
                    ],
                    $data
                )
            );

            $message = 'Riwayat pendidikan berhasil ditambahkan.';
        }

        $this->resetEducationForm();

        session()->flash(
            'education_success',
            $message
        );
    }

    public function editEducation(int $educationId): void
    {
        $education = $this->ownedEducation($educationId);

        $this->editingEducationId = $education->id;

        $this->educationLevel = $education->level;

        $this->educationInstitution = $education->institution;

        $this->educationMajor = $education->major ?? '';

        $this->educationGpa = $education->gpa !== null
            ? (string) $education->gpa
            : '';

        $this->educationStartYear = $education->start_year !== null
            ? (string) $education->start_year
            : '';

        $this->educationEndYear = $education->end_year !== null
            ? (string) $education->end_year
            : '';

        $this->educationStatus = $education->status ?? '';

        $this->educationDescription = $education->description ?? '';

        $this->educationSortOrder = $education->sort_order;

        $this->educationIsVisible = $education->is_visible;

        $this->resetValidation();
    }

    public function cancelEducationEdit(): void
    {
        $this->resetEducationForm();
    }

    public function deleteEducation(int $educationId): void
    {
        /*
        * Memastikan data pendidikan benar-benar dimiliki
        * oleh profil pengguna yang sedang login.
        */
        $education = $this->ownedEducation($educationId);

        /*
        * Gunakan destroy() dengan ID sebagai argumen.
        * Ini menghindari diagnostic Intelephense:
        * "Not enough arguments. Expected 1. Found 0."
        */
        Education::destroy($education->id);

        /*
        * Reset form apabila data yang sedang diedit
        * merupakan data yang baru saja dihapus.
        */
        if ($this->editingEducationId === $educationId) {
            $this->resetEducationForm();
        }

        session()->flash(
            'education_success',
            'Riwayat pendidikan berhasil dihapus.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        $profile = $this->ownedProfile();

        $educations = Education::query()
            ->where('profile_id', $profile->id)
            ->orderBy('sort_order')
            ->orderByDesc('end_year')
            ->orderByDesc('start_year')
            ->get();

        /*
         * Tidak menggunakan ->layout() karena layout sudah ditentukan
         * melalui atribut #[Layout('layouts.dashboard')].
         */
        return view('livewire.profile.about-editor', [
            'profile' => $profile,
            'educations' => $educations,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function profileRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'role' => [
                'nullable',
                'string',
                'max:150',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'birthDate' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'domicile' => [
                'nullable',
                'string',
                'max:150',
            ],

            'publicEmail' => [
                'nullable',
                'email',
                'max:255',
            ],

            'professionalStatus' => [
                'nullable',
                'string',
                'max:150',
            ],

            'workPreference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'aboutTitle' => [
                'nullable',
                'string',
                'max:255',
            ],

            'aboutDescription' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'linkedinUrl' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'githubUrl' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'cvUrl' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'languagesText' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'currentFocusText' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'isPublic' => [
                'boolean',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Ownership Helpers
    |--------------------------------------------------------------------------
    */

    private function ownedProfile(): Profile
    {
        $profile = Profile::query()
            ->whereKey($this->profileId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $profile instanceof Profile) {
            abort(404);
        }

        return $profile;
    }

    private function ownedEducation(int $educationId): Education
    {
        $profile = $this->ownedProfile();

        $education = Education::query()
            ->whereKey($educationId)
            ->where('profile_id', $profile->id)
            ->first();

        if (! $education instanceof Education) {
            abort(404);
        }

        return $education;
    }

    /*
    |--------------------------------------------------------------------------
    | Form Helpers
    |--------------------------------------------------------------------------
    */

    private function fillProfileForm(Profile $profile): void
    {
        $this->name = $profile->name;

        $this->role = $profile->role ?? '';

        $this->bio = $profile->bio ?? '';

        $this->birthDate = $profile->birth_date !== null
            ? $profile->birth_date->format('Y-m-d')
            : '';

        $this->domicile = $profile->domicile ?? '';

        $this->publicEmail = $profile->public_email ?? '';

        $this->professionalStatus = $profile->professional_status ?? '';

        $this->workPreference = $profile->work_preference ?? '';

        $this->aboutTitle = $profile->about_title ?? '';

        $this->aboutDescription = $profile->about_description ?? '';

        $this->linkedinUrl = $profile->linkedin_url ?? '';

        $this->githubUrl = $profile->github_url ?? '';

        $this->cvUrl = $profile->cv_url ?? '';

        $this->languagesText = implode(
            PHP_EOL,
            $profile->languages ?? []
        );

        $this->currentFocusText = implode(
            PHP_EOL,
            $profile->current_focus ?? []
        );

        $this->isPublic = (bool) $profile->is_public;
    }

    private function resetEducationForm(): void
    {
        $this->editingEducationId = null;

        $this->educationLevel = '';

        $this->educationInstitution = '';

        $this->educationMajor = '';

        $this->educationGpa = '';

        $this->educationStartYear = '';

        $this->educationEndYear = '';

        $this->educationStatus = '';

        $this->educationDescription = '';

        $this->educationSortOrder = 0;

        $this->educationIsVisible = true;

        $this->resetValidation();
    }

    private function emptyToNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== ''
            ? $value
            : null;
    }

    /**
     * Mengubah teks per baris menjadi array.
     *
     * Contoh input:
     *
     * Bahasa Indonesia
     * English
     *
     * Hasil:
     *
     * [
     *     'Bahasa Indonesia',
     *     'English',
     * ]
     */
    private function parseLines(string $value): array
    {
        $lines = preg_split(
            '/\r\n|\r|\n/',
            $value
        );

        if (! is_array($lines)) {
            return [];
        }

        return collect($lines)
            ->map(
                static fn (string $item): string => trim($item)
            )
            ->filter(
                static fn (string $item): bool => $item !== ''
            )
            ->unique()
            ->values()
            ->all();
    }
}
