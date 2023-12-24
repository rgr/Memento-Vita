import './bootstrap';

import Tooltip from "@ryangjchandler/alpine-tooltip";
import Alpine from 'alpinejs';

Alpine.plugin(
    Tooltip.defaultProps({
        delay: 250,
        theme: 'dark',
    })
)

window.Alpine = Alpine;
Alpine.start();
