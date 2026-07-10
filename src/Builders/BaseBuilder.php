<?php

namespace HardikSolanki\OpenAILaravel\Builders;

use HardikSolanki\OpenAILaravel\Models\APIKey;

abstract class BaseBuilder
{
    protected string $model;

    protected float $temperature = 0.7;

    protected int $maxTokens = 2000;

    protected ?int $teamId = null;

    protected ?int $userId = null;

    protected ?int $apiKeyId = null;

    protected array $metadata = [];

    public function __construct()
    {
        $this->model = config('openai.default_model', 'gpt-4');
    }

    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function temperature(float $temp): static
    {
        $this->temperature = $temp;

        return $this;
    }

    public function tokens(int $max): static
    {
        $this->maxTokens = $max;

        return $this;
    }

    public function team(int $id): static
    {
        $this->teamId = $id;

        return $this;
    }

    public function user(int $id): static
    {
        $this->userId = $id;

        return $this;
    }

    public function apiKey(int $id): static
    {
        $this->apiKeyId = $id;

        return $this;
    }

    public function metadata(array $data): static
    {
        $this->metadata = $data;

        return $this;
    }

    abstract public function generate();

    protected function resolveApiKeyId(): ?int
    {
        if ($this->apiKeyId !== null) {
            return $this->apiKeyId;
        }

        if ($this->teamId === null) {
            return null;
        }

        return APIKey::where('team_id', $this->teamId)->where('is_active', true)->value('id');
    }
}
