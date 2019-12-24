<?php

namespace App\Http\Controllers\Submission;

use App\Http\Controllers\Controller;
use App\SubmissionFile;
use App\TestCase;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class EvalFileController extends Controller
{
    public function evaluationFile(SubmissionFile $submissionFile)
    {
        $testCases = TestCase::where($submissionFile->question_id, 'question_id')->get();

        $command = "python /home/vagrant/code/storage/app/".$submissionFile->uuid.'.py';
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        dd($process->getOutput());
    }
}