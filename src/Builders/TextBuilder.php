<?php

namespace HardikSolanki\OpenAILaravel\Builders;

use Generator;
use HardikSolanki\OpenAILaravel\Events\BudgetLimitReached;
use HardikSolanki\OpenAILaravel\Events\RateLimitExceeded;
use HardikSolanki\OpenAILaravel\Exceptions\BudgetExceededException;
use HardikSolanki\OpenAILaravel\Exceptions\RateLimitExceededException as RateLimitExceededExceptionAlias;
use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use HardikSolanki\OpenAILaravel\Models\Team;
use HardikSolanki\OpenAILaravel\Services\ContextOptimizerService;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use HardikSolanki\OpenAILaravel\Services\CostCalculationService;
use HardikSolanki\OpenAILaravel\Services\OpenAIClientService;
use HardikSolanki\OpenAILaravel\Services\PromptCacheService;
use HardikSolanki\OpenAILaravel\Services\RateLimitService;
use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use HardikSolanki\OpenAILaravel\Support\CachedResponse;
use HardikSolanki\OpenAILaravel\Support\Response;
use HardikSolanki\OpenAILaravel\Support\StreamChunk;

class TextBuilder extends BaseBuilder
{
    protected ?string $prompt = null;

    protected array $history = [];

    protected bool $stream = false;

    protected ?string $systemPrompt = null;

    protected bool $saveToConversation = false;

    protected ?int $conversationId = null;

    protected bool $cacheEnabled = false;

    protected int $cacheTtl = 3600;

    public function __construct(
        protected ConversationService $conversations,
        protected UsageTrackingService $usage,
        protected RateLimitService $rateLimiter,
        protected CostCalculationService $costCalculator,
        protected ContextOptimizerService $contextOptimizer,
        protected PromptCacheService $promptCache,
        protected OpenAIClientService $client,
    ) {
        parent::__construct();
    }

    public function prompt(string $text): static
    {
        $this->prompt = $text;

        return $this;
    }

    public function history(array $messages): static
    {
        $this->history = $messages;

        return $this;
    }

    public function stream(bool $stream = true): static
    {
        $this->stream = $stream;

        return $this;
    }

    public function systemPrompt(string $prompt): static
    {
        $this->systemPrompt = $prompt;

        return $this;
    }

    public function saveToConversation(int $convId): static
    {
        $this->saveToConversation = true;
        $this->conversationId = $convId;

        return $this;
    }

    public function cache(int $ttlSeconds = 3600): static
    {
        $this->cacheEnabled = true;
        $this->cacheTtl = $ttlSeconds;

        return $this;
    }

    public function generate(): Response
    {
        $apiKeyId = $this->guardBudgetAndRateLimit();

        $messages = $this->buildMessages();

        if ($this->cacheEnabled && $this->teamId !== null) {
            $cached = $this->promptCache->get($this->teamId, $this->model, $messages);

            if ($cached) {
                return CachedResponse::from($cached);
            }
        }

        $raw = $this->client->chat($apiKeyId, [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
        ]);

        $content = $raw['choices'][0]['message']['content'] ?? '';
        $promptTokens = $raw['usage']['prompt_tokens'] ?? 0;
        $completionTokens = $raw['usage']['completion_tokens'] ?? 0;
        $cost = $this->costCalculator->calculateCost($this->model, $promptTokens, $completionTokens);

        $this->recordUsage($apiKeyId, $promptTokens, $completionTokens, $cost);

        if ($this->cacheEnabled && $this->teamId !== null) {
            $this->promptCache->set(
                $this->teamId,
                $this->model,
                $messages,
                ['content' => $content, 'prompt_tokens' => $promptTokens, 'completion_tokens' => $completionTokens],
                $promptTokens + $completionTokens,
                $cost,
                $this->cacheTtl
            );
        }

        $this->persistToConversation($content, $promptTokens, $completionTokens, $cost);

        return new Response($content, $promptTokens, $completionTokens, $cost, $raw);
    }

    public function streamGenerate(): Generator
    {
        $apiKeyId = $this->guardBudgetAndRateLimit();

        $messages = $this->buildMessages();

        $fullContent = '';
        $totalTokens = 0;
        $promptTokens = $this->contextOptimizer->estimateTokens(implode("\n", array_column($messages, 'content')));

        foreach ($this->client->chatStream($apiKeyId, [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'stream' => true,
        ]) as $chunk) {
            $delta = $chunk['choices'][0]['delta']['content'] ?? '';
            $fullContent .= $delta;
            $totalTokens = $chunk['usage']['total_tokens'] ?? $totalTokens;

            yield new StreamChunk($delta, $totalTokens);
        }

        $completionTokens = max(0, $totalTokens - $promptTokens);
        $cost = $this->costCalculator->calculateCost($this->model, $promptTokens, $completionTokens);

        $this->recordUsage($apiKeyId, $promptTokens, $completionTokens, $cost);
        $this->persistToConversation($fullContent, $promptTokens, $completionTokens, $cost);
    }

    protected function buildMessages(): array
    {
        $messages = [];

        if ($this->systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $this->systemPrompt];
        }

        foreach ($this->history as $message) {
            $messages[] = $message;
        }

        if ($this->prompt !== null) {
            $messages[] = ['role' => 'user', 'content' => $this->prompt];
        }

        return $this->contextOptimizer->optimizeMessages(
            $messages,
            $this->model,
            (float) config('openai.context_optimization.reserved_percentage', 0.15)
        );
    }

    protected function guardBudgetAndRateLimit(): ?int
    {
        if ($this->teamId === null) {
            return $this->resolveApiKeyId();
        }

        $budget = BudgetLimit::where('team_id', $this->teamId)->first();

        if ($budget && $budget->is_active && $budget->isExceeded()) {
            if ($budget->block_on_limit) {
                event(new BudgetLimitReached(Team::findOrFail($this->teamId), $budget));

                throw new BudgetExceededException($budget->remainingBudget());
            }

            event(new BudgetLimitReached(Team::findOrFail($this->teamId), $budget));
        }

        $apiKeyId = $this->resolveApiKeyId();

        if (config('openai.rate_limiting.enabled', true)) {
            $keyForLimit = $apiKeyId ?? 0;

            if (! $this->rateLimiter->isAllowed($this->teamId, $keyForLimit)) {
                if (config('openai.rate_limiting.block_when_exceeded', false)) {
                    $retryAfter = (int) config('openai.rate_limiting.max_requests_per_hour', 100) > 0
                        ? 3600
                        : 0;

                    event(new RateLimitExceeded($this->teamId, $apiKeyId, $retryAfter));

                    throw new RateLimitExceededExceptionAlias($retryAfter, $retryAfter);
                }
            } else {
                $this->rateLimiter->consumeTokens($this->teamId, $keyForLimit);
            }
        }

        return $apiKeyId;
    }

    protected function recordUsage(?int $apiKeyId, int $promptTokens, int $completionTokens, float $cost): void
    {
        if ($this->teamId === null || $this->userId === null) {
            return;
        }

        $this->usage->logUsage(
            $this->teamId,
            $this->userId,
            $this->conversationId,
            $this->model,
            $promptTokens,
            $completionTokens,
            $cost,
            'success',
            null,
            $apiKeyId
        );

        BudgetLimit::where('team_id', $this->teamId)->increment('current_spend', $cost);
    }

    protected function persistToConversation(string $content, int $promptTokens, int $completionTokens, float $cost): void
    {
        if (! $this->saveToConversation || $this->conversationId === null) {
            return;
        }

        if ($this->prompt !== null) {
            $this->conversations->addMessage($this->conversationId, 'user', $this->prompt);
        }

        $this->conversations->addMessage($this->conversationId, 'assistant', $content, [
            'tokens' => $promptTokens + $completionTokens,
            'cost' => $cost,
        ]);
    }
}
