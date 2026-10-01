(() => {
    const eye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
    const eyeOff = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m3 3 18 18M10.6 5.1 12 5c6.5 0 10 7 10 7a19 19 0 0 1-3.1 3.9M6.3 6.3A20 20 0 0 0 2 12s3.5 7 10 7a12 12 0 0 0 5.7-1.5M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
    document.querySelectorAll('input[type="password"]').forEach((input, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'password-field';
        input.before(wrapper);
        wrapper.append(input);
        if (!input.id) input.id = `password-field-${index}`;
        const button = document.createElement('button');
        const fieldName = input.name === 'password_confirmation' ? 'confirm password' : 'password';
        button.type = 'button';
        button.className = 'password-toggle';
        button.setAttribute('aria-controls', input.id);
        button.setAttribute('aria-label', `Show ${fieldName}`);
        button.setAttribute('aria-pressed', 'false');
        button.innerHTML = eye;
        wrapper.append(button);
        button.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.innerHTML = show ? eyeOff : eye;
            button.setAttribute('aria-label', `${show ? 'Hide' : 'Show'} ${fieldName}`);
            button.setAttribute('aria-pressed', String(show));
        });
    });

    document.querySelectorAll('[data-dialog-open]').forEach(button => {
        const dialog = document.getElementById(button.dataset.dialogOpen);
        if (!(dialog instanceof HTMLDialogElement)) return;
        button.addEventListener('click', () => dialog.showModal());
        dialog.querySelector('[data-dialog-close]')?.addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
                dialog.close();
            }
        });
        dialog.addEventListener('close', () => button.focus());
    });

    const navigation = document.querySelector('.navbar');
    const toggle = document.querySelector('.nav-toggle');
    toggle?.addEventListener('click', () => {
        const expanded = navigation.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(expanded));
    });
    navigation?.addEventListener('keydown', event => {
        if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
            navigation.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
        }
    });

    const appShell = document.querySelector('.app-shell');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
    
    function toggleSidebar() {
        const isOpen = appShell?.classList.toggle('is-sidebar-open');
        sidebarToggles.forEach(btn => btn.setAttribute('aria-expanded', String(isOpen)));
    }
    function closeSidebar() {
        appShell?.classList.remove('is-sidebar-open');
        sidebarToggles.forEach(btn => btn.setAttribute('aria-expanded', 'false'));
    }

    sidebarToggles.forEach(btn => btn.addEventListener('click', toggleSidebar));
    sidebarBackdrop?.addEventListener('click', closeSidebar);
    
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && appShell?.classList.contains('is-sidebar-open')) {
            closeSidebar();
        }
    });
})();
