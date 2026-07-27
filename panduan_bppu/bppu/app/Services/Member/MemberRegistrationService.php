<?php

namespace App\Services\Member;

use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Mail\MemberRegistrationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberRegistrationService
{
    public function createMember(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['nim'],
                'member_code' => $data['nim'],
                'password' => Hash::make($data['password']),
                'member_since' => now(),
                'total_points' => 0,
                'is_verified' => false,
            ]);

            $user->assignRole('buyer');

            $user->updateMembershipTier();

            return $user;
        });
    }

    public function sendVerificationEmail(User $user): void
    {
        $token = $this->generateVerificationToken($user);

        Mail::to($user->email)->send(new MemberRegistrationMail($user, $token));
    }

    public function generateVerificationToken(User $user): string
    {
        EmailVerificationToken::where('user_id', $user->id)->delete();

        $token = Str::random(64);

        EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        return $token;
    }

    public function verifyEmail(string $token): bool
    {
        $verificationToken = EmailVerificationToken::where('token', $token)->first();

        if (!$verificationToken || $verificationToken->isExpired()) {
            return false;
        }

        $user = $verificationToken->user;
        $user->update([
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        $verificationToken->delete();

        return true;
    }
}
