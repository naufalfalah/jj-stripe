<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\TextHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AIVoiceChatRequest;
use App\Models\AiContent;
use App\Services\AIChatService;
use App\Services\ElevenLabsService;
use App\Services\GoogleTextToSpeechService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AIVoiceChatController extends Controller
{
    protected $aiChatService;
    protected $elevenLabsService;
    protected $googleTextToSpeechService;

    public function __construct(AIChatService $aiChatService, ElevenLabsService $elevenLabsService, GoogleTextToSpeechService $googleTextToSpeechService)
    {
        $this->aiChatService = $aiChatService;
        $this->elevenLabsService = $elevenLabsService;
        $this->googleTextToSpeechService = $googleTextToSpeechService;
    }

    public function index()
    {
        $title = 'AI Voice Chat';
        $chats = AiContent::where('addedby_id', auth('web')->user()->id)
            ->orderBy('created_at')
            ->get();

        $data = [
            'breadcrumb_main' => $title,
            'breadcrumb' => $title,
            'title' => $title,
            'chats' => $chats,
        ];
        return view('client.ai-voice-chat.index', $data);
    }

    public function sendMessage(AIVoiceChatRequest $request)
    {
        try {
            $aiContent = $this->aiChatService->generateMessage($request->message);

            // Generate text to speech
            $audioFileName = "ai_content_{$aiContent->id}_audio_" . time() . '.mp3';
            $audioData = $this->googleTextToSpeechService->synthesizeSpeech($aiContent->text, $audioFileName);

            // Save audio
            if ($audioData) {
                $audioUrl = Storage::disk('ai-content')->url($audioFileName);
                Session::flash('audioUrl', $audioUrl);

                $aiContent->audio_path = $audioFileName;
                $aiContent->save();
            } else {
                Session::flash('audioError', 'Audio generation failed.');
                $audioUrl = null;
            }

            return redirect()->route('user.ai-voice-chat.index')->with([
                'aiResponse' => $result ?? 'No response from AI.',
                'audioUrl' => $audioUrl,
                'audioError' => session('audioError'),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('user.ai-voice-chat.index')->withErrors([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
