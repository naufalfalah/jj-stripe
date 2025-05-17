<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ElevenLabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AITextToSpeechController extends Controller
{
    private $elevenLabsService;

    public function __construct(ElevenLabsService $elevenLabsService)
    {
        $this->elevenLabsService = $elevenLabsService;
    }

    public function index()
    {
        $title = 'AI Speech to Text';
        $data = [
            'breadcrumb_main' => $title,
            'breadcrumb' => $title,
            'title' => $title,
        ];
        return view('client.ai-text-to-speech.index', $data);
    }

    public function generateSpeech(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'voice_id' => 'required|string',
        ]);

        $audioData = $this->elevenLabsService->generateVoice($request->text, $request->voice_id);

        if (!$audioData) {
            return redirect()->back()->withErrors(['error' => 'Audio generation failed.']);
        }

        $audioFileName = 'audio_' . time() . '.mp3';
        Storage::put('public/audio/' . $audioFileName, $audioData);
        $audioUrl = Storage::url('public/audio/' . $audioFileName);
        Session::flash('audioUrl', $audioUrl);
        
        return redirect()->route('user.ai-tts.index');
    }
}
