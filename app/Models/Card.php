<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'cards';
    protected $primaryKey = ['ISS_ID', 'CD_ID', 'CRD_SNR'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ISS_ID',
        'CD_ID',
        'CRD_SNR',
        'DC_CODE',
        'CTY_ID',
        'CRD_CHKDG',
        'CRD_INTSNR',
        'CRD_CERTIFICATE',
        'CRD_STATUS',
        'CRD_REGDATE',
        'CRD_REGUSER',
        'CRD_SECONDCOPYTAX',
        'is_personalized',
        'user_id',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Métodos CRUD básicos

    public static function getById($issId, $cdId, $crdSnr)
    {
        return self::where('ISS_ID', $issId)
            ->where('CD_ID', $cdId)
            ->where('CRD_SNR', $crdSnr)
            ->first();
    }

    public static function createCard(array $data)
    {
        return self::create($data);
    }

    public function updateCard(array $data)
    {
        $this->fill($data);
        $this->save();
        return $this;
    }

    public function deleteCard()
    {
        return $this->delete();
    }
}
