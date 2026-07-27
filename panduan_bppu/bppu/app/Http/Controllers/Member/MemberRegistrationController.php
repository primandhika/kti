<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\RegistrationRequest;
use App\Http\Requests\Member\SurveyRequest;
use App\Services\Member\MemberRegistrationService;
use App\Services\Member\MemberSurveyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberRegistrationController extends Controller
{
    public function __construct(
        protected MemberRegistrationService $registrationService,
        protected MemberSurveyService $surveyService
    ) {}

    public function showRegistrationForm()
    {
        $membershipTiers = \App\Models\MembershipTier::where('is_active', true)
            ->orderBy('order')
            ->get();

        return Inertia::render('SelfOrder/Register', [
            'membershipTiers' => $membershipTiers,
        ]);
    }

    public function register(RegistrationRequest $request)
    {
        try {
            $user = $this->registrationService->createMember($request->validated());

            session(['pending_survey_user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil! Silakan isi survey berikut.',
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
            ], 500);
        }
    }

    public function submitSurvey(SurveyRequest $request)
    {
        try {
            $userId = session('pending_survey_user_id') ?? $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid. Silakan registrasi ulang.',
                ], 400);
            }

            $user = \App\Models\User::findOrFail($userId);

            $this->surveyService->saveSurvey($user, $request->validated());

            $this->registrationService->sendVerificationEmail($user);

            session()->forget('pending_survey_user_id');

            return response()->json([
                'success' => true,
                'message' => 'Survey berhasil disimpan! Silakan cek email Anda untuk verifikasi akun.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan survey. Silakan coba lagi.',
            ], 500);
        }
    }

    public function showVerifyEmailPage(Request $request)
    {
        $token = $request->query('token');

        return Inertia::render('SelfOrder/VerifyEmail', [
            'token' => $token,
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token verifikasi tidak valid.',
            ], 400);
        }

        $verified = $this->registrationService->verifyEmail($token);

        if ($verified) {
            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi! Anda dapat login sekarang.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token verifikasi tidak valid atau sudah kadaluarsa.',
        ], 400);
    }

    public function showTermsConditions()
    {
        $membershipTiers = \App\Models\MembershipTier::where('is_active', true)
            ->orderBy('order')
            ->get();

        return Inertia::render('SelfOrder/TermsConditions', [
            'membershipTiers' => $membershipTiers,
        ]);
    }
}
