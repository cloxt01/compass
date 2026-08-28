import './bootstrap'
import './sidebar'
import intersect from '@alpinejs/intersect' // 1. Import plugin

import { createIcons, icons } from 'lucide'

// Expose lucide globally so dynamic components (Alpine x-for etc) can re-render icons
window.lucide = { createIcons: (opts = {}) => createIcons({ icons, ...opts }), icons }

createIcons({ icons })

// Alpine components: register on `alpine:init` — Livewire injects & starts Alpine.
// This ensures registration happens BEFORE Alpine.start() parses the DOM.
import glintsSearchLocation from './alpine/glints-search-location.js'

document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.plugin(intersect) // 2. Register plugin di sini
        window.Alpine.data('glintsSearchLocation', glintsSearchLocation)
    }
})