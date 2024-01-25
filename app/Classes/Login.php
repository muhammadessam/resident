<?php

namespace App\Classes;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseAuth;

class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('user_name')->label('اسم المتخدم')->required()->exists('users', 'user_name'),
        ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return $data;
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->getCredentialsFromFormData($this->form->getState());
        $user = User::where('user_name', $data['user_name'])->first();
        if ($user) {
            Filament::auth()->login($user);
            $user = Filament::auth()->user();
            if (($user instanceof FilamentUser) && (!$user->canAccessPanel(Filament::getCurrentPanel()))) {
                Filament::auth()->logout();
                $this->throwFailureValidationException();
            }
        } else {
            $this->throwFailureValidationException();
        }
        session()->regenerate();
        activity()->causedBy($user)->withProperty('date', now())->log('تم تسجيل الدخول بواسطة: ');
        return app(LoginResponse::class);
    }

}
