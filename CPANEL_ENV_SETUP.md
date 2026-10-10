# نصب MCP الو تعمیراتچی روی cPanel — مطابق الگوی PlayNexus

**بدون CMD، SSH، Composer و بدون نصب کانکتور جداگانه در ChatGPT.** سایت PHP 8.2 و MySQL فعلی حفظ می‌شود. MCP فقط مقاله معمولی، مقاله برند، تصویر شاخص و محتوای برند/دسته‌بندی را مدیریت می‌کند؛ **هیچ ابزاری برای شهر و استان ندارد**.

## نصب اولیه فقط یک بار

1. از فایل‌ها و دیتابیس سایت بک‌آپ بگیرید.
2. از `main` مخزن [فایل ZIP نسخه اصلی](https://github.com/sobhanCodesx/alotamirart/archive/refs/heads/main.zip) را بگیرید و **فایل‌های داخل پوشه ZIP** را با File Manager در ریشه سایت (`public_html`، کنار `index.php`) استخراج کنید. عکس‌های قبلی و پوشه‌های آپلود کاربر را حذف نکنید. `.htaccess` جدید همراه فایل‌هاست و مسیر تمیز `/api/mcp` را به PHP وصل می‌کند.
3. `public_html/.env` موجود را نگه دارید. اگر وجود ندارد، `.env.example` را در همان پوشه کپی و با نام `.env` ذخیره کنید. اطلاعات واقعی `DB_HOST`، `DB_NAME`، `DB_USERNAME`، `DB_PASSWORD`، `MCP_API_TOKEN` (همان GitHub Repository secret) و `MCP_AUTHOR_USER_ID` را وارد کنید. برای اجازه انتشار مقاله **با دستور صریح کاربر** مقدار `MCP_ALLOW_PUBLISH=1` را تنظیم کنید. `ALO_DEPLOY_ENABLED=0` برای انتشار مقاله مشکلی ندارد.
4. صفحه اصلی، صفحه مقاله و مدیریت سایت را باز کنید. آدرس `https://alotamiratchi.ir/.env` نباید محتوای فایل را نشان دهد. بازکردن `https://alotamiratchi.ir/api/mcp` در مرورگر باید **HTTP 405** (POST required) برگرداند؛ اگر 404، ریدایرکت یا صفحه عادی سایت دیدید، `.htaccess` یا فایل‌های `api/mcp` درست جایگزین نشده‌اند.
5. پس از نصب، workflow صف انتشار `AloTamiratchi MCP Content Publisher` را یک‌بار آزمایش کنید. این workflow از GitHub secret `MCP_API_TOKEN` استفاده می‌کند و از طریق **POST /api/mcp** درخواست خواندن/نوشتن می‌فرستد. تصویر شاخص از URL تاییدشده Wikimedia Commons، توسط **خود PHP هاست** دانلود و ذخیره می‌شود؛ نیازی به انتقال چند مگابایت base64 در JSON نیست.

## معماری واقعی مثل PlayNexus

- **ChatGPT ↔ GitHub**: درخواست مقاله کامل در `content-requests/*.json` ثبت می‌شود.
- **GitHub Actions ↔ MCP**: workflow مستقل `.github/workflows/mcp-content-publish.yml` فقط صف محتوایی را پردازش می‌کند؛ `api/mcp.php` داده را در جداول موجود MySQL درج/ویرایش می‌کند و تصویر شاخص را در مسیر فعلی سایت ذخیره می‌کند.
- **دیپلوی مجدد کد ممنوع در درخواست مقاله**: workflow `deploy-alo-production.yml` فقط با تغییر کدهای اجرایی سایت در `main` فعال می‌شود، نه درخواست‌های محتوا یا صف.
- انتشار مقاله با `MCP_ALLOW_PUBLISH=1` و `confirm=true` انجام می‌شود. ابزار `find_content` با اسلاگ دقیق مانع ساخت تکراری در اجرای مجدد می‌شود.
- تصاویر **داخل متن مقاله** آپلود نمی‌شوند؛ فقط تصویر شاخص/برند مجاز است.

## وضعیت اتصال

اتصال خواندن جداول فعلی با مسیر قبلی `/api/mcp.php` قبلاً تست شده بود؛ اما درخواست‌های مکرر POST به مسیر قبلی، صفحه 403 محافظ **BitNinja** برمی‌گرداندند. مسیر تمیز جدید با الگوی PlayNexus ساخته شده است، ولی **تا زمانی که ZIP جدید روی هاست نصب و از همان هاست تست نشده، نمی‌توان موفقیت انتشار را تضمین کرد**. اگر `/api/mcp` هم 403 BitNinja برگرداند، از پشتیبانی هاست بخواهید فقط علت مسدودشدن این API احراز هویت‌شده را بررسی کنند؛ محافظت کل سایت را غیرفعال نکنید.

توجه: **GitHub Actions دیپلوی خودکار کد فعلاً پیکربندی کامل ندارد** زیرا Secret `ALO_DEPLOY_URL` در GitHub خالی است. این مشکل به workflow انتشار مقاله مرتبط نیست. آپلود دستی یک‌باره ZIP از `main` برای نصب نسخه جدید کافی است. در ادامه برای انتشار مقاله دیگر نیازی به cPanel نیست.

## نمونه .env (مقادیر واقعی را در چت قرار ندهید)

```dotenv
APP_ENV=production
DB_HOST=localhost
DB_NAME=your_database
DB_USERNAME=your_username
DB_PASSWORD="your_password"
MCP_API_TOKEN=your_existing_private_token_at_least_32_chars
MCP_AUTHOR_USER_ID=1
MCP_ALLOW_PUBLISH=1
ALO_DEPLOY_ENABLED=0
```
