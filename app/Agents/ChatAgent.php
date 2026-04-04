<?php

namespace App\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;

class ChatAgent implements Agent, Conversational
{
    use Promptable;

    private array $conversationMessages = [];

    public function __construct(
        private string $systemInstructions,
    ) {}

    public function instructions(): string
    {
        return $this->systemInstructions;
    }

    public function messages(): iterable
    {
        return $this->conversationMessages;
    }

    public function withMessages(array $messages): self
    {
        $this->conversationMessages = $messages;

        return $this;
    }
}
