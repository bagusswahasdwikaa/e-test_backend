<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class UserFeedbackController extends Controller
{
    /**
     * User mengirim masukan ke admin
     */
    public function sendFeedback(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Masukan berhasil dikirim!'
        ], 201);
    }

    /**
     * User melihat daftar masukan yang sudah ia kirim
     */
    public function myFeedback()
    {
        $feedback = Feedback::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $feedback
        ]);
    }
}
