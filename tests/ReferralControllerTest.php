<?php

namespace Jijunair\LaravelReferral\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Jijunair\LaravelReferral\Models\Referral;
use Jijunair\LaravelReferral\Tests\Fixtures\User;

class ReferralControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_referrer_sets_referral_cookie(): void
    {
        $response = $this->get('/save20/ABC123');

        $response
            ->assertRedirect(route('home'))
            ->assertCookie(config('referral.cookie_name'), 'ABC123');
    }

    public function test_assign_referrer_keeps_existing_referral_cookie(): void
    {
        $response = $this
            ->withCookie(config('referral.cookie_name'), 'OLD123')
            ->get('/save20/NEW123');

        $response
            ->assertRedirect(route('home'))
            ->assertCookieMissing(config('referral.cookie_name'));
    }

    public function test_create_referral_code_for_existing_users(): void
    {
        $users = collect(range(1, 5))
            ->map(fn (int $number): User => User::query()->create([
                'name' => "User {$number}",
                'email' => "user{$number}@example.test",
            ]));

        $response = $this->get('/generate-ref-accounts');

        $response
            ->assertOk()
            ->assertJson(['message' => 'Referral codes generated for existing users.']);

        foreach ($users as $user) {
            $user->refresh();

            $this->assertTrue($user->hasReferralAccount());
            $this->assertNotNull($user->getReferralCode());
        }
    }

    public function test_referrable_trait_creates_account_with_optional_referrer(): void
    {
        $referrer = User::query()->create([
            'name' => 'Referrer',
            'email' => 'referrer@example.test',
        ]);

        $user = User::query()->create([
            'name' => 'Referred',
            'email' => 'referred@example.test',
        ]);

        $user->createReferralAccount($referrer->getKey());

        $this->assertDatabaseHas('referrals', [
            'user_id' => $user->getKey(),
            'referrer_id' => $referrer->getKey(),
        ]);

        $this->assertSame($referrer->getKey(), $user->refresh()->referralAccount->referrer_id);
    }

    public function test_user_can_be_resolved_by_referral_code(): void
    {
        $user = User::query()->create([
            'name' => 'Code Owner',
            'email' => 'owner@example.test',
        ]);

        Referral::query()->create([
            'user_id' => $user->getKey(),
            'referral_code' => 'ref_abc123',
        ]);

        $this->assertTrue($user->is(Referral::userByReferralCode('ref_abc123')));
        $this->assertNull(Referral::userByReferralCode('missing'));
    }
}
