<?php

namespace App\Http\Controllers;

use App\Models\Habitation;
use Illuminate\Http\Request;

class HabitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $habitations = Habitation::all();
        return response()->json($habitations);
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
            'intitule' => 'required',
        ]);
        $habitation = habitation::create($request->all());
        return response()->json(['message'=> 'habitation crée', 
        'habitation' => $habitation]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Habitation  $habitation
     * @return \Illuminate\Http\Response
     */
    public function show(Habitation $habitation)
    {
        return Habitation::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Habitation  $habitation
     * @return \Illuminate\Http\Response
     */
    public function edit(Habitation $habitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Habitation  $habitation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Habitation $habitation)
    {
        $habitation = habitation::findOrFail($id);
        $request->validate([
            'intitule'=> 'required',
        ]);
        $habitation->intitule = $request->intitule;
        
        $habitation->save();
        
        return response()->json([
            'message' => 'habitation modifié!',
            'habitation' => $habitation
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Habitation  $habitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Habitation $habitation)
    {
        $habitation= Habitation::find($id);
        $habitation->delete();
        return response()->json([
            'message' => 'habitation supprimé'
        ]);
    }
}
