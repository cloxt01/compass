const sidebar = document.getElementById('sidebar')
const overlay = document.getElementById('overlay')
const toggle = document.getElementById('brandToggle')

if (toggle) {
    toggle.addEventListener('click', () => {
        if (!sidebar || !overlay) return

        if (window.innerWidth >= 768) {
            sidebar.classList.toggle('collapsed')
        } else {
            sidebar.classList.toggle('-translate-x-full')
            overlay.classList.toggle('hidden')
        }
    })
}

if (overlay) {
    overlay.addEventListener('click', () => {
        if (!sidebar) return
        sidebar.classList.add('-translate-x-full')
        overlay.classList.add('hidden')
    })
}
