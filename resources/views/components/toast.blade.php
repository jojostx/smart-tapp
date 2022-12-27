@props(['hasCloseButton', 'showToast', 'type', 'timeout' => 5000])

<div x-data="toast" x-bind="dialogue" x-cloak class="toast" role="toast">
  <div class="toast-icon" x-html="getIcon"></div>

  <div x-text="message" class="ml-3 text-sm font-normal"></div>

  @if ($hasCloseButton)
  <button x-bind="closeButton" type="button" aria-label="close" class="close-button">
    <span class="sr-only">Close</span>
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
    </svg>
  </button>
  @endif

  @pushOnce('scripts:body-end')
  <script data-turbo-eval="false">
    document.addEventListener('alpine:init', () => {
      Alpine.data('toast', () => ({
        show_toast: '{{ $showToast }}',
        show_close_button: '{{ $hasCloseButton }}',
        message: '{{ $slot }}',
        timeout: '{{ $timeout }}',
        color: '{{ $type }}',
        colors: {
          default: {
            'icon': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'dialogue_classes': 'toast-default',
            'button_classes': 'close-button__default',
          },
          success: {
            'icon': `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`,
            'dialogue_classes': 'toast-success',
            'button_classes': 'close-button__success',
          },
          danger: {
            'icon': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'dialogue_classes': 'toast-danger',
            'button_classes': 'close-button__danger',
          },
          warning: {
            'icon': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            'dialogue_classes': 'toast-warning',
            'button_classes': 'close-button__warning',
          },
          info: {
            'icon': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'dialogue_classes': 'toast-info',
            'button_classes': 'close-button__info',
          },
        },

        get _timeout() {
          let result = parseInt(this.timeout);

          return (Boolean(result)) ? Math.abs(result) : 5000;
        },

        get _color() {
          return this.color || 'default';
        },

        // Data [getters] //
        getIcon() {
          return this.colors[this._color]['icon'];
        },

        getDialogueClasses() {
          return this.colors[this._color]['dialogue_classes'];
        },

        getButtonClasses() {
          return this.colors[this._color]['button_classes'];
        },

        // binders //
        closeButton: {
          ['@click']() {
            this.show_toast = false;
          },

          ['x-show']() {
            return this.show_close_button;
          },

          [':class']() {
            return this.getButtonClasses();
          },
        },

        dialogue: {
          ':class'() {
            return this.getDialogueClasses();
          },

          'x-show'() {
            return this.show_toast;
          },

          '@open-toast.window'($event) {
            if (!$event.detail.message) {
              return;
            }

            message = $event.detail.message;
            color = $event.detail.color;

            this.message = message;
            this.color = (color in this.colors) ? color : 'default';
            this.show_toast = true;

            setTimeout(() => {
              this.show_toast = false;
            }, $event.detail.timeout || this._timeout)
          },

          '@close-toast.window'() {
            this.show_toast = false;
          },
        },
      }))
    })
  </script>
  @endPushOnce
</div>