<?php

namespace App\Rules;

use App\Models\VCard;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PaymentReferenceValidationRule implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    // ...

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passes = false;

        switch ($this->data['payment_type']) {
            case 'VCARD':
                $passes = preg_match('/^9\d{8}$/', $value) && VCard::where('phone_number', $value)->exists();
                break;
            case 'MBWAY':
                $passes = preg_match('/^9\d{8}$/', $value);
                break;
            case 'PAYPAL':
                $passes = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                break;
            case 'IBAN':
                $passes = preg_match('/^[A-Z]{2}\d{23}$/', $value);
                break;
            case 'MB':
                $passes = preg_match('/^\d{5}-\d{9}$/', $value);
                break;
            case 'VISA':
                $passes = preg_match('/^4\d{15}$/', $value);
                break;
            default:
                $passes = false;
        }

        if (!$passes) {
            $fail("The payment reference is invalid.");
        }
    }
}