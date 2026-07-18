<?php

namespace App\Livewire\Landing;

use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $company = '';
    public string $subject = '';
    public string $message = '';

    // Honeypot: legitimate visitors never fill this field.
    public string $website = '';

    public bool $sent = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'company' => ['nullable', 'string', 'max:160'],
            'subject' => ['required', 'string', 'min:4', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'subject.required' => 'Please add a subject.',
            'message.required' => 'Please describe the project or opportunity.',
            'message.min' => 'Please provide at least 20 characters so the request is clear.',
        ];
    }

    public function submit(): void
    {
        // Return a neutral success state to bots without storing their payload.
        if ($this->website !== '') {
            $this->resetFormAfterSuccess();
            return;
        }

        $validated = $this->validate();
        $rateLimitKey = $this->rateLimitKey();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError(
                'message',
                'Too many submissions. Please try again in '.max(1, (int) ceil($seconds / 60)).' minute(s).'
            );
            return;
        }

        RateLimiter::hit($rateLimitKey, 3600);

        ContactMessage::query()->create([
            'name' => trim($validated['name']),
            'email' => mb_strtolower(trim($validated['email'])),
            'company' => $this->nullableTrim($validated['company'] ?? null),
            'subject' => trim($validated['subject']),
            'message' => trim($validated['message']),
            'status' => ContactMessage::STATUS_NEW,
            'ip_hash' => hash_hmac(
                'sha256',
                (string) request()->ip(),
                (string) config('app.key')
            ),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 500),
        ]);

        $this->resetFormAfterSuccess();
    }

    public function render(): View
    {
        return view('livewire.landing.contact-form');
    }

    private function rateLimitKey(): string
    {
        return 'portfolio-contact:'.hash('sha256', implode('|', [
            (string) request()->ip(),
            mb_strtolower(trim($this->email)),
        ]));
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function resetFormAfterSuccess(): void
    {
        $this->reset(['name', 'email', 'company', 'subject', 'message', 'website']);
        $this->resetValidation();
        $this->sent = true;
    }
}
