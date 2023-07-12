@php
    $darkMode = config('filament.dark_mode');
    $id = "chat-messages-panel";
@endphp

<div
    x-data="{
        isLoading: false,

        shouldCheckUniqueSelection: false,
        
        messages: @js($this->messages),

        init: function () {
          window.addEventListener('message-recieved', () => { new Audio(`{{ url('assets/message-sound.mp3') }}`).play() });

          $watch('messages', () => {
              if (! this.shouldCheckUniqueSelection) {
                  this.shouldCheckUniqueSelection = true

                  return
              }

              this.messages = [...new Set(this.messages)];

              this.shouldCheckUniqueSelection = false;
          });
        },

        markMessageAsSeen: async function (key) {
          if (this.isMessageSeen(key)) {
              return
          }

          this.messages.push(key)
          await $wire.markMessageAsSeen(key)
        },

        markAllMessageAsSeen: async function () {
            this.isLoading = true

            this.messages = (await $wire.markAllMessagesAsSeen()).map((key) => key.toString())

            this.isLoading = false
        },

        isMessageSeen: function (key) {
            return this.messages.includes(key)
        },
    }"
    wire:poll.30s
    class="relative flex items-center ml-4"
    >
    <!-- trigger button -->
    <button 
        x-tooltip.raw="Contact Support"
        @click="$dispatch('open-modal', { id: '{{ $id }}' });" 
        class="fixed bottom-0 right-0 flex justify-center w-12 h-12 p-2 mb-4 mr-4 transform rounded-full shadow-md bg-primary-800"
        >
        <span class="sr-only">
            Messages
        </span>
        @if($this->received_messages_count > 0)
            <span id="message-ping" class="absolute top-0 right-0 flex w-3 h-3">
                <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-danger-400"></span>
                <span class="relative inline-flex w-3 h-3 rounded-full bg-danger-500"></span>
            </span>
        @endif
        <x-heroicon-o-chat class="w-full h-auto text-white" />
    </button>

    <!-- Chat modal -->
    <x-filament-support::modal
        :$id
        :dark-mode="config('messages.dark_mode')"
        :close-button="false"
        slide-over
        width="md"
    >
        <nav class="absolute inset-x-0 top-0 z-10 px-4 py-4 space-y-2 bg-white border-b divide-y">
            <div class="flex items-center justify-between">
                <!-- user info -->
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8">
                        <path fill="none" d="M0 0h24v24H0z" />
                        <path d="M19.938 8H21a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1.062A8.001 8.001 0 0 1 12 23v-2a6 6 0 0 0 6-6V9A6 6 0 1 0 6 9v7H3a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h1.062a8.001 8.001 0 0 1 15.876 0zM3 10v4h1v-4H3zm17 0v4h1v-4h-1zM7.76 15.785l1.06-1.696A5.972 5.972 0 0 0 12 15a5.972 5.972 0 0 0 3.18-.911l1.06 1.696A7.963 7.963 0 0 1 12 17a7.963 7.963 0 0 1-4.24-1.215z" />
                    </svg>
                    <div class="pl-2">
                        <p class="font-semibold">{{ $issuer->name ?? tenant()->organization }}</p>
                        <p class="text-xs text-gray-600">Support Agent</p>
                    </div>
                </div>
                <!-- end user info -->

                <!-- chat box action -->
                <div class="flex items-center justify-between">
                    <a class="inline-flex p-2 rounded-full hover:bg-primary-50" href="tel:{{ $issuer->phone_number_e164 }}">
                        <x-heroicon-o-phone class="w-6 h-6" />
                    </a>

                    <x-filament-support::icon-button x-on:click="$dispatch('close-modal', { id: '{{ $id }}' });" :dark-mode="$darkMode" icon="heroicon-o-x" class="-my-2">
                        <x-slot name="label">
                            close message
                        </x-slot>
                    </x-filament-support::icon-button>
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

        <div 
            x-data="{
                seenMessages: new Set(),
                init: function () {
                    $nextTick(() => {
                        this.observeChanges();

                        $watch('seenMessages', async (seen_messages) => {
                            if (seen_messages.size >= 2) {
                                await this.markAsSeen([...seen_messages]);
                                seen_messages.clear();
                            }
                        });
                    });
                },
                getPropAfterRepaint: function (el, prop)
                {
                    if (el[prop] == 'undefined') return 0;

                    el.style.visibility = 'hidden';
                    document.body.appendChild(el);
                    let _prop = el[prop] + 0;
                    document.body.removeChild(el);
                    el.style.visibility = 'visible';
                    return _prop;
                },
                scrollToBottom: function (el) {
                    $nextTick(() => {
                        if (el) {
                            el.scrollIntoView();
                        } else {
                            $refs.scroll_pin.scrollIntoView();
                        }
                    });
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
                scrollToBottom($refs.scroll_pin);
                await $wire.markMessagesAsSeen();
            "
            x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') $dispatch('scroll-chat-to-bottom');"
            class="relative h-full py-16 space-y-2 overflow-y-auto text-sm text-gray-700 bg-gray-100 border rounded-md dark:text-gray-200">
            @foreach ($this->messages as $message)
                <div
                    data-key="@js($message->getKey())"
                    data-seen="@js($message->seen())"
                    @if($this->isOldestUnseenMessage($message)) x-ref="oldest_unseen" @endif
                    @class([
                        "inbox-message p-3 col-span-full",
                        "md:col-start-1 md:col-end-10" => $message->sender->is($driver),
                        "md:col-start-4 md:col-end-13" => $message->receiver->is($driver)
                    ])>
                    <div 
                        @class([
                            "flex items-center",
                            "flex-row" => $message->sender->is($driver),
                            "flex-row-reverse justify-start" => $message->receiver->is($driver)
                        ])>
                        @if ($message->sender->is($driver))
                            <div class="flex items-center justify-center w-8 h-8 text-xs text-white rounded-full bg-primary-800 shrink-0">You</div>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8 shrink-0">
                                <path fill="none" d="M0 0h24v24H0z" />
                                <path d="M19.938 8H21a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1.062A8.001 8.001 0 0 1 12 23v-2a6 6 0 0 0 6-6V9A6 6 0 1 0 6 9v7H3a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h1.062a8.001 8.001 0 0 1 15.876 0zM3 10v4h1v-4H3zm17 0v4h1v-4h-1zM7.76 15.785l1.06-1.696A5.972 5.972 0 0 0 12 15a5.972 5.972 0 0 0 3.18-.911l1.06 1.696A7.963 7.963 0 0 1 12 17a7.963 7.963 0 0 1-4.24-1.215z" />
                            </svg>
                        @endif

                        <div class="relative px-4 py-2 mx-2 text-sm bg-white shadow rounded-xl">
                            <p>{{ $message->body }}</p>

                            <div 
                                @class([
                                    "absolute flex items-center bottom-0 gap-1 -mb-5 text-xs text-gray-500",
                                    "left-0 ml-2"=> $message->sender->is($driver),
                                    "right-0 mr-2" => $message->receiver->is($driver)
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
            <div x-ref="scroll_pin" class="h-2 col-span-full"></div>
        </div>

        <div class="absolute inset-x-0 bottom-0 px-4 py-4 bg-white border-t">
            <form wire:submit.prevent="sendMessage" class="flex flex-row items-center w-full bg-white">
                <div class="relative flex-grow w-full">
                    {{ $this->form }}
                </div>
                <div class="ml-2">
                    <button type="submit" class="inline-flex p-2 rounded-full hover:bg-primary-50" title="send message" >
                        <x-heroicon-o-paper-airplane class="w-6 h-6 rotate-90 rounded-full" />
                    </button>
                </div>
            </form>
        </div>
    </x-filament-support::modal>
</div>