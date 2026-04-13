# affiliates_adv

Advanced affiliate helper module package for HostBill.

## Scope

This package is prepared in `module_adv/affiliates_adv` for deployment as an `Other` module named `affiliates_adv`.

Features included:

- Admin affiliate client listing
- Admin affiliate detail page
- Client-area affiliate dashboard
- Commission plans view/change
- Voucher create/list/delete
- Commission filtering and accurate SQL-based pagination
- Localization-ready labels through module `$lang`

## Package structure

- `class.affiliates_adv.php` - main module class
- `admin/class.affiliates_adv_controller.php` - admin controller
- `user/class.affiliates_adv_controller.php` - client-area controller
- `api/class.affiliates_adv_apiroutes.php` - API route handlers
- `api/affiliates_adv_apiroutes.json` - API route manifest
- `templates/admin/*` - admin templates
- `templates/user/*` - client-area templates

## Install

Deploy this package into HostBill `Other` modules path as:

- `includes/modules/Other/affiliates_adv/`

Expected result:

- `includes/modules/Other/affiliates_adv/class.affiliates_adv.php`
- `includes/modules/Other/affiliates_adv/admin/class.affiliates_adv_controller.php`
- `includes/modules/Other/affiliates_adv/user/class.affiliates_adv_controller.php`
- `includes/modules/Other/affiliates_adv/api/class.affiliates_adv_apiroutes.php`
- `includes/modules/Other/affiliates_adv/api/affiliates_adv_apiroutes.json`
- `includes/modules/Other/affiliates_adv/templates/...`

After copying files:

1. Open HostBill admin area.
2. Go to module management / Other modules.
3. Enable `affiliates_adv`.
4. Verify admin page is available with `?cmd=affiliates_adv`.
5. Verify client area page is available with `?cmd=affiliates_adv` for logged-in clients.

## Usage

### Admin area

Open:

- `?cmd=affiliates_adv`

Available actions:

- list affiliate-capable clients
- filter by client ID, email, status
- activate affiliate account for a client
- open affiliate detail page
- manage commission plan
- create/delete vouchers
- filter commissions by:
  - `order_id`
  - `paid_status`
  - `date_from`
  - `date_to`

### Client area

Open:

- `?cmd=affiliates_adv`

Available actions:

- activate affiliate account
- review affiliate summary/stats
- choose commission plan when HostBill allows manual selection
- create/delete vouchers
- review commissions with filters

## API routes

All API requests must send a bearer token header:

- `Authorization: Bearer $token`

Base routes are defined in:

- `api/affiliates_adv_apiroutes.json`

Handlers are implemented in:

- `api/class.affiliates_adv_apiroutes.php`

Current routes:

- `GET /affiliates_adv/@client_id/info`
- `GET /affiliates_adv/@client_id/commission-plans`
- `POST /affiliates_adv/@client_id/commission-plan/@commission_id`
- `POST /affiliates_adv/@client_id/vouchers/@plan_id`
- `GET /affiliates_adv/@client_id/vouchers`
- `GET /affiliates_adv/@client_id/commissions`

## API endpoint reference

Base header for all requests:

```bash
-H "Authorization: Bearer $token"
```

### 1. Get affiliate info

- Method: `GET`
- URL: `/affiliates_adv/@client_id/info`
- Headers:
  - `Authorization: Bearer $token`
- Query params: none

Example:

```bash
curl -X GET \
  -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/info"
```

Sample response:

```json
{
  "success": true,
  "affiliate": {
    "id": 10,
    "client_id": 123,
    "status": "Active",
    "balance": "25.00",
    "currency_id": 1,
    "landingpage": "https://your-hostbill.example/landing-page",
    "referral_url": "https://your-hostbill.example/?affid=10"
  }
}
```

### 2. Get commission plans

- Method: `GET`
- URL: `/affiliates_adv/@client_id/commission-plans`
- Headers:
  - `Authorization: Bearer $token`
- Query params:
  - `voucher_enabled=1` optional

Example:

```bash
curl -X GET \
  -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/commission-plans?voucher_enabled=1"
```

Sample response:

```json
{
  "success": true,
  "commisions": [
    {
      "id": 3,
      "name": "Default plan",
      "type": "Percent",
      "rate": "10%",
      "enable_voucher": 1
    }
  ]
}
```

### 3. Set commission plan

- Method: `POST`
- URL: `/affiliates_adv/@client_id/commission-plan/@commission_id`
- Headers:
  - `Authorization: Bearer $token`
- Body: none required

Example:

```bash
curl -X POST \
  -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/commission-plan/3"
```

Sample response:

```json
{
  "success": true,
  "commission_id": 3
}
```

### 4. Create voucher

- Method: `POST`
- URL: `/affiliates_adv/@client_id/vouchers/@plan_id`
- Headers:
  - `Authorization: Bearer $token`
- Body params:
  - `code`
  - `discount`
  - `cycle`
  - `expires`
  - `max_usage`
  - `audience`

Example:

```bash
curl -X POST \
  -H "Authorization: Bearer $token" \
  -d "code=AFFWELCOME10" \
  -d "discount=10" \
  -d "cycle=once" \
  -d "expires=2026-12-31" \
  -d "max_usage=100" \
  -d "audience=new" \
  "https://your-hostbill.example/api/affiliates_adv/123/vouchers/3"
```

Sample response:

```json
{
  "success": true,
  "voucher_id": 55
}
```

### 5. Get vouchers

- Method: `GET`
- URL: `/affiliates_adv/@client_id/vouchers`
- Headers:
  - `Authorization: Bearer $token`
- Query params: none

Example:

```bash
curl -X GET \
  -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/vouchers"
```

Sample response:

```json
{
  "success": true,
  "vouchers": [
    {
      "id": 55,
      "code": "AFFWELCOME10",
      "value": "10",
      "cycle": "once",
      "max_usage": 100,
      "num_usage": 0
    }
  ]
}
```

### 6. Get commissions

- Method: `GET`
- URL: `/affiliates_adv/@client_id/commissions`
- Headers:
  - `Authorization: Bearer $token`
- Query params:
  - `order_id`
  - `paid_status=paid|unpaid`
  - `date_from=YYYY-MM-DD`
  - `date_to=YYYY-MM-DD`
  - `page`
  - `perpage`
  - `orderby`

Example:

```bash
curl -X GET \
  -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/commissions?paid_status=paid&date_from=2026-01-01&date_to=2026-12-31&page=1&perpage=20&orderby=date_created|DESC"
```

Sample response:

```json
{
  "success": true,
  "orders": [
    {
      "id": 901,
      "order_id": 4001,
      "commission": "15.00",
      "paid": 1,
      "date_created": "2026-04-10 09:30:00",
      "firstname": "John",
      "lastname": "Doe",
      "total": "150.00"
    }
  ],
  "pagination": {
    "perpage": 20,
    "totalpages": 3,
    "sorterrecords": 45,
    "sorterpage": 0
  }
}
```

### Required authentication header

Example:

```bash
curl -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/info"
```

Example with filters:

```bash
curl -H "Authorization: Bearer $token" \
  "https://your-hostbill.example/api/affiliates_adv/123/commissions?paid_status=paid&page=1&perpage=20"
```

Notes:

- `$token` is the UserApi JWT token.
- Without this header, authentication should fail before route execution.

### Commissions API filters

Supported query params for commissions:

- `order_id`
- `paid_status` = `paid|unpaid`
- `date_from` = `YYYY-MM-DD`
- `date_to` = `YYYY-MM-DD`
- `page`
- `perpage`
- `orderby` (examples: `id|DESC`, `date_created|ASC`, `commission|DESC`)

Response includes:

- `orders`
- `pagination.perpage`
- `pagination.totalpages`
- `pagination.sorterrecords`
- `pagination.sorterpage`

## Notes

- This package is authored in `module_adv` but runtime template/controller paths expect deployment under `includes/modules/Other/affiliates_adv`.
- Accurate commission pagination is implemented by filtering and counting directly in SQL before loading detailed commission rows.
- If your HostBill build injects controller helper properties during controller bootstrap, `public $affiliate;` is already declared for compatibility.

## Development notes

Reference architecture used:

- `module_adv/locations_v2`

Business logic sources reused conceptually from HostBill core:

- `includes/modules/Site/affiliates/models/class.affiliates_user_model.php`
- `includes/components/affiliate/class.affiliate.php`
