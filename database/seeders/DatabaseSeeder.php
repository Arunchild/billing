<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Staff;
use App\Models\Unit;
use App\Models\Category;
use App\Models\TaxRate;
use App\Models\ProductGroup;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use App\Models\SaleReturn;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Transaction;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Disable FK constraints to flush everything cleanly
        Schema::disableForeignKeyConstraints();

        // List of tables to truncate
        $tables = [
            'users', 'staff', 'units', 'categories', 'tax_rates', 'product_groups',
            'customers', 'suppliers', 'products', 'accounts', 'expenses',
            'invoices', 'invoice_items', 'quotations', 'quotation_items',
            'sale_returns', 'sale_return_items', 'purchases', 'purchase_items',
            'purchase_orders', 'purchase_order_items', 'purchase_returns', 'purchase_return_items',
            'credit_notes', 'debit_notes', 'transactions', 'settings'
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // 2. Seed Product Groups (50 rows)
        $defaultGroups = [
            'Medicines', 'Medical Devices', 'Surgical', 
            'OTC / General', 'Nutraceuticals', 'Ayurvedic', 
            'Cosmetics', 'Diagnostics'
        ];
        foreach ($defaultGroups as $name) {
            ProductGroup::create(['name' => $name]);
        }
        for ($i = count($defaultGroups) + 1; $i <= 50; $i++) {
            ProductGroup::create(['name' => 'Group ' . $i]);
        }

        // 3. Seed Units (50 rows)
        $defaultUnits = ['Pcs', 'Box', 'Strip', 'Kg', 'Ltr', 'Mtr', 'Pack', 'Unit', 'Set', 'Bottle'];
        foreach ($defaultUnits as $index => $u) {
            Unit::create(['name' => $u, 'short_name' => $u]);
        }
        for ($i = count($defaultUnits) + 1; $i <= 50; $i++) {
            Unit::create(['name' => 'Unit ' . $i, 'short_name' => 'U' . $i]);
        }

        // 4. Seed Categories (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            Category::create([
                'name' => 'Category ' . $i,
                'description' => 'Description for Category ' . $i
            ]);
        }

        // 5. Seed Tax Rates (50 rows)
        $defaultTaxes = [
            ['name' => 'GST 5%', 'rate' => 5.00],
            ['name' => 'GST 12%', 'rate' => 12.00],
            ['name' => 'GST 18%', 'rate' => 18.00],
            ['name' => 'GST 28%', 'rate' => 28.00],
            ['name' => 'Exempt', 'rate' => 0.00]
        ];
        foreach ($defaultTaxes as $t) {
            TaxRate::create($t);
        }
        for ($i = count($defaultTaxes) + 1; $i <= 50; $i++) {
            TaxRate::create([
                'name' => 'Tax ' . $i,
                'rate' => 5.00 + ($i * 0.5)
            ]);
        }

        // 6. Seed Users & Staff (50 rows)
        // Admin user is required for logging in
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin',
            'password' => bcrypt('password'),
        ]);

        Staff::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'user_id' => $adminUser->id,
            'permissions' => ['dashboard', 'invoice', 'sale_return', 'quotation', 'purchase_bill', 'purchase_return', 'purchase_order', 'debit_note', 'credit_note', 'supplier', 'inventory', 'accounts', 'expense', 'customer', 'reports', 'staff', 'tools', 'master', 'settings'],
        ]);

        for ($i = 2; $i <= 50; $i++) {
            $user = User::create([
                'name' => 'Staff Member ' . ($i - 1),
                'email' => 'staff' . ($i - 1),
                'password' => bcrypt('password'),
            ]);

            Staff::create([
                'name' => 'Staff Member ' . ($i - 1),
                'email' => 'staff' . ($i - 1) . '@example.com',
                'role' => 'staff',
                'user_id' => $user->id,
                'permissions' => ['dashboard', 'invoice', 'customer'],
            ]);
        }

        // 7. Seed Customers (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            Customer::create([
                'name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@example.com',
                'phone' => '9887766' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => $i . ', Main Road, City Area, Bangalore',
                'gst_number' => '29AAAAA' . str_pad($i, 4, '0', STR_PAD_LEFT) . 'A1Z' . ($i % 10),
                'reg_no' => str_pad($i, 6, '0', STR_PAD_LEFT) . '/' . date('Y'),
                'age' => 20 + ($i % 60),
                'gender' => ($i % 2 == 0) ? 'M' : 'F',
                'city' => 'Bangalore',
                'barcode' => '82700' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'date_of_birth' => now()->subYears(20 + ($i % 60))->toDateString(),
                'pincode' => '56000' . ($i % 10),
            ]);
        }

        // 8. Seed Suppliers (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            Supplier::create([
                'name' => 'Supplier ' . $i,
                'email' => 'supplier' . $i . '@example.com',
                'phone' => '9123456' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => 'Supplier Address ' . $i,
                'gstin' => '33BBBBB' . str_pad($i, 4, '0', STR_PAD_LEFT) . 'B1Z' . ($i % 10),
            ]);
        }

        // 9. Seed Products (50+ rows)
        // First run the MedicalProductsSeeder to get realistic medical products
        $this->call(MedicalProductsSeeder::class);

        // MedicalProductsSeeder seeds 34 products. We generate 20 more to exceed 50.
        for ($i = 35; $i <= 55; $i++) {
            Product::create([
                'name' => 'Medical Product ' . $i,
                'description' => 'Detailed description for Medical Product ' . $i,
                'hsn_code' => '3004',
                'price' => 10.00 * $i,
                'stock' => 100 + $i,
                'group' => 'Medicines',
                'brand' => 'Brand ' . ($i % 5 + 1),
                'item_code' => 'MED-0' . $i,
                'print_name' => 'Product ' . $i,
                'purchase_price' => 8.00 * $i,
                'sale_price' => 10.00 * $i,
                'mrp' => 12.00 * $i,
                'unit' => 'Strip',
                'opening_stock' => 100 + $i,
                'current_stock' => 100 + $i,
                'low_level_limit' => 15,
                'cgst_rate' => 6.00,
                'sgst_rate' => 6.00,
                'igst_rate' => 12.00,
                'enable_tracking' => true,
            ]);
        }

        // 10. Seed Accounts (50 rows)
        $accountNames = ['Cash Account', 'HDFC Bank', 'SBI Bank', 'ICICI Bank', 'Petty Cash'];
        foreach ($accountNames as $index => $name) {
            Account::create([
                'name' => $name,
                'type' => ($index == 0 || $index == 4) ? 'cash' : 'bank',
                'account_number' => ($index == 0 || $index == 4) ? null : '123456789' . $index,
                'bank_name' => ($index == 0 || $index == 4) ? null : $name,
                'ifsc_code' => ($index == 0 || $index == 4) ? null : 'IFSC000' . $index,
                'opening_balance' => 50000.00,
                'current_balance' => 50000.00,
                'is_active' => true,
            ]);
        }
        for ($i = 6; $i <= 50; $i++) {
            Account::create([
                'name' => 'Bank Account ' . $i,
                'type' => 'bank',
                'account_number' => '987654321' . $i,
                'bank_name' => 'Bank of India ' . $i,
                'ifsc_code' => 'BOI0000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'opening_balance' => 10000.00 + ($i * 100),
                'current_balance' => 10000.00 + ($i * 100),
                'is_active' => true,
            ]);
        }

        // 11. Seed Expenses (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            Expense::create([
                'name' => 'Office Expense ' . $i,
                'amount' => 50.00 * $i,
                'date' => now()->subDays($i)->toDateString(),
                'description' => 'Description for office expense ' . $i
            ]);
        }

        // Get all products, customers, and suppliers for references
        $products = Product::all();
        $customers = Customer::all();
        $suppliers = Supplier::all();

        // Helper to get random items total calculations
        $getRandomItems = function($productCount = 2) use ($products) {
            $items = [];
            $subTotal = 0;
            $taxTotal = 0;
            $total = 0;
            $selectedProducts = $products->random($productCount);
            
            foreach ($selectedProducts as $p) {
                $qty = rand(1, 5);
                $price = $p->sale_price ?: 10.00;
                $taxRate = $p->cgst_rate + $p->sgst_rate;
                $itemSubTotal = $qty * $price;
                $itemTax = $itemSubTotal * ($taxRate / 100.00);
                $itemTotal = $itemSubTotal + $itemTax;

                $items[] = [
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'quantity' => $qty,
                    'price' => $price,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTax,
                    'total' => $itemTotal,
                    'hsn_sac_code' => $p->hsn_sac_code ?: '3004',
                ];

                $subTotal += $itemSubTotal;
                $taxTotal += $itemTax;
                $total += $itemTotal;
            }

            return [
                'items' => $items,
                'sub_total' => $subTotal,
                'tax_total' => $taxTotal,
                'total' => $total
            ];
        };

        // 12. Seed Invoices & Invoice Items (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $calc = $getRandomItems(rand(1, 3));

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'invoice_date' => now()->subDays($i)->toDateString(),
                'due_date' => now()->subDays($i)->addDays(15)->toDateString(),
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount' => 0.00,
                'total' => $calc['total'],
                'status' => ($i % 3 == 0) ? 'unpaid' : 'paid',
                'type' => 'gst',
            ]);

            foreach ($calc['items'] as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                    'item_code' => $item['hsn_sac_code'],
                ]);
            }
        }

        // 13. Seed Quotations & Quotation Items (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $calc = $getRandomItems(rand(1, 3));

            $quotation = Quotation::create([
                'customer_id' => $customer->id,
                'quotation_number' => 'QTN-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'quotation_date' => now()->subDays($i)->toDateString(),
                'valid_until' => now()->subDays($i)->addDays(30)->toDateString(),
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount' => 0.00,
                'total' => $calc['total'],
                'status' => ($i % 3 == 0) ? 'pending' : 'accepted',
                'type' => 'gst',
            ]);

            foreach ($calc['items'] as $item) {
                DB::table('quotation_items')->insert([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                    'item_code' => $item['hsn_sac_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 14. Seed Sale Returns & Sale Return Items (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $calc = $getRandomItems(rand(1, 2));

            $saleReturn = SaleReturn::create([
                'customer_id' => $customer->id,
                'return_number' => 'SRN-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'return_date' => now()->subDays($i)->toDateString(),
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount' => 0.00,
                'total' => $calc['total'],
                'status' => ($i % 2 == 0) ? 'pending' : 'approved',
                'type' => 'gst',
            ]);

            foreach ($calc['items'] as $item) {
                DB::table('sale_return_items')->insert([
                    'sale_return_id' => $saleReturn->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                    'item_code' => $item['hsn_sac_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Helper to get random purchase items total calculations
        $getRandomPurchaseItems = function($productCount = 2) use ($products) {
            $items = [];
            $subTotal = 0;
            $taxTotal = 0;
            $total = 0;
            $selectedProducts = $products->random($productCount);
            
            foreach ($selectedProducts as $p) {
                $qty = rand(5, 20);
                $price = $p->purchase_price ?: 8.00;
                $taxRate = $p->cgst_rate + $p->sgst_rate;
                $itemSubTotal = $qty * $price;
                $itemTax = $itemSubTotal * ($taxRate / 100.00);
                $itemTotal = $itemSubTotal + $itemTax;

                $items[] = [
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'quantity' => $qty,
                    'price' => $price,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTax,
                    'total' => $itemTotal,
                    'hsn_sac_code' => $p->hsn_sac_code ?: '3004',
                ];

                $subTotal += $itemSubTotal;
                $taxTotal += $itemTax;
                $total += $itemTotal;
            }

            return [
                'items' => $items,
                'sub_total' => $subTotal,
                'tax_total' => $taxTotal,
                'total' => $total
            ];
        };

        // 15. Seed Purchases & Purchase Items (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers->random();
            $calc = $getRandomPurchaseItems(rand(1, 3));

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'purchase_number' => 'PUR-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'purchase_date' => now()->subDays($i)->toDateString(),
                'due_date' => now()->subDays($i)->addDays(30)->toDateString(),
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount' => 0.00,
                'total' => $calc['total'],
                'paid_amount' => ($i % 3 == 0) ? 0.00 : $calc['total'],
                'status' => ($i % 3 == 0) ? 'pending' : 'received',
                'notes' => 'Bulk medicine purchase ' . $i,
            ]);

            foreach ($calc['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                ]);
            }
        }

        // 16. Seed Purchase Orders & Purchase Order Items (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers->random();
            $calc = $getRandomPurchaseItems(rand(1, 3));

            $po = PurchaseOrder::create([
                'supplier_id' => $supplier->id,
                'po_number' => 'PO-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'po_date' => now()->subDays($i)->toDateString(),
                'delivery_date' => now()->subDays($i)->addDays(7)->toDateString(),
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'total' => $calc['total'],
                'status' => ($i % 2 == 0) ? 'sent' : 'confirmed',
                'notes' => 'Regular stock supply ' . $i,
            ]);

            foreach ($calc['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                ]);
            }
        }

        // 17. Seed Purchase Returns & Purchase Return Items (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers->random();
            $calc = $getRandomPurchaseItems(rand(1, 2));

            $pr = PurchaseReturn::create([
                'supplier_id' => $supplier->id,
                'purchase_id' => rand(1, 50),
                'return_number' => 'PR-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'return_date' => now()->subDays($i)->toDateString(),
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount' => 0.00,
                'total' => $calc['total'],
                'status' => ($i % 2 == 0) ? 'pending' : 'approved',
                'notes' => 'Damaged products returned ' . $i,
            ]);

            foreach ($calc['items'] as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $pr->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                ]);
            }
        }

        // 18. Seed Credit Notes (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers->random();
            CreditNote::create([
                'supplier_id' => $supplier->id,
                'note_number' => 'CN-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'note_date' => now()->subDays($i)->toDateString(),
                'amount' => 100.00 * $i,
                'reason' => 'Defective goods or adjustments ' . $i,
            ]);
        }

        // 19. Seed Debit Notes (50 rows)
        for ($i = 1; $i <= 50; $i++) {
            $supplier = $suppliers->random();
            DebitNote::create([
                'supplier_id' => $supplier->id,
                'note_number' => 'DN-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'note_date' => now()->subDays($i)->toDateString(),
                'amount' => 80.00 * $i,
                'reason' => 'Pricing correction or return adjustment ' . $i,
            ]);
        }

        // 20. Seed Transactions (50 rows)
        $accounts = Account::all();
        for ($i = 1; $i <= 50; $i++) {
            $account = $accounts->random();
            Transaction::create([
                'account_id' => $account->id,
                'type' => ($i % 2 == 0) ? 'credit' : 'debit',
                'amount' => 150.00 * $i,
                'transaction_date' => now()->subDays($i)->toDateString(),
                'reference_number' => 'TX-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'category' => ($i % 2 == 0) ? 'Invoice Payment' : 'Expense',
                'description' => 'General business transaction ' . $i,
                'payment_method' => ['cash', 'card', 'upi', 'cheque'][$i % 4],
                'related_invoice_id' => ($i % 2 == 0) ? rand(1, 50) : null,
                'related_expense_id' => ($i % 2 != 0) ? rand(1, 50) : null,
            ]);
        }

        // 21. Seed Settings (50 rows)
        $defaultSettings = [
            'company_name' => 'Biofix Healthcare Pvt. Ltd.',
            'company_email' => 'info@biofixhealthcare.com',
            'company_phone' => '9442384497',
            'company_address' => "27/18A,\nSathia Complex, Sinclair Street, Marthandam",
            'company_gstin' => '33AANCB5605Q1Z9',
            'company_pan' => 'AANCB5605Q',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'invoice_prefix' => 'INV-',
            'quotation_prefix' => 'QTN-',
        ];

        foreach ($defaultSettings as $key => $val) {
            Setting::create(['key' => $key, 'value' => $val]);
        }

        for ($i = count($defaultSettings) + 1; $i <= 50; $i++) {
            Setting::create([
                'key' => 'custom_setting_key_' . $i,
                'value' => 'custom_setting_value_' . $i,
            ]);
        }
    }
}
