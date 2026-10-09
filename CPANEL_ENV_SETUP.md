# تنظیم فایل .env در cPanel — بدون CMD و Composer

راهنمای الو تعمیراتچی برای PHP 8.2؛ تغییرات فعلاً فقط در شاخه آزمایشی GitHub است.

## مراحل امن نصب با File Manager

1. از فایل‌ها و دیتابیس بک‌آپ بگیرید. در Settings فایل‌منیجر، Show Hidden Files (dotfiles) را روشن کنید.
2. ابتدا تغییر محافظتی .htaccess را بالای RewriteEngine On نصب کنید. اگر فایل .env در public_html باشد، باید درخواست مستقیم وب به آن با Require all denied مسدود شود.
3. در ریشه پروژه (پوشه index.php و config) از .env.example یک فایل .env بسازید. مقدارهای نمونه را جایگزین کنید؛ کلیدها و رمزهای واقعی را در GitHub یا چت قرار ندهید.
4. با مرورگر آدرس https://alotamiratchi.ir/.env را بررسی کنید: محتوای فایل نباید نمایش داده شود (403 مناسب است).
5. اطلاعات دیتابیس اصلی را از تنظیمات فعلی وارد DB_HOST، DB_NAME، DB_USERNAME و DB_PASSWORD کنید. اگر اعتبارنامه دیتابیس دوم دارید، DB_LEGACY_HOST، DB_LEGACY_NAME، DB_LEGACY_USERNAME و DB_LEGACY_PASSWORD را هم تنظیم کنید.
6. فقط پس از آماده شدن .env، فایل‌های جدید app/Support/env.php و config/database.php را روی هاست آپلود کنید. بعد، صفحه اصلی، پنل ادمین و چند مقاله را بازبینی کنید.
7. برای MCP، مقدار MCP_API_TOKEN باید حداقل 32 کاراکتر باشد و MCP_AUTHOR_USER_ID باید شناسه کاربر واقعی سایت باشد. MCP_ALLOW_PUBLISH را فعلاً 0 بگذارید.
8. برای دیپلوی خودکار، ابتدا api/deploy.php را دستی نصب کنید، GitHub Actions Secrets مربوط به MCP_API_TOKEN و ALO_DEPLOY_URL را تنظیم کنید؛ فقط پس از آزمایش، ALO_DEPLOY_ENABLED را 1 کنید.

## نکات مهم

- این فایل نیازی به ترمینال، SSH و Composer ندارد؛ PHP 8.2 کافی است.
- فایل .env نباید به GitHub برود و .gitignore آن را نادیده می‌گیرد.
- مقدارهای دارای فاصله یا علامت # را در کوتیشن بگذارید؛ کامنت‌ها با # شروع می‌شوند.
- متغیرهای محیطی قبلاً تعریف‌شده سرور بر مقدار فایل .env اولویت دارند.
- .htaccess و config/database.php طبق سیاست کنونی دیپلوی خودکار نیستند؛ نصب اولیه و تغییرشان باید با File Manager انجام شود.
- چون رمز دیتابیس قبلاً داخل ریپو ثبت شده بود، پس از جابه‌جایی به .env آن را در cPanel تعویض کنید.
- شاخه توسعه تا زمان تست واقعی روی هاست نباید روی production مرج شود.
