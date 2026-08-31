import './bootstrap'
import './sidebar'

import Alpine from 'alpinejs'
import intersect from '@alpinejs/intersect'

import { createIcons, icons } from 'lucide'

// Alpine
window.Alpine = Alpine
Alpine.plugin(intersect)

// Alpine components
import glintsSearchLocation from './alpine/glints-search-location.js'

Alpine.data('glintsSearchLocation', glintsSearchLocation)

// Start Alpine
Alpine.start()

// Lucide
window.lucide = {
    createIcons: (opts = {}) => createIcons({ icons, ...opts }),
    icons
}

createIcons({ icons })