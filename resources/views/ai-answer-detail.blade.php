@extends('layouts.app')

@section('title', 'AI Answer Detail · Compass')
@section('titleNavbar', 'AI Answer')

@php
    $matchScore = (int) ($answer->match_score ?? 0);
    $scoreClass = $matchScore >= 75 ? 'text-emerald-400' : ($matchScore >= 50 ? 'text-amber-400' : 'text-rose-400');
    $scoreRing = $matchScore >= 75 ? 'bg-emerald-500/10 border-emerald-500/30' : ($matchScore >= 50 ? 'bg-amber-500/10 border-amber-500/30' : 'bg-rose-500/10 border-rose-500/30');
@endphp

@section('content')
    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6" data-testid="ai-answer-detail">

        {{-- Header --}}
        <div class="saas-card p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <a href="{{ route('applications') }}" class="inline-flex items-center gap-1.5 text-xs text-[#71717a] hover:text-[#fafafa]">
                        <i class="fas fa-arrow-left"></i> Kembali ke Applications
                    </a>
                    <h1 class="mt-2 text-lg font-semibold tracking-tight text-[#fafafa] sm:text-xl">
                        {{ $answer->job_title ?? 'Lamaran' }}
                    </h1>
                    <p class="mt-1 text-xs text-[#a1a1aa]">
                        Job ID <span class="font-mono">{{ $answer->job_id }}</span>
                        · Provider <span class="capitalize">{{ $answer->provider }}</span>
                        · {{ $answer->created_at?->diffForHumans() }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if($answer->status === 'success')
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-medium text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Sukses
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-[11px] font-medium text-rose-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span> Gagal
                        </span>
                    @endif
                    <span class="inline-flex items-center rounded-full border border-[#262626] bg-[#0a0a0a] px-2.5 py-1 text-[11px] font-medium text-[#a1a1aa]">
                        {{ $answer->model ?? '—' }}
                    </span>
                </div>
            </div>

            @if($answer->error_message)
                <div class="mt-4 rounded-lg border border-rose-500/20 bg-rose-500/10 p-3 text-xs text-rose-200">
                    <strong class="font-semibold">Error:</strong> {{ $answer->error_message }}
                </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="saas-card p-5 {{ $scoreRing }} border">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Match Score</p>
                <p class="mt-2 text-3xl font-semibold {{ $scoreClass }}" data-testid="match-score">{{ $matchScore }}%</p>
                <p class="mt-1 text-[11px] text-[#71717a]">rata-rata confidence AI</p>
            </div>
            <div class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Terjawab</p>
                <p class="mt-2 text-3xl font-semibold text-[#fafafa]">
                    {{ ($answer->total_questions ?? 0) - ($answer->unanswered_count ?? 0) }}<span class="text-lg text-[#71717a]">/{{ $answer->total_questions ?? 0 }}</span>
                </p>
                <p class="mt-1 text-[11px] text-[#71717a]">pertanyaan diisi AI</p>
            </div>
            <div class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Token Usage</p>
                <p class="mt-2 text-3xl font-semibold text-[#fafafa]">{{ number_format($answer->tokens_total ?? 0) }}</p>
                <p class="mt-1 text-[11px] text-[#71717a]">
                    prompt {{ number_format($answer->tokens_prompt ?? 0) }} · completion {{ number_format($answer->tokens_completion ?? 0) }}
                </p>
            </div>
            <div class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Durasi</p>
                <p class="mt-2 text-3xl font-semibold text-[#fafafa]">{{ number_format(($answer->duration_ms ?? 0) / 1000, 2) }}s</p>
                <p class="mt-1 text-[11px] text-[#71717a]">respons OpenRouter</p>
            </div>
        </div>

        {{-- Per-question breakdown --}}
        @if(!empty($answer->per_question))
            <div class="saas-card overflow-hidden">
                <div class="border-b border-[#262626] px-5 py-4 sm:px-6">
                    <h2 class="text-sm font-semibold tracking-tight text-[#fafafa]">Rincian Per Pertanyaan</h2>
                    <p class="mt-1 text-xs text-[#a1a1aa]">Transparansi: jawaban AI berdasarkan profil kandidat, tanpa dilebih-lebihkan.</p>
                </div>

                <div class="divide-y divide-[#262626]">
                    @foreach($answer->per_question as $pq)
                        @php
                            $conf = (int) ($pq['confidence'] ?? 0);
                            $confClass = $conf >= 75 ? 'text-emerald-400' : ($conf >= 50 ? 'text-amber-400' : 'text-rose-400');
                            $confBg = $conf >= 75 ? 'bg-emerald-500/10 border-emerald-500/30' : ($conf >= 50 ? 'bg-amber-500/10 border-amber-500/30' : 'bg-rose-500/10 border-rose-500/30');
                        @endphp
                        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-12 sm:px-6" data-testid="per-question-row">
                            <div class="sm:col-span-7">
                                <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-[#71717a]">{{ $pq['name'] ?? '—' }} · {{ $pq['type'] ?? '—' }}</p>
                                <p class="mt-1 text-sm font-medium text-[#fafafa]">{{ $pq['question'] ?? '—' }}</p>
                                @if(!empty($pq['missing_info']))
                                    <p class="mt-2 text-xs italic text-amber-300/80">
                                        <i class="fas fa-info-circle mr-1"></i>{{ $pq['missing_info'] }}
                                    </p>
                                @endif
                            </div>
                            <div class="sm:col-span-4">
                                @if($pq['is_answered'] ?? false)
                                    <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-[#71717a]">Jawaban</p>
                                    <p class="mt-1 whitespace-pre-wrap break-words text-sm text-[#fafafa]">{{ $pq['answer_summary'] ?? '—' }}</p>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#333] bg-[#161616] px-2.5 py-1 text-[11px] font-medium text-[#9ca3af]">
                                        <i class="fas fa-minus-circle text-[10px]"></i> Tidak dijawab (data tidak tersedia)
                                    </span>
                                @endif
                            </div>
                            <div class="sm:col-span-1">
                                <span class="inline-flex w-fit items-center gap-1 rounded-full border {{ $confBg }} px-2.5 py-1 text-[11px] font-semibold {{ $confClass }}">
                                    {{ $conf }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Profile Context --}}
        <div class="saas-card p-5 sm:p-6">
            <details class="group">
                <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-[#fafafa]">
                    <span>Profil Kandidat yang Dikirim ke AI</span>
                    <i class="fas fa-chevron-down text-xs text-[#71717a] transition group-open:rotate-180"></i>
                </summary>
                <pre class="mt-4 max-h-96 overflow-auto rounded-lg border border-[#262626] bg-[#0a0a0a] p-4 text-xs text-[#a1a1aa]">{{ json_encode($answer->profile ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        </div>

        {{-- Raw response --}}
        <div class="saas-card p-5 sm:p-6">
            <details class="group">
                <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-[#fafafa]">
                    <span>Raw Response AI</span>
                    <i class="fas fa-chevron-down text-xs text-[#71717a] transition group-open:rotate-180"></i>
                </summary>
                <pre class="mt-4 max-h-96 overflow-auto rounded-lg border border-[#262626] bg-[#0a0a0a] p-4 text-xs text-[#a1a1aa]">{{ json_encode($answer->raw_response ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        </div>

        {{-- Final payload --}}
        <div class="saas-card p-5 sm:p-6">
            <details class="group">
                <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-[#fafafa]">
                    <span>Jawaban Akhir (yang dikirim ke provider)</span>
                    <i class="fas fa-chevron-down text-xs text-[#71717a] transition group-open:rotate-180"></i>
                </summary>
                <pre class="mt-4 max-h-96 overflow-auto rounded-lg border border-[#262626] bg-[#0a0a0a] p-4 text-xs text-[#a1a1aa]">{{ json_encode($answer->final_answers ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        </div>

    </div>
@endsection
