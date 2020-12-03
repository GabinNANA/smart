<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::all();
        return response()->json($questions);
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
            'idtype' => '',
            'intitule' => 'required',
            'ordre' => 'required',
            'type' => 'required',
        ]);
        $question = Question::create($request->all());
        return response()->json(['message'=> 'question crée', 
        'question' => $question]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Question::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $question = question::findOrFail($id);
        $request->validate([
            'idtype'=> '',
            'intitule'=> 'required',
            'ordre'=> 'required',
            'type'=> 'required',
        ]);
        $question->intitule = $request->intitule;
        $question->ordre = $request->ordre;
        $question->type = $request->type;
        
        $question->save();
        
        return response()->json([
            'message' => 'catégorie modifié!',
            'question' => $question
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $question= Question::find($id);
        $question->delete();
        return response()->json([
            'message' => 'question supprimé'
        ]);
    }
}
