#!/usr/bin/env python3
"""Verified idempotent publication into the refrigerator BRAND section, not general posts."""
import base64, json, re, sys
from io import BytesIO
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont
sys.path.insert(0,"scripts")
from mcp_content_job import tool
from alo_banner import make_banner

NAMES=[
("samsung","سامسونگ","resolved"),("lg","ال‌جی","resolved"),
("bosch","بوش","resolved"),("daewoo","دوو","resolved"),
("snowa","اسنوا","publish"),("emersun","امرسان","resolved"),
("electrosteel","الکترواستیل","resolved"),("depoint","دیپوینت","resolved"),
("himalaya","هیمالیا","resolved"),("beko","بکو","resolved"),
("hisense","هایسنس","publish"),("hitachi","هیتاچی","publish"),
("kenwood","کنوود","publish"),("eastcool","ایستکول","fixed"),
("pars","پارس","publish")]
BRIEFS={
"samsung":("اگر عدد نمایشگر طبیعی است اما کابین گرم مانده، دمای واقعی را از عدد تنظیم‌شده جدا کنید. تغییر دما بعد از قرار دادن غذای گرم یا باز ماندن در قابل انتظار است؛ ادامه آن به بررسی نیاز دارد.","در برخی مدل‌ها هنگام باز بودن در فن خاموش می‌شود. اگر فریزر سرد و یخچال گرم است، مسیر هوای بین محفظه‌ها و یخ پنهان را بدون باز کردن پنل بررسی ظاهری کنید.","پیش از ریست، کد خطا و شماره مدل را ثبت کنید؛ بررسی فن، سنسور و برد اینورتر را به متخصص بسپارید."),
"lg":("صدای تغییر دور کمپرسور در مدل‌های دارای کنترل متغیر لزوماً خرابی نیست؛ دمای واقعی کابین را نیز ثبت کنید.","اگر فریزر سرمای مناسب دارد اما یخچال گرم است، گردش هوای بین بخش‌ها و انسداد دریچه‌ها را بررسی کنید؛ همیشه ایراد از خود موتور نیست.","شارژ گاز یا تعویض کمپرسور بدون تأیید نشتی یا ایراد مدار، هزینه اضافه ایجاد می‌کند."),
"bosch":("آلارم دما ممکن است بعد از باز ماندن در یا افزودن مقدار زیاد مواد تازه فعال شود؛ معنی دقیق هشدار را از دفترچه مدل دنبال کنید.","تراز دستگاه، گیر نکردن کشوها و بسته شدن کامل در را پیش از تعویض سنسور یا برد بررسی کنید.","برای تشخیص خطا، کد نمایشگر و شماره دقیق مدل را ثبت کنید؛ برداشت از مدل دیگر قابل اعتماد نیست."),
"daewoo":("در یخچال‌های مجهز به آب‌سردکن، ابتدا مشخص کنید سیستم از مخزن داخلی یا شلنگ آب شهری تغذیه می‌شود.","در مدل متصل به آب شهری، خم شدن شلنگ، بسته بودن شیر یا گرفتگی فیلتر از احتمالات کم شدن آب هستند؛ در صورت نشتی، مسیر تغذیه را ایمن قطع کنید.","پیش از تعویض شیر برقی یا پمپ، تکنسین باید علت کاهش جریان آب را آزمایش کند."),
"snowa":("سرد ماندن فریزر و گرم شدن بخش یخچال می‌تواند از اختلال گردش هوا و برفک پشت پنل ناشی شود، نه صرفاً ضعف موتور.","صدای تماس پره فن با یخ یا بازگشت عیب پس از یخ‌زدایی یک سرنخ است؛ پنل داخلی را بدون تخصص باز نکنید.","بررسی هیتر، فیوز حرارتی، سنسور و فن باید طبق مدل و با آزمون انجام شود، نه تعویض هم‌زمان قطعات."),
"emersun":("رطوبت کنار در یا تشکیل برفک نزدیک لبه کابین می‌تواند از تماس نامناسب لاستیک با بدنه باشد.","گیر کردن کشو، بسته نشدن در یا تراز نبودن کابین گاهی ظاهر خرابی واشر را ایجاد می‌کند؛ از حرارت مستقیم برای تغییر شکل واشر استفاده نکنید.","تعمیرکار باید لولا و تماس دور در را ارزیابی کرده و واشر را با شماره مدل دقیق سفارش دهد."),
"electrosteel":("در دستگاه‌های دوقلو خرابی یک کابین الزاماً مربوط به کابین دیگر نیست؛ دمای هر واحد را جدا ثبت کنید.","فاصله تهویه و ورودی برق هر دستگاه را مستقل بررسی کنید؛ ساختار کنترل مدل‌های مختلف دوقلو یکسان نیست.","بازدید تخصصی باید روی همان کابین معیوب و با شماره مدل مربوط انجام شود، نه تعویض هم‌زمان قطعات دو دستگاه."),
"depoint":("اگر بعضی مواد کنار دریچه سردکننده یخ می‌زنند اما بقیه کابین گرم است، مشکل می‌تواند به توزیع نامناسب هوا مربوط باشد.","قرارگیری ظرف حجیم روبه‌روی مسیر هوا و تنظیم دمای بیش از حد پایین را پیش از اتهام به برد کنترل بررسی کنید.","فن، دریچه و حسگر باید مطابق مدل دیپوینت آزمون شوند؛ اختلاف دمای طبقات را برای تعمیرکار ثبت کنید."),
"himalaya":("صدای تق‌تق یا لرزش ممکن است از تماس بدنه با کابینت، ظروف داخل طبقات یا فن باشد و لزوماً به کمپرسور ربط ندارد.","محل و زمان صدا را ثبت کنید؛ اگر همراه افت سرما از محفظه داخلی می‌آید، برخورد فن با یخ هم باید بررسی شود.","تکنسین باید منبع صدا را تعیین کند؛ تعویض کمپرسور صرفاً به دلیل صدای تازه بدون تست منطقی نیست."),
"beko":("رطوبت اطراف در و برفک نزدیک لبه یخچال می‌تواند نشانه ورود هوای گرم باشد؛ اما پارگی لاستیک تنها علت نیست.","بسته شدن کامل در، گیر نکردن بسته‌ها و تنظیم پایه‌ها را بررسی کنید؛ چسب قوی و سشوار داغ برای اصلاح لاستیک توصیه نمی‌شود.","برای انتخاب واشر دقیق باید مدل و وضعیت لولا بررسی شود؛ قطعه با اندازه نامناسب عیب را ادامه می‌دهد."),
"hisense":("کدهای خطا در سری‌های مختلف یکسان تفسیر نمی‌شوند. پیش از هر ریست از نمایشگر عکس بگیرید.","دمای واقعی محفظه، زمان نمایش خطا و وضعیت بسته شدن در را ثبت کنید و از آزمون‌های برقی خودسرانه پرهیز کنید.","آزمایش سنسور و کنترل الکترونیکی باید با دفترچه همان مدل انجام شود، نه براساس عکس کد خطای دستگاه دیگر."),
"hitachi":("در یخچال‌های چندمحفظه‌ای، دمای متفاوت طبقات و محفظه سبزیجات همیشه به معنی خرابی کمپرسور نیست.","تنظیم مستقل بخش‌ها و گرفتگی مسیر هوا توسط بسته‌های غذایی را کنترل کنید؛ بازماندن طولانی در هم نتیجه ارزیابی را عوض می‌کند.","نقشه هوارسانی و تعداد سنسورها در مدل‌ها متفاوت است و تعمیرکار باید شماره مدل دقیق را بداند."),
"kenwood":("گرم شدن برخی سطوح بدنه هنگام دفع حرارت می‌تواند طبیعی باشد؛ شاخص مهم‌تر حفظ دمای مناسب داخل محفظه است.","در آشپزخانه‌های کم‌جا، فاصله تهویه پشت دستگاه و تماس نداشتن با منبع حرارتی اهمیت دارد.","شارژ مبرد صرفاً بر اساس گرم شدن بدنه تصمیم درستی نیست؛ جریان هوا، فن و دمای کابین باید سنجیده شود."),
"eastcool":("در یخچال‌های کوچک، پرکردن بیش از حد کابین یا گذاشتن مواد نزدیک سطح سرد می‌تواند سرمایش را نامتعادل کند.","تنظیم ترموستات و زمان کافی برای رسیدن کابین به دمای پایدار بعد از بارگذاری غذای گرم را در نظر بگیرید.","ساختار کنترل یخچال کوچک ممکن است با یخچال نوفراست متفاوت باشد؛ تعمیرکار باید نوع سیستم را تشخیص دهد."),
"pars":("خاموش و روشن شدن دوره‌ای کمپرسور معمولاً بخشی از تنظیم دماست؛ مشکل زمانی مطرح است که دمای واقعی خارج از محدوده باقی بماند.","تنظیم ترموستات، آب‌بندی در و تهویه اطراف را پیش از تعویض قطعات کنترل کنید.","آزمون ترموستات و قطعات راه‌اندازی باید ایمن انجام شود؛ اتصال مستقیم سیم‌ها برای کاربر خطرناک است.")
}
def branded_image(spec,key,name):
    b64,_=make_banner(spec)
    with Image.open(BytesIO(base64.b64decode(b64))) as img:
        im=img.convert("RGB")
        d=ImageDraw.Draw(im)
        font_path="/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
        font=ImageFont.truetype(font_path,20)
        d.rounded_rectangle((16,8,370,43),radius=10,fill=(247,249,250))
        d.text((350,15),name+" - چمستان",anchor="ra",direction="rtl",font=font,fill=(20,48,69))
        d.line((6,im.height-12,im.width-6,im.height-12),fill=((hash(key)&100)+60,153,194),width=5)
        for quality in (78,65,54,44,34,24,14,8):
            out=BytesIO()
            im.save(out,format="WEBP",quality=quality,method=6)
            if len(out.getvalue())<=30000:
                return base64.b64encode(out.getvalue()).decode("ascii"),len(out.getvalue())
    raise RuntimeError("Unable to compress distinct brand cover")
categories=tool("list_content",{"type":"brand","limit":50}).get("records") or []
matched=[c for c in categories if str(c.get("name","")).strip()=="تعمیرات یخچال"]
if len(matched)!=1:raise RuntimeError("Refrigerator BRAND SECTION unresolved")
category_id=int(matched[0]["id"])
print("BRAND_SECTION_VERIFIED",category_id,flush=True)
results=[]
errors=[]
for key,name,suffix in NAMES:
    try:
        path=Path("content-requests")/("chamestan-"+key+"-brand-"+suffix+"-20261102.json")
        job=json.loads(path.read_text(encoding="utf-8"))
        fields=job["fields"];slug=fields["slug"]
        if slug!=key+"-refrigerator-repair-chamestan":raise ValueError("Unexpected slug")
        if "چمستان" not in fields["title"]:raise ValueError("City/title mismatch")
        existing=tool("find_content",{"type":"brand_article","slug":slug}).get("record")
        if existing:
            if int(existing.get("brand_id") or 0)!=category_id:
                raise RuntimeError("Existing record is in incorrect brand section")
            item=existing
            print("REUSE_BRAND_DRAFT_OR_POST",key,item["id"],flush=True)
        else:
            focus=fields["title"].split("|",1)[-1].strip()
            b1,b2,b3=BRIEFS[key]
            title=fields["title"]
            content=(
                "<h1>"+html.escape(title)+"</h1>"
                "<p>در چمستان، بررسی عیب یخچال "+html.escape(name)+" را باید از نشانه واقعی و شماره مدل آغاز کرد. این راهنما روی "+html.escape(focus)+" متمرکز است و ادعای نمایندگی رسمی سازنده ندارد.</p>"
                "<h2>چطور نشانه اصلی را از مشکل ظاهری جدا کنیم؟</h2><p>"+b1+"</p>"
                "<p>برای مقایسه با مراحل عیب‌یابی عمومی، <a href=\"https://alotamiratchi.ir/refrigerator-repair-in-chamestan\">تعمیر یخچال در چمستان</a> را ببینید. مسیر تعمیر به نوع دستگاه و علائم واقعی وابسته است.</p>"
                "<h2>"+html.escape(focus)+"؛ بررسی بدون دستکاری قطعات</h2><p>"+b2+"</p>"
                "<h2>آزمایش‌های تخصصی و پیشگیری از تعویض بی‌مورد</h2><p>"+b3+"</p>"
                "<h2>چه اطلاعاتی برای انتخاب تعمیرکار در چمستان لازم است؟</h2><p>مدل، عکس برچسب مشخصات، تاریخ شروع مشکل و تفاوت وضعیت یخچال و فریزر را ثبت کنید. اگر کد خطا یا صدای غیرعادی وجود دارد، آن را نیز یادداشت کنید. تعمیرکار با این اطلاعات می‌تواند قطعه مناسب را دقیق‌تر انتخاب کند.</p>"
                "<h2>آیا تعمیر در محل امکان‌پذیر است؟</h2><p>بررسی تنظیمات، لاستیک در، فن یا برخی سنسورها در بسیاری مدل‌ها در محل انجام می‌شود؛ اما تعمیر برد یا مدار تبرید ممکن است تجهیزات خاص بخواهد. پیش از شروع درباره هزینه عیب‌یابی و رسید خدمات پرسش کنید. پوشش خدمات در چمستان باید از ارائه‌دهنده تأیید شود.</p>"
                "<p>برای مطالعه مطالب دیگر درباره دستگاه‌ها و مشکلات سرمایش، <a href=\"https://tekanino.ir/refrigerator\">بخش تعمیر یخچال تکانی‌نو</a> را ببینید. اطلاعات عمومی جای تشخیص دستگاه مشخص را نمی‌گیرد.</p>"
                "<h2>هزینه تعمیر چگونه تعیین می‌شود؟</h2><p>مدل دستگاه، قطعه لازم، میزان کار و شرایط ضمانت در قیمت نهایی مؤثر است. هزینه قطعه و اجرت را جدا بپرسید. اگر دستگاه گارانتی معتبر دارد ابتدا شرایط استفاده از خدمات رسمی سازنده را بررسی کنید.</p>"
                "<h2>اقدام‌های ایمن پیش از مراجعه</h2><p>بسته شدن کامل در، تراز بودن پایه‌ها، باز بودن دریچه‌های هوا و تنظیم دما را مطابق دفترچه بررسی کنید. در صورت بوی سوختگی، آسیب کابل یا نشتی نزدیک برق از دستکاری دستگاه خودداری کنید.</p>"
                "<h2>سؤالات متداول</h2><h3>آیا افت سرما همیشه ناشی از کمپرسور است؟</h3><p>خیر؛ مسیر هوا، سنسور، آب‌بندی و شرایط نصب هم باید بررسی شود.</p><h3>آیا بدون مدل دقیق می‌توان قطعه سفارش داد؟</h3><p>برای اطمینان از سازگاری، شماره مدل و در صورت نیاز شناسه قطعه ضروری است.</p>"
                "<h2>جمع‌بندی</h2><p>برای "+html.escape(focus)+" در یخچال "+html.escape(name)+"، ابتدا شرایط قابل مشاهده را ثبت کنید و سپس تعمیرکاری انتخاب کنید که با مدل دستگاه آشنا باشد.</p>"
            )
            if len(content)<2800:raise ValueError("Article content unexpectedly short")
            spec=dict(job.get("banner") or {"template":"refrigerator-topic","topic":"airflow","max_bytes":30000})
            spec["max_bytes"]=30000
            art,size=branded_image(spec,key,name)
            print("BRAND_IMAGE_BYTES",key,size,flush=True)
            entry=tool("create_content",{
                "type":"brand_article",
                "fields":{"title":title,"slug":slug,"content":content,"des":fields.get("description") or ("راهنمای تعمیر یخچال "+name+" در چمستان؛ بررسی علائم و انتخاب تعمیرکار."),"brand_id":category_id},
                "image_base64":art})
            item=entry["record"]
        if int(item.get("status") or 0)!=1:
            tool("set_content_published",{"type":"brand_article","id":int(item["id"]),"published":True,"confirm":True})
        verified=tool("get_content",{"type":"brand_article","id":int(item["id"])})["record"]
        if (verified.get("slug")!=slug or int(verified.get("brand_id") or 0)!=category_id or
            int(verified.get("status") or 0)!=1 or not verified.get("img") or
            "چمستان" not in str(verified.get("content") or "")):
            raise RuntimeError("Publish readback verification failed")
        results.append([key,name,int(item["id"]),slug])
        print("CHAMESTAN_BRAND_PUBLISHED_VERIFIED",key,int(item["id"]),flush=True)
    except Exception as e:
        errors.append([key,type(e).__name__,str(e)[:180]])
        print("CHAMESTAN_BRAND_ERROR",key,type(e).__name__,str(e)[:180],flush=True)
print("CHAMESTAN_BATCH_RESULT",json.dumps({"published":len(results),"items":results,"errors":errors},ensure_ascii=False),flush=True)
if errors:raise SystemExit(2)
