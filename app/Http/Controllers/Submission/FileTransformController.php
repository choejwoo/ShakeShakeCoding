<?php

namespace App\Http\Controllers\Submission;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileTransformController extends Controller
{
    static public function fileTransform($submissionFile, $filepath , $blocks)
    {
        if($blocks != null)
            $blockArrays = $blocks;
        else return 0;


        $contents = '';

        foreach($blockArrays as $blockArray) // blocks 돌면서 parse
        {
            $content = $blockArray['content'];
            $depth = $blockArray['depth'];
            $type = $blockArray['type'];
            $checkFor = substr($content,0,3); // for type check 용도

            if($type == "end-for" || $type == "begin-for") // 무시할 것들
                continue;


            for($i = 0; $i < $depth; $i++)
            {
                $contents .= "\t";
            }

            $contents .= $content."\n";
            if($checkFor == "for")
            {
                for($i=0;$i<$depth; $i++)
                {
                    $contents .= "\t";
                }
                $contents .= "\tpass\n";
            } // for 문일때, check 후 해당 depth 에 pass 추가
        }

            //dd($contents);
        Storage::disk('local')->put("file/".$submissionFile->uuid.'.py' , $contents); // py 파일 생성

    }
}
