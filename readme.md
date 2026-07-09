# gks ERP

A complete ERP for WordPress: invoicing with myDATA/AADE compliance, inventory, CRM, orders, and WooCommerce integration.

![License: GPLv2 or later](https://img.shields.io/badge/license-GPLv2%20or%20later-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)

## Description

gks ERP is a comprehensive business management system that runs seamlessly alongside your WordPress installation. Designed for small and medium-sized businesses, it provides all the essential tools to manage your daily operations from a single, unified interface.

### Key Features

**Invoicing & Greek Tax Compliance (myDATA / AADE)**
Issue invoices, credit notes, and receipts fully compliant with Greek tax authority (AADE) requirements. Automatic submission to the myDATA platform with MARK number retrieval. Supports all Greek VAT rates and document types required by Greek legislation.

**Inventory Management**
Track stock levels in real time. Manage products, categories, units of measurement, and warehouse movements. Get low-stock alerts and full movement history per product.

**Customer & Supplier Management (CRM)**
Maintain a complete database of customers and suppliers with contact details, transaction history, and balance tracking. Quickly access outstanding balances and recent activity for any contact.

**Orders & Purchases**
Create and manage sales orders and purchase orders. Link orders to invoices, track fulfillment status, and manage supplier relationships efficiently.

**WooCommerce Integration**
Sync products, orders, and customers between gks ERP and your WooCommerce store. Automatically import WooCommerce orders into the ERP and issue invoices directly from the order list.

### Who is it for?

gks ERP is ideal for:

- Greek businesses that need myDATA / AADE compliance
- Small and medium-sized e-commerce stores using WooCommerce
- Any business that wants a simple, integrated ERP without expensive standalone software

### Free & Pro

gks ERP is available as a free (ad-supported) version and a Pro version with priority support. Both versions are installed via the companion WordPress plugin.

### gks ERP App

**gks ERP** lives next to your WordPress installation. Your team uses the same accounts they already have — no extra setup, no duplicate user management.

- Website (app): [gks-erp.com](https://www.gks-erp.com/)
- Website (company): [gks.gr](https://www.gks.gr/)

## Requirements

| Requirement | Version |
|---|---|
| WordPress | 6.0+ |
| PHP | 7.4+ |

## Features

<details>
<summary><strong>Manage</strong></summary>

- Contacts
- Contact Groups
- VIES EU VAT Number Verification
- Users
- Products
- Product Categories
- Product Brands
- Product Attributes
- Product Stock
- Pricelists - Coupons
- Shipping Methods
- Payment Methods
- Languages
- Geographic Data
- Units of Measurement
- Banks and Accounts
- Companies
- Branches
- Warehouses
- Journals
- Series
- Print Forms
- eShops
- Application Settings
- User Settings
- Customization
- My Objects

</details>

<details>
<summary><strong>CRM</strong></summary>

- Leads
- Activity
- Calendar
- Map
- Tasks
- Devices
- Campaigns
- Short URL
- SMS
- Viber Bot
- Email
- Sales Channels
- Reports

</details>

<details>
<summary><strong>Warehouse</strong></summary>

- Delivery Note
- Product Stock
- Lots - Serial Number
- Balances (Lots - Serial Numbers)
- Reports

</details>

<details>
<summary><strong>Sales</strong></summary>

- Orders
- Reports

</details>

<details>
<summary><strong>Production</strong></summary>

- Tasks
- Workstations
- Bill of Materials
- Select Workstation
- Production Line

</details>

<details>
<summary><strong>Accounting</strong></summary>

- Invoices
- Invoices Reports
- Scan QR Code
- Payments - Receipts
- Payments - Receipts Reports
- Intensive Retail
- Payment Provider

</details>

<details>
<summary><strong>Assets</strong></summary>

- Assets
- Asset Movements
- Asset Service
- Asset Inventories Counting

</details>

<details>
<summary><strong>General Features</strong></summary>

- Favorites
- List View
- Bulk SMS/Email Campaign
- Download - Links
- File Explorer

</details>

### Applications

The system consists of 3 applications:

- gks ERP App
- gks ERP App Mobile
- gks ERP App Desktop

[See all features](https://www.gks-erp.com/features/) · Business in Greece? [Check features](https://www.gks-erp.com/gr-features/)

## External Services

This plugin connects to an external service hosted at `tools.gks.gr`, operated by gks Software (the plugin developer).

### gks Software Update Service (tools.gks.gr)

**What it is and what it is used for:**
The plugin uses a remote endpoint at `tools.gks.gr` to check for the latest available version of gks ERP and retrieve the download URL and metadata (version number, file size, changelog) needed to perform the installation and update process.

**What data is sent and when:**
A request is made to `https://tools.gks.gr/gks_erp/latest.json` when the user initiates an installation or update check from the plugin's admin interface. The request contains no personal data — it includes only a random cache-busting parameter to prevent browser/server caching of the response. No user data, site data, or personally identifiable information is transmitted.

**Service provider:**
This service is provided by gks Software.
- Terms of Service: https://www.gks-erp.com/terms/
- Privacy Policy: https://www.gks-erp.com/privacy-policy/

## FAQ

**Is the app free?**
Yes, it's free with ads. To remove ads, you'll need to purchase a license.

**Is the app open source?**
Yes. The app's source code is open source.

**What about my data?**
It's your business, it's your data. If you own the server, you already have access to the data, and we do not.

## License

GPLv2 or later — see [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)