<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Categorie;
use App\Models\Proposition;
use App\Models\Type;
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
     * Display a listing of equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function equipement()
    {
        $equipement = DB::table('propositions')
            ->select(DB::raw('propositions.*,categories.intitule as categorieintitule,
                        (SELECT label FROM types WHERE questions.idtype = id) AS intituletype'))
            ->join('categories', 'categories.id', 'propositions.idcategorie')
            ->join('questions', 'questions.id', 'propositions.idquestion')
            ->where('questions.intituletype', 'equipement')
            ->get();
        return response()->json($equipement);
    }

    /**
     * Display a listing of the question depend of type habitation et etat.
     *
     * @return \Illuminate\Http\Response
     */
    public function getquestionhabitation(Request $request)
    {
        $typehabitation = $request->habitation;
        $etat = $request->etat;

        $questions = DB::table('questions')
                 ->select('questions.*')
                 ->addSelect(['typequestion' => Type::select('label')
                        ->whereColumn('idtype', 'types.id')
                        ->limit(1),
                    ])
                 ->join('habitation_questions','habitation_questions.idquestion','questions.id')
                 ->where('habitation_questions.idhabitation',$typehabitation)
                 ->where('habitation_questions.etat',$etat)
                 ->orderBy('ordre','asc')
                 ->get();
        $equipement = array();
        foreach ($questions as $key => $value) {
            $miniquestion = array();
            $proposition = Proposition::select('propositions.*')
                ->addSelect(['namecategorie' => Categorie::select('intitule')
                        ->whereColumn('idcategorie', 'categories.id')
                        ->limit(1),
                ])->where('idquestion', '=', $value->id)->get();
            $miniquestion['id'] = $value->id;
            $miniquestion['intitule'] = $value->intitule;
            $miniquestion['type'] = $value->type;
            $miniquestion['typequestion'] = $value->typequestion;
            $miniquestion['proposition'] = $proposition;
            array_push($equipement,$miniquestion);
        }
        return response()->json($equipement);
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
    public function destroy(Question $question)
    {
        //
    }

    /**
     * get question with two first question answer.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function questionAnswer($idquestionreponse)
    {
        
    }
}
