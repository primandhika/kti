<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;

class PostReactionController extends Controller
{
    private const VALID_REACTIONS = ['like', 'informatif', 'inspiratif'];

    public function toggle(Request $request, Post $post)
    {
        $request->validate([
            'reaction_type' => ['required', 'string', 'in:' . implode(',', self::VALID_REACTIONS)],
        ]);

        $sessionId = $request->session()->getId();
        $reactionType = $request->reaction_type;

        $existing = PostReaction::where('post_id', $post->id)
            ->where('reaction_type', $reactionType)
            ->where('session_id', $sessionId)
            ->first();

        if ($existing) {
            $existing->delete();
            $active = false;
        } else {
            PostReaction::create([
                'post_id'       => $post->id,
                'reaction_type' => $reactionType,
                'session_id'    => $sessionId,
                'ip_address'    => $request->ip(),
            ]);
            $active = true;
        }

        $counts = PostReaction::where('post_id', $post->id)
            ->selectRaw('reaction_type, COUNT(*) as total')
            ->groupBy('reaction_type')
            ->pluck('total', 'reaction_type');

        $userReactions = PostReaction::where('post_id', $post->id)
            ->where('session_id', $sessionId)
            ->pluck('reaction_type');

        return response()->json([
            'active'        => $active,
            'reaction_type' => $reactionType,
            'counts'        => $counts,
            'user_reactions' => $userReactions,
        ]);
    }

    public function getReactions(Request $request, Post $post)
    {
        $sessionId = $request->session()->getId();

        $counts = PostReaction::where('post_id', $post->id)
            ->selectRaw('reaction_type, COUNT(*) as total')
            ->groupBy('reaction_type')
            ->pluck('total', 'reaction_type');

        $userReactions = PostReaction::where('post_id', $post->id)
            ->where('session_id', $sessionId)
            ->pluck('reaction_type');

        return response()->json([
            'counts'        => $counts,
            'user_reactions' => $userReactions,
        ]);
    }
}
