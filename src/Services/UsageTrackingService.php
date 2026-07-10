<?php

namespace HardikSolanki\OpenAILaravel\Services;

use Carbon\Carbon;
use HardikSolanki\OpenAILaravel\Models\UsageLog;
use HardikSolanki\OpenAILaravel\Support\UsageStatistics;

class UsageTrackingService
{
    public function logUsage(
        int $teamId,
        int $userId,
        ?int $conversationId,
        string $model,
        int $promptTokens,
        int $completionTokens,
        float $cost,
        string $status = 'success',
        ?string $error = null,
        ?int $apiKeyId = null,
    ): UsageLog {
        return UsageLog::create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'conversation_id' => $conversationId,
            'api_key_id' => $apiKeyId,
            'model' => $model,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'cost' => $cost,
            'status' => $status,
            'error_message' => $error,
        ]);
    }

    public function getTeamUsage(int $teamId, ?Carbon $startDate = null, ?Carbon $endDate = null): UsageStatistics
    {
        return $this->summarize(
            UsageLog::where('team_id', $teamId)
                ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
                ->when($endDate, fn ($q) => $q->where('created_at', '<=', $endDate))
        );
    }

    public function getUserUsage(int $teamId, int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): UsageStatistics
    {
        return $this->summarize(
            UsageLog::where('team_id', $teamId)
                ->where('user_id', $userId)
                ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
                ->when($endDate, fn ($q) => $q->where('created_at', '<=', $endDate))
        );
    }

    public function getDailyBreakdown(int $teamId, Carbon $date): array
    {
        return UsageLog::where('team_id', $teamId)
            ->whereDate('created_at', $date)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as requests, SUM(total_tokens) as tokens, SUM(cost) as cost')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    public function getModelBreakdown(int $teamId): array
    {
        return UsageLog::where('team_id', $teamId)
            ->selectRaw('model, COUNT(*) as requests, SUM(total_tokens) as tokens, SUM(cost) as cost')
            ->groupBy('model')
            ->get()
            ->keyBy('model')
            ->toArray();
    }

    protected function summarize($query): UsageStatistics
    {
        $logs = $query->get();

        return new UsageStatistics(
            totalRequests: $logs->count(),
            totalTokens: (int) $logs->sum('total_tokens'),
            totalCost: (float) $logs->sum('cost'),
            byModel: $logs->groupBy('model')->map(fn ($g) => [
                'requests' => $g->count(),
                'tokens' => (int) $g->sum('total_tokens'),
                'cost' => (float) $g->sum('cost'),
            ])->toArray(),
            byUser: $logs->groupBy('user_id')->map(fn ($g) => [
                'requests' => $g->count(),
                'tokens' => (int) $g->sum('total_tokens'),
                'cost' => (float) $g->sum('cost'),
            ])->toArray(),
        );
    }
}
