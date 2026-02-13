import './bootstrap';
import './sidebar'

import axios from 'axios';
window.axios = axios;

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar')
    const toggle = document.getElementById('sidebarToggle')
    const backdrop = document.getElementById('sidebar-backdrop')

    if (!sidebar || !toggle || !backdrop) return

    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full')
        backdrop.classList.remove('hidden')
    }

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full')
        backdrop.classList.add('hidden')
    }

    toggle.addEventListener('click', openSidebar)
    backdrop.addEventListener('click', closeSidebar)
})
