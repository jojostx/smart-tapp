<x-filament::page>
    <div
        x-data="{ 
            open: true,
            chat_styles: { maxHeight:'600px' },
        }"
        x-init="Alpine.effect(() => {
            const mql_o = window.matchMedia(`(orientation: portrait)`);
            const setChatMaxHeight = () => {
                height = window.innerHeight - $el.getBoundingClientRect().top - 16;
                chat_styles.maxHeight = `${height}px`;
            };
            setChatMaxHeight();
            mql_o.addEventListener('change', setChatMaxHeight);

            const mql = window.matchMedia(`(max-width: 768px)`);
            const setChatSidebarVisibility = (e) => {
                if (e === true || e === false ) {
                    open = e;
                } else {
                    open = !e.matches;
                }
            };
            setChatSidebarVisibility(!mql.matches)
            mql.addEventListener('change', (e) => {
                setChatMaxHeight();
                setChatSidebarVisibility(e);
            });
        })" 
        x-bind:style="chat_styles" 
        class="relative grid grid-cols-6 overflow-hidden min-h-[480px] bg-white border border-gray-300 rounded-lg shadow-sm"
        >
        <div 
            x-cloak 
            x-show="open" 
            @keydown.escape.window="window.matchMedia(`(max-width: 768px)`).matches && (open = false)" 
            @click.outside="window.matchMedia(`(max-width: 768px)`).matches && (open = false)" 
            class="absolute inset-0 z-[5] md:static h-full md:col-span-2">
            <div 
                x-cloak 
                x-show="open" 
                x-transition.opacity 
                class="absolute inset-0 bg-slate-900/75 md:hidden"
            >
            </div>

            <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="-translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="-translate-x-0" x-transition:leave-end="-translate-x-full" @click.outside="window.matchMedia(`(max-width: 768px)`).matches && (open = false)" class="absolute inset-y-0 left-0 flex flex-col w-11/12 h-full bg-white md:static md:w-full md:border-r md:border-gray-200">
                <div class="flex-shrink-0 text-gray-300 search-box">
                    <div class="flex justify-between p-2 py-4 pl-3 border-b border-gray-200">

                        <div class="relative w-full mr-2 filament-chat-search-input">
                            <x-filament::inbox.search.input />

                            @if (filled($results))
                            <x-filament::inbox.search.results-container :results="$results" wire-click-event="'getMessageable'" />
                            @endif
                        </div>

                        <x-filament::icon-button @click="open = false" :dark-mode="false" :icon="'heroicon-o-x'" class="md:hidden" title="close chat drawer">
                            <x-slot name="label">
                                close chat drawer
                            </x-slot>
                        </x-filament::icon-button>
                    </div>
                </div>

                <x-filament::inbox.messageable.list :active-menu="$this->activeMenu" :title="'Recent Conversations'" :messageables="$this->messageables" :$selectedMessageable :wire-click-event="'getMessageable'" :sum-attribute="'sent_messages_count'" />
            </div>
        </div>

        <div 
            x-bind:style="chat_styles" 
            class="relative z-0 col-span-full flex flex-col flex-auto h-full min-h-[480px] px-4 md:col-span-4">
            <div 
                wire:loading.flex
                wire:target="mountInboxAction" 
                class="items-center justify-center h-full">
                <svg class="w-20 h-20 m-auto" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                    <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-400" stroke-width="3">
                        <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="0s"></animate>
                        <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="0s"></animate>
                    </circle>
                    <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-800" stroke-width="3">
                        <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-0.5s"></animate>
                        <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-0.5s"></animate>
                    </circle>
                </svg>
            </div>

            @if (filled($selectedMessageable))
            <nav 
                wire:loading.remove 
                wire:target="mountInboxAction" 
                class="relative flex items-center py-4">
                <div class="mr-1 md:hidden">
                    <x-filament::icon-button @click="open = !open" :dark-mode="false" :icon="'heroicon-o-menu-alt-2'" title="menu">
                        <x-slot name="label">
                            menu
                        </x-slot>
                    </x-filament::icon-button>
                </div>
                <!-- user info -->
                <div class="flex items-center">
                    <div style="background-image: url('{{ getUiAvatarUrl($selectedMessageable->name) }}')" class="w-8 h-8 bg-gray-200 bg-center bg-cover rounded-full dark:bg-gray-900"></div>
                    <div class="pl-2">
                        <p class="flex items-center gap-1 font-semibold text-gray-500 capitalize">
                            <span class="text-gray-900">{{ $selectedMessageable->name }}</span>
                            &#x2022;
                            <x-dynamic-component :component="$this->isAdmin($selectedMessageable::class) ? 'heroicon-o-user' : 'heroicon-o-support'" class="w-4 h-4 shrink-0" />
                        </p>
                        <p class="text-xs text-gray-600">{{ $this->onlineStatus }}</p>
                    </div>
                </div>
                <!-- end user info -->
            </nav>

            <div
                x-init="$el.scrollIntoView();"
                wire:loading.remove 
                wire:target="mountInboxAction"
                class="relative flex flex-col flex-auto flex-shrink-0 h-[80%] bg-gray-100">
                <!-- chat box -->
                <div
                    x-data="{
                        seenMessages: new Set(),
                        init: function () {
                            $nextTick(() => {
                                this.scrollToBottom($refs.oldest_unseen);
                                this.observeChanges();

                                $watch('seenMessages', async (seen_messages) => {
                                    if (seen_messages.size >= 2) {
                                        await this.markAsSeen([...seen_messages]);
                                        seen_messages.clear();
                                    }
                                    console.log(seen_messages);
                                });
                            });
                        },
                        scrollToBottom: function (el) {
                            if (el) {
                                $el.scrollTop = el.scrollHeight - el.getBoundingClientRect().height;
                            } else {
                                $el.scrollTop = $el.scrollHeight;
                            }
                        },
                        observeChanges: async function () {
                            const io = new IntersectionObserver(async (entries) => {
                                let messagesToBeMarkedAsSeen = [];

                                entries.forEach(async (entry) => {
                                    if (entry.isIntersecting) {
                                        if (entry.target == $refs.scroll_pin) {
                                            await this.markAsSeen('all');
                                            return;
                                        }
                                        if (entry.target.dataset.seen === 'false') {
                                            messagesToBeMarkedAsSeen.push(entry.target.dataset.key);
                                        }
                                    }
                                });

                                await this.markAsSeen(messagesToBeMarkedAsSeen.map((n) => parseInt(n) || 0));
                            }, { root: $el, rootMargin: '0px', threshold: 0.7 });

                            let messages = document.querySelectorAll('.inbox-message');
                            messages.forEach((el) => {
                                io.observe(el);
                            });

                            io.observe($refs.scroll_pin);
                        },
                        markAsSeen: async function (key) {
                            await $wire.markMessagesAsSeen(key)
                        }
                    }"
                    x-on:scroll-chat-to-bottom.window="
                        scrollToBottom();
                        await $wire.markMessagesAsSeen();
                    "
                    class="grid grid-cols-12 gap-y-2 pb-4 mb-[4.5rem] overflow-y-auto bg-gray-100">

                    @foreach ($this->messages as $message)
                    <div
                        data-key="{{ $message->getKey() }}"
                        data-seen="@js($message->seen())"
                        @if($this->isOldestUnseenMessage($message)) x-ref="oldest_unseen" @endif
                        @class([
                            "inbox-message p-3 col-span-full",
                            "md:col-start-1 md:col-end-8" => $message->sender->is($selectedMessageable),
                            "md:col-start-6 md:col-end-13" => $message->receiver->is($selectedMessageable)
                        ])>
                        <div 
                            @php 
                                $params = $message->sender->is($selectedMessageable) ? ['background' => 'random'] : ['background' => '111827', 'color' => 'FFFFFF'];
                                $avatarUrl = getUiAvatarUrl($message->sender->name, $params);
                            @endphp
                            @class([
                                "flex items-center",
                                "flex-row" => $message->sender->is($selectedMessageable),
                                "flex-row-reverse justify-start" => $message->receiver->is($selectedMessageable)
                            ])>
                            <div style="background-image: url('{{ $avatarUrl }}')" class="w-8 h-8 bg-gray-200 bg-center bg-cover rounded-full shrink-0 dark:bg-gray-900"></div>
                            <div class="relative px-4 py-2 mx-2 text-sm bg-white shadow rounded-xl">
                                <p>{{ $message->body }}</p>

                                <div 
                                    @class([
                                        "absolute flex items-center bottom-0 gap-1 -mb-5 text-xs text-gray-500",
                                        "left-0 ml-2"=> $message->sender->is($selectedMessageable),
                                        "right-0 mr-2" => $message->receiver->is($selectedMessageable)
                                    ])>
                                    @if($this->messageIsSeen($message))
                                        <x-heroicon-o-eye class="w-3 h-3" />
                                        &#x2022;
                                    @endif
                                    {{ $message->created_at->shortAbsoluteDiffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div x-ref="scroll_pin" class="col-span-full h-2"></div>
                </div>
                <!-- chat box -->

                <form wire:submit.prevent="sendMessage" class="absolute bottom-0 flex flex-row items-center w-full py-4 bg-white">
                    <div class="flex-grow relative w-full">
                        {{ $this->form }}
                    </div>
                    <div class="ml-4">
                        <x-filament::button type="submit" :icon="'heroicon-o-paper-airplane'" title="send message" style="padding-top: 0.625rem; padding-bottom: 0.625rem">
                            {{ __('Send') }}
                        </x-filament::button>
                    </div>
                </form>
            </div>
            @else
            <div class="relative flex items-center justify-center w-full h-full">
                <p class="text-xl font-medium">No recent conversations</p>
            </div>
            @endif
        </div>
    </div>
</x-filament::page>
