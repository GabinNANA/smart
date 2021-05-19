<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Categorie;
use App\Models\Proposition;
use App\Models\type;
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
            ->whereIn('propositions.id', function($query){
                $query->select('idequipement')
                ->from('equipement_outputs');
            })
            ->orderBy('choix')
            ->get();
        return response()->json($equipement);
    }
    public function produit()
    {
        $equipement = DB::table('propositions')
            ->select('propositions.*',
            DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
            ->join('questions', 'questions.id', 'propositions.idquestion')
            ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'produit')
            ->get();
        return response()->json($equipement);
    }

    public function environnement()
    {
        $environnement = DB::table('propositions')
            ->select('propositions.*','categories.intitule as categorieintitule,',
            DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
            ->join('categories', 'categories.id', 'propositions.idcategorie')
            ->join('questions', 'questions.id', 'propositions.idquestion')
            ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'environnement')
            ->get();
        return response()->json($environnement);
    }
    public function Equipementcategoriesearch(Request $request){
        //var_dump($request->all());
        //echo $request->categorie;
        if ($request->searchelt !== null AND $request->categorie !== null AND strlen($request->searchelt) != 0 AND strlen($request->categorie) != 0) {
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->join('etat_equipement', 'etat_equipement.idequipement', 'propositions.id')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
                ->where('etat',$request->etat)
                ->where('categories.id',$request->categorie)
                ->where('propositions.choix','LIKE',"%".$request->searchelt.'%')
                ->orderBy('choix')
                ->get();
        }
        elseif ($request->searchelt !== null AND $request->categorie === null AND strlen($request->searchelt) != 0 AND strlen($request->categorie) == 0) {
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->join('etat_equipement', 'etat_equipement.idequipement', 'propositions.id')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
                ->where('etat',$request->etat)
                ->where('propositions.choix','LIKE',"%".$request->searchelt.'%')
                ->orderBy('choix')
                ->get();
        }
        elseif ($request->searchelt === null AND $request->categorie !== null AND strlen($request->searchelt) == 0 AND strlen($request->categorie) != 0) {
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->join('etat_equipement', 'etat_equipement.idequipement', 'propositions.id')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
                ->where('etat',$request->etat)
                ->where('categories.id',$request->categorie)
                ->orderBy('choix')
                ->get();
        }
        else{
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->join('etat_equipement', 'etat_equipement.idequipement', 'propositions.id')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement')
                ->where('etat',$request->etat)
                ->orderBy('choix')
                ->get();
        }
        
        return response()->json($equipement);
    }
    public function Environnementcategoriesearch(Request $request){
        //var_dump($request->all());
        //echo $request->categorie;
        if ($request->searchelt !== null AND $request->categorie !== null AND strlen($request->searchelt) != 0 AND strlen($request->categorie) != 0) {
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'environnement')
                ->where('categories.id',$request->categorie)
                ->where('propositions.choix','LIKE',"%".$request->searchelt.'%')
                ->get();
        }
        elseif ($request->searchelt !== null AND $request->categorie === null AND strlen($request->searchelt) != 0 AND strlen($request->categorie) == 0) {
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'environnement')
                ->where('propositions.choix','LIKE',"%".$request->searchelt.'%')
                ->get();
        }
        elseif ($request->searchelt === null AND $request->categorie !== null AND strlen($request->searchelt) == 0 AND strlen($request->categorie) != 0) {
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'environnement')
                ->where('categories.id',$request->categorie)
                ->get();
        }
        else{
            $equipement = DB::table('propositions')
                ->select('propositions.*','categories.intitule as categorieintitule,',
                DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'environnement')
                ->get();
        }
        
        return response()->json($equipement);
    }

    public function Environnementcategorie($id){
        $equipement = DB::table('propositions')
                ->select('propositions.*', 'categories.intitule as categorieintitule,',
                    DB::raw('(SELECT label FROM types WHERE questions.idtype = id) as intituletype'))
                ->join('categories', 'categories.id', 'propositions.idcategorie')
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'), '=', 'equipement')
                ->where('categories.id', $id)
                ->get();
        return response()->json($equipement);
    }

    public function CategorieEquipement()
    {
        $catequip = DB::table('categories')
            ->select('categories.*')
            ->whereIn('id', function($query){
                $query->select('idcategorie')
                ->from(with(new Proposition)->getTable())
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'equipement');
            })
            ->get();
        return response()->json($catequip);
    }

    public function CategorieEnvironnement()
    {
        $catequip = DB::table('categories')
            ->select('categories.*')
            ->whereIn('id', function($query){
                $query->select('idcategorie')
                ->from(with(new Proposition)->getTable())
                ->join('questions', 'questions.id', 'propositions.idquestion')
                ->where(DB::raw('(SELECT label FROM types WHERE questions.idtype = id)'),'=', 'environnement');
            })
            ->get();
        return response()->json($catequip);
    }
    /**
     * Display a listing of the question depend of type habitation et etat.
     *
     * @return \Illuminate\Http\Response
     */
    public function getquestionhabitation($lesdeux)
    {

        // $questionsex = DB::table('habitation_questions')
        //          ->select('habitation_questions.*')
        //          ->where('etat','construction')
        //          ->get();
        // //echo count($questionsex);
        // foreach ($questionsex as $key => $value) {
        //     //echo $key."<br>";
        //     DB::table('habitation_questions')->insert(
        //          array(
        //                 'idhabitation'     =>   $value->idhabitation, 
        //                 'idquestion'   =>   $value->idquestion,
        //                 'etat'   =>   'exploitation'
        //          )
        //     );
        // }
        $typehabitation = explode('-',$lesdeux)[0];
        $etat = explode('~',explode('-',$lesdeux)[1])[0];
        $exclure =  count(explode('~',explode('-',$lesdeux)[1])) > 1 ? (explode('~',explode('-',$lesdeux)[1])[1] == 'niveau' ? 'hauteursaisie' : 'niveau') : '';
        
        $produit = count(explode('~',explode('-',$lesdeux)[1])) > 2 ? explode(',',explode('~',explode('-',$lesdeux)[1])[2]) : [];
       // echo "SELECT questions.*,(SELECT label FROM types WHERE types.id=questions.idtype limit 1) as typequestion FROM questions JOIN habitation_questions ON habitation_questions.idquestion=questions.id WHERE habitation_questions.idhabitation=".$typehabitation." AND habitation_questions.etat='".$etat."' AND (SELECT label FROM types WHERE types.id=questions.idtype limit 1) != '".$exclure."' ORDER BY ordre ASC,questions.id ASC";
        $questions = DB::select("SELECT questions.*,(SELECT label FROM types WHERE types.id=questions.idtype limit 1) as typequestion FROM questions JOIN habitation_questions ON habitation_questions.idquestion=questions.id WHERE habitation_questions.idhabitation=".$typehabitation." AND habitation_questions.etat='".$etat."' AND (SELECT label FROM types WHERE types.id=questions.idtype limit 1) != '".$exclure."' AND questions.id != 17 ORDER BY ordre ASC,questions.id ASC");
        // DB::table('questions')
        //          ->select('questions.*')
        //          ->addSelect(['typequestion' => Type::select('label')
        //                 ->whereColumn('idtype', 'types.id')
        //                 ->limit(1),
        //             ])
        //          ->join('habitation_questions','habitation_questions.idquestion','questions.id')
        //          ->where('habitation_questions.idhabitation',$typehabitation)
        //          ->where('habitation_questions.etat',$etat)
        //         ->where(function ($query) {
        //             $query->select('label')
        //                 ->from('types')
        //                 ->whereColumn('idtype', 'types.id')
        //                 ->limit(1);
        //         }, 'Pro')
        //          ->orderBy('ordre')->orderBy('questions.id')
        //          ->get();
        $equipement = array();
        foreach ($questions as $key => $value) {
            if ($typehabitation == 18) {
                if ((in_array('90', $produit) OR in_array('91', $produit) OR in_array('92', $produit) OR in_array('93', $produit) OR in_array('94', $produit) OR in_array('95', $produit) OR in_array('96', $produit) OR in_array('97', $produit) OR in_array('98', $produit) OR in_array('103', $produit) OR in_array('100', $produit) OR in_array('101', $produit) OR in_array('106', $produit) OR in_array('107', $produit) OR in_array('105', $produit) OR in_array('108', $produit)) AND $value->typequestion =='tonne') {
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
                    $miniquestion['nbautrehabitation'] = DB::table('liaison_habitation')->select('*')->where('liaison_habitation.idhabitationdebut',$typehabitation)->count();
                    array_push($equipement,$miniquestion);
                }
                if ((in_array('91', $produit) OR in_array('92', $produit) OR in_array('93', $produit) OR in_array('94', $produit) OR in_array('95', $produit) OR in_array('99', $produit) OR in_array('100', $produit) OR in_array('101', $produit) OR in_array('102', $produit) OR in_array('104', $produit) OR in_array('105', $produit) OR in_array('109', $produit) OR in_array('110', $produit) OR in_array('111', $produit)) AND $value->typequestion == 'volume') {
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
                    $miniquestion['nbautrehabitation'] = DB::table('liaison_habitation')->select('*')->where('liaison_habitation.idhabitationdebut',$typehabitation)->count();
                    array_push($equipement,$miniquestion);
                }
                if ((in_array('103', $produit)) AND $value->typequestion == 'effectif') {
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
                    $miniquestion['nbautrehabitation'] = DB::table('liaison_habitation')->select('*')->where('liaison_habitation.idhabitationdebut',$typehabitation)->count();
                    array_push($equipement,$miniquestion);
                }
                if ($value->typequestion == 'niveau' || $value->typequestion == 'hauteursaisie' || $value->typequestion == 'superficie' || $value->typequestion == 'budget') {
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
                    $miniquestion['nbautrehabitation'] = DB::table('liaison_habitation')->select('*')->where('liaison_habitation.idhabitationdebut',$typehabitation)->count();
                    array_push($equipement,$miniquestion);
                }
            }
            else{
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
                $miniquestion['nbautrehabitation'] = DB::table('liaison_habitation')->select('*')->where('liaison_habitation.idhabitationdebut',$typehabitation)->count();
                array_push($equipement,$miniquestion);
            }
            
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
