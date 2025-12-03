// client-chat-search.js
document.addEventListener("DOMContentLoaded", function () {
    // Common search functionality for both modal and chat list
    function normalizeSearchTerm(term) {
        if (!term) return '';
        return term.toString()
            .toLowerCase()
            .normalize('NFD') // Normalize to decomposed form
            .replace(/[\u064B-\u065F]/g, '') // Remove Arabic diacritics
            .replace(/[أإآ]/g, 'ا') // Normalize Arabic alef variants
            .replace(/ة/g, 'ه') // Normalize ta marbuta
            .trim();
    }

    // Client Selection Modal Search
    const clientSelectionModal = document.getElementById("clientSelectionModal");
    const clientSearchInput = document.getElementById("clientSearch");
    const clientList = document.getElementById("clientList");
    let clientsData = [];

    // Chat List Search
    const chatSearchInput = document.getElementById("chats-search-field");
    const conversationsList = document.getElementById("conversationsList");

    // Initialize both search functionalities
    if (clientSelectionModal) {
        initClientModalSearch();
    }

    if (conversationsList) {
        initChatListSearch();
    }

    function initClientModalSearch() {
        // Fetch clients data
        fetchClients();

        // Setup search input with debounce
        let searchTimeout;
        clientSearchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterClients(e.target.value);
            }, 300);
        });
    }

    function initChatListSearch() {
        if (!chatSearchInput || !conversationsList) return;

        const chatItems = Array.from(conversationsList.querySelectorAll('li[data-name]'));

        chatSearchInput.addEventListener('input', (e) => {
            const searchTerm = normalizeSearchTerm(e.target.value);

            chatItems.forEach(item => {
                const name = normalizeSearchTerm(item.dataset.name);
                const company = normalizeSearchTerm(item.dataset.company);
                const matches = !searchTerm || name.includes(searchTerm) || company.includes(searchTerm);

                item.classList.toggle('hidden', !matches);
            });
        });
    }

    async function fetchClients() {
        try {
            const response = await fetch('/chat-clients');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            clientsData = await response.json();
            renderClients(clientsData);
        } catch (error) {
            console.error('Error fetching clients:', error);
            showError("فشل في تحميل العملاء");
        }
    }

    function filterClients(query) {
        if (!query) {
            renderClients(clientsData);
            return;
        }

        const normalizedQuery = normalizeSearchTerm(query);
        const filtered = clientsData.filter(client => {
            const companyMatch = normalizeSearchTerm(client.company_name).includes(normalizedQuery);
            const repMatch = normalizeSearchTerm(client.sales_rep_name).includes(normalizedQuery);
            return companyMatch || repMatch;
        });

        renderClients(filtered);
    }

    function renderClients(clients) {
        if (!clientList) return;

        clientList.innerHTML = '';

        if (clients.length === 0) {
            const noResultsMsg = clientSearchInput?.value
                ? 'لا يوجد عملاء مطابقين'
                : 'لا يوجد عملاء';
            clientList.innerHTML = `<li class="px-3 py-4 text-center text-gray-500">${noResultsMsg}</li>`;
            return;
        }

        clients.forEach(client => {
            const li = document.createElement('li');
            li.className = 'p-3 hover:bg-gray-100 cursor-pointer flex items-center';

            // Avatar/Initial
            const avatarInitial = client.company_name?.charAt(0).toUpperCase() || '?';
            const avatarHtml = `<div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-purple-600 font-medium mr-3">${avatarInitial}</div>`;

            // Client info
            const companyName = client.company_name || 'Unknown Company';
            const salesRepName = client.sales_rep_name || 'غير محدد';

            li.innerHTML = `
                ${avatarHtml}
                <div>
                    <div class="font-medium">${companyName}</div>
                    <div class="text-sm text-gray-500">${salesRepName}</div>
                </div>
            `;

            li.addEventListener('click', () => {
                window.location.href = `/client/${client.id}/message`;
            });

            clientList.appendChild(li);
        });
    }

    function showError(message) {
        if (clientList) {
            clientList.innerHTML = `
                <li class="px-3 py-4 text-center text-red-500">
                    ${message}
                </li>
            `;
        }
    }

    // Modal open/close functionality (keep your existing code)
    const newChatBtn = document.getElementById("newChatBtn");
    const closeClientModal = document.getElementById("closeClientModal");

    if (newChatBtn) {
        newChatBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            openModal();
        });
    }

    if (closeClientModal) {
        closeClientModal.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeModal();
        });
    }

    function openModal() {
        if (clientSelectionModal) {
            clientSelectionModal.classList.remove("hidden");
            document.body.style.overflow = "hidden";
            clientSearchInput.value = "";
            clientSearchInput.focus();
            fetchClients();
        }
    }

    function closeModal() {
        if (clientSelectionModal) {
            clientSelectionModal.classList.add("hidden");
            document.body.style.overflow = "";
        }
    }
});
