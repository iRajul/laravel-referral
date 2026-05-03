<?php

namespace Jijunair\LaravelReferral\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Jijunair\LaravelReferral\Models\Referral;

trait Referrable
{
    /**
     * Get the referrals associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Referral, $this>
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referral account of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Referral, $this>
     */
    public function referralAccount(): HasOne
    {
        return $this->hasOne(Referral::class, 'user_id');
    }

    /**
     * Check if the user has a referral account.
     */
    public function hasReferralAccount(): bool
    {
        return ! is_null($this->referralAccount);
    }

    /**
     * Get the referral link for the user.
     */
    public function getReferralLink(): string
    {
        if (! $this->hasReferralAccount()) {
            return '';
        }

        return url('/').'/'.trim((string) config('referral.route_prefix'), '/').'/'.$this->getReferralCode();
    }

    /**
     * Get the referral code of the user's referral account.
     */
    public function getReferralCode(): ?string
    {
        if ($this->hasReferralAccount()) {
            return $this->referralAccount->referral_code;
        }

        return null;
    }

    /**
     * Create a referral account for the user.
     */
    public function createReferralAccount(?int $referrerID = null): void
    {
        $prefix = (string) config('referral.ref_code_prefix', '');
        $length = (int) config('referral.referral_length', 8);
        $referralCode = $this->generateUniqueReferralCode($prefix, $length);

        Referral::query()->create([
            'user_id' => $this->getKey(),
            'referrer_id' => $referrerID,
            'referral_code' => $referralCode,
        ]);
    }

    /**
     * Generate a unique referral code.
     */
    private function generateUniqueReferralCode(string $prefix, int $length): string
    {
        $prefix = strtolower($prefix);
        $code = $prefix.strtolower(Str::random($length));

        while (Referral::query()->where('referral_code', $code)->exists()) {
            $code = $prefix.strtolower(Str::random($length));
        }

        return $code;
    }
}
