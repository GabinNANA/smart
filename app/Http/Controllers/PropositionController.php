<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use Illuminate\Http\Request;
use App\Models\User_reponse;
use App\Models\Output;
use DB;
use PDF;

class PropositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $propositions = Proposition::all();
        return response()->json($propositions);
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

    
    public function Getmaintenance($valeur){
        $equipement = explode('~', $valeur)[0];
        $iddebut = explode('~', $valeur)[1];
        $iduser = explode('~', $valeur)[2];

        $arrayresult = array();$untablo = array();
        $element = DB::select('SELECT * FROM maintenance_equipement WHERE idequipement='.$equipement);
        foreach ($element as $key => $value) {
          $unelement = array();
          $unelement['id']=$value->id;
          $unelement['intitule']=$value->intitule;
          $unelement['periode']=$value->periode;
          $unelement['frequence']=$value->frequence;
          $unelement['mesdate'] = DB::select('SELECT * FROM maintenance_date WHERE idequipement='.$equipement.' AND iddebut='.$iddebut.' AND idmaintenance='.$value->id.' AND idutilisateur='.$iduser.' AND idequipement IS NOT NULL AND isfonctionnement=0 ORDER BY id DESC LIMIT 1');
          $arrayresult[] = $unelement;
         } 
         
        // $pdf = PDF::loadView('equipement', compact('arrayresult'));
        // $content = $pdf->output();
        // file_put_contents('maintenance'.$idequipement.$iddebut.'.pdf', $content);
        // $lienpdf = 'http://backend.pharmcogroup.net/'.'maintenance'.$idequipement.$iddebut.'.pdf';

         $untablo[0] = $arrayresult;
         $untablo[1] = DB::select('SELECT * FROM maintenance_date WHERE idequipement='.$equipement.' AND iddebut='.$iddebut.' AND idutilisateur='.$iduser.' AND isfonctionnement=1 ORDER BY id DESC LIMIT 1');;


        return  response()->json($untablo);
    }
    
    public function Getmaintenanceenv($valeur){
        $environnement = explode('~', $valeur)[0];
        $iddebut = explode('~', $valeur)[1];
        $iduser = explode('~', $valeur)[2];
        
        $arrayresult1 = DB::select('SELECT * FROM maintenance_date WHERE iddebut='.$iddebut.' AND idmaintenance='.$environnement.' AND idutilisateur='.$iduser.' AND idequipement IS NULL ORDER BY id DESC');
        $nomequipe = DB::table('outputs')
                ->select('classement')
                ->where('id', $environnement)
                ->first()->classement;
        $pdf = PDF::loadView('equipement', compact('arrayresult1','nomequipe'));
        $content = $pdf->output();
        file_put_contents('maintenanceenvironnement'.$environnement.$iddebut.'.pdf', $content);
        $lienpdf = 'http://backend.pharmcogroup.net/'.'maintenanceenvironnement'.$environnement.$iddebut.'.pdf';

         $untablo[0] = $arrayresult1;
         $untablo[1] = $lienpdf;


        return  response()->json($untablo);
    }

    public function Getin($valeur)
    {
        $equipement = DB::table('user_reponses')
                ->select('response')
                ->join('questions', 'questions.id', 'user_reponses.idquestion')
                ->join('types', 'questions.idtype', 'types.id')
                ->where('types.label', 'equipement')
                ->where('user_reponses.idparent', $valeur)
                ->first()->response;
        //echo $equipement;
        $firstquestion = User_reponse::where('id',$valeur)->first();
        $tablo = strlen($equipement) == 1 ? explode(';',$equipement) : explode(';',substr($equipement, 1));
        $propositions = Proposition::whereIn('id',$tablo)->orderBy('choix')->get();
        $suitetat = '';
        if ($firstquestion->etat == 'construction') {
            
        }
        else {
            $suitetat = " AND moment_frequence NOT LIKE '%avant mise en service%'
             AND moment_frequence NOT LIKE \"%lors d'une nouvelle installation%\" AND moment_frequence NOT LIKE '%avant la première utilisation%'";
        }
        $suitetat = ' AND type NOT LIKE "%riodique%" ';        
        $resultat = array();
        $arrayresult = array();
        foreach($propositions as $key=>$value){ 
            //echo  'SELECT output_equipements.*,equipement_outputs.idequipement FROM output_equipements,equipement_outputs WHERE output_equipements.id !=1 AND output_equipements.id = idoutput AND idequipement='.$value->id.' '.$suitetat;
            $getouput = DB::select('SELECT output_equipements.*,equipement_outputs.idequipement,COALESCE((SELECT is_verification_avant_mise_service FROM equipement_is_date where equipement_is_date.idequipement=equipement_outputs.idequipement),"") as isavantmiseserve,COALESCE((SELECT is_bon_fonctionnement FROM equipement_is_date where equipement_is_date.idequipement=equipement_outputs.idequipement),"") as isbonfonctionnement,COALESCE((SELECT is_epreuve_incident FROM equipement_is_date where equipement_is_date.idequipement=equipement_outputs.idequipement),"") as isepreuveincident,COALESCE((SELECT periode_verification_avant_mise_service FROM equipement_is_date where equipement_is_date.idequipement=equipement_outputs.idequipement),"") as periodeavantmiseserve,COALESCE((SELECT periode_bon_fonctionnement FROM equipement_is_date where equipement_is_date.idequipement=equipement_outputs.idequipement),"") as periodebonfonctionnement,COALESCE((SELECT periode_epreuve_incident FROM equipement_is_date where equipement_is_date.idequipement=equipement_outputs.idequipement),"") as periodeincident FROM output_equipements,equipement_outputs WHERE output_equipements.id !=1 AND output_equipements.id = idoutput AND idequipement='.$value->id.' '.$suitetat);  
            //$resultat['choix'] = $value->choix;  
            $resultat[$value->choix] = $getouput;                                    
        }
        $time = time();
        //$time= substr($time, -1,3); 
        //return view('equipement',compact('resultat'));
        $pdf = PDF::loadView('equipement', compact('resultat'));
        $content = $pdf->output();
        file_put_contents('equipement'.$valeur.'.pdf', $content);
        $lienpdf = 'http://backend.pharmcogroup.net/'.'equipement'.$valeur.'.pdf';
        $arrayresult[0]=$resultat;
        $arrayresult[1]='';
        $arrayresult[2]=$lienpdf;
        $arrayresult[3]=$firstquestion->etat;

        return  response()->json($arrayresult);
    }

    public function Getdateequipement($valeur)
    {
        $arrayresult = array();
        $idequipement = explode('~', $valeur)[0];
        $iddebut = explode('~', $valeur)[1];
        $iduser = explode('~', $valeur)[2];
        $arrayresult[0] = DB::select('SELECT * FROM equipement_date WHERE idequipement='.$idequipement.' AND idutilisateur='.$iduser.' AND idreponsedebut='.$iddebut.' ORDER BY id DESC');  
        $arrayresult[1] = DB::select('SELECT * FROM maintenance_date WHERE idequipement='.$idequipement.' AND idutilisateur='.$iduser.' AND iddebut='.$iddebut.' AND isfonctionnement=1 ORDER BY id DESC');  
        $arrayresult[2] = DB::select('SELECT * FROM maintenance_date WHERE idequipement='.$idequipement.' AND idutilisateur='.$iduser.' AND iddebut='.$iddebut.' AND idequipement IS NOT NULL AND isfonctionnement=0 ORDER BY id DESC');  
        $array = $arrayresult;
        $arrayresult1 = array();
        $getouput = DB::select('SELECT COALESCE((SELECT is_verification_avant_mise_service FROM equipement_is_date where equipement_is_date.idequipement='.$idequipement.' LIMIT 1),"") as isavantmiseserve,COALESCE((SELECT is_bon_fonctionnement FROM equipement_is_date where equipement_is_date.idequipement='.$idequipement.' LIMIT 1),"") as isbonfonctionnement'); 
        $isavant = count($getouput) == 0 ? 0 : $getouput[0]->isavantmiseserve;
        $isbonfonctionnement = count($getouput) == 0 ? 0 : $getouput[0]->isbonfonctionnement; 
        $pdf = PDF::loadView('equipement', compact('array','isavant','isbonfonctionnement'));
        $content = $pdf->output();
        file_put_contents('historiqueequipement'.$idequipement.$valeur.'.pdf', $content);
        $lienpdf = 'http://backend.pharmcogroup.net/'.'historiqueequipement'.$idequipement.$valeur.'.pdf';
        $arrayresult1[0]=$array;
        $arrayresult1[1]=$lienpdf;

        return  response()->json($arrayresult1);
        //return  response()->json($arrayresult);
    }

    public function Getlastdateequipement($valeur)
    {
        $arrayresult = array();
        $idequipement = explode('~', $valeur)[0];
        $iddebut = explode('~', $valeur)[1];
        $arrayresult = DB::select('SELECT * FROM equipement_date WHERE idequipement='.$idequipement.' AND idreponsedebut='.$iddebut.' ORDER BY id DESC LIMIT 1');  

        return  response()->json($arrayresult);
    }
    
    public function getenvironnement($lesdeux,Request $request)
    {
        $resultat = array();
        $arrayresult = array();
        $lienpdf = '';$isenvironnement=0;
        $arraytitre = array();$array = array();$arraytitre2 = array();$array2 = array();
        $response = new UserReponseController();
        $exigence = $response->outputexigence($lesdeux,$request);
        //print_r($exigence);
        $isetude=0;$isnotice=0;$isdanger=0;$isurgence=0;$isetudedetaille=0;$isaudit=0;
        if (count((array)$exigence) != 0) {
            $eltoutput = $exigence[0];
            foreach ($eltoutput as $key => $value) {
                if ($value->titre == 'output 71') {
                    $isnotice ++;
                }
                if ($value->titre == 'output 72') {
                    $isetude ++;
                }
                if ($value->titre == 'output 73') {
                    $isetudedetaille ++;
                }
                if ($value->titre == 'output 74') {
                    $isaudit ++;
                }
                if ($value->titre == 'output 82') {
                    $isdanger ++;
                }
                if ($value->titre == 'output 81') {
                    $isurgence ++;
                }
                if ($value->titre == 'output 71' || $value->titre == 'output 72' || $value->titre == 'output 73' || $value->titre == 'output 74') {
                    $isenvironnement++;
                }
            }
        }
        if ($isenvironnement != 0) {
            if ($isaudit != 0) {
                $output = Output::whereIn('titre', ['output 74'])->limit(1)->get();
                foreach($output as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre2 []= $value->titre;
                        $array2 []= $value;
                    }                                     
                }
            }
            elseif ($isetudedetaille != 0) {
                $output = Output::whereIn('titre', ['output 73'])->limit(1)->get();
                foreach($output as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre2 []= $value->titre;
                        $array2 []= $value;
                    }                                     
                }
            }
            elseif ($isetude != 0) {
                $output = Output::whereIn('titre', ['output 72'])->limit(1)->get();
                foreach($output as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre2 []= $value->titre;
                        $array2 []= $value;
                    }                                     
                }
            }            
            elseif ($isnotice != 0) {                
                $output = Output::whereIn('titre', ['output 71'])->limit(1)->get();
                foreach($output as $key=>$value){
                    if(!in_array($value->titre,$arraytitre)){
                        $arraytitre2 []= $value->titre;
                        $array2 []= $value;
                    }                                     
                }
            }
        }        
        if ($isdanger != 0) {                
          $output = Output::whereIn('titre', ['output 82'])->limit(1)->get();
          foreach($output as $key=>$value){
              if(!in_array($value->titre,$arraytitre)){
                  $arraytitre2 []= $value->titre;
                  $array2 []= $value;
              }                                     
          }
        }       
        if ($isurgence != 0) {                
          $output = Output::whereIn('titre', ['output 81'])->limit(1)->get();
          foreach($output as $key=>$value){
              if(!in_array($value->titre,$arraytitre)){
                  $arraytitre2 []= $value->titre;
                  $array2 []= $value;
              }                                     
          }
        }


        $firstquestion = User_reponse::where('id',$lesdeux)->first();
        if ($firstquestion->etat == 'construction') {
            $output = Output::whereIn('titre', ['output 1000','output 1800','output 1900','output 2000','output 2100','output 2200','output 2300'])->get();
        }
        else{
            $output = Output::whereIn('titre', ['output 1800','output 1900','output 2000','output 2200','output 2300'])->get();
        }
        
        foreach($output as $key=>$value){
            if(!in_array($value->titre,$arraytitre)){
                $arraytitre2 []= $value->titre;
                $array2[]= $value;
            }                                     
        }
        $getouput = DB::select('SELECT * FROM  outputs WHERE isenvironnement=1');
        foreach($getouput as $key=>$value){
            if(!in_array($value->titre,$arraytitre)){
                $arraytitre []= $value->titre;
                $array []= $value;
            }                                     
        }  
      
        //return view('environnement',compact('array','array2'));
        $pdf = PDF::loadView('environnement', compact('array','array2'));
        $content = $pdf->output();
        file_put_contents('environnement'.$lesdeux.'.pdf', $content);
        $lienpdf = 'http://backend.pharmcogroup.net/'.'environnement'.$lesdeux.'.pdf';
        $arrayresult[0]=$array;
        $arrayresult[1]='';
        $arrayresult[2]=$lienpdf;
        $arrayresult[3]=$array2;

        return  response()->json($arrayresult);
    }
    
    public function getformation()
    {
        $resultatg = array();$resultatd = array();
        $arrayresult = array();
        $resultatg = DB::select('SELECT * FROM  formation WHERE equipement = "general"');
        foreach(DB::select('SELECT * FROM  formation WHERE equipement != "general" GROUP BY equipement') as $elt){
            $monarray = array();
            $monarray['id'] = $elt->id;
            $monarray['equipement'] = $elt->equipement;
            $monarray['element'] = DB::select('SELECT * FROM  formation WHERE equipement = "'.$elt->equipement.'"');
            $resultatd[] = $monarray;
        }
        $arrayresult[0]=$resultatg;
        $arrayresult[1]=$resultatd;

        return  response()->json($arrayresult);
    }

    public function setislu($idrappel){
      DB::table('rappel')
            ->where('id', $idrappel)
            ->update(['islu' => 1]);
    }
    
    public function getnotification($lesdeux)
    {
        $resultatg = array();
        $arrayresult = array();
        $suite ='';$idusers=explode('~', $lesdeux)[0];$iddebut=explode('~', $lesdeux)[1];
        if (isset(explode('~', $lesdeux)[2])) {
            $suite = ' AND DATE_FORMAT(daterappel,"%Y-%m-%d") = "'.explode('~', $lesdeux)[2].'"';
        }
        // DB::table('rappel')
        //     ->where('idusers', $idusers)
        //     ->where('iddebut', $iddebut)
        //     ->update(['islu' => 1]);
        //echo 'SELECT * FROM  rappel WHERE idusers = '.$idusers.' AND iddebut ='.$iddebut.' '.$suite;
        $resultatg = DB::select('SELECT * FROM  rappel WHERE idusers = '.$idusers.' AND iddebut ='.$iddebut.' '.$suite);
        $arrayresult=$resultatg;

        return  response()->json($arrayresult);
    }    
    
    public function getentreprise($lesdeux)
    {
        $resultatg = array();$resultatd = array();
        $arrayresult = array();$suite = '';
        if ($lesdeux != 'un' AND $lesdeux != 'all') {
            $suite = 'AND id IN (SELECT identreprise FROM entreprise_formation WHERE idformation='.$lesdeux.')';
        }
        $resultatg = DB::select('SELECT *,(SELECT GROUP_CONCAT(formation.equipement) FROM formation,`entreprise_formation` WHERE formation.id=entreprise_formation.idformation AND entreprise_formation.identreprise=entreprise.id) as mesequipements FROM  entreprise WHERE id IS NOT NULL '.$suite.' ORDER BY id DESC');
        $arrayresult=$resultatg;

        return  response()->json($arrayresult);
    }
    
    public function getsecurite()
    {
        $resultat = array();
        $arrayresult = array();

        $arrayresult[0]=$resultat;
        $arrayresult[1]='';
        $arrayresult[2]=$lienpdf;

        return  response()->json($arrayresult);
    }

    public function saverappel(Request $request){
        foreach (DB::select('SELECT * FROM  equipement_date WHERE idutilisateur='.$request->iduser.' AND idreponsedebut='.$request->iddebut.' AND DATE_SUB(STR_TO_DATE(prochain_rappel,"%Y-%m-%d"), INTERVAL 15 DAY) = "'.date('Y-m-d').'"')  as $key => $value) {
          $titre = 'Rappel pour votre '.Proposition::where('id',$value->idequipement)->first()->choix;
          $message = "La prochaine date de verification de votre ".Proposition::where('id',$value->idequipement)->first()->choix.' est dans dix (10) jours. Veuillez vous preparer pour refaire la verification.';

            DB::insert('INSERT INTO `rappel`(`idusers`, `iddebut`, `titre`, `message`) VALUES (?,?,?,?)', array($request->iduser, $request->iddebut,$titre,$message));          
        }
        foreach (DB::select('SELECT * FROM  equipement_date WHERE idutilisateur='.$request->iduser.' AND idreponsedebut='.$request->iddebut.' AND DATE_SUB(STR_TO_DATE(prochain_rappel,"%Y-%m-%d"), INTERVAL 1 DAY) = "'.date('Y-m-d').'"')  as $key => $value) {
          $titre = 'Rappel pour votre '.Proposition::where('id',$value->idequipement)->first()->choix;
          $message = "La prochaine date de verification de votre ".Proposition::where('id',$value->idequipement)->first()->choix.' est dans un (1) jour. Veuillez vous preparer pour refaire la verification.';

            DB::insert('INSERT INTO `rappel`(`idusers`, `iddebut`, `titre`, `message`) VALUES (?,?,?,?)', array($request->iduser, $request->iddebut,$titre,$message));          
        }
        foreach (DB::select('SELECT * FROM  equipement_date WHERE idutilisateur='.$request->iduser.' AND idreponsedebut='.$request->iddebut.' AND DATE_SUB(STR_TO_DATE(prochain_rappel,"%Y-%m-%d"), INTERVAL 1 MONTH) = "'.date('Y-m-d').'"')  as $key => $value) {
          $titre = 'Rappel pour votre '.Proposition::where('id',$value->idequipement)->first()->choix;
          $message = "La prochaine date de verification de votre ".Proposition::where('id',$value->idequipement)->first()->choix.' est dans un (1) mois. Veuillez vous preparer pour refaire la verification.';

            DB::insert('INSERT INTO `rappel`(`idusers`, `iddebut`, `titre`, `message`) VALUES (?,?,?,?)', array($request->iduser, $request->iddebut,$titre,$message));          
        }
        foreach (DB::select('SELECT * FROM  equipement_date WHERE idutilisateur='.$request->iduser.' AND idreponsedebut='.$request->iddebut.' AND DATE_SUB(STR_TO_DATE(prochain_rappel,"%Y-%m-%d"), INTERVAL 1 HOUR) = "'.date('Y-m-d').'"')  as $key => $value) {
          $titre = 'Rappel pour votre '.Proposition::where('id',$value->idequipement)->first()->choix;
          $message = "La prochaine date de verification de votre ".Proposition::where('id',$value->idequipement)->first()->choix.' est dans une (1) heure. Veuillez vous preparer pour refaire la verification.';

            DB::insert('INSERT INTO `rappel`(`idusers`, `iddebut`, `titre`, `message`) VALUES (?,?,?,?)', array($request->iduser, $request->iddebut,$titre,$message));          
        }
        foreach (DB::select('SELECT * FROM  equipement_date WHERE idutilisateur='.$request->iduser.' AND idreponsedebut='.$request->iddebut.' AND DATE_SUB(STR_TO_DATE(prochain_rappel,"%Y-%m-%d"), INTERVAL 6 HOUR) = "'.date('Y-m-d').'"')  as $key => $value) {
          $titre = 'Rappel pour votre '.Proposition::where('id',$value->idequipement)->first()->choix;
          $message = "La prochaine date de verification de votre ".Proposition::where('id',$value->idequipement)->first()->choix.' est dans six (6) heure. Veuillez vous preparer pour refaire la verification.';

            DB::insert('INSERT INTO `rappel`(`idusers`, `iddebut`, `titre`, `message`) VALUES (?,?,?,?)', array($request->iduser, $request->iddebut,$titre,$message));          
        }
        $result = array();
        $result[0] = count(DB::select('SELECT * FROM  rappel WHERE idusers='.$request->iduser.' AND iddebut='.$request->iddebut.' AND islu=0'));

        return  response()->json($result);
    }

    public function getnbnlu(Request $request){
        $result = array();
        $result[0] = count(DB::select('SELECT * FROM  rappel WHERE idusers='.$request->iduser.' AND iddebut='.$request->iddebut.' AND islu=0'));

        return  response()->json($result);
    }

    public function savemaintenance(Request $request){
        $iddebut = $request->iddebut;
        $iduser = $request->iduser;
        $idequipement = $request->idequipement;
        $montablo = explode(';', $request->datefournisseurmaintenance);

        for ($i=0; $i <count($montablo) ; $i++) { 
            $idmaintenance = explode('~', $montablo[$i])[2];
            $dateprevue = explode('~', $montablo[$i])[0];
            $fournisseur = explode('~', $montablo[$i])[1];
            $datereel = explode('~', $montablo[$i])[3];
            $fournisseurreel = '';
            $idfonctionnement = explode('~', $montablo[$i])[4];
            DB::insert('INSERT INTO `maintenance_date`(`idequipement`,`idutilisateur`, `iddebut`, `idmaintenance`, `date_prevue`, `fournisseur`, `date_reel`, `fournisseur_reel`, `isfonctionnement`) VALUES (?,?,?,?,?,?,?,?,?)', array($idequipement,$iduser,$iddebut,$idmaintenance,$dateprevue,$fournisseur,$datereel,$fournisseurreel,$idfonctionnement));
        }
    }

    public function deletehistorique(Request $request)
    {
        if ($request->type == 'equipement') {
            DB::table('equipement_date')->where('id', $request->id)->delete();
        }
        if ($request->type == 'maintenance') {
            DB::table('maintenance_date')->where('id', $request->id)->delete();
        }
    }

    public function savedate(Request $request){

        DB::insert('INSERT INTO `equipement_date`(`idutilisateur`, `idreponsedebut`, `idequipement`, `date_nouvelle_installation`, `fait_par_nouvelle_installation`, `date_acquisition`, `fait_par_acquisition`, `date_derniere_avant_mise_service`, `fait_par_avant_mise_service`, `date_prevue_avant_mise_service`, `date_derniere_bon_fonctionnement`, `fair_par_bon_fonctionnement`, `date_prevue_prochain_bon_fonctionnement`, `date_verification_incident`,date_reel_avant_mise_service,date_reel_prochain_bon_fonctionnement,fait_verification_incident,fait_par_reel_bon_fonctionnement,fait_par_reel_avant_mise_service) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array($request->iduser, $request->iddebut,$request->idequipement,$request->datenouvelleinstallation,$request->fournisseurnouvelleinstallation,$request->dateacquisition,$request->fournisseuracquisition,$request->dateavantpremiereutilisation,$request->fournisseuravantpremiereutilisation,$request->prevueavantpremiereutilisation,$request->dateverificationbonfonctionnement,$request->fournisseurverificationapresincident,$request->prevuebonfonctionnement,$request->dateverificationapresincident,$request->reelavantpremiereutilisation,$request->reelbonfonctionnement,$request->fournisseurverificationapresincident,$request->fournisseurreelbonfonctionnement,$request->fournisseurreelavantpremiereutilisation));
        // $array = array();
        // $montablo = explode(';', $request->dates);
        // for ($i=0; $i <count($montablo) ; $i++) { 
        //     $date = explode('~', $montablo[$i])[0];$frequence = explode('~', $montablo[$i])[1];$idequipement = explode('~', $montablo[$i])[2];
        //     if ($frequence == 'quotidienne' || $frequence == 'reguliere'){
        //       $periode = '1';
        //       $frequence = 'day';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if ($frequence == 'hebdomadaire') {
        //       $periode = '7';
        //       $frequence = 'day';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if ($frequence == 'trimestriel') {
        //       $periode = '3';
        //       $frequence = 'month';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if  ($frequence == 'semestriel') {
        //       $periode = '6';
        //       $frequence = 'month';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if  ($frequence == 'mensuelle' || strpos($frequence, 'mensuelle')) {
        //       $periode = '1';
        //       $frequence = 'month';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if  (strpos($frequence, 'jours')) {
        //       $periode = filter_var($frequence, FILTER_SANITIZE_NUMBER_INT);
        //       $frequence = 'day';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if  (strpos($frequence, 'mois')) {
        //       $periode = filter_var($frequence, FILTER_SANITIZE_NUMBER_INT);
        //       $frequence = 'month';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     else if(strpos($frequence, 'an')) {
        //       $periode = filter_var($frequence, FILTER_SANITIZE_NUMBER_INT);
        //       $frequence = 'year';
        //       $prochain = date('Y-m-d', strtotime($date. ' + '.$periode.' '.$frequence));
        //     }
        //     DB::insert('INSERT INTO `equipement_date`(`idutilisateur`, `idreponsedebut`, `idequipement`, `datevaleur`,prochain_rappel, `periode`, `frequence`,dateacquisition, `fournisseuracquisition`, `fournisseurverification`) VALUES (?,?,?,?,?,?,?,?,?,?)', array($request->iduser, $request->iddebut,$idequipement,$date,$prochain,$periode,$frequence,$request->dateacquisition,$request->fournisseuracquisition,$request->fournisseurverification));
        //     $array[] = (Proposition::where('id',$idequipement)->first()->choix).'~'.$date.'~'.$prochain;

        //     $maintenir = DB::select('SELECT * FROM  maintenance_equipement WHERE idequipement='.$idequipement);
        //     foreach ($maintenir as $key => $value) {
        //         $date1  = $date;
        //         $date2  = $prochain;
        //         $output = [];
        //         $time   = strtotime($date1);
        //         $last   = date('Y-m-d', strtotime($date2));

        //         do {
        //             $time = strtotime('+'.$value->periode.' '.$value->frequence, $time);
        //             $month = date('Y-m-d', $time);
        //             $total = date('t', $time);

        //             $output[] = $month;

        //         } while ($month != $last);

        //         foreach ($output as $valeur) {
        //             DB::insert('INSERT INTO `maintenance_date`(`idequipement`, `iddebut`, `idmaintenance`, `date_prevue`) VALUES (?,?,?,?)', array($idequipement,$request->iddebut,$value->id,$valeur));
        //         }
        //     }
        // }

        $arrayresult = array();
        $lienpdf='';
        // $time = time();
        // $pdf = PDF::loadView('equipement', compact('array'));
        // $content = $pdf->output();
        // file_put_contents('equipement'.$time.'.pdf', $content);
        // $lienpdf = 'http://backend.pharmcogroup.net/'.'equipement'.$time.'.pdf';

        $arrayresult[0]=$lienpdf;
        return  response()->json($arrayresult);

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
            'idcategorie' => '',
            'idquestion' => 'required',
            'choix' => 'required',
        ]);
        $proposition = Proposition::create($request->all());
        return response()->json(['message'=> 'proposition crée', 
        'proposition' => $proposition]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Proposition::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function edit(Proposition $proposition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $proposition = proposition::findOrFail($id);
        $request->validate([
            'idquestion'=> 'required',
            'idcategorie'=> '',
            'choix'=> 'required',
        ]);
        $proposition->idquestion = $request->idquestion;
        $proposition->idcategorie = $request->idcategorie;
        $proposition->choix = $request->choix;
        
        $proposition->save();
        
        return response()->json([
            'message' => 'proposition modifié!',
            'proposition' => $proposition
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proposition  $proposition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $proposition= Proposition::find($id);
        $proposition->delete();
        return response()->json([
            'message' => 'proposition supprimé'
        ]);
    }
}