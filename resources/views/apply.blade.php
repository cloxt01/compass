@extends('layouts.app')

@section('title', 'Apply · Compass')
@section('titleNavbar', 'Apply')

@section('content')
    {{-- MAIN LAYOUT GRID --}}
    <section
        x-data="{
        leftHeight: null,
        sync() {
            if (window.innerWidth >= 1280) {
                this.leftHeight = this.$refs.leftCol.offsetHeight;
            } else {
                this.leftHeight = null;
            }
        }
    }"
        x-init="
        sync();
        new ResizeObserver(() => sync()).observe($refs.leftCol);
        window.addEventListener('resize', () => sync());
    "
        class="grid grid-cols-1 gap-6 xl:grid-cols-3 items-start w-full"
    >

        {{-- COLUMN LEFT: STATS, PANEL & PROVIDER CONFIGURATION (2/3 Width) --}}
        <div x-ref="leftCol" class="xl:col-span-2 flex flex-col gap-6 w-full">

            <div class="w-full">
                <livewire:stats-overview />
            </div>

            <div class="w-full">
                <livewire:panel-configuration :accounts="$accounts" :adapters="$adapters" />
            </div>

            <div class="w-full">
                <livewire:provider-configuration :accounts="$accounts" :adapters="$adapters" />
            </div>
        </div>

        {{-- COLUMN RIGHT: LIVE MONITORING & ACTIVITY TIMELINE (1/3 Width) --}}
        <div
            class="xl:col-span-1 flex flex-col gap-6 w-full overflow-hidden"
            :style="leftHeight ? `height: ${leftHeight}px` : ''"
        >
            <div class="w-full">
                <livewire:live-monitoring />
            </div>

            <div class="saas-card p-5 flex flex-col flex-1 min-h-0 overflow-hidden w-full">
                <div class="flex-1 min-h-0 overflow-y-auto pr-1 custom-scroll space-y-1">
                    <livewire:activity-timeline />
                </div>
            </div>

        </div>
    </section>
@endsection
@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Geist', system-ui, sans-serif;
            background: #0A0A0A;
            color: #FAFAFA;
        }
        .font-mono {
            font-family: 'Geist Mono', monospace !important;
        }

        /* Glassmorphism SaaS Dashboard Card */
        .saas-card {
            background: #111111;
            border: 1px solid #262626;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .saas-card:hover {
            border-color: #333333;
        }

        /* Standar Form Input SaaS Custom Style */
        .saas-input {
            border: 1px solid #262626;
            background: #0A0A0A;
            outline: 0;
            transition: all 0.2s ease;
        }
        .saas-input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }

        /* Rapi & Tipis Scrollbar Modifikasi */
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #262626;
            border-radius: 9999px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }
    </style>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.private(`users.{{ auth()->id() }}`)
                .listen('.JobStatus', (incomingEvent) => {
                    Livewire.dispatch('job-status-updated', {
                        payload: {
                            data: {
                                status: incomingEvent.status,
                                provider: incomingEvent.provider,
                                jobId: incomingEvent.data?.job?.id || null,
                                jobTitle: incomingEvent.data?.job?.title || null,
                                jobCompany: incomingEvent.data?.job?.company || null,
                            }
                        }
                    });

                    Livewire.dispatch('queue-status-refreshed', {
                        pending: incomingEvent.pending || 0
                    });
                });
        }
    });
</script>
