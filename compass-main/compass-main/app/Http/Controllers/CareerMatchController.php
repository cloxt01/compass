<?php

namespace App\Http\Controllers;

use App\Models\AiAnswerHistory;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;

class CareerMatchController extends Controller
{
    private const DEFAULT_WEIGHTS = [
        'skills' => 35,
        'experience' => 25,
        'location' => 15,
        'salary' => 15,
        'education' => 10,
    ];

    private function normalizedWeights(mixed $saved): array
    {
        $weights = self::DEFAULT_WEIGHTS;
        if (!is_array($saved)) {
            return $weights;
        }

        foreach ($weights as $key => $default) {
            if (isset($saved[$key]) && is_numeric($saved[$key])) {
                $weights[$key] = max(0, min(100, (int) $saved[$key]));
            }
        }

        return $weights;
    }

    public function index()
    {
        $user = auth()->user();
        $config = $user->apply_configuration ?? [];
        $careerMatch = is_array($config) && is_array($config['career_match'] ?? null) ? $config['career_match'] : [];
        $careerAi = is_array($config) && is_array($config['career_ai'] ?? null) ? $config['career_ai'] : [];
        $savedModel = $careerAi['model'] ?? null;
        $model = is_string($savedModel) && trim($savedModel) !== ''
            ? trim($savedModel)
            : (config('services.openrouter.model') ?: OpenRouterService::DEFAULT_MODEL);

        return view('career-match', [
            'weights' => $this->normalizedWeights($careerMatch['weights'] ?? []),
            'model' => $model,
        ]);
    }

    public function saveWeights(Request $request)
    {
        $weights = $request->validate([
            'weights' => ['required', 'array'],
            'weights.skills' => ['required', 'integer', 'min:0', 'max:100'],
            'weights.experience' => ['required', 'integer', 'min:0', 'max:100'],
            'weights.location' => ['required', 'integer', 'min:0', 'max:100'],
            'weights.salary' => ['required', 'integer', 'min:0', 'max:100'],
            'weights.education' => ['required', 'integer', 'min:0', 'max:100'],
        ])['weights'];

        if (array_sum($weights) !== 100) {
            return response()->json(['message' => 'Total bobot harus tepat 100%.'], 422);
        }

        $configuration = $request->user()->apply_configuration ?? [];
        if (!is_array($configuration)) {
            $configuration = [];
        }
        if (!is_array($configuration['career_match'] ?? null)) {
            $configuration['career_match'] = [];
        }
        $configuration['career_match']['weights'] = $weights;
        $request->user()->update(['apply_configuration' => $configuration]);

        return response()->json(['success' => true, 'weights' => $weights]);
    }

    public function answer(Request $request, OpenRouterService $openRouter)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:3000'],
            'candidate_context' => ['required', 'string', 'max:8000'],
            'job_context' => ['nullable', 'string', 'max:8000'],
        ]);

        try {
            $answer = $openRouter->answerScreening(
                $data['question'],
                $data['candidate_context'],
                $data['job_context'] ?? '',
                $request->user()
            );

            $resolvedModel = $openRouter->resolveConfig($request->user())['model'] ?? null;
            $history = AiAnswerHistory::create([
                'user_id' => $request->user()->id,
                'model' => $resolvedModel,
                'question' => $data['question'],
                'candidate_context' => $data['candidate_context'],
                'job_context' => $data['job_context'] ?? null,
                'answer' => $answer,
            ]);

            return response()->json([
                'answer' => $answer,
                'history_id' => $history->id,
                'created_at' => $history->created_at->toIso8601String(),
                'model' => $resolvedModel,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => $exception->getMessage()], 502);
        }
    }

    public function historyIndex(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $items = AiAnswerHistory::query()
            ->where('user_id', $request->user()->id)
            ->when($query !== '', function ($q) use ($query) {
                $like = '%' . $query . '%';
                $q->where(function ($sub) use ($like) {
                    $sub->where('question', 'like', $like)
                        ->orWhere('answer', 'like', $like)
                        ->orWhere('job_context', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'model', 'question', 'answer', 'job_context', 'created_at']);

        return response()->json([
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'model' => $item->model,
                'question' => $item->question,
                'answer' => $item->answer,
                'job_context' => $item->job_context,
                'created_at' => $item->created_at->toIso8601String(),
                'created_at_human' => $item->created_at->diffForHumans(),
            ]),
            'count' => $items->count(),
        ]);
    }

    public function historyDestroy(Request $request, int $id)
    {
        $deleted = AiAnswerHistory::query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Riwayat tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true]);
    }

    public function score(Request $request, OpenRouterService $openRouter)
    {
        $data = $request->validate([
            'candidate' => ['required', 'string', 'max:10000'],
            'job' => ['required', 'string', 'max:10000'],
            'use_ai_reasoning' => ['nullable', 'boolean'],
        ]);
        $configuration = $request->user()->apply_configuration ?? [];
        $saved = is_array($configuration) && is_array($configuration['career_match'] ?? null)
            ? ($configuration['career_match']['weights'] ?? [])
            : [];
        $weights = $this->normalizedWeights($saved);
        $candidate = mb_strtolower($data['candidate']);
        $job = mb_strtolower($data['job']);
        $signals = [
            'skills' => ['skill', 'php', 'laravel', 'javascript', 'python', 'sql', 'komunikasi', 'leadership', 'react', 'node', 'java', 'go', 'devops', 'aws'],
            'experience' => ['tahun', 'pengalaman', 'senior', 'junior', 'lead', 'manager', 'intern'],
            'location' => ['lokasi', 'remote', 'jakarta', 'bandung', 'surabaya', 'hybrid', 'onsite', 'wfh', 'wfo'],
            'salary' => ['gaji', 'salary', 'rp', 'idr', 'kompensasi', 'juta'],
            'education' => ['pendidikan', 'sarjana', 's1', 's2', 'diploma', 'degree', 'smk', 'sma'],
        ];
        $breakdown = [];
        $total = 0;
        foreach ($signals as $key => $terms) {
            $hits = count(array_filter($terms, fn ($term) => str_contains($candidate, $term) && str_contains($job, $term)));
            $available = count(array_filter($terms, fn ($term) => str_contains($job, $term)));
            $ratio = $available > 0 ? min(100, (int) round(($hits / $available) * 100)) : 70;
            $contribution = (int) round($ratio * ($weights[$key] / 100));
            $total += $contribution;
            $breakdown[$key] = ['score' => $ratio, 'weight' => $weights[$key], 'contribution' => $contribution];
        }

        $total = min(100, $total);
        $useAi = $data['use_ai_reasoning'] ?? true;
        $aiReason = '';
        if ($useAi) {
            try {
                $aiReason = $openRouter->reasonMatch($data['candidate'], $data['job'], $breakdown, $total, $request->user());
            } catch (\Throwable $e) {
                report($e);
                $aiReason = '';
            }
        }
        $fallbackReason = $total >= 75
            ? 'Profil Anda cukup selaras dengan kebutuhan utama lowongan.'
            : 'Ada beberapa area yang perlu diperkuat atau dikonfirmasi sebelum melamar.';

        return response()->json([
            'score' => $total,
            'breakdown' => $breakdown,
            'reason' => $aiReason !== '' ? $aiReason : $fallbackReason,
            'ai_reasoning_used' => $aiReason !== '',
        ]);
    }
}
