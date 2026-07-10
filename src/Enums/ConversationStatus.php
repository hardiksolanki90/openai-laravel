<?php

namespace HardikSolanki\OpenAILaravel\Enums;

enum ConversationStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
