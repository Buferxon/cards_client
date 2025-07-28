<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function store($data_all)
    {
        try {

            // dd($data_all);
            $data = (array) ($data_all[0]);
            // Asegurarse de que user_id esté en los datos antes de crear
            $card = Card::create(
                [
                    'ISS_ID' => $data['iss_id'],
                    'user_id' => $data['user_id'],
                    'CD_ID' => $data['cd_id'],
                    'CRD_SNR' => $data['crd_snr'],
                    'DC_CODE' => $data['dc_code'],
                    'CTY_ID' => $data['cty_id'],
                    'CRD_CHKDG' => $data['crd_chkdg'],
                    'CRD_INTSNR' => $data['crd_intsnr'],
                    'CRD_CERTIFICATE' => $data['crd_certificate'],
                    'CRD_STATUS' => $data['crd_status'],
                    'CRD_REGDATE' => $data['crd_regdate'],
                    'CRD_REGUSER' => $data['crd_reguser'],
                    'CRD_SECONDCOPYTAX' => $data['crd_secondcopytax'],
                    'is_personalized' => $data['is_personalized'],
                ]
            );

            return [
                'success' => true,
                'message' => 'Card stored successfully',
                'data' => $card
            ];
        } catch (\Exception $e) {
            // Manejo de excepciones

            dd($e);
            return [
                'success' => false,
                'error' => 'Error al almacenar la tarjeta',
                'message' => $e->getMessage()
            ];
        }
    }
}
