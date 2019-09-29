<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolRequest;
use App\Rol;

class RolController extends Controller
{
    public function index()
    {
        $rols = Rol::latest()->get();

        return response()->json($rols);
    }

    public function store(RolRequest $request)
    {
        $rol = Rol::create($request->all());

        return response()->json($rol, 201);
    }

    public function show($id)
    {
        $rol = Rol::findOrFail($id);

        return response()->json($rol);
    }

    public function update(RolRequest $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->all());

        return response()->json($rol, 200);
    }

    public function destroy($id)
    {
        Rol::destroy($id);

        return response()->json(null, 204);
    }
}