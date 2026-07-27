<?php

namespace App\Services\Member;

use App\Models\User;
use App\Models\MemberSurvey;

class MemberSurveyService
{
    public function saveSurvey(User $user, array $data): MemberSurvey
    {
        return MemberSurvey::create([
            'user_id' => $user->id,
            'rating_pelayanan' => $data['rating_pelayanan'],
            'rating_kualitas' => $data['rating_kualitas'],
            'rating_variasi' => $data['rating_variasi'],
            'rating_harga' => $data['rating_harga'],
            'rating_suasana' => $data['rating_suasana'],
            'menu_favorit' => $data['menu_favorit'],
            'menu_rekomendasi' => $data['menu_rekomendasi'],
            'kritik_saran' => $data['kritik_saran'],
        ]);
    }

    public function hasSurvey(User $user): bool
    {
        return MemberSurvey::where('user_id', $user->id)->exists();
    }
}
