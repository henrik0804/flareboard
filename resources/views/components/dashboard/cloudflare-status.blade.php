<?php

use App\Models\CloudflareStatus;
use App\Enums\Cloudflare\SummaryStatus;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {

    #[Computed]
    public function status(): CloudflareStatus
    {
        $status = CloudflareStatus::current()->first();
        
        if (!$status) {
            // Return a default status when none exists
            $status = new CloudflareStatus([
                'status' => SummaryStatus::None,
                'current_description' => 'Unable to fetch current system status.',
                'updated_at_cloudflare' => null,
                'started_at' => null,
            ]);
        }
        
        return $status;
    }

    #[Computed]
    public function enumStatus(): SummaryStatus
    {
        return $this->status->status;
    }
};
?>

<div
    {{ $attributes->merge(['class' => 'relative flex flex-col h-full transition-colors duration-300 ' . $this->enumStatus->backgroundClass()]) }} wire:poll>

    <div class="flex items-start justify-between p-5 pb-2">
        <div class="flex items-center gap-3">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/5">
                @if($this->enumStatus === SummaryStatus::Critical)
                    <svg class="h-6 w-6 {{ $this->enumStatus->iconClass() }}" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                @else
                    <svg class="h-6 w-6 {{ $this->enumStatus->iconClass() }}" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M18.303 7.242a.75.75 0 00-.548.365 4.504 4.504 0 00-1.874-1.296 6.753 6.753 0 00-4.258-.18A6.74 6.74 0 006.75 11.25c0 .23.017.456.049.678a5.25 5.25 0 00-1.33 10.322h13.78a4.5 4.5 0 00.87-8.916.75.75 0 00-.816.906 3 3 0 11-.58-5.954.75.75 0 00.58-.944z"/>
                        <path d="M9.75 12a.75.75 0 000 1.5h4.5a.75.75 0 000-1.5h-4.5z" opacity="0.5"/>
                    </svg>
                @endif
            </div>

            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Cloudflare
                </h3>
                <p class="text-sm font-bold text-gray-900 dark:text-white">
                    Status
                </p>
            </div>
        </div>

        <div
            class="flex shrink-0 items-center gap-2 rounded-full border px-2.5 py-1 text-xs font-medium {{ $this->enumStatus->badgeClass() }}">
            <span class="relative flex h-2 w-2">
                @if($this->enumStatus !== SummaryStatus::None)
                    <span
                        class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75 {{ $this->enumStatus->pulseClass() }}"></span>
                @endif
                <span class="relative inline-flex h-2 w-2 rounded-full {{ $this->enumStatus->pulseClass() }}"></span>
            </span>
            <span class="hidden sm:inline">{{ $this->enumStatus->label() }}</span>
            <span
                class="sm:hidden">{{ $this->enumStatus === SummaryStatus::None ? 'OK' : 'Issue' }}</span>
        </div>
    </div>

    <div class="flex flex-1 flex-col justify-center px-5 py-1">
        <p class="line-clamp-2 text-sm font-medium leading-relaxed text-gray-700 dark:text-gray-300">
            {{ $this->status->current_description }}
        </p>
    </div>

    <div
        class="mt-auto border-t border-gray-200/50 bg-white/50 px-5 py-3 backdrop-blur-sm dark:border-white/5 dark:bg-white/5">
        <div class="flex items-end justify-between gap-2">
            <div class="flex flex-col gap-1">
                @if($this->status->started_at)
                    <div
                        class="flex items-center gap-1.5 text-[11px] font-medium leading-none text-gray-500 dark:text-gray-400"
                        title="Incident started at {{ $this->status->started_at }}">
                        <svg class="h-3 w-3 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
                        </svg>
                        <span
                            class="{{ $this->enumStatus !== SummaryStatus::None ? 'text-orange-600 dark:text-orange-400 font-bold' : '' }}">
                            Since {{ $this->status->started_at->diffForHumans(null, true) }}
                        </span>
                    </div>
                @endif

                <div
                    class="flex items-center gap-1.5 text-[11px] font-medium leading-none text-gray-500 dark:text-gray-400"
                    title="Last checked at {{ $this->status->updated_at_cloudflare }}">
                    <svg class="h-3 w-3 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                    <span>Updated {{ $this->status->updated_at_cloudflare ? $this->status->updated_at_cloudflare->diffForHumans(null, true) : 'N/A' }}</span>
                </div>
            </div>

            <a href="https://www.cloudflarestatus.com"
               target="_blank"
               class="group flex items-center gap-1 text-xs font-semibold text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white pb-px self-center">
                Details
                <svg class="h-3 w-3 transition-transform duration-200 group-hover:translate-x-0.5" fill="none"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="pointer-events-none absolute inset-0 z-20 rounded-xl {{ $this->enumStatus->borderClass() }}"></div>
</div>
