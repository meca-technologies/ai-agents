<div
    class="page-body mt-2 relative after:h-px after:w-full after:bg-[var(--tblr-body-bg)] after:absolute after:top-full after:left-0 after:-mt-px">
    <div class="container-fluid">
        <div class="row">
            @foreach ($aiList as $entry)
                <?php
                if (optional($entry->cloned)->id) {
                    $entry = $entry->cloned;
                }
                ?>
                <div data-filter="medical"
                    class="col-lg-4 col-xl-3 col-md-6 py-8 10 px-16 relative border-b border-solid border-t-0 border-s-0 border-[var(--tblr-border-color)] group max-xl:px-10">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.chat.aiChatUpdate', $entry->id)) }}"
                        class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white float-right"
                        title="{{ __('Edit') }}">
                        <svg width="13" height="12" viewBox="0 0 16 15" fill="none" stroke="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.3125 2.55064L12.8125 5.94302M11.5 12.3038H15M4.5 14L13.6875 5.09498C13.9173 4.87223 14.0996 4.60779 14.224 4.31676C14.3484 4.02572 14.4124 3.71379 14.4124 3.39878C14.4124 3.08377 14.3484 2.77184 14.224 2.48081C14.0996 2.18977 13.9173 1.92533 13.6875 1.70259C13.4577 1.47984 13.1849 1.30315 12.8846 1.1826C12.5843 1.06205 12.2625 1 11.9375 1C11.6125 1 11.2907 1.06205 10.9904 1.1826C10.6901 1.30315 10.4173 1.47984 10.1875 1.70259L1 10.6076V14H4.5Z"
                                stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <div class="flex flex-col justify-center text-center relative">
                        <div class="inline-flex items-center justify-center w-[128px] h-[128px] rounded-full mx-auto mb-6 transition-shadow text-[44px] font-medium overflow-hidden border-solid border-[6px] !border-white shadow-[0_1px_2px_rgba(0,0,0,0.07)] text-[rgba(0,0,0,0.65)] whitespace-nowrap overflow-ellipsis dark:!border-current group-hover:shadow-xl"
                            style="background: {{ $entry->color }};">
                            @if ($entry->slug === 'ai-chat-bot')
                                <img class="w-full h-full object-cover object-center" src="/assets/img/chat-default.jpg"
                                    alt="{{ __($entry->name) }}">
                            @elseif ($entry->image)
                                <img class="w-full h-full object-cover object-center" src="/{{ $entry->image }}"
                                    alt="{{ __($entry->name) }}">
                            @else
                                <span
                                    class="block w-full text-center whitespace-nowrap overflow-hidden overflow-ellipsis">{{ __($entry->short_name) }}</span>
                            @endif
                        </div>
                        <h3 class="mb-0">{{ __($entry->name) }}</h3>
                        <p class="text-muted">{{ __($entry->description) }}</p>
                        <!-- link to the chat -->
                        <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.chat.chat', $entry->slug)) }}"
                            class="block w-full h-full absolute top-0 left-0 z-2"></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
