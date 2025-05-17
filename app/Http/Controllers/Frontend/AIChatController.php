<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AIChatService;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    protected $aiChatService;

    public function __construct(AIChatService $aiChatService)
    {
        $this->aiChatService = $aiChatService;
    }

    public function index()
    {
        $title = 'AI Chat';
        $data = [
            'breadcrumb_main' => $title,
            'breadcrumb' => $title,
            'title' => $title,
        ];
        return view('client.ai-chat.index', $data);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $result = $this->aiChatService->generateMessage($request->message);

            $conversationHistory = session('conversationHistory', []);
            $conversationHistory[] = [
                'user_message' => $request->message,
                'ai_response' => $result ?? 'No response from AI.',
            ];
            session(['conversationHistory' => $conversationHistory]);

            return redirect()->route('user.ai-chat.index')->with([
                'aiResponse' => $result ?? 'No response from AI.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('user.ai-chat.index')->withErrors([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
