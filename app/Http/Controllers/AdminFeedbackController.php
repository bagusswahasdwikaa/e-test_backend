<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    /**
     * Admin melihat semua masukan dari user.
     */
    public function index()
    {
        $feedback = Feedback::with('user')
            ->orderBy('is_read', 'asc')       // unread first
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($feedback);
    }

    /**
     * Admin melihat detail satu masukan (menandai sebagai terbaca).
     */
    public function show($id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);

        if (!$feedback->is_read) {
            $feedback->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Feedback berhasil diambil',
            'data' => $feedback
        ]);
    }

    public function markAsRead($id)
    {
        $feedback = Feedback::findOrFail($id);

        if (!$feedback->is_read) {
            $feedback->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Feedback berhasil ditandai sebagai dibaca',
            'data' => $feedback
        ]);
    }

    /**
     * Admin melihat jumlah notifikasi masukan baru (yang belum dibaca)
     */
    public function unreadCount()
    {
        $count = Feedback::where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'unread' => $count
        ]);
    }

     public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback berhasil dihapus'
        ]);
    }
}
