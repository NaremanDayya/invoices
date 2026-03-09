@props(['tableId', 'columns', 'storageKey'])

<div class="dropdown">
    <button class="btn btn-outline-secondary rounded-xl px-3 py-2 d-flex align-items-center gap-2 dropdown-toggle"
            type="button" id="columnSelector{{ $tableId }}" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-layout-three-columns"></i>
        <span>الأعمدة</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="columnSelector{{ $tableId }}" style="min-width: 280px;">
        <h6 class="dropdown-header px-0 mb-2">اختر الأعمدة المراد عرضها</h6>
        <div class="column-selector-list" style="max-height: 400px; overflow-y: auto;">
            @foreach($columns as $key => $label)
            <div class="form-check mb-2">
                <input class="form-check-input column-toggle" 
                       type="checkbox" 
                       value="{{ $key }}" 
                       id="col_{{ $tableId }}_{{ $key }}"
                       data-table="{{ $tableId }}"
                       data-storage="{{ $storageKey }}"
                       checked>
                <label class="form-check-label" for="col_{{ $tableId }}_{{ $key }}">
                    {{ $label }}
                </label>
            </div>
            @endforeach
        </div>
        <div class="dropdown-divider my-2"></div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary flex-fill" onclick="selectAllColumns_{{ $tableId }}()">
                <i class="bi bi-check-all me-1"></i>تحديد الكل
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="deselectAllColumns_{{ $tableId }}()">
                <i class="bi bi-x-lg me-1"></i>إلغاء الكل
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const storageKey = '{{ $storageKey }}';
    const tableId = '{{ $tableId }}';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Load saved column preferences
        loadColumnPreferences(tableId, storageKey);
        
        // Listen for column toggle changes
        document.querySelectorAll(`#columnSelector${tableId}`).forEach(dropdown => {
            dropdown.closest('.dropdown-menu').querySelectorAll('.column-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleColumn(tableId, this.value, this.checked, storageKey);
                });
            });
        });
    });

    function loadColumnPreferences(tableId, storageKey) {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            const columns = JSON.parse(saved);
            
            // Apply saved preferences
            Object.keys(columns).forEach(colKey => {
                const checkbox = document.getElementById(`col_${tableId}_${colKey}`);
                if (checkbox) {
                    checkbox.checked = columns[colKey];
                    toggleColumn(tableId, colKey, columns[colKey], storageKey, false);
                }
            });
        }
    }

    function toggleColumn(tableId, columnKey, show, storageKey, save = true) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const headers = table.querySelectorAll('thead th');
        const index = Array.from(headers).findIndex(th => 
            th.getAttribute('data-column') === columnKey
        );
        
        if (index === -1) return;
        
        // Toggle header
        if (headers[index]) {
            headers[index].style.display = show ? '' : 'none';
        }
        
        // Toggle cells in all rows
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells[index]) {
                cells[index].style.display = show ? '' : 'none';
            }
        });
        
        // Save to localStorage
        if (save) {
            saveColumnPreferences(tableId, storageKey);
        }
    }

    function saveColumnPreferences(tableId, storageKey) {
        const preferences = {};
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle').forEach(checkbox => {
            preferences[checkbox.value] = checkbox.checked;
        });
        localStorage.setItem(storageKey, JSON.stringify(preferences));
    }

    // Make functions globally available
    window[`selectAllColumns_${tableId}`] = function() {
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle').forEach(checkbox => {
            checkbox.checked = true;
            toggleColumn(tableId, checkbox.value, true, storageKey);
        });
    };

    window[`deselectAllColumns_${tableId}`] = function() {
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle').forEach(checkbox => {
            checkbox.checked = false;
            toggleColumn(tableId, checkbox.value, false, storageKey);
        });
    };

    // Export functions that respect column selection
    window[`getVisibleColumns_${storageKey}`] = function() {
        const saved = localStorage.getItem(storageKey);
        if (!saved) return null;
        
        const columns = JSON.parse(saved);
        return Object.keys(columns).filter(key => columns[key]);
    };
})();
</script>
@endpush
