# Mahwi Import & Template Module

This update adds bulk template download and import for:

- Products
- Suppliers
- Customers
- Purchases
- Sales

## Install

After extracting the project:

```bash
composer install
php artisan migrate
php artisan optimize:clear
```

The module uses `maatwebsite/excel` for XLSX/CSV handling. If the existing `vendor` directory is being reused instead of running a fresh install, run:

```bash
composer update maatwebsite/excel --with-all-dependencies
```

## Usage

Open **Import & Templates** from the Transactions menu.

1. Download a template.
2. Remove/replace the example row.
3. Fill in the records.
4. Upload the file.
5. Review validation errors and the preview.
6. Confirm the import.

Uploads are validated before database changes are made.

## Matching rules

- Products: SKU is the shop-scoped identifier.
- Categories: matched by name and created automatically when missing during product import.
- Suppliers: matched by name within the current shop.
- Customers: matched by phone, then email, then name.
- Purchases: grouped by Purchase Reference.
- Sales: grouped by Sale Reference.

Purchase imports increase stock and create purchase stock movements. Sales decrease stock and create sale stock movements. Re-importing an existing Purchase Reference or Sale Reference first reverses the previous imported stock effect, then rebuilds that transaction.

`shop_id` is never accepted from the uploaded file; it comes from the authenticated user.
