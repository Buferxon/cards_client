<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

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
        $serialHex = $value;
        $serialHex = str_replace(':', '', $serialHex); // Remove colons
        // Split hex string into pairs
        $hexPairs = str_split($serialHex, 2);
        // Reverse array of pairs
        $reversedPairs = array_reverse($hexPairs);
        // Join back to string
        $reversedHex = implode('', $reversedPairs);
        // Convert to integer
        $crd_intsnr = hexdec($reversedHex);

        // dd($crd_intsnr); // Debugging line to check the converted value

        $reg_card = DB::connection('oracle_mercury')->select("
            select c.ISS_ID,
                c.CD_ID,
                c.CRD_SNR,
                c.CTY_ID,
                c.CRD_CHKDG,
                c.CRD_INTSNR,
                c.CRD_CERTIFICATE,
                c.CRD_STATUS,
                c.CRD_REGDATE,
                c.CRD_REGUSER,
                c.CRD_SECONDCOPYTAX,
                u.USR_NAME as user_name,
                ud.USRDOC_NUMBER as user_document
            from MERCURY.CARDS c
                left join MERCURY.CARDSXUSERS cu on c.ISS_ID = cu.ISS_ID and c.CD_ID = cu.CD_ID and c.CRD_SNR = cu.CRD_SNR
                left join MERCURY.USERS u on cu.USR_ID = u.USR_ID and u.USR_STATUS='A'
                left join MERCURY.USERDOCUMENTS ud on u.USR_ID = ud.USR_ID and ud.USRDOC_STATUS='A'
            where c.CRD_INTSNR = :CRD_INTSNR
            and (ud.DT_ID in (87, 88, 83) or ud.DT_ID is null)
        ", [
            'CRD_INTSNR' => $crd_intsnr
        ]);

        if (empty($reg_card)) {
            $fail('La tarjeta con el serial interno proporcionado no existe o no cumple con los criterios.');
        }
    }
}
