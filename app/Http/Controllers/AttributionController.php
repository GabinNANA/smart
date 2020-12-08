<?php

namespace App\Http\Controllers;

use App\Models\Habitation_question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributionController extends Controller
{
    public function index()
    {
        $questions = DB::table('questions')
            ->get();
            
        $results = array();
        foreach ($questions as $question) {
            $result = array();
            $habitations = DB::table('habitation_questions')
            ->where('idquestion',$question->id)
                ->get();

            $result['id'] = $question->id;
            $result['intitule'] = $question->intitule;
            $result['ordre'] = $question->ordre;
            $result['habitations'] = $habitations;

            array_push($results,$result);
        }

        return response()->json($results);
    }
    public function store(Request $request)
    {
        $request->validate([
            'idhabitation' => 'required',
            'idquestion' => 'required',
            'etat' => 'required',
        ]);
        $habitation_question = Habitation_question::create($request->all());

        return response()->json(['message'=> 'question crée', 'habitation_question' => $habitation_question]);
    }
    public function destroy($id)
    {
        
        $question= Habitation_question::find($id);
        $question->delete();
        return response()->json([
            'message' => 'habitation_question supprimé'
        ]);
    }
}
