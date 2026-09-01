import './bootstrap'
import './sidebar'

import { createIcons, icons } from 'lucide'

import glintsSearchLocation from './alpine/glints-search-location.js'

// Lucide
window.lucide = {
    createIcons: (opts = {}) => createIcons({ icons, ...opts }),
    icons
}

createIcons({ icons })