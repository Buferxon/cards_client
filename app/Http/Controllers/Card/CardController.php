<?php

namespace App\Http\Controllers\Card;

use App\Helpers\CardHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\GetCardRequest;
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

            return [
                'success' => false,
                'error' => 'Error al almacenar la tarjeta',
                'message' => $e->getMessage()
            ];
        }
    }




    /**
     * @OA\Get(
     *     path="/api/v1/cards/{crd_intsnr}",
     *     summary="Obtener información de una tarjeta por su número de serie interno",
     *     description="Retorna la información de una tarjeta específica utilizando el número de serie interno (crd_intsnr).",
     *     operationId="getCard",
     *     tags={"Cards"},
     *     @OA\Parameter(
     *         name="crd_intsnr",
     *         in="path",
     *         description="Número de serie interno de la tarjeta",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarjeta obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tarjeta obtenida exitosamente"),
     *             @OA\Property(property="card_number", type="string", example="19-06-05213930-4"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="iss_id", type="string", example="19"),
     *                     @OA\Property(property="cd_id", type="string", example="6"),
     *                     @OA\Property(property="crd_snr", type="string", example="5213930"),
     *                     @OA\Property(property="cty_id", type="string", example="1"),
     *                     @OA\Property(property="dc_code", type="string", nullable=true, example=null),
     *                     @OA\Property(property="crd_chkdg", type="string", example="4"),
     *                     @OA\Property(property="crd_intsnr", type="string", example="538327456"),
     *                     @OA\Property(property="crd_certificate", type="string", example="0"),
     *                     @OA\Property(property="crd_status", type="string", example="A"),
     *                     @OA\Property(property="crd_regdate", type="string", format="date-time", example="2024-08-23 09:18:07"),
     *                     @OA\Property(property="crd_reguser", type="string", example="PROCEDURE"),
     *                     @OA\Property(property="crd_secondcopytax", type="string", example="0"),
     *                     @OA\Property(property="user_name", type="string", example="ROBINSON BUENAVENTURA FERNANDEZ"),
     *                     @OA\Property(property="user_document", type="string", example="1006155540")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="La tarjeta con el número de serie proporcionado no existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="La tarjeta con el número de serie proporcionado no existe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener la tarjeta",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al obtener la tarjeta"),
     *             @OA\Property(property="error", type="string", example="Mensaje de error")
     *         )
     *     )
     * )
     */

    //     {
    //   "success": true,
    //   "message": "Tarjeta obtenida exitosamente",
    //   "card_number": "19-6-05213930-4",
    //   "data": [
    //     {
    //       "iss_id": "19",
    //       "cd_id": "6",
    //       "crd_snr": "5213930",
    //       "cty_id": "1",
    //       "dc_code": null,
    //       "crd_chkdg": "4",
    //       "crd_intsnr": "538327456",
    //       "crd_certificate": "0",
    //       "crd_status": "A",
    //       "crd_regdate": "2024-08-23 09:18:07",
    //       "crd_reguser": "PROCEDURE",
    //       "crd_secondcopytax": "0",
    //       "user_name": "ROBINSON BUENAVENTURA FERNANDEZ",
    //       "user_document": "1006155540"
    //     }
    //   ]
    // }


    public function getCard(GetCardRequest $request)
    {

        try {
            $crd_intsnr = $request->input('crd_intsnr');


            // Validar que el número de serie de la tarjeta exista
            $card = CardHelper::getCardByIntSnr($crd_intsnr);

            // dd($card);

            if (!$card) {
                return response()->json([
                    'success' => false,
                    'message' => 'La tarjeta con el número de serie proporcionado no existe'
                ], 404);
            }

            // dd($card);

            $cd_id_padded = str_pad($card[0]->cd_id, 2, '0', STR_PAD_LEFT);

            // Asegurarse de que CRD_SNR tenga 8 dígitos, rellenando con ceros a la izquierda si es necesario
            $crd_snr_padded = str_pad($card[0]->crd_snr, 8, '0', STR_PAD_LEFT);
            $card_number = $card[0]->iss_id . '-' . $cd_id_padded . '-' . $crd_snr_padded . '-' . $card[0]->crd_chkdg;
            return response()->json([
                'success' => true,
                'message' => 'Tarjeta obtenida exitosamente',
                'card_number' => $card_number,
                'data' => $card
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la tarjeta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
