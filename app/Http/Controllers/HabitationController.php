<?php

namespace App\Http\Controllers;

use App\Models\Habitation;
use Illuminate\Http\Request;
use DB;
use App\Models\Categorie;
use App\Models\Proposition;
use App\Models\type;

class HabitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $habitations = Habitation::orderBy('intitule','asc')
                     ->get();
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
    public function show($id)
    {
        return Habitation::find($id);
    }
    public function autre($id)
    {
        return Habitation::where('id','!=',$id)->get();
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
    public function update(Request $request, $id)
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

    public function autrehabitation($typehabitation)
    {
        $idhabitation = $typehabitation;
        $etat = 'construction';


        $habitation = DB::select('SELECT * FROM liaison_habitation WHERE idhabitationdebut='.$idhabitation);
        $autrequestion = array();$otherquestions = array();
        foreach ($habitation as $key => $value) {
            if ($value->idhabitationfin == 19) {
                $fin = 3;
            }
            else{
                $fin = 1;
            }
            for ($i=0; $i <$fin ; $i++) { 
                $autrequestion['id'] = $value->idhabitationfin.($value->idhabitationfin == 19 ? '_'.($i+1) : '');
                $autrequestion['intitule'] = habitation::where('id',$value->idhabitationfin)->first()->intitule.($value->idhabitationfin == 19 ? ' '.($i+1) : '');
                $questions = DB::table('questions')
                     ->select('questions.*')
                     ->addSelect(['typequestion' => Type::select('label')
                            ->whereColumn('idtype', 'types.id')
                            ->limit(1),
                        ])
                     ->join('habitation_questions','habitation_questions.idquestion','questions.id')
                     ->where('habitation_questions.idhabitation',$value->idhabitationfin)
                     ->where('habitation_questions.etat',$etat)
                     ->orderBy('ordre','asc')
                     ->get();
                $equipement = array();
                foreach ($questions as $cle => $valeur) {
                    if ($value->idhabitationfin == 1 && $valeur->typequestion == 'chambre') {
                        $miniquestion = array();
                        $proposition = Proposition::select('propositions.*')
                            ->addSelect(['namecategorie' => Categorie::select('intitule')
                                    ->whereColumn('idcategorie', 'categories.id')
                                    ->limit(1),
                            ])->where('idquestion', '=', $valeur->id)->get();
                        $miniquestion['id'] = $valeur->id;
                        $miniquestion['intitule'] = $valeur->intitule;
                        $miniquestion['type'] = $valeur->type;
                        $miniquestion['typequestion'] = $valeur->typequestion;
                        $miniquestion['proposition'] = $proposition;
                        array_push($equipement,$miniquestion);
                    }
                    if ($value->idhabitationfin == 10 && $valeur->typequestion == 'effectif') {
                        $miniquestion = array();
                        $proposition = Proposition::select('propositions.*')
                            ->addSelect(['namecategorie' => Categorie::select('intitule')
                                    ->whereColumn('idcategorie', 'categories.id')
                                    ->limit(1),
                            ])->where('idquestion', '=', $valeur->id)->get();
                        $miniquestion['id'] = $valeur->id;
                        $miniquestion['intitule'] = $valeur->intitule;
                        $miniquestion['type'] = $valeur->type;
                        $miniquestion['typequestion'] = $valeur->typequestion;
                        $miniquestion['proposition'] = $proposition;
                        array_push($equipement,$miniquestion);
                    }
                    if ($value->idhabitationfin == 9 && ($valeur->typequestion == 'lit' || $valeur->typequestion == 'consultation')) {
                        $miniquestion = array();                     
                        if ($value->idhabitationdebut == 10) {
                            $proposition = Proposition::select('propositions.*')
                                ->addSelect(['namecategorie' => Categorie::select('intitule')
                                        ->whereColumn('idcategorie', 'categories.id')
                                        ->limit(1),
                                ])
                                ->where('idquestion', '=', $valeur->id)
                                ->whereIn('id',[87])
                                ->get();
                        }    

                        $miniquestion['id'] = $valeur->id;
                        $miniquestion['intitule'] = $valeur->intitule;
                        $miniquestion['type'] = $valeur->type;
                        $miniquestion['typequestion'] = $valeur->typequestion;
                        $miniquestion['proposition'] = $proposition;
                        array_push($equipement,$miniquestion);
                    }
                    if ($value->idhabitationfin == 8 && ($valeur->typequestion == 'superficie' || $valeur->typequestion == 'nature')) {

                        $miniquestion = array();
                        $proposition = Proposition::select('propositions.*')
                            ->addSelect(['namecategorie' => Categorie::select('intitule')
                                    ->whereColumn('idcategorie', 'categories.id')
                                    ->limit(1),
                            ])->where('idquestion', '=', $valeur->id)->get();
                        $miniquestion['id'] = $valeur->id;
                        $miniquestion['intitule'] = $valeur->intitule;
                        $miniquestion['type'] = $valeur->type;
                        $miniquestion['typequestion'] = $valeur->typequestion;
                        $miniquestion['proposition'] = $proposition;
                        array_push($equipement,$miniquestion);
                    }
                    if ($value->idhabitationfin == 19 && ($valeur->typequestion == 'place' ||$valeur->typequestion == 'superficie' || $valeur->typequestion == 'nature')) {
                        $miniquestion = array();
                        if ($value->idhabitationdebut == 17) {
                            $proposition = Proposition::select('propositions.*')
                                ->addSelect(['namecategorie' => Categorie::select('intitule')
                                        ->whereColumn('idcategorie', 'categories.id')
                                        ->limit(1),
                                ])
                                ->where('idquestion', '=', $valeur->id)
                                ->get();
                        }
                        if ($value->idhabitationdebut == 1) {
                            $proposition = Proposition::select('propositions.*')
                                ->addSelect(['namecategorie' => Categorie::select('intitule')
                                        ->whereColumn('idcategorie', 'categories.id')
                                        ->limit(1),
                                ])
                                ->where('idquestion', '=', $valeur->id)
                                ->whereIn('id',[154,153,144,143,148,147])
                                ->get();
                        } 
                        if ($value->idhabitationdebut == 7) {
                            $proposition = Proposition::select('propositions.*')
                                ->addSelect(['namecategorie' => Categorie::select('intitule')
                                        ->whereColumn('idcategorie', 'categories.id')
                                        ->limit(1),
                                ])
                                ->where('idquestion', '=', $valeur->id)
                                ->whereIn('id',[154,152,147,148,143,144,152])
                                ->get();
                        }   
                        if ($value->idhabitationdebut == 6) {
                            $proposition = Proposition::select('propositions.*')
                                ->addSelect(['namecategorie' => Categorie::select('intitule')
                                        ->whereColumn('idcategorie', 'categories.id')
                                        ->limit(1),
                                ])
                                ->where('idquestion', '=', $valeur->id)
                                ->whereIn('id',[144,143,154,153])
                                ->get();
                        }  
                        if ($value->idhabitationdebut == 10) {
                            $proposition = Proposition::select('propositions.*')
                                ->addSelect(['namecategorie' => Categorie::select('intitule')
                                        ->whereColumn('idcategorie', 'categories.id')
                                        ->limit(1),
                                ])
                                ->where('idquestion', '=', $valeur->id)
                                ->whereIn('id',[144])
                                ->get();
                        }                  
                        
                        $miniquestion['id'] = $valeur->id;
                        $miniquestion['intitule'] = $valeur->intitule;
                        $miniquestion['type'] = $valeur->type;
                        $miniquestion['typequestion'] = $valeur->typequestion;
                        $miniquestion['proposition'] = $proposition;
                        array_push($equipement,$miniquestion);
                    }
                    if (($value->idhabitationfin == 3 || $value->idhabitationfin == 6 || $value->idhabitationfin == 7 || $value->idhabitationfin == 13 || $value->idhabitationfin == 14 || $value->idhabitationfin == 15) && $valeur->typequestion == 'superficie') {
                        $miniquestion = array();
                        $proposition = Proposition::select('propositions.*')
                            ->addSelect(['namecategorie' => Categorie::select('intitule')
                                    ->whereColumn('idcategorie', 'categories.id')
                                    ->limit(1),
                            ])->where('idquestion', '=', $valeur->id)->get();
                        $miniquestion['id'] = $valeur->id;
                        $miniquestion['intitule'] = $valeur->intitule;
                        $miniquestion['type'] = $valeur->type;
                        $miniquestion['typequestion'] = $valeur->typequestion;
                        $miniquestion['proposition'] = $proposition;
                        array_push($equipement,$miniquestion);
                    }
                }
                $questions = $equipement;

                $autrequestion['questions'] = $questions; 
                $otherquestions[] = $autrequestion;
            }
            
        }
        //print_r($otherquestions);
        return  response()->json($otherquestions);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Habitation  $habitation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $habitation= Habitation::find($id);
        $habitation->delete();
        return response()->json([
            'message' => 'habitation supprimé'
        ]);
    }
}