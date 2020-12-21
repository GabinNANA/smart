<?php

namespace App\Http\Controllers;

use App\Models\User_reponse;
use App\Models\Output;
use App\Models\Habitation;
use DB;
use Illuminate\Http\Request;

class UserReponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_reponse = User_reponse::all();
        return response()->json($user_reponse);
    }
    /**
     * Display all output to create result.
     *
     * @return \Illuminate\Http\Response
     */
    public function output($id)
    {
        $firstquestion = User_reponse::where('id',$id)->first();
        if($firstquestion){
            $typehabitation = Habitation::where('id',$firstquestion->idhabitation)->first();
            $etat = $firstquestion->etat;
            $array = array();
            $arraytitre = array();
            $getoutputgeneralite = Output::whereIn('titre',['output1000','output 1100','output 1200','output 1300',
            'output 1400','output 1500','output 1600','output 1700','output 1800','output 1900','output 2000',
            'output 2100','output 2200','output 2300','output 2400'])->get();
            foreach($getoutputgeneralite as $key=>$value){
                if(!in_array($value->titre,$arraytitre)){
                    $arraytitre []= $value->titre;
                    $array []= $value;
                }                
            }
            //getoutput hotel
            if(strtolower($typehabitation->intitule) == 'hôtel' OR strtolower($typehabitation->intitule) == 'hotel'
            OR strtolower($typehabitation->intitule) == 'hotels'){
                
                $getoutputgeneralitesuite = Output::whereIn('titre', ['output 4','output 41','output 42','output 3','output 30',
                'output 31','output 32','output 33','output 34','output 4','output 35','output 50','output 51','output 52',
                'output 53', 'output 54'])->get();
                    
                foreach($getoutputgeneralitesuite as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $array []= $value;
                    }                
                }
                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                    $hauteur = ($niveau - 1) * 2.8;
                    $chambre = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'chambre')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                    $effectif = (2 * $chambre) + 10;

                    if($niveau > 5){
                        $output = Output::where('titre', 'output 6')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                
                        }
                    }                    
                    if ($chambre > 10 and $chambre < 100 and $effectif > 100 and $effectif < 300) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){                            
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                                     
                        }
                    }
                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                                       
                        }
                    }
                    if ($chambre > 100 and $effectif > 100 and $effectif < 300) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                                     
                        }
                    }
                    if ($chambre > 100 and $effectif > 300 and $effectif < 700) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                                      
                        }
                    }
                    if ($chambre > 100 and $effectif > 700 and $effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){   
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                                        
                        }
                    }
                    if ($chambre > 100 and $effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                                        
                        }
                    }

                    $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($superficie){
                        if($superficie->response < 2){
                            $output = Output::where('titre', 'output 71')->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                       
                            }
                        }
                        if($superficie->response > 2 AND $superficie->response < 20){
                            $output = Output::where('titre', 'output 72')->get();
                            foreach($output as $key=>$value){
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                              
                            }
                        }
                        if($superficie->response > 20){
                            $output = Output::where('titre', 'output 73')->get();
                            foreach($output as $key=>$value){
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                    
                            }
                        }
                    }                    

                    $etoile = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'etoile')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($etoile){
                        if($etoile->response == 1){
                            $output = Output::where('titre', 'output 71')->get();
                            foreach($output as $key=>$value){      
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                     
                            }
                        }
                        if($etoile->response > 1){
                            $output = Output::where('titre', 'output 72')->get();
                            foreach($output as $key=>$value){
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                  
                            }
                        }
                    }

                    $budget = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'budget')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($budget){
                        if($budget->response > 100000000){
                            $output = Output::whereIn('titre', ['output 9','output 331'
                            ,'output 332','output 333'])->get();
                            foreach ($output as $key => $value) {
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                
                            }

                        }
                    }

                    $travauxvoiries = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.intitule','LIKE', '%voirie%')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($travauxvoiries->response=="Oui" AND $etat == 'execution'){
                        $output = Output::whereIn('titre', ['output 16','output 161'])->get();
                        array_push($getoutputgeneralite, $output);
                            foreach($output as $key=>$value){
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                     
                            }
                    }
                    $effectiftravailleurs = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.intitule','LIKE', '%travailleur%')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($effectiftravailleurs){
                        if($effectiftravailleurs->response >= 50 AND $etat == 'execution'){
                            $output = Output::whereIn('titre', ['output 9','output 331'
                            ,'output 332','output 333'])->get();
                            foreach($output as $key=>$value){
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                   
                            }
                        }
                    }

                    if($etat == 'execution'){
                        $output = Output::whereIn('titre', ['output 18','output 19','output 20','output 21',
                        'output 12','output 22','output 23'])->get();
                        foreach ($output as $key => $value) {
                            if(!in_array($value->titre,$arraytitre)){
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }                
                        }

                    }

                    if($etat == 'exploitation'){
                        $output = Output::whereIn('titre', ['output 24','output 25','output 30','output 31',
                        'output 331','output 32'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                   
                            }

                        $monclassification = User_reponse::classification($id); 
                        foreach ($monclassification as $key => $value) {
                            if($value->titre == 'ouput 106'){
                                $output = Output::whereIn('titre', ['output 26','output 29'])->get();
                            foreach($output as $key=>$value){
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                             
                            }
                            }
                            if($value->titre == 'ouput 105'){
                                $output = Output::whereIn('titre', ['output 27'])->get();
                            foreach($output as $key=>$value){    
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                          
                            }
                            }
                            if($value->titre == 'ouput 104'){
                                $output = Output::whereIn('titre', ['output 28'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }                                
                            }
                            }
                        }
                    }
                }
            }

            return  $array;
        }
    }

    /**
     * Display classification of habitation.
     *
     * @return \Illuminate\Http\Response
     */
    public function classification($id)
    {
        $request = new Request;
        $firstquestion = User_reponse::where('id',$id)->first();
        if($firstquestion){
            $typehabitation = Habitation::where('id',$firstquestion->idhabitation)->first();
            $etat = $firstquestion->etat;
            if((strtolower($typehabitation->intitule) == 'hôtel' OR strtolower($typehabitation->intitule) == 'hotel' 
            OR strtolower($typehabitation->intitule) == 'hotels') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                $chambre = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'chambre')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $effectif = (2*$chambre)+10;
                if($effectif < 100 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 101')->get();
                }
                if($effectif > 100 AND $effectif < 200 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 102')->get();
                }
                if($effectif > 300 AND $effectif < 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 103')->get();
                }
                if($effectif > 700 AND $effectif < 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 104')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 105')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output1 = Output::where('titre', 'output 106')->get();
                }
                if ($hauteur > 50) {
                    $output1 = Output::where('titre', 'output 107')->get();
                }
                if ($effectif < 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                if ($effectif > 100 and $effectif < 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                if ($effectif > 300 and $effectif < 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                if ($effectif > 700 and $effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                if ($chambre > 10 and $chambre < 100 and $effectif > 100 and $effectif < 300) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                if ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                if ($chambre > 100 and $effectif > 100 and $effectif < 300) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                if ($chambre > 100 and $effectif > 300 and $effectif < 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                if ($chambre > 100 and $effectif > 700 and $effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                if ($chambre > 100 and $effectif > 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                return response()->json($montablo);
            }
        }
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
        // $request->validate([
        //     'idusers' => 'required',
        //     'idhabitation' => '',
        //     'etat' => '',
        //     'idquestion' => '',
        //     'idparent' => '',
        //     'reponse' => 'required'
        // ]);
        $user_reponse = user_reponse::create($request->all());
        return response()->json(['message'=> 'Réponse utilisateur crée', 
        'user_reponse' => $user_reponse]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User_reponse::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function edit(User_reponse $user_reponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_reponse = user_reponse::findOrFail($id);
        $request->validate([
            'idusers' => 'required',
            'idhabitation' => '',
            'etat' => '',
            'idquestion' => '',
            'idparent' => '',
            'reponse' => 'required'
        ]);
        $user_reponse->idusers = $request->idusers;
        $user_reponse->idhabitation = $request->idhabitation;
        $user_reponse->etat = $request->etat;
        $user_reponse->idquestion = $request->idquestion;
        $user_reponse->idparent = $request->idparent;
        $user_reponse->response = $request->response;
        
        $user_reponse->save();
        
        return response()->json([
            'message' => 'user_reponse modifié!',
            'user_reponse' => $user_reponse
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User_reponse  $user_reponse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_reponse= User_reponse::find($id);
        $user_reponse->delete();
        return response()->json([
            'message' => 'user_reponse supprimé'
        ]);
    }
}
