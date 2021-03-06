<?php

namespace App\Http\Controllers\Question;

use App\Block;
use App\Exceptions\WrongPathException;
use App\Question;
use App\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    function show() {
        return view('question/add', ['header_title'=>'문제 출제']
        );
    }

    public function add(Request $request)
    {
        $todo_code = new Question();
        $todo_code->code = $request->text;
        $todo_code->professor_id = Auth::user()->id;
        $todo_code->title = $request->title;
        $todo_code->description = $request->description;
        $todo_code->save();

        $todo_test = new TestCase();
        $todo_test->input = $request->input;
        $todo_test->output = $request->output;
        $todo_test->question_id = $todo_code->id;
        $todo_test->save();

        return json_encode(array('problem_num'=>$todo_code->id));
    }

    function editAnswer($problem_num){
        $description = Question::where('id', $problem_num)->select('code')->first();
        return view('question.edit', ['problem_num'=>$problem_num, 'description'=>$description->code, 'header_title'=>'문제 만들기']);
    }

    function addBlinkBlock($problem_num, Request $request){
        $request->text = str_replace('&nbsp;', ' ', $request->text);
        $temp = explode('!!]]', $request->text);
        $sequence=1;
        for($i=0;$i<sizeof($temp);$i++){
            $result[$i] = strstr($temp[$i], '[[!!');
            $result[$i] = substr($result[$i], 4);

            if(strlen($result[$i])>0) {
                $todo_block = new Block();
                $todo_block->question_id = $problem_num;
                $todo_block->sequence = $sequence;
                $todo_block->type = '0';
                $todo_block->content = $result[$i];
                $todo_block->save();

                $sequence++;
            }
        }

        $block = $request->text;

        for($i=0;$i<sizeof($temp)-1;$i++){
            $result[$i] = '[[!!'.$result[$i].'!!]]';
            $temp_block = explode($result[$i], $block);
            $block = $temp_block[0].$temp_block[1];
        }

        $block = explode('<br>', $block);

        for($i=0;$i<sizeof($block);$i++){
            if(strlen($block[$i])>1) {
//                echo('blink = '.ltrim($block[$i]).' size = '.strlen($block[$i]).'<br>');
                $todo_block = new Block();
                $todo_block->question_id = $problem_num;
                $todo_block->type = '1';
                $todo_block->sequence = Null;
                $todo_block->content = trim($block[$i]);
                $todo_block->save();
            }
        }
    }
}
