<?php

namespace Jijunair\LaravelReferral\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'referral_code',
        'referrer_id',
    ];

    /**
     * Get the user associated with the referral.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Model, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('referral.user_model'), 'user_id');
    }

    /**
     * Get the referrer associated with the referral.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Model, $this>
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(config('referral.user_model'), 'referrer_id');
    }

    /**
     * Retrieve the user by referral code.
     */
    public static function userByReferralCode(string $code): ?Model
    {
        $referral = self::query()->where('referral_code', $code)->first();
        if (! $referral instanceof self) {
            return null;
        }

        $userModel = config('referral.user_model');

        if (! is_string($userModel) || ! is_subclass_of($userModel, Model::class)) {
            return null;
        }

        return $userModel::query()->find($referral->user_id);
    }
}
