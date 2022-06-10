require('./bootstrap');
import Alpine from 'alpinejs';
import Tooltip from "@ryangjchandler/alpine-tooltip";
import mask from '@alpinejs/mask'

Alpine.plugin(mask)
Alpine.plugin(Tooltip);

window.Alpine = Alpine;

Alpine.start();