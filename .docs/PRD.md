# PRD — Dynamic Product Pricing Engine for WooCommerce

**Heritage Craft & Jewels**  
**Aligned with:** `HC_Jewels_Dynamic_Pricing_Spec_v1.0.docx` (June 2, 2026)  
**Status:** Ready for development & QA

---

## Document Change Summary (PRD vs Spec v1.0)

The following differences and enhancements were introduced in **HC_Jewels_Dynamic_Pricing_Spec_v1.0** relative to the original PRD. This PRD now reflects both the spec’s business rules and the WooCommerce runtime architecture.

| Area | Original PRD | Spec v1.0 Enhancement |
|------|--------------|------------------------|
| **Product model** | Single metal per product (`gold` OR `silver`) | **Multi-material**: gold + silver + diamond on one product; unused materials = zero |
| **Gold pricing** | Flat gold rate per gram | **24K base rate** with purity-derived rate: `24K Rate × (Purity ÷ 24)` |
| **Gold purity** | Listed as optional future feature | **Required**: 14K, 18K, 22K, 24K only; reject invalid purity on save |
| **Diamond** | Listed as optional future feature | **In scope**: diamond weight (carat) × global diamond rate |
| **Final formula** | `Metal Value + Making Charge` | `Gold Cost + Silver Cost + Diamond Cost + Making Charge` |
| **Making charge weight** | Product `weight` (single field) | **`total_weight`** used for per-gram making charge (may differ from sum of material weights) |
| **Global rates** | Gold + silver per gram | Gold **(24K)** + silver + **diamond per carat** + optional **default making charge** |
| **Product overrides** | Per-product making charge only | Per-product making charge **overrides** global default making charge |
| **Computed fields** | Runtime price only | Read-only breakdown: `gold_cost`, `silver_cost`, `diamond_cost`, `making_charge_cost`, `final_price` |
| **Tax / currency** | GST/VAT listed as future | **Explicitly out of scope** in v1.0 (NPR only, no tax in formula) |
| **Rate history UI** | Optional low-priority feature | **Out of scope** in spec v1.0 (order snapshots remain for checkout audit) |
| **Customer display** | Required product breakdown | Spec v1.0: backend calculation focus; **storefront breakdown retained** as UX layer (non-authoritative) |
| **API / bulk import** | Nepal API auto-sync (5 min) | Spec: bulk CSV/API **out of scope**; **operational API sync retained** as extended requirement for live Nepal market rates |

---

## Project Overview

Build a centralized dynamic pricing engine for WooCommerce that:

- Automatically calculates product sale prices from **admin-managed material rates** (gold 24K, silver, diamond)
- Supports **multi-material products** (gold, silver, diamond in any combination)
- Derives **gold purity pricing** (14K–24K) from a single 24K base rate
- Supports making charge by **gram** or **piece**, with optional global default
- Propagates rate changes **instantly** across all products without per-product resave or `_price` bulk updates
- Recalculates prices **server-side** at display, cart, and checkout
- Prevents stale-price checkout issues via **rate versioning** and cart snapshots
- Optionally auto-syncs Nepal market rates via API (operational extension beyond spec v1.0)

### Key Objectives (from Spec v1.0)

- A single rate update propagates instantly across all affected products
- Product prices always reflect the latest admin-configured (or synced) market rates
- Making charges support per-gram and per-piece billing models simultaneously across the catalog
- Gold purity (14K–24K) is derived from the base 24K rate via a standard formula

---

## Objectives

### Business Goals

- Maintain accurate live jewellery pricing across mixed-material SKUs
- Reduce manual pricing operations
- Prevent revenue loss from stale metal/material rates
- Ensure smooth checkout during rate updates
- Support Nepal gold/silver market pricing (via manual admin and optional API sync)

### Technical Goals

- Centralized global rate store with versioning
- Runtime WooCommerce price calculation (never bulk-update `_price`)
- Immutable order snapshots for audit
- Server-authoritative pricing; client display is estimate-only

---

## Global Price Settings (Admin-Managed)

All product prices are derived from these global values. Updating any rate recalculates all dynamic products immediately (no product save required).

| Rate Parameter | Field Key | Unit |
|----------------|-----------|------|
| Gold Rate (24K) | `gold_rate_24k` | Rs. per gram |
| Silver Rate | `silver_rate` | Rs. per gram |
| Diamond Rate | `diamond_rate` | Rs. per carat |
| Default Making Charge (optional) | `default_making_charge` | Rs. per gram or per piece (fallback) |

**Legacy / runtime store keys** (implementation): `gold_rate_per_gram` maps to 24K gold rate; `silver_rate_per_gram`; sync metadata: `last_synced_at`, `rate_version`, `rate_source` (`manual` \| `api`).

> The **Default Making Charge** is a fallback. Individual products may override with their own `making_charge_type` and `making_charge_value`.

---

## Gold Purity Calculation

The admin-configured gold rate is the **24-karat (24K)** market rate per gram.

### Purity Formula

```
Applicable Gold Rate = 24K Gold Rate × (Purity ÷ 24)
```

### Supported Purities

| Purity | Multiplier | Example (24K = Rs. 15,002.56/g) |
|--------|------------|----------------------------------|
| 24K | 24 ÷ 24 = 1.0000 | Rs. 15,002.56 / g |
| 22K | 22 ÷ 24 = 0.9167 | Rs. 13,752.35 / g |
| 18K | 18 ÷ 24 = 0.7500 | Rs. 11,251.92 / g |
| 14K | 14 ÷ 24 = 0.5833 | Rs. 8,751.49 / g |

**Rule:** Only **14K, 18K, 22K, 24K** are supported. The system **must reject** product saves with any other purity value.

---

## Making Charge Models

Each dynamic product uses **one** making charge method.

| Method | Description |
|--------|-------------|
| **A — Per Gram** | `Making Charge = Total Product Weight (g) × Making Charge Rate (Rs./g)` |
| **B — Per Piece** | `Making Charge = Fixed Amount (Rs.)` — independent of weight |

**Examples**

- Per gram: `5.25 g × Rs. 600/g = Rs. 3,150.00`
- Per piece: Fixed `Rs. 2,000` (e.g. pendant, regardless of weight)

---

## Product-Level Material Configuration

Each product declares which materials it contains. Only applicable fields are filled; unused materials are **zero/empty**.

### Material Fields per Product

| Material | Configurable Fields |
|----------|---------------------|
| **Gold** | `gold_weight` (g), `gold_purity` (14K \| 18K \| 22K \| 24K) |
| **Silver** | `silver_weight` (g) |
| **Diamond** | `diamond_weight` (carat) |
| **Making** | `making_charge_type` (per_gram \| per_piece), `making_charge_value` (Rs.) |
| **Weight** | `total_weight` (g) — used for **per-gram** making charge |

### Example Product Configurations

| Field | Product A (Ring) | Product B (Pendant) | Product C (Chain) |
|-------|------------------|---------------------|-------------------|
| Gold Weight | 3.50 g | 0.25 g | 8.00 g |
| Gold Purity | 22K | 14K | 22K |
| Silver Weight | — | 5.00 g | — |
| Diamond Weight | — | 0.50 ct | — |
| Total Weight | 3.50 g | 5.25 g | 8.00 g |
| Making Charge Type | Per Gram | Per Gram | Per Piece |
| Making Charge Value | Rs. 600/g | Rs. 600/g | Rs. 2,000 |

### Legacy Single-Metal Fields (Deprecation)

For backward compatibility, existing implementations may map:

- `_metal_type` + `_metal_weight` → single gold OR silver line item  
- New products should use the **multi-material** field set above.

---

## Price Calculation Formulas

### Individual Cost Components

```
Gold Cost    = gold_weight × (24K Gold Rate × Purity ÷ 24)
Silver Cost  = silver_weight × Silver Rate
Diamond Cost = diamond_weight (ct) × Diamond Rate
```

**Making Charge**

```
If per_gram:  Making Charge = total_weight × making_charge_value
If per_piece: Making Charge = making_charge_value (fixed)
```

If `making_charge_value` is empty on the product, use global `default_making_charge` when configured.

### Final Product Price

```
Final Price = Gold Cost + Silver Cost + Diamond Cost + Making Charge
```

- Any material with **zero weight** or **zero rate** contributes **Rs. 0**
- **Rounding:** intermediate values retain full precision; **final price rounded to 2 decimal places**

### Worked Example — Pendant Set (Spec v1.0)

| Parameter | Value |
|-----------|-------|
| Gold Weight / Purity | 0.25 g / 14K |
| Silver Weight | 5.00 g |
| Diamond Weight | 0.50 ct |
| Total Weight | 5.25 g |
| Making | Per gram @ Rs. 600/g |
| Gold Rate (24K) | Rs. 15,002.56/g |
| Silver Rate | Rs. 428.66/g |
| Diamond Rate | Rs. 5,000.00/ct |

| Component | Calculation | Amount (Rs.) |
|-----------|-------------|--------------|
| Gold Cost | 0.25 × (15,002.56 × 14 ÷ 24) | 2,187.87 |
| Silver Cost | 5.00 × 428.66 | 2,143.30 |
| Diamond Cost | 0.50 × 5,000.00 | 2,500.00 |
| Making Charge | 5.25 × 600 | 3,150.00 |
| **FINAL PRICE** | Sum | **9,981.17** |

### Simple Examples (Silver-Only Legacy)

**Necklace — making by gram:** 50 g silver @ Rs. 500/g + Rs. 600/g making → Rs. 55,000  
**Baby bangle — making by piece:** 10 g @ Rs. 500/g + Rs. 1,500/piece → Rs. 6,500

---

## System Fields Reference

### Global Settings (Admin Panel)

| Field Name | Type |
|------------|------|
| `gold_rate_24k` | Decimal (Rs./g) |
| `silver_rate` | Decimal (Rs./g) |
| `diamond_rate` | Decimal (Rs./ct) |
| `default_making_charge` | Decimal (optional fallback) |
| `last_synced_at` | Timestamp (UTC) |
| `rate_version` | Integer (monotonic) |
| `rate_source` | `manual` \| `api` |

### Product Fields

| Field Name | Type / Values |
|------------|---------------|
| `gold_weight` | Decimal (g) |
| `gold_purity` | Enum: `14K` \| `18K` \| `22K` \| `24K` |
| `silver_weight` | Decimal (g) |
| `diamond_weight` | Decimal (carat) |
| `total_weight` | Decimal (g) |
| `making_charge_type` | Enum: `per_gram` \| `per_piece` |
| `making_charge_value` | Decimal (Rs.) |

### System-Calculated Fields (Read-Only)

| Field Name | Description |
|------------|-------------|
| `gold_cost` | From gold weight × effective gold rate |
| `silver_cost` | From silver weight × silver rate |
| `diamond_cost` | From diamond weight × diamond rate |
| `making_charge_cost` | From making charge type + value + total weight |
| `final_price` | Sum of all components (authoritative server value) |

---

## Expected System Behavior (QA Checklist)

| # | Behavior |
|---|----------|
| 1 | When admin saves updated Gold, Silver, or Diamond rates, final prices for all dynamic products update instantly |
| 2 | Per-gram making charge uses `total_weight × making_charge_value` |
| 3 | Per-piece making charge uses fixed `making_charge_value` regardless of weight |
| 4 | Applicable gold rate is always `current 24K rate × (purity ÷ 24)` |
| 5 | Final price reflects most recently saved/synced rates (no stale cached authoritative price) |
| 6 | Zero weight for any material → Rs. 0 for that component |
| 7 | Product save rejected if `gold_purity` is not 14K, 18K, 22K, or 24K (when gold weight > 0) |

---

## System Architecture (WooCommerce)

### Important Principle

Products must **NOT** store static final prices in `_price` / `_regular_price` for dynamic SKUs.

The system must:

- Store only **formula / material configuration** on products
- Calculate prices **dynamically at runtime** from global rates
- **Never** loop all products to rewrite prices on sync

### CRITICAL: Do Not Bulk-Update WooCommerce Product Prices

On rate sync or manual rate save, the system must **NEVER**:

- Update `_price` on all products
- Run product recalculation batch jobs
- Regenerate catalog pricing in the database

**Reason:** performance, cache staleness, race conditions, checkout inconsistency.

---

## Security Requirements

### Client-Side Pricing Must NOT Be Trusted

Frontend JavaScript may only:

- Display estimated live pricing / breakdown
- Improve UX responsiveness

Frontend must **NEVER**:

- Determine payable amount
- Send trusted totals to the server
- Store authoritative price

### Server-Side Pricing Authority

Server must recalculate during:

| Stage | Required |
|-------|----------|
| Product display | YES |
| Add to cart | YES |
| Cart refresh | YES |
| Checkout load | YES |
| Before payment | YES |
| Order creation | YES |

---

## Cart Protection Strategy

### Rate Versioning

Each cart item stores:

| Key | Purpose |
|-----|---------|
| `rate_version` | Snapshot version at add-to-cart / recalc |
| `metal_rate` / component rates | Snapshot rates used |
| `calculated_price` | Snapshot unit price |
| `snapshot_formula` | Audit JSON of components |

### Checkout Validation

Before payment, if cart `rate_version` ≠ current global `rate_version`:

- Recalculate cart totals server-side
- Refresh checkout state
- Notify customer:

> Gold/Silver rates updated. Your cart has been refreshed using the latest pricing.

### Atomic Checkout Lock

At order creation / payment:

- Latest rates applied one final time
- Line totals frozen
- Immutable order snapshot written

After order creation, order pricing **never changes**.

### Order Snapshot Requirements

| Meta Key | Purpose |
|----------|---------|
| `_order_gold_rate` | 24K gold rate at order time |
| `_order_silver_rate` | Silver rate at order time |
| `_order_diamond_rate` | Diamond rate at order time (new) |
| `_order_rate_version` | Audit trail |
| `_order_weight` / material weights | Snapshot |
| `_order_making_charge` | Snapshot |
| `_order_gold_cost`, `_order_silver_cost`, `_order_diamond_cost` | Component breakdown (new) |
| `_order_final_price` | Immutable unit/line reference |

---

## Rate Management

### Admin Manual Update

Admin can set gold (24K), silver, diamond, and optional default making charge. Changes:

- Increment `rate_version`
- Affect all dynamic products instantly
- Do not require product resave

### Automatic API Sync (Operational Extension)

- **Source:** Gold and Silver Price in Nepal API (configurable URL filter)
- **Interval:** every 5 minutes (WP-Cron)
- **Fallback:** visitor-triggered sync (throttled)
- **Manual sync:** admin button
- **Failure:** retain previous rates; log error; do not block checkout
- **Scope note:** Spec v1.0 excludes bulk CSV/API import; this cron sync is an **operational** extension for live Nepal bullion rates (gold/silver); diamond rate remains **manual** unless a future API is defined

### Peak Hour — Soft Sync

Sync updates **only** global rate options — no product loops, no queues, no checkout blocking.

---

## Cache Strategy

Use `DONOTCACHEPAGE` (or equivalent) for:

- Cart
- Checkout
- Any custom pricing AJAX endpoints

---

## Product Page UX (Storefront)

Display **informational** breakdown (server-rendered values; not client-trusted):

- Current material rates (gold 24K effective rate for purity, silver, diamond if applicable)
- Material weights and purity
- Per-component costs (gold, silver, diamond)
- Making charge breakdown
- Estimated total

**Disclaimer:**  
Prices are based on live material rates and may update during checkout.

---

## WooCommerce Hooks

| Purpose | Hooks |
|---------|--------|
| Dynamic price | `woocommerce_product_get_price`, `woocommerce_product_get_regular_price` (+ variation equivalents) |
| Cart | `woocommerce_add_cart_item_data`, `woocommerce_get_cart_item_from_session`, `woocommerce_before_calculate_totals` |
| Checkout | `woocommerce_checkout_process` |
| Order snapshot | `woocommerce_checkout_create_order_line_item`, `woocommerce_checkout_create_order` |

---

## Out of Scope (Spec v1.0)

Do not implement without a separate change request:

- Tax / VAT in final price formula
- Currency conversion (NPR only)
- Admin price history graph / rate change audit UI
- Bulk rate import via CSV
- Customer-facing **rate ticker** as a standalone feature (breakdown on product page is in scope as UX, not a market data feed)

---

## Future Enhancements (Post v1.0)

| Feature | Priority |
|---------|----------|
| Wastage % | Medium |
| Stone pricing beyond diamond carat model | Medium |
| Temporary checkout rate lock | Medium |
| Multi-currency | Low |
| Live websocket pricing | Low |
| Full rate history graph (admin) | Low |

---

## Non-Functional Requirements

| Requirement | Goal |
|-------------|------|
| Sync speed | < 2 sec |
| Checkout integrity | 100% |
| Dynamic pricing accuracy | Real-time from global rates |
| Product update jobs on sync | None |
| Scalability | High (O(1) per product view) |
| API failure resilience | Required |

---

## Architecture Principles

### DO

- Centralized global rates (gold 24K, silver, diamond)
- Multi-material runtime calculation
- Purity-derived gold rates
- Server-authoritative pricing
- Immutable order snapshots
- Lightweight global-only sync

### DO NOT

- Trust frontend totals
- Store static final prices on dynamic SKUs
- Bulk-update all products on rate change
- Rely on cached cart/checkout prices
- Accept invalid gold purity values

---

## Implementation Notes (Current Codebase)

Theme classes under `inc/classes/`:

- `class-metal-rate-store.php` — global option store
- `class-metal-price-calculator.php` — formula engine (extend for multi-material + purity)
- `class-metal-rate-sync.php` — WP-Cron API sync
- `class-wc-dynamic-metal-pricing.php` — WooCommerce hooks
- `class-metal-pricing-admin.php` — admin UI + product fields

**Migration required:** evolve from single `_metal_type` / `_metal_weight` model to spec field set (`gold_weight`, `gold_purity`, `silver_weight`, `diamond_weight`, `total_weight`, etc.).

---

## Expected Outcome

The system will provide:

- Accurate live multi-material jewellery pricing (gold purity, silver, diamond)
- Centralized rate management with instant propagation
- Secure checkout with versioned cart protection
- Immutable order audit snapshots
- Scalable WooCommerce integration without catalog price bulk writes
- Production-grade reliability for Heritage Craft & Jewels ecommerce
