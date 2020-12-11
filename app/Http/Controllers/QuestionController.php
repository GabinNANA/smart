<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Categorie;
use App\Models\Proposition;
use App\Models\Type;
use DB;
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
     * Display a listing of equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function equipement()
    {
        $equipement = DB::table('propositions')
            ->select('propositions.*','categories.intitule as categorieintitule,',
            DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
            ->join('categories', 'categories.id', 'propositions.idcategorie')
            ->join('questions', 'questions.id', 'propositions.idquestion')
            ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
            ->get();
        return response()->json($equipement);
    }

    public function Equipementcategorie($id){
        $request = new  Request;
        if($request->input('query') != ''){
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
                ->where('categories.id',$request->input('categorie'))
                ->where('propositions.choix','LIKE',"%".$request->input('query').'%')
                ->get();
        }
        else{
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
                ->where('categories.id',$id)
                ->get();
        }
        return response()->json($equipement);
    }

    /**
     * Display a categorie listing of equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function CategorieEquipement()
    {
        $equipement = DB::table('categories')
            ->select('categories.*')
            ->whereIn('id', function($query){
                $query->select('idcategorie')
                ->from(with(new Proposition)->getTable())
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement');
            })
            ->get();
        return response()->json($equipement);
    }

    /**
     * Display a listing of the question depend of type habitation et etat.
     *
     * @return \Illuminate\Http\Response
     */
    public function getquestionhabitation($lesdeux)
    {
        $typehabitation = explode('-',$lesdeux)[0];
        $etat = explode('-',$lesdeux)[1];

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
