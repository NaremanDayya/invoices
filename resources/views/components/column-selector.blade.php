@props(['tableId', 'columns', 'storageKey'])

<div class="dropdown">
    <button class="btn btn-outline-secondary rounded-xl px-3 py-2 d-flex align-items-center gap-2 dropdown-toggle"
            type="button" id="columnSelector{{ $tableId }}" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-layout-three-columns"></i>
        <span>الأعمدة</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="columnSelector{{ $tableId }}" style="min-width: 320px;" onclick="event.stopPropagation()">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 fw-bold">اختيار الأعمدة للعرض</h6>
            <button type="button" class="btn-close" data-bs-toggle="dropdown" aria-label="Close"></button>
        </div>
        
        <div class="search-box mb-3" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 8px 12px;">
            <i class="bi bi-search text-muted"></i>
            <input type="text" id="columnSearch_{{ $tableId }}" placeholder="بحث عن عمود..." style="border: none; background: transparent; outline: none; width: 100%; font-size: 0.85rem;">
        </div>
        
        <div class="column-selector-list" style="max-height: 350px; overflow-y: auto;">
            @foreach($columns as $key => $label)
            <div class="form-check mb-2 column-item" data-label="{{ $label }}">
                <input class="form-check-input column-toggle-temp" 
                       type="checkbox" 
                       value="{{ $key }}" 
                       id="col_{{ $tableId }}_{{ $key }}"
                       data-table="{{ $tableId }}"
                       data-storage="{{ $storageKey }}"
                       checked>
                <label class="form-check-label" for="col_{{ $tableId }}_{{ $key }}" style="cursor: pointer;">
                    {{ $label }}
                </label>
            </div>
            @endforeach
        </div>
        
        <div class="dropdown-divider my-3"></div>
        
        <div class="d-flex gap-2 mb-2">
            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="selectAllColumnstemp_{{ $tableId }}()">
                <i class="bi bi-check-all me-1"></i>تحديد الكل
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="resetColumns_{{ $tableId }}()">
                <i class="bi bi-arrow-clockwise me-1"></i>إعادة تعيين
            </button>
        </div>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-light flex-fill" data-bs-toggle="dropdown">
                إلغاء
            </button>
            <button type="button" class="btn btn-sm btn-primary flex-fill" onclick="applyColumnSelection_{{ $tableId }}()">
                <i class="bi bi-check-lg me-1"></i>تطبيق
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
        // Load saved column preferences on page load
        loadColumnPreferences(tableId, storageKey);
        
        // Setup column search
        const searchInput = document.getElementById(`columnSearch_${tableId}`);
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const items = document.querySelectorAll(`#columnSelector${tableId}`).item(0)
                    ?.closest('.dropdown').querySelectorAll('.column-item');
                
                items?.forEach(item => {
                    const label = item.getAttribute('data-label').toLowerCase();
                    item.style.display = label.includes(query) ? '' : 'none';
                });
            });
        }
        
        // When dropdown opens, sync temp checkboxes with saved state
        const dropdownElement = document.querySelector(`#columnSelector${tableId}`);
        if (dropdownElement) {
            dropdownElement.addEventListener('shown.bs.dropdown', function() {
                syncTempCheckboxes(tableId, storageKey);
            });
        }
    });

    function loadColumnPreferences(tableId, storageKey) {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            const columns = JSON.parse(saved);
            
            // Apply saved preferences to actual table
            Object.keys(columns).forEach(colKey => {
                toggleColumn(tableId, colKey, columns[colKey]);
            });
        }
    }
    
    function syncTempCheckboxes(tableId, storageKey) {
        const saved = localStorage.getItem(storageKey);
        const columns = saved ? JSON.parse(saved) : {};
        
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle-temp').forEach(checkbox => {
            // If saved preference exists, use it; otherwise default to checked
            checkbox.checked = columns[checkbox.value] !== undefined ? columns[checkbox.value] : true;
        });
    }

    function toggleColumn(tableId, columnKey, show) {
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
    }

    // Apply button - saves and applies the selection
    window[`applyColumnSelection_${tableId}`] = function() {
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        const preferences = {};
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle-temp').forEach(checkbox => {
            preferences[checkbox.value] = checkbox.checked;
            toggleColumn(tableId, checkbox.value, checkbox.checked);
        });
        
        localStorage.setItem(storageKey, JSON.stringify(preferences));
        
        // Close dropdown
        const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
        if (bsDropdown) {
            bsDropdown.hide();
        }
    };

    // Select all columns
    window[`selectAllColumnstemp_${tableId}`] = function() {
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle-temp').forEach(checkbox => {
            checkbox.checked = true;
        });
    };

    // Reset to default (all checked)
    window[`resetColumns_${tableId}`] = function() {
        const dropdown = document.querySelector(`#columnSelector${tableId}`);
        if (!dropdown) return;
        
        dropdown.closest('.dropdown').querySelectorAll('.column-toggle-temp').forEach(checkbox => {
            checkbox.checked = true;
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
