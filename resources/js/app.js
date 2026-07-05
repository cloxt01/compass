import './bootstrap'
import './sidebar'



import { createIcons, icons } from 'lucide'

createIcons({
    icons,
})
import glintsSearchLocation from './alpine/glints-search-location.js'
Alpine.data('glintsSearchLocation', glintsSearchLocation)

