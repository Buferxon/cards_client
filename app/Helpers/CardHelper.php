<?php
// app/Helpers/CardHelper.php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class CardHelper
{
  public static function getCardByIntSnr($crd_intsnr)
  {



    // Verificar si el formato es "19-6-05213930-4"
    if (preg_match('/^(\d+)-(\d+)-(\d+)-(\d+)$/', $crd_intsnr, $matches)) {
      $iss_id = $matches[1];
      $cd_id = $matches[2];
      $crd_snr = $matches[3];

      return DB::connection('oracle_mercury')->select("
            select c.ISS_ID,
                   c.CD_ID,
                   c.CRD_SNR,
                   c.CTY_ID,
                   c.DC_CODE,
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
             left join MERCURY.CARDSXUSERS cu
                on c.ISS_ID = cu.ISS_ID
               and c.CD_ID = cu.CD_ID
               and c.CRD_SNR = cu.CRD_SNR
             left join MERCURY.USERS u
                on cu.USR_ID = u.USR_ID
               and u.USR_STATUS='A'
             left join MERCURY.USERDOCUMENTS ud
                on u.USR_ID = ud.USR_ID
               and ud.USRDOC_STATUS='A' and (ud.DT_ID in (87, 88, 83) or ud.DT_ID is null)
             where c.ISS_ID = :ISS_ID
               and c.CD_ID = :CD_ID
               and c.CRD_SNR = :CRD_SNR
               and rownum <= 1
            ", [
        'ISS_ID' => $iss_id,
        'CD_ID' => $cd_id,
        'CRD_SNR' => $crd_snr
      ]);
    }


    $serialHex = str_replace(':', '', $crd_intsnr); // Remove colons

    // Split hex string into pairs
    $hexPairs = str_split($serialHex, 2);
    // Reverse array of pairs
    $reversedPairs = array_reverse($hexPairs);
    // Join back to string
    $reversedHex = implode('', $reversedPairs);
    // Convert to integer
    $crd_intsnr = hexdec($reversedHex);

    return DB::connection('oracle_mercury')->select("
            select c.ISS_ID,
                   c.CD_ID,
                   c.CRD_SNR,
                   c.CTY_ID,
                   c.DC_CODE,
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
         left join MERCURY.CARDSXUSERS cu
                on c.ISS_ID = cu.ISS_ID
               and c.CD_ID = cu.CD_ID
               and c.CRD_SNR = cu.CRD_SNR
         left join MERCURY.USERS u
                on cu.USR_ID = u.USR_ID
               and u.USR_STATUS='A'
         left join MERCURY.USERDOCUMENTS ud
                on u.USR_ID = ud.USR_ID
               and ud.USRDOC_STATUS='A' and (ud.DT_ID in (87, 88, 83) or ud.DT_ID is null)
             where c.CRD_INTSNR = :CRD_INTSNR and rownum <= 1
               
        ", [
      'CRD_INTSNR' => $crd_intsnr
    ]);
  }

  public static function getMultipleCardsByIntSnr($crd_intsnr_array)
  {
    if (empty($crd_intsnr_array)) {
      return [];
    }

    // Crear placeholders para la consulta IN
    $placeholders = implode(',', array_fill(0, count($crd_intsnr_array), '?'));

    return DB::connection('oracle_mercury')->select("
            select c.ISS_ID,
                   c.CD_ID,
                   c.CRD_SNR,
                   c.CTY_ID,
                   c.DC_CODE,
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
         left join MERCURY.CARDSXUSERS cu
                on c.ISS_ID = cu.ISS_ID
               and c.CD_ID = cu.CD_ID
               and c.CRD_SNR = cu.CRD_SNR
         left join MERCURY.USERS u
                on cu.USR_ID = u.USR_ID
               and u.USR_STATUS='A'
         left join MERCURY.USERDOCUMENTS ud
                on u.USR_ID = ud.USR_ID
               and ud.USRDOC_STATUS='A' and (ud.DT_ID in (87, 88, 83) or ud.DT_ID is null)
             where c.CRD_INTSNR IN ({$placeholders})
        ", $crd_intsnr_array);
  }
}
