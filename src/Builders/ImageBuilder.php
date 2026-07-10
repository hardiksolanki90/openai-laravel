<?php

namespace HardikSolanki\OpenAILaravel\Builders;

use HardikSolanki\OpenAILaravel\Services\OpenAIClientService;
use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use HardikSolanki\OpenAILaravel\Support\GeneratedImage;
use Illuminate\Support\Collection;

class ImageBuilder extends BaseBuilder
{
    protected ?string $prompt = null;

    protected string $size = '1024x1024';

    protected string $quality = 'standard';

    protected int $n = 1;

    public function __construct(
        protected OpenAIClientService $client,
        protected UsageTrackingService $usage,
    ) {
        parent::__construct();
        $this->model = 'dall-e-3';
    }

    public function prompt(string $text): static
    {
        $this->prompt = $text;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function quality(string $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function count(int $n): static
    {
        $this->n = $n;

        return $this;
    }

    /**
     * @return Collection<int, GeneratedImage>
     */
    public function generate(): Collection
    {
        $apiKeyId = $this->resolveApiKeyId();

        $raw = $this->client->image($apiKeyId, [
            'model' => $this->model,
            'prompt' => $this->prompt,
            'size' => $this->size,
            'quality' => $this->quality,
            'n' => $this->n,
        ]);

        $images = collect($raw['data'] ?? [])->map(fn ($item) => new GeneratedImage($item['url'] ?? ''));

        if ($this->teamId !== null && $this->userId !== null) {
            $this->usage->logUsage(
                $this->teamId,
                $this->userId,
                null,
                $this->model,
                0,
                0,
                0.0,
                'success',
                null,
                $apiKeyId
            );
        }

        return $images;
    }
}
