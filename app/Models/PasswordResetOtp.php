<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $table = 'password_reset_otps';
    protected $primaryKey = 'otp_id';

    protected $fillable = [
        'email',
        'otp_code',
        'token',
        'expires_at',
        'is_verified',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Check if the OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP matches and is valid.
     */
    public function isValid(string $code): bool
    {
        return !$this->is_verified
            && !$this->isExpired()
            && hash_equals((string) $this->otp_code, (string) $code);
    }
}
