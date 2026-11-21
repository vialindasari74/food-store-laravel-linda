<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class MyProfileController extends Controller implements HasMiddleware
{
    /**
     * middleware
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            'auth:api'
        ];
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $profile = Customer::query()
            ->where('id', auth()->guard('api')->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Detail Profil',
            'customer' => $profile
        ]);
    }

    /**
     * update
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        //run validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:customers,email,' . auth()->guard('api')->user()->id,
            'password' => 'sometimes|min:8',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // ambil profil customer yang login
        $profile = Customer::query()
            ->where('id', auth()->guard('api')->user()->id)
            ->firstOrFail();

        // Jika ada upload gambar, hapus dan update
        if ($request->hasFile('image')) {

            // hapus gambar lama jika ada
            if ($profile->image) {
                Storage::disk('public')->delete('avatars/' . $profile->image);
            }

            // upload gambar baru
            $image = $request->file('image');
            $image->storeAs('avatars', $image->hashName());

            // Simpan nama file ke database
            $profile->image = $image->hashName();
        }

        // update data profil
        $profile->name = $request->name;
        $profile->email = $request->email;

        // update password jika disediakan
        if ($request->filled('password')) {
            $profile->password = Hash::make($request->password);
        }

        // Simpan perubahan
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Update profil berhasil',
            'data' => $profile
        ]);
    }
}