<?php

namespace App\Http\Controllers;

use App\Models\type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = Type::all();
        return response()->json($types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
        ]);
        $type = Type::create($request->all());
        return response()->json(['message'=> 'type crée', 
        'type' => $type]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Type::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(type $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $type = type::findOrFail($id);
        $request->validate([
            'label'=> 'required',
        ]);
        $type->label = $request->label;

        $type->save();
        
        return response()->json([
            'message' => 'type modifié!',
            'type' => $type
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type= Type::find($id);
        $type->delete();
        return response()->json([
            'message' => 'type supprimé'
        ]);
    }
}
