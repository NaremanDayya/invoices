# Database Seeders Documentation

## Overview
This directory contains comprehensive database seeders that populate the database with realistic test data covering all possible scenarios for the invoicing system.

## Seeders Included

### 1. ClientsTableSeeder
Creates 10 diverse clients with various characteristics:
- Clients with multiple invoices
- Clients with overdue payments
- Clients with credit notes
- Clients with partial payments
- Clients with cancelled invoices
- Clients with fully paid invoices
- Clients with mixed payment statuses

**Fields populated:**
- Name, email, phone, address
- Default payment day
- Grace period days

### 2. ServicesTableSeeder
Creates 8 different service types:
- Human Resources (خدمات الموارد البشرية)
- Security Services (خدمات الأمن والحراسة)
- Cleaning & Maintenance (خدمات النظافة والصيانة)
- Management Consulting (الاستشارات الإدارية)
- IT Services (خدمات تقنية المعلومات)
- Training & Development (التدريب والتطوير)
- Construction Services (خدمات المقاولات)
- Logistics & Transportation (خدمات النقل واللوجستيات)

### 3. InvoicesTableSeeder
Creates 20+ invoices with comprehensive scenarios:

**Payment Statuses:**
- ✅ Fully paid invoices
- ⏳ Pending invoices
- ⚠️ Overdue invoices
- 🔴 Late payment invoices
- ❌ Cancelled invoices
- 💰 Partially paid invoices

**Invoice Variations:**
- Small invoices (< 20,000 SAR)
- Medium invoices (20,000 - 100,000 SAR)
- Large invoices (> 100,000 SAR)
- Invoices with different workforce compositions
- Invoices with various tax rates
- Recent and historical invoices

**Fields populated:**
- Invoice number, dates (generation, due, payment)
- Client and service associations
- Workforce details (workers, supervisors, managers, users)
- Work days and daily rates
- Financial calculations (base price, tax, total)
- Payment tracking (paid amount, status)
- Credit note tracking
- Cancellation details (if applicable)

### 4. PaymentsTableSeeder
Creates payment records with multiple scenarios:

**Payment Types:**
- 💵 Full single payments
- 📊 Multiple partial payments (2-3 installments)
- ⏰ Pending payments
- 🔄 Various payment methods (bank transfer, cash, check, credit card, online)

**Features:**
- Realistic payment dates
- Reference numbers
- Payment descriptions
- Status tracking (completed, pending, cancelled)

### 5. CreditNotesTableSeeder
Creates credit notes with diverse scenarios:

**Credit Note Types:**
- 📝 Single credit notes
- 📋 Multiple credit notes on same invoice
- 💰 Large credit notes (> 20,000 SAR)
- 🔢 Small credit notes (< 5,000 SAR)
- ❌ Inactive/cancelled credit notes
- 🆕 Recent credit notes

**Reasons Include:**
- Workforce adjustments
- Contractual discounts
- Service delays
- Quality issues
- Quantity discounts
- Customer complaints
- Contract settlements
- Loyalty discounts

## Usage

### Running All Seeders
```bash
php artisan db:seed
```

This will run all seeders in the correct order:
1. ClientsTableSeeder
2. ServicesTableSeeder
3. InvoicesTableSeeder
4. PaymentsTableSeeder
5. CreditNotesTableSeeder

### Running Individual Seeders
```bash
php artisan db:seed --class=ClientsTableSeeder
php artisan db:seed --class=ServicesTableSeeder
php artisan db:seed --class=InvoicesTableSeeder
php artisan db:seed --class=PaymentsTableSeeder
php artisan db:seed --class=CreditNotesTableSeeder
```

### Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

**⚠️ Warning:** This will drop all tables and recreate them with fresh data.

## Data Statistics

After running all seeders, you will have:
- **10 Clients** with various profiles
- **8 Services** covering different business types
- **20+ Invoices** with all possible statuses and scenarios
- **30+ Payments** including full, partial, and pending payments
- **15+ Credit Notes** with various amounts and reasons

## Testing Scenarios Covered

### Invoice Scenarios
✅ Fully paid invoices  
✅ Pending invoices awaiting payment  
✅ Overdue invoices (past due date)  
✅ Late invoices (within grace period)  
✅ Cancelled invoices  
✅ Invoices with credit notes  
✅ Invoices with partial payments  
✅ Invoices with multiple payments  
✅ Large enterprise invoices  
✅ Small business invoices  

### Payment Scenarios
✅ Single full payment  
✅ Multiple partial payments  
✅ Pending payments  
✅ Various payment methods  
✅ Payments with reference numbers  
✅ Recent and historical payments  

### Credit Note Scenarios
✅ Single credit note per invoice  
✅ Multiple credit notes per invoice  
✅ Large credit amounts  
✅ Small credit amounts  
✅ Active credit notes  
✅ Inactive/cancelled credit notes  
✅ Various credit reasons  
✅ Recent credit notes  

### Client Scenarios
✅ Clients with multiple invoices  
✅ Clients with overdue payments  
✅ Clients with perfect payment history  
✅ Clients with mixed payment statuses  
✅ Clients with credit notes  
✅ Clients with cancelled invoices  

## Relationships Tested

- ✅ Client → Invoices (One-to-Many)
- ✅ Service → Invoices (One-to-Many)
- ✅ Invoice → Payments (One-to-Many)
- ✅ Invoice → Credit Notes (One-to-Many)
- ✅ Payment → Invoice (Many-to-One)
- ✅ Credit Note → Invoice (Many-to-One)

## Notes

1. **Order Matters**: Seeders must be run in the specified order due to foreign key constraints.
2. **Realistic Data**: All data uses realistic Saudi Arabian business scenarios with Arabic names and addresses.
3. **Financial Accuracy**: All calculations are accurate including tax (15% VAT), totals, and credit notes.
4. **Date Ranges**: Invoices span from 4 months ago to present day for realistic reporting.
5. **Status Distribution**: Balanced distribution of payment statuses for comprehensive testing.

## Troubleshooting

### Error: Foreign key constraint fails
**Solution**: Make sure to run seeders in order or use `php artisan migrate:fresh --seed`

### Error: Duplicate entry
**Solution**: Clear the database first with `php artisan migrate:fresh` then seed

### Error: Class not found
**Solution**: Run `composer dump-autoload` to regenerate autoload files

## Customization

To modify the number of records or scenarios:
1. Edit the respective seeder file
2. Adjust the loops or data arrays
3. Run the seeder again

Example:
```php
// In ClientsTableSeeder.php
// Change the number of clients by modifying the $clients array
```

## Support

For issues or questions about the seeders, refer to the main application documentation or contact the development team.
