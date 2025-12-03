document.addEventListener('DOMContentLoaded', () => {
    const searchInput      = document.getElementById('chats-search-field');
    const listContainer    = document.getElementById('conversationsList');
    const emptyState       = document.getElementById('noConvoMsg');

    if (!searchInput || !listContainer) return;

    const items = Array.from(listContainer.querySelectorAll('li[data-name]'));

    function filterList() {
        const q = searchInput.value.trim().toLowerCase();
        let visible = 0;

        items.forEach(li => {
            const name    = li.dataset.name;
            const company = li.dataset.company;

            const show = !q || name.includes(q) || company.includes(q);

            li.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        // toggle empty-state
        if (emptyState) emptyState.classList.toggle('hidden', visible !== 0);
    }

    // run once on load (helps if browser kept text in the field)
    filterList();

    // filter as user types
    searchInput.addEventListener('input', filterList);
});
