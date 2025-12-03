<!-- Client Selection Modal -->
<div id="clientSelectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto min-h-screen">
    <div class="flex items-center justify-center relative w-full h-[500px] px-4 mt-20 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div
            class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle">
            <div class="px-4 pt-5 pb-4 flex flex-col h-full bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full p-4 border-b">
                        <div class="flex items-center justify-between mb-4">
                            <h1 class="text-xl font-semibold text-gray-900">
                                اختر عميلاً
                            </h1>
                            <button type="button" class="text-gray-400 hover:text-gray-500" id="closeClientModal">
                                <span class="sr-only">Close</span>
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Search input -->
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
<input type="text" id="modalSearchInput"
    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-[#a855f7]  sm:text-sm"
    placeholder="ابحث عن العملاء...">
                        </div>

                        <!-- Client list -->
                        <div class="mt-2 max-h-60 overflow-y-auto">
                            <ul class="divide-y divide-gray-200" id="clientList">
                                <!-- Client items will be populated here -->
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
