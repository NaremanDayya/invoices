<!-- Include required libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    /* PDF Export Styles */
    .export-content {
        display: none;
    }

    .pdf-header {
        display: none;
    }

    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
        body * {
            visibility: hidden;
        }
        .export-content, .export-content * {
            visibility: visible;
        }
        .export-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background: white;
        }
        .pdf-header {
            display: block !important;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
    }

    .pdf-header .header-logo {
        max-height: 180px !important;
        max-width: 300px !important;
        height: auto !important;
        width: auto !important;
        object-fit: contain !important;
        margin-bottom: 15px;
    }

    .pdf-header .header-text {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
    }

    .pdf-header .report-info {
        color: #6b7280;
        font-size: 14px;
        margin-top: 10px;
    }

    .export-content table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-family: 'Arial', sans-serif;
    }

    .export-content th {
        background-color: #f2f2f2;
        font-weight: bold;
        padding: 10px;
        border: 1px solid #ddd;
        text-align: right;
    }

    .export-content td {
        padding: 8px 10px;
        border: 1px solid #ddd;
        text-align: right;
    }

    .export-content .summary-box {
        background-color: #f9f9f9;
        padding: 15px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
        border-radius: 5px;
        margin-top: 20px;
    }

    .pdf-footer {
        display: none;
    }

    @media print {
        .pdf-footer {
            display: block !important;
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #333;
            color: #666;
            font-size: 12px;
        }
    }

    /* Export Dropdown Styles */
    .export-dropdown {
        position: relative;
        display: inline-block;
    }

    .export-dropdown .dropdown-menu {
        min-width: 200px;
        padding: 10px 0;
    }

    .export-dropdown .dropdown-item {
        padding: 8px 15px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .export-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .export-dropdown .dropdown-item i {
        width: 20px;
        text-align: center;
    }
</style>

<script>
    // Common Export Functions
    function setupExportDropdown(exportBtnId, contentId, tableId, reportTitle) {
        const exportBtn = document.getElementById(exportBtnId);
        if (!exportBtn) return;

        exportBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('active');
        });

        // Setup export buttons
        document.querySelectorAll(`#${exportBtnId} + .dropdown-menu .export-pdf`).forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                exportToPDF(contentId, reportTitle);
            });
        });

        document.querySelectorAll(`#${exportBtnId} + .dropdown-menu .export-excel`).forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                exportTableToExcel(tableId, reportTitle);
            });
        });
    }

    function exportToPDF(contentId, filename) {
        const element = document.getElementById(contentId);
        if (!element) {
            alert('عنصر التصدير غير موجود');
            return;
        }

        // Store original content and show it
        const originalContent = element.innerHTML;
        element.style.display = 'block';

        // Add timestamp to filename
        const timestamp = new Date().toISOString().slice(0, 10);
        const fullFilename = `${filename}_${timestamp}.pdf`;

        const opt = {
            margin: [10, 10, 10, 10],
            filename: fullFilename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: true,
                onclone: function(clonedDoc) {
                    // Ensure RTL styling for the cloned document
                    clonedDoc.body.style.direction = 'rtl';
                    clonedDoc.body.style.textAlign = 'right';
                    clonedDoc.body.style.fontFamily = "'Segoe UI', 'Arial', 'Tahoma', sans-serif";

                    // Make sure Arabic text is properly rendered
                    const style = clonedDoc.createElement('style');
                    style.innerHTML = `
                        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');
                        * {
                            font-family: 'Tajawal', 'Segoe UI', 'Arial', sans-serif !important;
                        }
                        .arabic-text {
                            font-family: 'Tajawal', sans-serif !important;
                        }
                    `;
                    clonedDoc.head.appendChild(style);
                }
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait',
                compress: true
            },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Show loading indicator
        element.innerHTML = `
            <div style="text-align: center; padding: 50px; font-family: 'Tajawal';">
                <i class="fas fa-spinner fa-spin fa-2x" style="color: #4f46e5; margin-bottom: 20px;"></i>
                <h3 style="color: #4f46e5;">جاري تحضير ملف PDF...</h3>
                <p>يرجى الانتظار قليلاً</p>
            </div>
        `;

        html2pdf().set(opt).from(element).save().then(() => {
            // Restore original content
            element.innerHTML = originalContent;
            element.style.display = 'none';
        }).catch(err => {
            console.error('PDF export error:', err);
            alert('حدث خطأ أثناء تصدير PDF');
            element.innerHTML = originalContent;
            element.style.display = 'none';
        });
    }

    function exportTableToExcel(tableId, filename) {
        let table = document.getElementById(tableId);
        if (!table) {
            table = document.querySelector('table');
        }

        if (!table) {
            alert('لم يتم العثور على جدول للتصدير');
            return;
        }

        // Create a temporary table for export
        const tempTable = table.cloneNode(true);

        // Remove action buttons and no-print elements
        tempTable.querySelectorAll('.no-print, .table-actions, .btn, .clickable-cell, .dropdown').forEach(el => {
            el.remove();
        });

        // Remove last column (actions column) if it exists
        const actionColumns = tempTable.querySelectorAll('td:last-child, th:last-child');
        actionColumns.forEach(col => {
            if (col.tagName === 'TH' || col.tagName === 'TD') {
                col.remove();
            }
        });

        // Add timestamp to filename
        const timestamp = new Date().toISOString().slice(0, 10);
        const fullFilename = `${filename}_${timestamp}`;

        // Convert table to Excel
        const ws = XLSX.utils.table_to_sheet(tempTable);

        // Auto-size columns
        const wscols = [];
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let max_length = 0;
            for (let R = range.s.r; R <= range.e.r; ++R) {
                const cell = ws[XLSX.utils.encode_cell({c: C, r: R})];
                if (cell && cell.v) {
                    const cell_length = cell.v.toString().length;
                    if (cell_length > max_length) max_length = cell_length;
                }
            }
            wscols.push({wch: Math.min(max_length + 2, 50)});
        }
        ws['!cols'] = wscols;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, filename);

        // Save file
        XLSX.writeFile(wb, `${fullFilename}.xlsx`);
    }

    // Initialize all export dropdowns on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.export-dropdown .dropdown-menu').forEach(menu => {
                if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                    menu.classList.remove('active');
                }
            });
        });
    });
</script>
