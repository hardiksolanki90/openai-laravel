<?php

namespace HardikSolanki\OpenAILaravel\Enums;

enum APIKeyStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case REVOKED = 'revoked';
}
