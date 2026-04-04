<?php

namespace App\Contracts;

use App\Models\Chat;
use App\Models\ChatMessage;

interface ChatAgentContract
{
    /**
     * Generate a response for a user message within a chat context.
     */
    public function answer(Chat $chat, string $message): ChatMessage;

    /**
     * Set the system prompt/instructions for the agent.
     */
    public function setSystemPrompt(string $prompt): self;
}
