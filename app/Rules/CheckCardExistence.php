<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use App\Helpers\CardHelper;
use App\Models\Card;

class CheckCardExistence implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // $value contendrá el valor de 'CRD_INTSNR' que se está validando.

        $reg_card = CardHelper::getCardByIntSnr($value);

        if (empty($reg_card)) {
            $fail('La tarjeta con el serial interno proporcionado no existe o no cumple con los criterios.');
        }

        $card = Card::where('ISS_ID', $reg_card->iss_id ?? null)
            ->where('CD_ID', $reg_card->cd_id ?? null)
            ->where('CRD_SNR', $reg_card->crd_snr ?? null)
            ->first();

        if (!empty($card)) {
            $fail('La tarjeta con el serial interno proporcionado ya se encuentra registrada.');
        }
    }
}
