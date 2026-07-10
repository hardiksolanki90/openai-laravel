<?php

namespace HardikSolanki\OpenAILaravel\Traits;

trait HasTimestamps
{
    public function getCreatedAtForHumans(): ?string
    {
        return $this->created_at?->diffForHumans();
    }

    public function getUpdatedAtForHumans(): ?string
    {
        return $this->updated_at?->diffForHumans();
    }
}
