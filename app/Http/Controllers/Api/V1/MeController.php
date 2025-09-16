<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;

class MeController extends Controller
{
    // GET /api/me
    public function show(Request $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        return response()->json([
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
            'firebase_uid' => $u->firebase_uid,
        ]);
    }

    //PUT /api/me
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $u */
        $u = $request->attributes->get('auth_user');

        $u->fill($request->validated());
        $u->save();

        return response()->json([
            'id' => $u->id,
            'name' => $u->name,
            'username' => $u->username,
            'email' => $u->email,
        ]);
    }
}
