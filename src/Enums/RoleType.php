<?php

namespace HardikSolanki\OpenAILaravel\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case VIEWER = 'viewer';
}
