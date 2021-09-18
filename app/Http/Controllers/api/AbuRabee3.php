<?php

    namespace App\Http\Controllers\api;

    use App\Aburabee3_chat;
    use App\Aburabee3_text;
    use App\Aburabee3_unanswered;

    use App\Event;
    use App\Events\AbuRabee3_Bind;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;

    class AbuRabee3 extends Controller
    {
        

        public function Answering(Request $request)
        {    

            $validator = Validator::make($request->all(), [ 'Question' => 'required', ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $analyze_sentence = array_values(array_filter(explode(' ', $request->get('Question'))));
            
            $merge_matches=array();
            
            $GetTags = Aburabee3_text::all()
            ->map(function ($Data) {
                $Data['patterns_and_responses'] = json_decode($Data->patterns_and_responses);
                return $Data;
            });

            $CountTagMatches = [];
            foreach($GetTags as $Tag){
                
                $Patterns = $Tag->patterns_and_responses->Patterns;
                $Responses = $Tag->patterns_and_responses->Responses;

                //Convert Patterns Hash Map Array To "Only Patterns Element" Array
                $OnlyPatternsStrings = [];
                foreach($Patterns as $Pattern){ $OnlyPatternsStrings[] = $Pattern[1]; }
                
                //Convert Responses Hash Map Array To "Only Responses Element" Array
                $OnlyResponsesStrings = [];
                foreach($Responses as $Response){ $OnlyResponsesStrings[] = $Response[1]; }

                $TagMatches = [];

                foreach($analyze_sentence as $word){
                    
                    //Only Elements That Match The Word Will Be Set To A New Array
                    $ArrayOfMatchingElement = array_filter($OnlyPatternsStrings, function($var) use ($word) { return preg_match("/$word/i", $var); });

                    //Counting The New Array Elements Will Count How Many Times A Single Word Matched The Elements
                    $TagMatches[] = count($ArrayOfMatchingElement);
                }
                
                //array_sum($TagMatches) : Summation Number Gives How Many Times A Single Tag Matched Each Word Of The Sentence
                $CountTagMatches[] = [
                    "Tag" => $Tag->tag,
                    "RandomAnswer" => $OnlyResponsesStrings[array_rand($OnlyResponsesStrings)],
                    "Matches" => array_sum($TagMatches)
                ];
 
            }

            //Get Most Matched Tag
            array_multisort(array_column($CountTagMatches, 'Matches'), SORT_DESC, $CountTagMatches);
            $Answer = $CountTagMatches[0];
            
            if($Answer['Matches'] == 0){
                $unanswered = new Aburabee3_unanswered;
                $unanswered->member_role = user_role();
                $unanswered->member_id = user()->id;
                $unanswered->message = $request->get('Question');
                $unanswered->answered = 0; 
                $unanswered->save();
            }

            //STORE QUESTION IN CHATTING TABLE
            $store_msgs = new Aburabee3_chat;
            $store_msgs->member_role = user_role();
            $store_msgs->member_id = user()->id;
            $store_msgs->user_msg = $request->get('Question');
            $store_msgs->aburabee3_msg = ($Answer['Matches'] == 0) ? "Am Sorry Can You Explain More Please ?" : $Answer['RandomAnswer'];
            $store_msgs->plugin = 0;
            $store_msgs->save();

            
            event(new AbuRabee3_Bind($store_msgs));
            return Result($store_msgs);
            
        }

        public function GetMsgs()
        {
            $get = Aburabee3_chat::where('member_id', user()->id)->orderBy('id', 'DESC')->paginate(25);

            return $get;
        }

        public function fetch(){

            $Get = Aburabee3_text::orderBy('id', 'DESC')->paginate(50)->map(function ($Data) {
                $Data['Data'] = json_decode($Data->patterns_and_responses);
                return $Data;
            });
            
            return response()->json($Get, 200);

        }

        public function AddResponse(Request $request){
            
            $new = new Aburabee3_text;
            $new->tag = $request->get('tag');
            $new->patterns_and_responses = '{"Patterns":[],"Responses":[]}';
            $new->save();

            return response()->json('seccuss', 200); 
        }

        public function DeleteResponse(Request $request){
            
            $delete = Aburabee3_text::find($request->get('id'))->delete();

            if(!$delete) { return response()->json('invalid id', 404); }
            
            return response()->json('seccuss', 200);

        }

        public function GetChatMsgs(){
            
            $get = Aburabee3_unanswered::where('answered', 0)->paginate(50);

            return response()->json($get, 200);

        }

        public function teach_aburabee3(Request $request){
            
            $validator = Validator::make($request->all(), [ 'id' => 'required|exists:aburabee3_unanswereds,id', 'tag' => 'required', 'patterns_and_responses' => 'required', ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $new = new Aburabee3_text;
            $new->tag = $request->get('tag');
            $new->patterns_and_responses = $request->get('patterns_and_responses');
            $new->save();

            $update = Aburabee3_unanswered::find($request->get('id'));
            $update->answered = 1;
            $update->save();

            return response()->json(null, 200);

        }
        
        public function SavePatternsAndResponses(Request $request){

            $validator = Validator::make($request->all(), [ 'id' => 'required|exists:aburabee3_texts,id', ]);
            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $update = Aburabee3_text::find($request->get('id'));
            $update->patterns_and_responses = $request->get('patterns_and_responses');
            $update->save();

            return response()->json(null, 200);
        }
    }
