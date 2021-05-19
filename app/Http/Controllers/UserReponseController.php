<?php

namespace App\Http\Controllers;

use App\Models\User_reponse;
use App\Models\Output;
use App\Models\Habitation;
use DB;
use Illuminate\Http\Request;
use PDF;
use App\Models\Proposition;

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
    public function questionreponse($id)
    {
        $niveau = DB::table('user_reponses')
                    ->select('*')
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->where('idparent', $id)
                    ->get();
        //var_dump($niveau);
        $arraytitre=array();$soeautre=0;
        foreach($niveau as $key=>$value){ 
            $untablo=array();
            if ($value->isautre != 0) {
                $soeautre++;
                if (strpos($value->intitule, 'produit')) {
                    if($value->response!=0){
                        $untablo['intitule'] = $value->intitule;
                        $mesreponse='';                    
                        $produit = explode(';', $value->response);
                        for ($i=0; $i <count($produit) ; $i++) { 
                            $mesreponse .=','.Proposition::where('id',$produit[$i])->first()->choix;
                        }
                        $untablo['response'] = substr($mesreponse, 1); 
                    }  
                }
                else{
                    $untablo['intitule'] = $value->intitule.' '.Habitation::where('id',$value->idhabitation)->first()->intitule;
                    $untablo['response'] = $value->response;      
                }                      
            }
            else{
                if (strpos($value->intitule, 'quipements')) {
                    //if($value->response!=0){
                        //echo 'un';
                        $untablo['intitule'] = $value->intitule;
                        $mesreponse='';                    
                        //print_r($value->response)
                        $produit = explode(';', substr($value->response,1));
                        for ($i=0; $i <count($produit) ; $i++) { 
                            $mesreponse .=','.Proposition::where('id',$produit[$i])->first()->choix;
                        }
                        //echo substr($mesreponse,1);
                        $untablo['response'] = substr($mesreponse,1); 
                    //}  
                }
                else{
                    if ($soeautre > 0) {
                        if (strpos($value->intitule, 'effectif')) {
                            $untablo['intitule'] = $value->intitule.'';
                        }
                        else{
                            $untablo['intitule'] = $value->intitule.'';
                        }
                    }
                    else{
                        if (strpos($value->intitule, 'effectif')) {
                            $untablo['intitule'] = $value->intitule.' '.Habitation::where('id',$value->idhabitation)->first()->intitule;
                        }
                        else{
                            $untablo['intitule'] = $value->intitule.'';
                        }
                    }                
                    $untablo['response'] = is_numeric($value->response) ? $this->format_money($value->response) : $value->response;
                }
                
            }           
            if (count($untablo) != 0) {
                $arraytitre[] = $untablo;  
            }                   
        }       
        //($arraytitre);
        return $arraytitre;
    }

    public function format_money($number){
        $n = $number;
        /*if($_SESSION['lang']=='fr')
            $n = number_format($number, 0, ',', ' ');
        else
            $n = number_format($number);*/
        $n = number_format($number, 0);
         //return str_replace(",", " ", $n)." Fcfa";
         return str_replace(",", " ", $n);
    }

    public function historique($id)
    {
        if (count(explode('~', $id)) > 1) {
            //$niveau = DB::query('SELECT user_reponses.*,habitations.intitule FROM user_reponses,habitations WHERE user_reponses.idhabitation = habitations.id AND user_reponses.idparent = 0 AND user_reponses.idu='.$id.' AND DATE_FORMAT(user_reponses.created_at,"%Y-%m-%d") ="'.$request->datehis.'"');
            $niveau = DB::table('user_reponses')
                    ->select('user_reponses.*','habitations.intitule')
                    ->join('habitations', 'habitations.id', 'user_reponses.idhabitation')
                    ->where('idparent', '0')
                    ->where('user_reponses.idusers', explode('~', $id)[0])
                    ->where(DB::raw("DATE_FORMAT(user_reponses.created_at, '%Y-%m-%d')"), explode('~', $id)[1])
                    ->orderBy('id','DESC')
                    ->get();
        }
        else{
            $niveau = DB::table('user_reponses')
                    ->select('user_reponses.*','habitations.intitule')
                    ->join('habitations', 'habitations.id', 'user_reponses.idhabitation')
                    ->where('idparent', '0')
                    ->where('user_reponses.idusers', $id)
                    ->orderBy('id','DESC')
                    ->get();
        }
        
        return $niveau;
    }
    public function outputexigence($id,Request $request)
    {
        $firstquestion = User_reponse::where('id',$id)->first();
        if($firstquestion){
            $isetude=0;$isnotice=0;$isetudedetaille=0;$isaudit=0;
            $typehabitation = Habitation::where('id',$firstquestion->idhabitation)->first();
            $etat = $firstquestion->etat;
            $arrayresult = array();
            $array = array();
            $arraytitre = array();
            //echo $typehabitation->intitule;
            //getoutput hotel
            if((strtolower($typehabitation->intitule) == 'hôtel' OR strtolower($typehabitation->intitule) == 'ecoles' OR strtolower($typehabitation->intitule) == 'ecole' OR strtolower($typehabitation->intitule) == 'hôtels' OR strtolower($typehabitation->intitule) == 'hotel' OR strtolower($typehabitation->intitule) == 'hotels' OR strtolower($typehabitation->intitule) == 'centre commercial' OR strtolower($typehabitation->intitule) == 'commerces'  OR strtolower($typehabitation->intitule) == 'magasins'  OR strtolower($typehabitation->intitule) == 'marché'  OR strtolower($typehabitation->intitule) == 'bureaux'  OR strtolower($typehabitation->intitule) == 'bureau'  OR strtolower($typehabitation->intitule) == 'hopitaux'   OR strtolower($typehabitation->intitule) == 'hôpitaux'  OR strtolower($typehabitation->intitule) == 'restaurant' OR strtolower($typehabitation->intitule) == 'restaurants'  OR strtolower($typehabitation->intitule) == 'salle' OR strtolower($typehabitation->intitule) == 'salles' OR strtolower($typehabitation->intitule) == 'entrepôts') AND $etat == 'construction'){
                $getoutputgeneralitesuite = Output::whereIn('titre', ['output 4','output 41','output 42','output 3','output 30',
                    'output 31','output 32','output 33','output 34','output 4','output 35','output 50','output 51','output 52',
                    'output 53', 'output 54'])->get();
                    
                foreach($getoutputgeneralitesuite as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                
                }
            }
            
            if(strtolower($typehabitation->intitule) == 'habitations' AND $etat == 'construction'){
                $getoutputgeneralitesuite = Output::whereIn('titre', ['output 41','output 42','output 3','output 30',
                    'output 31','output 32','output 33','output 34'])->get();
                    
                foreach($getoutputgeneralitesuite as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                
                }
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                if ($niveau > 2) {
                    $getoutputgeneralitesuite = Output::whereIn('titre', ['output 4','output 50','output 51','output 52',
                    'output 53', 'output 54'])->get();
                    
                    foreach($getoutputgeneralitesuite as $key=>$value){
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                
                    }
                }
            }

            if(strtolower($typehabitation->intitule) == 'hôtel' OR strtolower($typehabitation->intitule) == 'hôtels' OR strtolower($typehabitation->intitule) == 'hotel'
            OR strtolower($typehabitation->intitule) == 'hotels'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau - 1) * 2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;
                    $chambre = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'chambre')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }

                    

                    if($niveau > 5){
                        $output = Output::where('titre', 'output 6')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    }
                    //echo $chambre.' '.$effectif;                    
                    if ($chambre > 10 and $chambre < 100 and $effectif > 100 and $effectif <=300) {
                        //echo "string";
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){                            
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                     
                        }
                    }
                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                       
                        }
                    }
                    if ($chambre > 100 and $effectif > 100 and $effectif <= 300) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                     
                        }
                    }
                    if ($chambre > 100 and $effectif > 300 and $effectif <= 700) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                      
                        }
                    }
                    if ($chambre > 100 and $effectif > 700 and $effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){   
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                        
                        }
                    }
                    if ($chambre > 100 and $effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
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

                    $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($superficie){
                        $superficie->response = (int)preg_replace('/\D/ui','',$superficie->response);
                        if($superficie->response <= 2){
                            $isnotice++;

                        }
                        if($superficie->response > 2 AND $superficie->response < 20){
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetude++;
                            }                            
                        }
                        if($superficie->response > 20){
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetudedetaille++;
                            }
                        }                       
                    }  
                    if ($etoile) {
                       if ($etoile->response == 1) {
                           $isnotice++;
                       }
                       else{
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetude++;
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
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach ($output as $key => $value) {
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                
                            }

                        }
                    }
                }
            }

            if(strtolower($typehabitation->intitule) == 'centre commercial' OR strtolower($typehabitation->intitule) == 'boutiques,commerces' 
            OR strtolower($typehabitation->intitule) == 'magasins'  OR strtolower($typehabitation->intitule) == 'marché'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau - 1) * 2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;

                    $superficie = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'superficie')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;
                    $superficie = (int)preg_replace('/\D/ui','',$superficie);
                    $niveau = (int)preg_replace('/\D/ui','',$niveau);
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }

                    if($niveau > 5){
                        $output = Output::where('titre', 'output 6')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    } 

                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                       
                        }
                    }
                    if ($effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                        
                        }
                    }

                    if($superficie > 2500){                        
                        if ($etat == 'exploitation') {
                            $isaudit++;
                        }
                        else{
                            $isetude++;
                        }   
                    }  
                    else{
                        $isnotice++;
                    }
                    $budget = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'budget')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    //print_r($budget);
                    if($budget){
                        //print_r($budget->response);
                        if($budget->response >= 100000000){
                            $output = Output::where('titre', 'output 9')->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                        if($budget->response <= 500000000){
                            $isnotice++;
                        }
                        if($budget->response > 500000000 AND $budget->response <= 2000000000){                            
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetude++;
                            }                            
                        }
                        if($budget->response > 2000000000){
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetudedetaille++;
                            }                    
                            $output = Output::whereIn('titre', ['output 32'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                }
            }

            if(strtolower($typehabitation->intitule) == 'restaurant' OR strtolower($typehabitation->intitule) == 'restaurant'  OR strtolower($typehabitation->intitule) == 'restaurants'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;
                    $nature = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'nature')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;
                    $superficie = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'superficie')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $superficie = (int)preg_replace('/\D/ui','',$superficie);
                    $niveau = (int)preg_replace('/\D/ui','',$niveau);
                     
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }

                    if($niveau > 5){
                        $output = Output::where('titre', 'output 6')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    } 

                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                       
                        }
                    }
                    if ($effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
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
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                    $isnotice++;
                }
            }
            if(strtolower($typehabitation->intitule) == 'salle' OR strtolower($typehabitation->intitule) == 'salles'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;
                    $nature = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'nature')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;
                    $superficie = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'superficie')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $superficie = (int)preg_replace('/\D/ui','',$superficie);
                    $niveau = (int)preg_replace('/\D/ui','',$niveau);
                     
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }

                    if($niveau > 5){
                        $output = Output::where('titre', 'output 6')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    } 

                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                       
                        }
                    }
                    if ($effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
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
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                    $isnotice++;
                }
            }

            if(strtolower($typehabitation->intitule) == 'ecole' OR strtolower($typehabitation->intitule) == 'ecoles'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;

                    if($niveau > 5){
                        $output = Output::where('titre', 'output 6')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    } 
                    
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }
                    if ($effectif >1500) {                        
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    }
                    if ($effectif < 1500) {                        
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
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
                        if($superficie->response < 1){
                            $isnotice++;
                        }
                        if($superficie->response > 1 AND $superficie->response < 10){                            
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetude++;
                            }                            
                        }
                        if($superficie->response > 10){
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetudedetaille++;
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
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                }
            }
            if(strtolower($typehabitation->intitule) == 'bureaux' OR strtolower($typehabitation->intitule) == 'bureau'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;

                    $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;

                    $superficie = (int)preg_replace('/\D/ui','',$superficie);
                    $niveau = (int)preg_replace('/\D/ui','',$niveau);
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }

                    $budget = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'budget')
                        ->where('user_reponses.idparent', $id)
                        ->first();

                $isnotice++;
                if ($effectif > 1500) {
                    $output = Output::where('titre', 'output 82')->get();
                    foreach($output as $key=>$value){    
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                                       
                    }
                }
                if ($effectif > 300  AND $effectif < 1500) {
                    $output = Output::where('titre', 'output 81')->get();
                    foreach($output as $key=>$value){
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                                        
                    }
                }
                if ($niveau > 5 ) {
                    $getoutputgeneralitesuite = Output::whereIn('titre', ['output 6'])->get();
                    
                    foreach($getoutputgeneralitesuite as $key=>$value){
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                
                    }
                }
                    if($budget){
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){                                    
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                }
            }
            if(strtolower($typehabitation->intitule) == 'habitations' OR strtolower($typehabitation->intitule) == 'habitation'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;

                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;

                    $logement = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'logement')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;

                    $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first();
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first();
                        }                        
                    }

                    if ($niveau > 5 ) {
                        $getoutputgeneralitesuite = Output::whereIn('titre', ['output 6'])->get();
                        
                        foreach($getoutputgeneralitesuite as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    }

                    $effectif= $effectif->response;
                    
                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                       
                        }
                    }
                    if ($effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                        
                        }
                    }
                    //echo $logement;
                    if ($logement > 14 AND $logement < 50) {
                        $isnotice++;
                    }

                    if ($logement >= 50 AND $logement < 201) {
                        if ($etat == 'exploitation') {
                            $isaudit++;
                        }
                        else{
                            $isetude++;
                        }     
                    }

                    if ($logement > 200) {
                        if ($etat == 'exploitation') {
                            $isaudit++;
                        }
                        else{
                            $isetudedetaille++;
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
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                }
            }
            if(strtolower($typehabitation->intitule) == 'hopitaux' OR strtolower($typehabitation->intitule) == 'hôpitaux'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;
                
                $lits = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'lit')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;

                    if ($niveau > 5 ) {
                        $getoutputgeneralitesuite = Output::whereIn('titre', ['output 6'])->get();
                        
                        foreach($getoutputgeneralitesuite as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                
                        }
                    }

                    
                    $leseffectifs = DB::table('user_reponses')                    
                        ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->orderBy('useridi','DESC')
                        ->get();
                    if ($leseffectifs) {
                        if (count($leseffectifs) > 1) {
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idquestion', '40')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }
                        else{
                            $effectif = DB::table('user_reponses')                    
                                ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                                ->join('types', 'questions.idtype', 'types.id')
                                ->where('types.label', 'effectif')
                                ->where('user_reponses.idparent', $id)
                                ->orderBy('useridi','DESC')
                                ->first()->response;
                        }                        
                    }

                    if ($effectif > 1500) {
                        $output = Output::where('titre', 'output 82')->get();
                        foreach($output as $key=>$value){    
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                       
                        }
                    }
                    if ($effectif < 1500) {
                        $output = Output::where('titre', 'output 81')->get();
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                        
                        }
                    }
                    $typehopital = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'typehopital')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                    if ($typehopital =='Centre de santé' OR $typehopital =='Morgue'  OR $typehopital =='Laboratoire'  OR $typehopital =='Biomédicales' ) {
                        $isnotice++;
                    }
                    if ($typehopital =='Hôpital de district' OR $typehopital =='Hôpital régional'  OR $typehopital =='Laboratoire d\'analyse et de recherche') {
                        if ($etat == 'exploitation') {
                            $isaudit++;
                        }
                        else{
                            $isetude++;
                        }
                    }
                    if ($typehopital =='Hôpital central' OR $typehopital =='Hôpital général') {
                        if ($etat == 'exploitation') {
                            $isaudit++;
                        }
                        else{
                            $isetudedetaille++;
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
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                        if($budget->response <= 500000000){
                            $isnotice++;
                        }
                        if($budget->response > 500000000 AND $budget->response <= 2000000000 ){
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetude++;
                            }   
                        }
                        if($budget->response >  2000000000 ){
                            if ($etat == 'exploitation') {
                                $isaudit++;
                            }
                            else{
                                $isetudedetaille++;
                            }                  
                        }
                    }
                }
            }

            if(strtolower($typehabitation->intitule) == 'entrepôts' OR strtolower($typehabitation->intitule) == 'entrepôts'){

                if($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation'){
                    // $niveau = DB::table('user_reponses')
                    //     ->select('response')
                    //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    //     ->join('types', 'questions.idtype', 'types.id')
                    //     ->where('types.label', 'niveau')
                    //     ->where('user_reponses.idparent', $id)
                    //     ->first()->response;
                    // $hauteur = ($niveau-1)*2.8;
                    $hauteur = DB::table('user_reponses')
                            ->select('response')
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'hauteur')
                            ->where('user_reponses.idparent', $id)
                            ->first()->response;

                    $niveau = ($hauteur/2.8)+1;

                    $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;

                if ($niveau > 5 ) {
                    $getoutputgeneralitesuite = Output::whereIn('titre', ['output 6'])->get();
                    
                    foreach($getoutputgeneralitesuite as $key=>$value){
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                
                    }
                }
                    $superficie = (int)preg_replace('/\D/ui','',$superficie);
                    if ($superficie > 500) {
                        if ($etat == 'exploitation') {
                            $isaudit++;
                        }
                        else{
                            $isetude++;
                        }         
                    }

                    if ($superficie < 500) {
                        $isnotice++;
                    }

                    $budget = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'budget')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                    if($budget){
                        if($budget->response >= 100000000){
                            $output = Output::whereIn('titre', ['output 9'])->get();
                            foreach($output as $key=>$value){ 
                                if(!in_array($value->titre,$arraytitre)){
                                    if ($request->recherche !== null) {
                                        if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                            $arraytitre []= $value->titre;
                                            $array []= $value;
                                        }
                                    }
                                    else{
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }                                       
                            }
                        }
                    }
                }
            }

            //var_dump($typehabitation->intitule);
            if ($etat == 'construction') {
                $output = Output::whereIn('titre', ['output 331' ,'output 332','output 333','output 41','output 32','output 42'])->get();
                foreach ($output as $key => $value) {
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                
                }
            }
            else{
                $output = Output::whereIn('titre', ['output 331' ,'output 332'])->get();
                foreach ($output as $key => $value) {
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                
                }
            }            
            
            $parcstationnement = DB::table('user_reponses')
                ->select('response')
                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                ->join('types', 'questions.idtype', 'types.id')
                ->where('types.intitule','LIKE', '%Nombre de véhicule de votre parc de stationnement%')
                ->where('user_reponses.idparent', $id)
                ->first();
            if ($parcstationnement) {
                if($parcstationnement->response > 200){
                    $output = Output::whereIn('titre', ['output 81'])->get();
                    //array_push($getoutputgeneralite, $output);
                        foreach($output as $key=>$value){
                            if(!in_array($value->titre,$arraytitre)){
                                if ($request->recherche !== null) {
                                    if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                        $arraytitre []= $value->titre;
                                        $array []= $value;
                                    }
                                }
                                else{
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }                                     
                        }
                }
            }
            // $travauxvoiries = DB::table('user_reponses')
            //     ->select('response')
            //     ->join('questions', 'questions.id', 'user_reponses.idquestion')
            //     ->join('types', 'questions.idtype', 'types.id')
            //     ->where('types.intitule','LIKE', '%voirie%')
            //     ->where('user_reponses.idparent', $id)
            //     ->first();
            // if ($travauxvoiries) {
            //     echo "string";
            //     if($travauxvoiries->response=="Oui"){
            //         $output = Output::whereIn('titre', ['output 16'])->get();
            //         //array_push($getoutputgeneralite, $output);
            //             foreach($output as $key=>$value){
            //                 if(!in_array($value->titre,$arraytitre)){
            //                     if ($request->recherche !== null) {
            //                         if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
            //                             $arraytitre []= $value->titre;
            //                             $array []= $value;
            //                         }
            //                     }
            //                     else{
            //                         $arraytitre []= $value->titre;
            //                         $array []= $value;
            //                     }
            //                 }                                     
            //             }
            //     }
            // }
            
            $effectiftravailleurs = DB::table('user_reponses')
                ->select('response')
                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                ->join('types', 'questions.idtype', 'types.id')
                ->where('types.intitule','LIKE', '%travailleur%')
                ->where('user_reponses.idparent', $id)
                ->first();
            if($effectiftravailleurs){
                if($effectiftravailleurs->response >= 50){
                    $output = Output::whereIn('titre', ['output 17'])->get();
                    foreach($output as $key=>$value){
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                //echo $value->classement;
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                                   
                    }
                }
            }

            // if($etat == 'execution'){

            // }

            if($etat == 'exploitation'){
                $output = Output::whereIn('titre', ['output 18','output 19','output 20','output 21',
                'output 12','output 22','output 23'])->get();
                foreach ($output as $key => $value) {
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                
                }
                $output = Output::whereIn('titre', ['output 24','output 25',
                'output 331'])->get();
                foreach($output as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                                   
                }
                $output = Output::whereIn('titre', ['output 40'])->get();
                foreach($output as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                                   
                }

                $monclassification = $this->classification($id); 
                $uneclassification = (json_decode(json_encode($monclassification))->original);

                if(strpos($uneclassification->output1[0]->classement,'IGH')  !== false){
                    $output = Output::whereIn('titre', ['output 26','output 27','output 28','output 29','output 31'])->get();
                    foreach($output as $key=>$value){
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                             
                    }
                }
                if(strpos($uneclassification->output1[0]->classement,'ERP de 1ere')  !== false || strpos($uneclassification->output1[0]->classement,'ERP de 2e')  !== false){
                    $output = Output::whereIn('titre', ['output 27'])->get();
                    foreach($output as $key=>$value){    
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                          
                    }
                }
                if(strpos($uneclassification->output1[0]->classement,'ERP')  !== false){
                    $output = Output::whereIn('titre', ['output 28','output 29','output 31'])->get();
                    foreach($output as $key=>$value){ 
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                                
                    }
                }
            }    


            if ($isaudit != 0) {
                $output = Output::whereIn('titre', ['output 74'])->get();
                foreach($output as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                                
                }
            }
            elseif ($isetudedetaille != 0) {
                $output = Output::whereIn('titre', ['output 73'])->get();
                foreach($output as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                                
                }
            }
            elseif ($isetude != 0) {
                $output = Output::whereIn('titre', ['output 72'])->get();
                foreach($output as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                                
                }
            }            
            elseif ($isnotice != 0) {                
                $output = Output::whereIn('titre', ['output 71'])->get();
                foreach($output as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        if ($request->recherche !== null) {
                            if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }
                        else{
                            $arraytitre []= $value->titre;
                            $array []= $value;
                        }
                    }                                
                }
            }        
          

            $time = time();
            //$time= substr($time, -1,3); 
            //return view('exigence',compact('array'));
            $pdf = PDF::loadView('exigence', compact('array'));
            $content = $pdf->output();
            file_put_contents('exigence'.$id.'.pdf', $content);
            $lienpdf = 'http://backend.pharmcogroup.net/'.'exigence'.$id.'.pdf';
            $arrayresult[0]=$array;
            $arrayresult[1]='';
            $arrayresult[2]=$lienpdf;

            return  $arrayresult;
        }
    }


    public function outputgenereralite($id,$iduser,Request $request)
    {
        $firstquestion = User_reponse::where('id',$id)->first();
        if ($firstquestion) {
            if ($firstquestion->idusers == 0 AND $iduser != 0) {
                $firstquestion->idusers = $iduser;
                $firstquestion->save();
            }
            
                    //echo "ici";
            $arrayresult = array();
            if($firstquestion){
                $typehabitation = Habitation::where('id',$firstquestion->idhabitation)->first();
                $etat = $firstquestion->etat;

                $array = array();
                $arraytitre = array();

                if ($etat == 'construction') {
                    $getoutputgeneralite = Output::whereIn('titre',['output 1100','output 1200','output 1300',
                    'output 1400','output 1500','output 1600','output 1700','output 2400'])->get();            
                    foreach($getoutputgeneralite as $key=>$value){ 
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                                
                    }
                }
                elseif ($etat == 'exploitation'){
                   // echo "string";
                    $getoutputgeneralite = Output::whereIn('titre',['output 1550','output 1600','output 1700','output 2200','output 2300',])->get();            
                    foreach($getoutputgeneralite as $key=>$value){ 
                        if(!in_array($value->titre,$arraytitre)){
                            if ($request->recherche !== null) {
                                if (strpos(strtoupper($value->classement), strtoupper($request->recherche)) !== false) {
                                    $arraytitre []= $value->titre;
                                    $array []= $value;
                                }
                            }
                            else{
                                $arraytitre []= $value->titre;
                                $array []= $value;
                            }
                        }                                
                    }
                }
                

                $time = time();
                //$time= substr($time, -1,3); 
                //return view('generalite',compact('array'));
                $pdf = PDF::loadView('generalite', compact('array'));
                $content = $pdf->output();
                file_put_contents('generalite'.$id.'.pdf', $content);
                $lienpdf = 'http://backend.pharmcogroup.net/'.'generalite'.$id.'.pdf';
                $arrayresult[0]=$array;
                $arrayresult[1]='';
                $arrayresult[2]=$lienpdf;

                return  $arrayresult;
            }
        }
        else{
            return [];
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
        $firstquestion = User_reponse::where('id',$id)->first();$environnement='';
        $generalitefree = array();$output=array();$output1=array();
        if($firstquestion){
            $typehabitation = Habitation::where('id',$firstquestion->idhabitation)->first();
            $etat = $firstquestion->etat;
            if((strtolower($typehabitation->intitule) == 'hôtel' OR strtolower($typehabitation->intitule) == 'hôtels' OR strtolower($typehabitation->intitule) == 'hotel' OR strtolower($typehabitation->intitule) == 'hotels') 
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
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }

                if($effectif <= 100 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 101')->get();
                }
                if($effectif > 100 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 102')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 103')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
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
                if ($effectif <= 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 100 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($chambre > 10 and $chambre < 100 and $effectif > 100 and $effectif < 300) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($chambre > 100 and $effectif > 100 and $effectif < 300) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($chambre > 100 and $effectif > 300 and $effectif < 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($chambre > 100 and $effectif > 700 and $effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($chambre > 100 and $effectif > 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }
            //echo strtolower($typehabitation->intitule);
            if((strtolower($typehabitation->intitule) == 'centre commercial' OR strtolower($typehabitation->intitule) == 'commerces' OR strtolower($typehabitation->intitule) == 'boutiques,commerces'  OR strtolower($typehabitation->intitule) == 'magasins'  OR strtolower($typehabitation->intitule) == 'marché') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                    $superficie = (int)preg_replace('/\D/ui','',$superficie);
                    $niveau = (int)preg_replace('/\D/ui','',$niveau);
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }
                
                
                if($effectif <= 100 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 111')->get();
                }
                if($effectif > 100 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 112')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 113')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 114')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 115')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output1 = Output::where('titre', 'output 206')->get();
                }                
                if ($hauteur > 50) {
                    $output1 = Output::where('titre', 'output 107')->get();
                }
                if ($effectif <= 200) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 200 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }

                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }
            if((strtolower($typehabitation->intitule) == 'restaurant'  OR strtolower($typehabitation->intitule) == 'restaurants') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                $nature = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'nature')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $superficie = (int)preg_replace('/\D/ui','',$superficie);
                $niveau = (int)preg_replace('/\D/ui','',$niveau);
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }
                
                if($effectif <= 200 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 121')->get();
                }
                if($effectif > 200 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 122')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 123')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 124')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 125')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output1 = Output::where('titre', 'output 206')->get();
                }
                if ($hauteur > 50) {
                    $output1= Output::where('titre', 'output 107')->get();
                }
                if ($effectif <= 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 100 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }

                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }

            if((strtolower($typehabitation->intitule) == 'salle'  OR strtolower($typehabitation->intitule) == 'salles') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                $nature = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'nature')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $superficie = (int)preg_replace('/\D/ui','',$superficie);
                $niveau = (int)preg_replace('/\D/ui','',$niveau);
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }
                
                if(($nature == 'Salle de réunion' || $nature == 'Salle de conférences' || $nature == 'Salle de quartier') AND ($effectif < 200 AND $hauteur < 28)){
                    $output1 = Output::where('titre','output 191')->get();
                }                
                if(($nature == 'Salle de spectacles' || $nature == 'Salle de projection' || $nature == 'Salle de cinéma') AND ($effectif < 50 AND $hauteur < 28)){
                    $output1 = Output::where('titre','output 191')->get();
                }
                if(($nature == 'Salle de sports') AND $effectif <= 200 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 199')->get();
                }
                if($effectif > 200 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 192')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 193')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 194')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 195')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output1 = Output::where('titre', 'output 206')->get();
                }
                if ($hauteur > 50) {
                    $output1= Output::where('titre', 'output 107')->get();
                }
                if ($effectif <= 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 100 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }

                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }
            if((strtolower($typehabitation->intitule) == 'hôpitaux' OR strtolower($typehabitation->intitule) == 'hopitaux') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                
                $lits = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'lit')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;

                $consultation = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'consultation')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }
                
                if($effectif <= 100 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 131')->get();
                }
                if($effectif < 100 AND $lits > 20){
                    $output1 = Output::where('titre','output 132')->get();
                }
                if($effectif > 100 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 132')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 133')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 134')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 135')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output1 = Output::where('titre', 'output 206')->get();
                }
                if ($hauteur > 50) {
                    $output1 = Output::where('titre', 'output 107')->get();
                }
                if ($effectif <= 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 100 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }

                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }
            if((strtolower($typehabitation->intitule) == 'ecoles' OR strtolower($typehabitation->intitule) == 'ecole') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }
                
                if($effectif <= 200 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 141')->get();
                }
                if($effectif > 200 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 142')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 143')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 144')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 145')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output = Output::where('titre', 'output 206')->get();
                }
                if ($hauteur > 50) {
                    $output = Output::where('titre', 'output 107')->get();
                }
                if ($effectif <= 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 100 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }

                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }            
            if((strtolower($typehabitation->intitule) == 'bureaux') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $hauteur = ($niveau-1)*2.8;
                $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $superficie = (int)preg_replace('/\D/ui','',$superficie);
                $niveau = (int)preg_replace('/\D/ui','',$niveau);
                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first()->response;
                    }                        
                }
                
                if($effectif <= 200 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 151')->get();
                }
                if($effectif > 200 AND $effectif <= 300 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 125')->get();
                }
                if($effectif > 300 AND $effectif <= 700 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 153')->get();
                }
                if($effectif > 700 AND $effectif <= 1500 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 154')->get();
                }
                if ($effectif > 1500 and $hauteur < 28) {
                    $output1 = Output::where('titre', 'output 155')->get();
                }
                if ($hauteur < 50 and $hauteur > 28) {
                    $output1 = Output::where('titre', 'output 206')->get();
                }
                if ($hauteur > 50) {
                    $output1 = Output::where('titre', 'output 107')->get();
                }
                if ($effectif <= 100) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 100 and $effectif <= 300) {
                    $output = Output::where('titre', 'output 201')->get();
                }
                elseif ($effectif > 300 and $effectif <= 700) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 700 and $effectif <= 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }
                elseif ($effectif > 1500) {
                    $output = Output::where('titre', 'output 203')->get();
                }
                elseif ($effectif < 1500) {
                    $output = Output::where('titre', 'output 202')->get();
                }

                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }    
            if((strtolower($typehabitation->intitule) == 'habitations') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){

                $hauteur = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'hauteur')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;

                $niveau = ($hauteur/2.8)+1;
                

                $leseffectifs = DB::table('user_reponses')                    
                    ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                    ->join('questions', 'questions.id', 'user_reponses.idquestion')
                    ->join('types', 'questions.idtype', 'types.id')
                    ->where('types.label', 'effectif')
                    ->where('user_reponses.idparent', $id)
                    ->orderBy('useridi','DESC')
                    ->get();
                if ($leseffectifs) {
                    if (count($leseffectifs) > 1) {
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idquestion', '40')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first();
                    }
                    else{
                        $effectif = DB::table('user_reponses')                    
                            ->select((['user_reponses.id AS useridi', 'user_reponses.response']))
                            ->join('questions', 'questions.id', 'user_reponses.idquestion')
                            ->join('types', 'questions.idtype', 'types.id')
                            ->where('types.label', 'effectif')
                            ->where('user_reponses.idparent', $id)
                            ->orderBy('useridi','DESC')
                            ->first();
                    }                        
                }

                if ($effectif) {
                    $effectif = $effectif->response;
                    if ($effectif <= 100) {
                        $output = Output::where('titre', 'output 201')->get();
                    }
                    elseif ($effectif > 100 and $effectif <= 300) {
                        $output = Output::where('titre', 'output 201')->get();
                    }
                    elseif ($effectif > 300 and $effectif <= 700) {
                        $output = Output::where('titre', 'output 202')->get();
                    }
                    elseif ($effectif > 700 and $effectif <= 1500) {
                        $output = Output::where('titre', 'output 202')->get();
                    }
                    elseif ($effectif > 1500) {
                        $output = Output::where('titre', 'output 203')->get();
                    }
                    elseif ($effectif < 1500) {
                        $output = Output::where('titre', 'output 202')->get();
                    }
                }
                //echo $niveau;
                
                if($niveau < 2){
                    $output1 = Output::where('titre','output 161')->get();
                }
                if($niveau > 1 AND $niveau < 5){
                    $output1 = Output::where('titre','output 162')->get();
                }
                if($niveau > 4 AND $niveau < 8 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 163')->get();
                }
                if($niveau > 7 AND $hauteur < 28){
                    $output1 = Output::where('titre','output 164')->get();
                }
                if ($hauteur > 28) {
                    $output1 = Output::where('titre', 'output 165')->get();
                }
                if ($hauteur > 50) {
                    $output1 = Output::where('titre', 'output 406')->get();
                }


                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                return response()->json($montablo);
            }

            if((strtolower($typehabitation->intitule) == 'entrepôts') 
            AND ($etat == 'execution' OR $etat == 'construction' OR $etat == 'exploitation')){

                $superficie = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'superficie')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;                
                $superficie = (int)preg_replace('/\D/ui','',$superficie);
                if ($superficie > 500) {
                    $environnement = 'oui';
                }
                else{
                    $environnement = '';
                }
                $niveau = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'niveau')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                $niveau = (int)preg_replace('/\D/ui','',$niveau);
                $hauteur = ($niveau-1)*2.8;



                $produit = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'produit')
                        ->where('user_reponses.idparent', $id)
                        ->first()->response;
                if($produit){
                    $produit = explode(';', $produit);
                }
                else{
                    $produit = [];
                }

                $volume = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'volume')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                if ($volume) {
                    $volume = $volume->response;
                    if($volume > 5000){
                        $output1 = Output::where('titre','output 171')->get();
                    }
                    if (
                        (in_array('99', $produit) AND $volume > 15000) OR 
                        (in_array('102', $produit) AND $volume > 100) OR 
                        (in_array('104', $produit) AND $volume > 300) OR 
                        (in_array('109', $produit) AND $volume > 8) OR 
                        (in_array('110', $produit) AND $volume > 160) OR 
                        (in_array('110', $produit) AND $volume > 4 AND $volume < 40) OR 
                        (in_array('111', $produit) AND $volume > 1) OR 
                        (in_array('111', $produit) AND $volume > 0.25 AND $volume < 0.1)) 
                    {
                        $output = Output::where('titre','output 203')->get();
                    }
                    elseif (
                        in_array('91', $produit) OR 
                        (in_array('99', $produit) AND $volume > 5000 AND $volume < 15000) OR 
                        in_array('100', $produit) OR in_array('101', $produit) OR 
                        (in_array('102', $produit) AND $volume > 25 AND $volume < 100) OR 
                        (count($produit) == 0 AND $distance > 20) OR 
                        (in_array('104', $produit) AND $volume < 300) OR 
                        (in_array('105', $produit)) OR  
                        (in_array('108', $produit)) OR 
                        (in_array('109', $produit) AND $volume > 2 AND $volume < 8) OR 
                        (in_array('110', $produit) AND $volume > 40 AND $volume < 160) OR 
                        (in_array('111', $produit) AND $volume > 0.1 AND $volume < 1)) 
                    {
                        $output = Output::where('titre','output 202')->get();
                    }
                    elseif (in_array('92', $produit) OR 
                        in_array('93', $produit) OR 
                        in_array('94', $produit)) 
                    {
                        $output = Output::where('titre','output 201')->get();
                    }
                }
                $tonne = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'tonne')
                        ->where('user_reponses.idparent', $id)
                        ->first();

                if ($tonne) {
                    $tonne = $tonne->response;
                    if($tonne > 500){
                        $output1 = Output::where('titre','output 171')->get();
                    }
                    if (
                        (in_array('90', $produit) AND $tonne > 10) OR 
                        (in_array('103', $produit) AND $tonne > 0.2)) 
                    {
                        $output = Output::where('titre','output 203')->get();
                    }
                    elseif (
                        (in_array('90', $produit) AND $tonne > 5 AND $tonne < 10) OR 
                        in_array('91', $produit) OR 
                        (in_array('96', $produit) AND $tonne > 2) OR 
                        (in_array('97', $produit) AND $tonne > 2) OR 
                        (in_array('98', $produit) AND $tonne > 0.5)  OR 
                        in_array('100', $produit) OR in_array('101', $produit) OR 
                        (in_array('103', $produit) AND $tonne > 0.5 AND $tonne < 0.2) OR 
                        (in_array('105', $produit)) OR 
                        (in_array('106', $produit) AND $tonne > 50) OR 
                        (in_array('107', $produit) AND $tonne > 0.5) OR 
                        (in_array('108', $produit))) 
                    {
                        $output = Output::where('titre','output 202')->get();
                    }
                    elseif (in_array('92', $produit) OR 
                        in_array('93', $produit) OR 
                        in_array('94', $produit) OR 
                        (in_array('103', $produit) AND $tonne > 0.2 AND $tonne < 0.005) OR 
                        (in_array('106', $produit) AND $tonne > 25 AND $tonne < 50)) 
                    {
                        $output = Output::where('titre','output 201')->get();
                    }
                }

                $distance = DB::table('user_reponses')
                        ->select('response')
                        ->join('questions', 'questions.id', 'user_reponses.idquestion')
                        ->join('types', 'questions.idtype', 'types.id')
                        ->where('types.label', 'effectif')
                        ->where('user_reponses.idparent', $id)
                        ->first();
                if ($distance) {
                    $distance = $distance->response;
                    if ( 
                        (count($produit) == 0 AND $distance < 20)) 
                    {
                        $output = Output::where('titre','output 203')->get();
                    }
                    elseif (
                        in_array('91', $produit) OR 
                        in_array('100', $produit) OR in_array('101', $produit) OR 
                        (count($produit) == 0 AND $distance > 20) OR  
                        (in_array('105', $produit)) OR 
                        (in_array('108', $produit))) 
                    {
                        $output = Output::where('titre','output 202')->get();
                    }
                    elseif (in_array('92', $produit) OR 
                        in_array('93', $produit) OR 
                        in_array('94', $produit)) 
                    {
                        $output = Output::where('titre','output 201')->get();
                    }
                }
                
                if (
                    in_array('91', $produit) OR 
                    in_array('100', $produit) OR 
                    in_array('101', $produit)) 
                {
                    $output = Output::where('titre','output 202')->get();
                }
                elseif (in_array('92', $produit) OR 
                    in_array('93', $produit) OR 
                    in_array('94', $produit)) 
                {
                    $output = Output::where('titre','output 201')->get();
                }
                
                // if (count($output) == 0) {
                //     if ($distance > 20) {
                //         $output = Output::where('titre','output 202')->get();
                //     }
                //     if ($distance < 20) {
                //         $output = Output::where('titre','output 203')->get();
                //     }
                // }
                $arraytitre = array();
                $output23 = Output::where('ispayer', 2)->get();
                foreach($output23 as $key=>$value){ 
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre []= $value->titre;
                        $generalitefree []= $value;
                    }                                
                }
                $montablo = array();
                $montablo['output1']=$output1;
                $montablo['output']=$output;
                $montablo['generalitefree'] = $generalitefree;
                $montablo['environnement'] = $environnement;
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
        //echo $request->idusers;
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

    public function supprimer(Request $request)
    {
        //echo 'DELETE FROM user_reponses WHERE idusers='.$request->iduser.' AND id='.$request->iddebut;

        DB::table('user_reponses')->where('idusers', $request->iduser)->where('idparent', $request->iddebut)->delete();
        DB::table('user_reponses')->where('idusers', $request->iduser)->where('id', $request->iddebut)->delete();
        DB::table('equipement_date')->where('idutilisateur', $request->iduser)->where('idreponsedebut', $request->iddebut)->delete();
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