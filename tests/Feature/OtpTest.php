<?php

namespace Tests\Feature;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sending OTP fails when validation requirements are not met.
     */
    public function test_send_otp_validation_errors(): void
    {
        // 1. Missing email
        $response = $this->postJson('/api/send-otp', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // 2. Invalid email format
        $response = $this->postJson('/api/send-otp', ['email' => 'not-an-email']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // 3. Email does not exist in users table
        $response = $this->postJson('/api/send-otp', ['email' => 'unknown@example.com']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test sending OTP succeeds for an existing user.
     */
    public function test_send_otp_successfully(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'traveler@example.com',
        ]);

        $response = $this->postJson('/api/send-otp', [
            'email' => 'traveler@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP code has been sent to your email successfully.',
                'data' => [
                    'email' => 'traveler@example.com',
                    'expires_in_minutes' => 10,
                ],
            ]);

        $this->assertDatabaseHas('password_reset_otps', [
            'email'       => 'traveler@example.com',
            'is_verified' => false,
        ]);

        Mail::assertSent(PasswordResetOtpMail::class, function ($mail) {
            return $mail->hasTo('traveler@example.com') && strlen($mail->otpCode) === 6;
        });
    }

    /**
     * Test rate limiting / cooldown on sending OTP.
     */
    public function test_send_otp_cooldown_rate_limiting(): void
    {
        Mail::fake();

        User::factory()->create(['email' => 'frequent@example.com']);

        // First attempt succeeds
        $res1 = $this->postJson('/api/send-otp', ['email' => 'frequent@example.com']);
        $res1->assertStatus(200);

        // Immediate second attempt triggers 429
        $res2 = $this->postJson('/api/send-otp', ['email' => 'frequent@example.com']);
        $res2->assertStatus(429)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test verify OTP validation errors.
     */
    public function test_verify_otp_validation_errors(): void
    {
        $response = $this->postJson('/api/verify-otp', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'otp_code']);

        $response = $this->postJson('/api/verify-otp', [
            'email'    => 'user@example.com',
            'otp_code' => '123', // Less than 6 characters
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['otp_code']);
    }

    /**
     * Test verify OTP fails when no OTP was requested.
     */
    public function test_verify_otp_fails_when_no_active_otp_exists(): void
    {
        $response = $this->postJson('/api/verify-otp', [
            'email'    => 'nonexistent@example.com',
            'otp_code' => '123456',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'No active OTP request found for this email address.',
            ]);
    }

    /**
     * Test verify OTP fails when incorrect code is provided.
     */
    public function test_verify_otp_fails_with_invalid_code(): void
    {
        PasswordResetOtp::create([
            'email'       => 'test@example.com',
            'otp_code'    => '654321',
            'expires_at'  => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'email'    => 'test@example.com',
            'otp_code' => '111111',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid OTP code. Please check and try again.',
            ]);
    }

    /**
     * Test verify OTP fails when expired.
     */
    public function test_verify_otp_fails_when_expired(): void
    {
        PasswordResetOtp::create([
            'email'       => 'expired@example.com',
            'otp_code'    => '999888',
            'expires_at'  => now()->subMinute(),
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'email'    => 'expired@example.com',
            'otp_code' => '999888',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'OTP code has expired. Please request a new one.',
            ]);
    }

    /**
     * Test verify OTP succeeds, marks verified, and issues reset token.
     */
    public function test_verify_otp_succeeds_and_issues_token(): void
    {
        PasswordResetOtp::create([
            'email'       => 'success@example.com',
            'otp_code'    => '123456',
            'expires_at'  => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'email'    => 'success@example.com',
            'otp_code' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'email',
                    'reset_token',
                ],
            ]);

        $this->assertDatabaseHas('password_reset_otps', [
            'email'       => 'success@example.com',
            'is_verified' => true,
        ]);
    }

    /**
     * Test full password reset flow using verified reset token.
     */
    public function test_reset_password_with_verified_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'resetme@example.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = 'secure-reset-token-example-123456789012345678901234567890';

        PasswordResetOtp::create([
            'email'       => 'resetme@example.com',
            'otp_code'    => '123456',
            'token'       => $token,
            'expires_at'  => now()->addMinutes(10),
            'is_verified' => true,
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email'                 => 'resetme@example.com',
            'reset_token'           => $token,
            'password'              => 'newsecurepassword123',
            'password_confirmation' => 'newsecurepassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now log in with your new password.',
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newsecurepassword123', $user->password));

        // OTP record should be deleted after consumption
        $this->assertDatabaseMissing('password_reset_otps', [
            'email' => 'resetme@example.com',
        ]);
    }
}
