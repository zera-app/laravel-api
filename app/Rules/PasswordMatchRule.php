<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class PasswordMatchRule implements ValidationRule
{
    public function __construct(private string $uuid)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($this->uuid);

        if (!Hash::check($value, $user->password)) {
            $fail(__('The :attribute was not match with current password.'));
        }

        return;
    }
}
