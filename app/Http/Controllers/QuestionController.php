<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = DB::table('questions')
            ->get();
        $results = array();
            
        foreach ($questions as $question) {
            $result = array();

            if($question->idtype!=NULL){

                $type = DB::table('types')->find($question->idtype);
                $result['label']=$type->intitule;

            }else{
                $result['label']='';          
                
            }
            $result['question']=$question;
            array_push($results,$result);
        }
        return  response()->json($results);
    }

    /**
     * Liste des questions avec leurs propositions
     */
    public function proposition()
    {
        $questions = DB::table('questions')
            ->join('propositions', 'questions.id', '=', 'propositions.idquestion')
            ->select('questions.*')
            ->distinct('id')
            ->get();
            
        $results = array();
        foreach ($questions as $question) {
            $result = array();
            $propositions = DB::table('propositions')
                ->where('idquestion', $question->id)
                ->get();

            $result['intitule'] = $question->intitule;
            $result['ordre'] = $question->ordre;
            $result['obligatoire'] = $question->obligatoire;
            $result['proposition'] = $propositions;

            array_push($results,$result);
        }

        return response()->json($results);
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
            'ordre' => '',
            'type' => 'required',
            'obligatoire'=>''
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
        $question = Question::find($id);
        
        $result = array();
        $propositions = DB::table('propositions')
            ->where('idquestion', $question->id)
            ->get();

        $result['id'] = $question->id;
        $result['idtype'] = $question->idtype;
        $result['intitule'] = $question->intitule;
        $result['obligatoire'] = $question->obligatoire;
        $result['ordre'] = $question->ordre;
        $result['type'] = $question->type;
        $result['proposition'] = $propositions;
        $result['tailletab'] = sizeof($propositions);

        return response()->json($result);
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
            'ordre'=> '',
            'type'=> 'required',
            'obligatoire'=>''
        ]);
        $question->intitule = $request->intitule;
        $question->ordre = $request->ordre;
        $question->type = $request->type;
        $question->obligatoire = $request->obligatoire;
        
        $question->save();
        
        return response()->json([
            'message' => 'catégorie modifié!',
            'question' => $question
        ]);
    }

    public function destroyprop($id)
    {
        $propositions = DB::table('propositions')
            ->where('idquestion',$id)
            ->delete();

        return response()->json([
            'message' => 'Propositions supprimées'
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
        $propositions = DB::table('propositions')
            ->where('idquestion',$id)
            ->delete();

        $question= Question::where('id',$id)
            ->delete();
            
        return response()->json([
            'message' => 'question supprimé'
        ]);
    }
}
