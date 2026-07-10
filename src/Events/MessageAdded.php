<?php

namespace HardikSolanki\OpenAILaravel\Events;

use HardikSolanki\OpenAILaravel\Models\Conversation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageAdded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public string $role,
        public string $content,
    ) {
    }
}
