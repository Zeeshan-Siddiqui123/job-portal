(() => {
    let openDropdown = null;

    document.querySelectorAll('.custom-dropdown select').forEach((select, number) => {
        const wrapper = select.parentElement;
        const id = `custom-dropdown-${number}`;
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'custom-dropdown__trigger';
        trigger.id = `${id}-trigger`;
        trigger.setAttribute('role', 'combobox');
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.setAttribute('aria-controls', `${id}-list`);
        trigger.setAttribute('aria-required', String(select.required));
        const label = select.getAttribute('aria-label') || Array.from(select.labels || []).map(item => item.textContent.trim()).join(' ') || select.name;
        trigger.setAttribute('aria-label', label);

        const value = document.createElement('span');
        value.className = 'custom-dropdown__value';
        trigger.append(value);
        const error = document.createElement('span');
        error.id = `${id}-error`;
        error.className = 'custom-dropdown__error';
        error.setAttribute('aria-live', 'polite');
        trigger.setAttribute('aria-describedby', [select.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));

        const list = document.createElement('div');
        list.id = `${id}-list`;
        list.className = 'custom-dropdown__list';
        list.setAttribute('role', 'listbox');
        list.setAttribute('aria-label', label);
        list.hidden = true;
        document.body.append(list);

        const options = Array.from(select.options);
        const enabled = index => options[index] && !options[index].disabled && !options[index].hidden && !options[index].parentElement.disabled;
        let active = select.selectedIndex;
        let search = '';
        let searchTimer;

        const items = options.map((option, index) => {
            const item = document.createElement('div');
            item.id = `${id}-option-${index}`;
            item.className = 'custom-dropdown__option';
            item.setAttribute('role', 'option');
            item.setAttribute('aria-disabled', String(!enabled(index)));
            item.textContent = option.textContent;
            item.hidden = option.hidden;
            item.addEventListener('pointermove', () => { if (enabled(index)) highlight(index, false); });
            item.addEventListener('click', () => { if (enabled(index)) { choose(index); close(); trigger.focus(); } });
            list.append(item);
            return item;
        });

        function sync() {
            value.textContent = options[select.selectedIndex]?.textContent || 'Select an option';
            trigger.disabled = select.matches(':disabled');
            items.forEach((item, index) => item.setAttribute('aria-selected', String(index === select.selectedIndex)));
            if (select.validity.valid) {
                trigger.removeAttribute('aria-invalid');
                error.textContent = '';
            }
        }

        function highlight(index, scroll = true) {
            active = index;
            items.forEach((item, itemIndex) => item.classList.toggle('is-active', itemIndex === index));
            if (items[index]) {
                trigger.setAttribute('aria-activedescendant', items[index].id);
                if (scroll) items[index].scrollIntoView({ block: 'nearest' });
            }
        }

        function position() {
            const bounds = trigger.getBoundingClientRect();
            const below = window.innerHeight - bounds.bottom - 12;
            const above = bounds.top - 12;
            const useAbove = below < 220 && above > below;
            list.style.width = `${Math.min(Math.max(bounds.width, 160), window.innerWidth - 16)}px`;
            list.style.maxHeight = `${Math.max(0, Math.min(280, useAbove ? above : below))}px`;
            list.style.left = `${Math.max(8, Math.min(bounds.left, window.innerWidth - list.offsetWidth - 8))}px`;
            list.style.top = `${useAbove ? bounds.top - list.offsetHeight - 6 : bounds.bottom + 6}px`;
        }

        function open() {
            if (trigger.disabled) return;
            if (openDropdown) openDropdown();
            sync();
            list.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            position();
            highlight(enabled(select.selectedIndex) ? select.selectedIndex : options.findIndex((_, index) => enabled(index)));
            openDropdown = close;
        }

        function close() {
            list.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            trigger.removeAttribute('aria-activedescendant');
            search = '';
            clearTimeout(searchTimer);
            if (openDropdown === close) openDropdown = null;
        }

        function choose(index) {
            if (!enabled(index)) return;
            const changed = select.selectedIndex !== index;
            select.selectedIndex = index;
            sync();
            if (changed) {
                select.dispatchEvent(new Event('input', { bubbles: true }));
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        trigger.addEventListener('click', () => list.hidden ? open() : close());
        trigger.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                if (!list.hidden) { event.preventDefault(); close(); }
                return;
            }
            if (event.key === 'Tab') {
                if (!list.hidden) { choose(active); close(); }
                return;
            }
            if (['Enter', ' '].includes(event.key)) {
                event.preventDefault();
                if (list.hidden) open();
                else { choose(active); close(); }
                return;
            }
            if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
                event.preventDefault();
                const wasClosed = list.hidden;
                if (wasClosed) open();
                const available = options.map((_, index) => index).filter(enabled);
                let next = available.indexOf(active);
                if (event.key === 'Home') next = 0;
                else if (event.key === 'End') next = available.length - 1;
                else if (!wasClosed) next += event.key === 'ArrowDown' ? 1 : -1;
                if (available.length) highlight(available[Math.max(0, Math.min(next, available.length - 1))]);
                return;
            }
            if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                event.preventDefault();
                search += event.key.toLocaleLowerCase();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => { search = ''; }, 700);
                const match = options.findIndex((option, index) => enabled(index) && option.textContent.trim().toLocaleLowerCase().startsWith(search));
                if (match !== -1) {
                    if (list.hidden) choose(match);
                    else highlight(match);
                }
            }
        });

        document.addEventListener('pointerdown', event => {
            if (!wrapper.contains(event.target) && !list.contains(event.target)) close();
        });
        document.addEventListener('focusin', event => { if (!wrapper.contains(event.target) && !list.contains(event.target)) close(); });
        window.addEventListener('resize', () => { if (!list.hidden) position(); });
        window.addEventListener('scroll', event => { if (!list.hidden && !list.contains(event.target)) position(); }, true);
        select.addEventListener('change', sync);
        select.addEventListener('focus', () => trigger.focus());
        select.addEventListener('invalid', event => {
            event.preventDefault();
            trigger.setAttribute('aria-invalid', 'true');
            error.textContent = select.validationMessage;
            if (!select.form || select.form.querySelector(':invalid') === select) trigger.focus();
        });
        select.form?.addEventListener('reset', () => setTimeout(() => { close(); sync(); }, 0));
        Array.from(select.labels || []).forEach(item => item.addEventListener('click', event => {
            event.preventDefault();
            trigger.focus();
        }));

        wrapper.append(trigger, error);
        select.classList.add('custom-dropdown__native');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');
        sync();
    });
})();
