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
                class="relative flex flex-col flex-auto flex-shrink-0 h-[80%]">
                <!-- chat box -->
                <div
                    x-data="{
                        init: function () {
                            $nextTick(() => {
                                this.scrollToBottom($refs.oldest_unseen);
                            })
                        },
                        isVisible: function (el, container) {
                            if (!container) { container = $el }
                            const { bottom: eb, height: eh, top: et } = el.getBoundingClientRect();
                            const { bottom: cb, height: ch, top: ct } = container.getBoundingClientRect();

                            return et <= ct ? ct - et <= eh : eb - cb <= eh;
                        },
                        toggleAnchoring: function () {
                            const pin = $refs.scroll_pin;
                            console.log(pin);
                            console.log(isVisible(pin, $el));
                        },
                        scrollToBottom: function (el) {
                            if (el) {
                                $el.scrollTop = el.scrollHeight - el.getBoundingClientRect().height;
                            } else {
                                $el.scrollTop = $el.scrollHeight;
                            }
                        },
                        observeChanges: function () {
                            const pin = $refs.scroll_pin;

                            let M_options = { childList: true };
                            const M_observer = new MutationObserver((mutationsList, obv) => {
                                mutationsList.forEach((mutation) => {
                                    if (mutation.type === 'childList') {
                                        this.scrollToBottom(pin);
                                    }
                                })
                            });

                            let I_options = {
                                root: $el,
                                rootMargin: '0px',
                                threshold: 1.0
                            }
                            const I_observer = new IntersectionObserver((entries) => {
                                entries.forEach((entry) => {
                                    if (entry.isIntersecting) {
                                        M_observer.observe($el, M_options);
                                    } else {
                                        M_observer.disconnect();
                                    }
                                });
                            }, I_options);
                            I_observer.observe(pin);
                        },
                        markAsSeen: async function (key) {
                            await $wire.markMessagesAsSeen(key)
                        }
                    }"
                    x-on:scroll-chat-to-bottom.window="
                        scrollToBottom();
                        await $wire.markMessagesAsSeen();
                    "
                    class="grid grid-cols-12 gap-y-2 pb-4 h-full mb-[4.5rem] overflow-y-auto bg-gray-100">

                    @foreach ($this->messages as $message)
                    <div
                        @if($this->oldestUnseenMessage?->is($message)) x-ref="oldest_unseen" @endif
                        @class([
                            "p-3 col-span-full",
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
                                    @if($message->seen())
                                        <x-heroicon-o-eye class="w-3 h-3" />
                                        &#x2022;
                                    @endif
                                    {{ $message->created_at->shortAbsoluteDiffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div id="scroll_pin" x-ref="scroll_pin" class="col-span-full h-2"></div>
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


<!-- Messege area: start -->
{{-- <div class="relative col-span-4 flex flex-col justify-center max-h-[600px]">
        <div class="absolute top-0 z-10 w-full p-3 px-4 bg-white border-b border-gray-200">
            <div class="flex justify-between">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-200">
                        H
                    </div>
                    <div class="ml-2 text-sm font-semibold">Henry Boyd</div>
                </div>
            </div>
        </div>

        <div class="px-4 pt-5 pb-12 h-[80%] space-y-2 overflow-y-auto message-area">
            <!-- Chats -->
            <div class="flex justify-start receive-chat">
                <div class="relative px-4 bg-primary-400 text-white py-2 text-sm max-w-[60%] rounded font-light">
                    <i class="absolute fa fa-caret-up text-primary-400 -top-2"></i>
                    <p>
                        I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?
                    </p>
                </div>
            </div>
            <div class="flex justify-start receive-chat">
                <div class="px-4 bg-primary-400 text-white py-2 text-sm max-w-[60%] rounded font-light">
                    <p>
                        I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?
                    </p>
                </div>
            </div>
            <div class="flex justify-end send-chat">
                <div class="px-4 bg-primary-200 text-gray-500 py-2 text-sm max-w-[60%] rounded font-light">
                    <p>
                        I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?
                    </p>
                </div>
            </div>
            <div class="flex justify-start receive-chat">
                <div class="px-4 bg-primary-400 text-white py-2 text-sm max-w-[60%] rounded font-light">
                    <p>
                        I got two tickets to go to see this awesome band.
                    </p>
                </div>
            </div>
            <div class="flex justify-start receive-chat">
                <div class="px-4 bg-primary-400 text-white py-2 text-sm max-w-[60%] rounded font-light">
                    <p>
                        I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?
                    </p>
                </div>
            </div>
            <div class="flex justify-end send-chat">
                <div class="relative px-4 bg-primary-200 text-gray-500 py-2 text-sm max-w-[60%] rounded font-light">
                    <i class="absolute -bottom-2 fa fa-caret-down text-primary-200 right-4"></i>
                    <p>
                        I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Message: start -->
        <div class="absolute bottom-0 w-full p-4 bg-white border-t border-gray-200">
            <textarea id="chat" rows="1" class="block p-2.5 w-full text-sm placeholder-gray-500 transition duration-75 border-transparent rounded-lg bg-gray-400/10 focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500" placeholder="Your message..."></textarea>
        </div>
        <!-- Footer message end -->
    </div> --}}
<!-- Messege area: end -->

{{-- <div x-data="{
    open: false,
    sendMessage: function() {
        const chatbox = $refs.chatbox;
        const chatboxInput = $refs.chatbox_input;

        if ((message = chatboxInput.value?.trim()) == '') {
            return;
        }
        
        const currentdate = new Date();
        let options = {hour: '2-digit', minute: '2-digit'};
        let time = currentdate.toLocaleTimeString('en-us', options)

        const bubble = `
            <div class='flex flex-row-reverse mb-4'>
            <div class='flex flex-col items-center flex-none ml-4 space-y-1'>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' class='w-6 h-6 rounded-full'><path fill='none' d='M0 0h24v24H0z'/><path d='M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-5-8h2a3 3 0 0 0 6 0h2a5 5 0 0 1-10 0z'/></svg>
                <span class='block text-xs'>You</span>
            </div>
            <div class='relative flex-1 mb-2'>
                <div class='p-2 text-sm text-gray-800 rounded-lg bg-primary-100'>${message}</div>
                <span class='text-xs leading-none text-gray-600'>${time}</span>
            </div>
            </div>`;

        chatboxInput.value = '';
        chatbox.insertAdjacentHTML('beforeend', bubble);
        chatbox.scrollTop = chatbox.scrollHeight;
    }
    }" class="flex flex-col col-span-4 bg-white border shadow-xl dark:bg-gray-800 sm:rounded-lg">
    <nav class="p-2 space-y-2 border-b divide-y shadow ">
        <div class="flex items-center justify-between">
            <!-- user info -->
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8">
                    <path fill="none" d="M0 0h24v24H0z" />
                    <path d="M19.938 8H21a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1.062A8.001 8.001 0 0 1 12 23v-2a6 6 0 0 0 6-6V9A6 6 0 1 0 6 9v7H3a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h1.062a8.001 8.001 0 0 1 15.876 0zM3 10v4h1v-4H3zm17 0v4h1v-4h-1zM7.76 15.785l1.06-1.696A5.972 5.972 0 0 0 12 15a5.972 5.972 0 0 0 3.18-.911l1.06 1.696A7.963 7.963 0 0 1 12 17a7.963 7.963 0 0 1-4.24-1.215z" />
                </svg>
                <div class="pl-2">
                    <p class="font-semibold">John Doe</p>
                    <p class="text-xs text-gray-600">Support Agent</p>
                </div>
            </div>
            <!-- end user info -->

            <!-- chat box action -->
            <div>
                <button class="inline-flex p-2 rounded-full hover:bg-primary-50" href="tel:08034092332">
                    <x-heroicon-o-phone class="w-6 h-6" />
                </a>

                <button @click="open = false" class="inline-flex p-2 rounded-full hover:bg-primary-50" type="button">
                    <x-heroicon-o-x class="w-6 h-6" />
                </button>
            </div>
            <!-- end chat box action -->
        </div>
        <div class="pt-2">
            <span class="flex text-xs text-gray-600">
                <span>
                    <svg class="w-4 h-4" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M7 13A6 6 0 107 1a6 6 0 000 12z" stroke="#A2A2A2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7 9.999v-3.5a.5.5 0 00-.5-.5h-1m.75-2.5a.25.25 0 110 .5.25.25 0 010-.5M5.5 10h3" stroke="#A2A2A2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span>
                    &nbsp;Typically responds within 30 mins
                </span>
            </span>
        </div>
    </nav>

    <div x-ref="chatbox" class="flex-1 px-4 py-4 space-y-4 overflow-y-auto">
        <!-- chat message -->
        <div class="flex">
            <div class="items-center flex-none mr-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
            </div>
            <div class="flex flex-col max-w-[60%] relative flex-1">
                <p class="p-2 text-sm text-white rounded-lg bg-primary-400">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                <span class="text-xs leading-none text-primary-600">2 min ago</span>
            </div>
        </div>
        <!-- end chat message -->

        <div class="flex flex-col justify-start gap-1 receive-chat">
            <div class="px-4 bg-primary-400 text-white py-2 text-sm max-w-[60%] rounded-lg">
                <p>
                    I got two tickets to go to see this awesome band.
                </p>
            </div>
            <p class="text-xs leading-none text-primary-600">2 min ago</p>
        </div>

        <!-- chat message -->
        <div class="flex flex-row-reverse">
            <div class="flex flex-col items-center flex-none ml-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
                <span class='block text-xs'>You</span>
            </div>
            <div class="max-w-[60%] relative flex-1">
                <p class="p-2 text-sm text-gray-800 rounded-lg bg-primary-100">Lorem ipsum dolor sit amet, consectetur adipisicing elit.Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                <span class="text-xs leading-none text-gray-600">2 min ago</span>
            </div>
        </div>
        <!-- end chat message -->

        <!-- chat message -->
        <div class="flex">
            <div class="flex flex-col items-center flex-none mr-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
            </div>
            <div class="max-w-[60%] relative flex-1">
                <p class="p-2 text-sm text-white rounded-lg bg-primary-400">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                <span class="text-xs leading-none text-primary-600">2 min ago</span>
            </div>
        </div>
        <!-- end chat message -->

        <!-- chat message -->
        <div class="flex flex-row-reverse">
            <div class="flex flex-col items-center flex-none ml-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
                <span class='block text-xs'>You</span>
            </div>
            <div class="max-w-[60%] relative flex-1">
                <p class="p-2 text-sm text-gray-800 rounded-lg bg-primary-100">consectetur adipisicing elit.Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                <span class="text-xs leading-none text-gray-600">2 min ago</span>
            </div>
        </div>
        <!-- end chat message -->
    </div>

    <div class="flex items-center p-3 space-x-2 border-t">
        <div class="w-full">
            <input x-ref="chatbox_input" @keyup.enter="sendMessage" class="w-full border border-gray-300 rounded-md" type="text" value="" placeholder="Send message..." autofocus />
        </div>

        <!-- chat send action -->
        <div>
            <button @click="sendMessage" class="inline-flex p-2 rounded-full hover:bg-primary-50" type="button">
                <x-heroicon-o-paper-airplane class="w-6 h-6 rotate-90 rounded-full" />
            </button>
        </div>
        <!-- end chat send action -->
    </div>
</div> --}}


{{-- <div x-data="{ activeMenu: '{{ $this->activeMenu }}' }"
class="flex flex-col flex-grow">
<div class="px-4 py-2 border-b">
    <p class="text-xs font-medium text-gray-500">Recent Conversations</p>
</div>

<div class="flex flex-col">
    <button @click="activeMenu = activeMenu == 'drivers' ? false : 'drivers'" :class="{ 'button-active': activeMenu === 'drivers' }" class="flex items-center justify-between px-4 py-4 text-xs border-b">
        <span class="flex items-center gap-2 font-bold">
            <x-heroicon-o-support class="w-4 h-4 shrink-0" />
            Drivers
            @if($sum = $drivers->sum('sent_messages_count'))
            <span class="flex items-center justify-center w-4 h-4 ml-auto text-xs font-medium leading-none text-white rounded-full bg-primary-500">
                {{ $sum }}
            </span>
            @endif
        </span>

        <span class="flex items-center justify-center w-5 h-5 text-gray-600">
            <x-heroicon-o-chevron-down x-bind:class="{ 'rotate-180' : activeMenu === 'drivers' }" class="transition-all duration-300 origin-center" />
        </span>
    </button>
    <ul x-show="activeMenu === 'drivers'" x-cloak x-collapse class="flex flex-col h-full px-2 py-2 space-y-1 overflow-y-scroll border-b max-h-48">
        @foreach ($drivers as $driver)
        <li wire:key="user-{{ $driver->uuid }}">
            <button wire:click="getMessageable('{{ $driver->uuid }}')" class="@if($selectedMessageable?->is($driver)) bg-primary-100 @endif hover:bg-primary-100 w-full flex items-center p-2 transition rounded-lg hover:cursor-pointer">
                <div style="background-image: url('{{ getUiAvatarUrl($driver->name) }}')" class="w-8 h-8 bg-gray-200 bg-center bg-cover rounded-full dark:bg-gray-900"></div>
                <div class="ml-2 text-left">
                    <p class="text-sm font-semibold capitalize">{{ $driver->name }}</p>
                    <p class="text-xs font-medium text-gray-600">{{ $driver->phone_number }}</p>
                </div>
                @if ($driver->sent_messages_count)
                <span class="flex items-center justify-center w-4 h-4 ml-auto text-xs leading-none text-white rounded-full bg-primary-500">
                    {{ $driver->sent_messages_count }}
                </span>
                @endif
            </button>
        </li>
        @endforeach
    </ul>
</div>

<div class="flex flex-col">
    <button @click="activeMenu = activeMenu === 'users' ? false : 'users'" :class="{ 'button-active': activeMenu === 'users' }" class="flex items-center justify-between px-4 py-4 text-xs border-b">
        <span class="flex items-center gap-2 font-bold">
            <x-heroicon-o-user class="w-4 h-4 shrink-0" />
            Admins
            @if($sum = $admins->sum('sent_messages_count'))
            <span class="flex items-center justify-center w-4 h-4 ml-auto text-xs font-medium leading-none text-white rounded-full bg-primary-500">
                {{ $sum }}
            </span>
            @endif
        </span>

        <span class="flex items-center justify-center w-5 h-5 text-gray-600">
            <x-heroicon-o-chevron-down x-bind:class="{ 'rotate-180' : activeMenu === 'users' }" class="transition-all duration-300 origin-center" />
        </span>
    </button>
    <ul x-show="activeMenu === 'users'" x-cloak x-collapse class="flex flex-col h-full px-2 py-2 space-y-1 overflow-y-scroll border-b max-h-48">
        @foreach ($admins as $admin)
        <li wire:key="user-{{ $admin->uuid }}">
            <button wire:click="getMessageable('{{ $admin->uuid }}')" class="@if($selectedMessageable?->is($admin)) bg-primary-100 @endif w-full flex items-center p-2 transition rounded-lg hover:bg-primary-100 hover:cursor-pointer">
                <div style="background-image: url('{{ getUiAvatarUrl($admin->name) }}')" class="w-8 h-8 bg-gray-200 bg-center bg-cover rounded-full dark:bg-gray-900"></div>
                <div class="ml-2 text-left">
                    <p class="text-sm font-semibold capitalize">{{ $admin->name }}</p>
                    <p class="text-xs font-medium text-gray-600">{{ $admin->email }}</p>
                </div>
                @if ($admin->sent_messages_count)
                <span class="flex items-center justify-center w-4 h-4 ml-auto text-xs leading-none text-white rounded-full bg-primary-500">
                    {{ $admin->sent_messages_count }}
                </span>
                @endif
            </button>
        </li>
        @endforeach
    </ul>
</div>
</div> --}}

{{-- <input wire:model.lazy="message" placeholder="Type message..." type="text" class="w-full text-sm p-2.5 placeholder-gray-500 transition duration-75 border border-transparent border-gray-300 rounded-lg bg-gray-400/10 focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500" autofocus /> --}}
