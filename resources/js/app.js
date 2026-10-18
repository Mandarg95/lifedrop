import './bootstrap';

const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');

const openSidebar = () => {
    sidebar?.classList.remove('-translate-x-full');
    overlay?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
};

const closeSidebar = () => {
    sidebar?.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

document.querySelector('[data-sidebar-open]')?.addEventListener('click', openSidebar);
document.querySelector('[data-sidebar-close]')?.addEventListener('click', closeSidebar);
overlay?.addEventListener('click', closeSidebar);

window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        document.body.classList.remove('overflow-hidden');
        overlay?.classList.add('hidden');
        sidebar?.classList.remove('-translate-x-full');
    } else {
        sidebar?.classList.add('-translate-x-full');
    }
});

document.querySelectorAll('[data-toast]').forEach((toast, index) => {
    window.setTimeout(() => {
        toast.style.transition = 'opacity .3s, transform .3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-8px)';
        window.setTimeout(() => toast.remove(), 320);
    }, 4200 + index * 150);
});

document.querySelectorAll('[data-toast-close]').forEach((button) => {
    button.addEventListener('click', () => button.closest('[data-toast]')?.remove());
});

document.querySelectorAll('[data-confirm]').forEach((element) => {
    element.addEventListener('submit', (event) => {
        if (!window.confirm(element.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('[data-autosubmit]').forEach((element) => {
    element.addEventListener('change', () => element.form?.submit());
});

const params = new URLSearchParams(window.location.search);
const focusTarget = document.querySelector('[data-focus-filter]');
if (focusTarget && params.has('q')) {
    focusTarget.focus();
    focusTarget.setSelectionRange(focusTarget.value.length, focusTarget.value.length);
}
