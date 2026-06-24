<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Helpers\CardHelper;
use App\Models\Card;
use Illuminate\Support\Facades\Log;

class CheckCardExistence implements ValidationRule
{
    protected $tipo;

    public function __construct($tipo)
    {
        $this->tipo = $tipo;
    }




    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // $value contendrá el valor de 'CRD_INTSNR' que se está validando.

            $reg_card = CardHelper::getCardByIntSnr($value);

            if (empty($reg_card)) {
                $fail('La tarjeta con el serial interno proporcionado no existe o no cumple con los criterios.');
                return;
            }

            $cardData = $reg_card[0];

            $card = Card::where('ISS_ID', $cardData->iss_id)
                ->where('CD_ID', $cardData->cd_id)
                ->where('CRD_SNR', $cardData->crd_snr)
                ->first();

            if (!empty($card) and $this->tipo == 1) {

                if (strpos($value, '-') !== false) {
                    $fail('El número de tarjeta proporcionado ya se encuentra registrado.');
                } else {
                    $fail('La tarjeta con el serial interno proporcionado ya se encuentra registrada.');
                }
            }
        } catch (\Exception $e) {
            Log::warning('Card existence validation exception', [
                'attribute' => $attribute,
                'value' => $value,
                'error' => $e->getMessage(),
            ]);

            $fail('Error al validar la tarjeta: ' . $e->getMessage());
        }
    }
}
