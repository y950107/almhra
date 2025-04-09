<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Request;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseAuth;
use Filament\Models\Contracts\FilamentUser;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
 
class Login extends BaseAuth
{

    
    public function authenticate(): ?LoginResponse
    {
       
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
     
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            Notification::make()
            ->title('هناك خطأ في المعلومات المدخلة')
            ->danger()
            ->send();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getUserNameFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
 
    protected function getUserNameFormComponent(): Component 
    {
        return TextInput::make('username')
            ->label('رقم الهاتف / البريد الالكتروني')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    } 

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['username'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'phone';
        return [
            $login_type => $data['username'],
            'password'  => $data['password'],
        ];
    }


    public function getHeading(): string
    {
        $panel = Filament::getCurrentPanel()->getId();

        return match ($panel) {
            'admin' => 'تسجيل الدخول - لوحة الإدارة',
            'teacher' => 'تسجيل الدخول - صفحةالمعلمين ',
            'student' => 'تسجيل الدخول - صفحة الطالب',
            default => 'تسجيل الدخول',
        };
    }

    
}