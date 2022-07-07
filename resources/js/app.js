import './bootstrap';

import Alpine from 'alpinejs';
import Tooltip from "@ryangjchandler/alpine-tooltip";
import mask from '@alpinejs/mask';
import focus from '@alpinejs/focus';

Alpine.plugin(mask);
Alpine.plugin(Tooltip);
Alpine.plugin(focus);

window.Alpine = Alpine;

Alpine.start();