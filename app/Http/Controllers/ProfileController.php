<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;  // <== Tambahkan ini
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $fullName = trim($user->first_name . ' ' . $user->last_name);

        return response()->json([
            'full_name'  => $fullName,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'bio'        => $user->bio,
            'photo_url'  => $user->photo_url,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'full_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio'        => ['nullable', 'string', 'max:500'],
            'photo_url'  => ['nullable', 'string'], // base64 string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $fullName = trim($request->input('full_name'));
        $parts = explode(' ', $fullName, 2);
        $firstName = $parts[0];
        $lastName = $parts[1] ?? '';

        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $request->input('email');
        $user->bio = $request->input('bio', '');

        if ($request->filled('photo_url')) {
            $base64 = $request->input('photo_url');

            if (Str::startsWith($base64, 'data:image')) {
                @list($type, $fileData) = explode(';', $base64);
                @list(, $fileData) = explode(',', $fileData);

                if ($fileData) {
                    $decoded = base64_decode($fileData);
                    if ($decoded !== false) {
                        $fileName = 'profile_' . $user->id . '_' . time() . '.png';
                        $filePath = 'profile_photos/' . $fileName;

                        Storage::disk('public')->put($filePath, $decoded);

                        $user->photo_url = asset('storage/' . $filePath);
                    }
                }
            }
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'full_name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email,
            'bio' => $user->bio,
            'photo_url' => $user->photo_url,
        ]);
    }
    
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if (!$user->photo_url) {
            return response()->json([
                'message' => 'Tidak ada foto profil untuk dihapus.',
            ], 404);
        }

        // Ambil path file dari URL, contoh: https://yourdomain.com/storage/profile_photos/namafile.png
        $storagePath = parse_url($user->photo_url, PHP_URL_PATH); // /storage/profile_photos/namafile.png

        // Konversi ke path relatif dari disk 'public'
        $relativePath = str_replace('/storage/', '', $storagePath); // profile_photos/namafile.png

        // Cek apakah file ada dan hapus
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        // Kosongkan photo_url di database
        $user->photo_url = null;
        $user->save();

        return response()->json([
            'message' => 'Foto profil berhasil dihapus.',
        ]);
    }
}
