#!/usr/bin/env python3
"""Render a Persian editorial banner as a small WebP for the MCP article image."""
import base64
from io import BytesIO
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def make_banner(spec):
    if not isinstance(spec, dict):
        raise ValueError("banner must be an object")
    if spec.get("template") == "refrigerator-topic":
        return make_refrigerator_topic_banner(spec)
    if spec.get("template") == "lg-fridge-rudsar":
        return make_lg_fridge_rudsar_banner(spec)
    if spec.get("template") != "refrigerator-gasket":
        raise ValueError("Unsupported editorial banner template")
    variant = spec.get("variant", "steps")
    if variant not in ("steps", "not-sticking"):
        raise ValueError("Unsupported editorial banner variant")
    from PIL import Image, ImageDraw, ImageFont
    width, height = 1200, 630
    img = Image.new("RGB", (width, height))
    px = img.load()
    for y in range(height):
        for x in range(width):
            t = (x / width) * .35 + (y / height) * .65
            if variant == "not-sticking":
                px[x, y] = (int(10 + 13 * t), int(40 + 37 * t), int(54 + 37 * t))
            else:
                px[x, y] = (int(7 + 5 * t), int(29 + 33 * t), int(68 + 42 * t))
    d = ImageDraw.Draw(img)
    font_base = ROOT / "public/src/fonts"
    font_bold = font_base / "Vazir-Black.ttf"
    font_reg = font_base / "Vazir-Medium.ttf"
    if not font_bold.is_file():
        font_bold = Path("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf")
        font_reg = font_bold
    def font(n, bold=False):
        return ImageFont.truetype(str(font_bold if bold else font_reg), n)
    def rtl(t, x, y, size, fill, bold=True):
        d.text((x, y), t, font=font(size, bold), fill=fill, anchor="ra", direction="rtl")
    # Strong diagonal accent band, fine grid, branded editorial panels.
    d.polygon([(800, 0), (1200, 0), (1200, 630), (945, 630)], fill=(14, 74, 131))
    for x in range(20, 1200, 60):
        d.line((x, 0, x - 70, height), fill=(17, 55, 95), width=1)
    d.rounded_rectangle((42, 43, 371, 105), radius=30, fill=(255, 158, 30))
    if variant == "not-sticking":
        rtl("آموزش ویژه نوار شیاری", 343, 64, 27, (15, 34, 64))
        rtl("در یخچال", 765, 159, 87, (255, 255, 255))
        rtl("نمی‌چسبد؟", 765, 263, 81, (255, 190, 53))
        rtl("بدون چسب", 765, 365, 84, (255, 255, 255))
        d.rounded_rectangle((48, 430, 772, 499), radius=19, fill=(17, 80, 102), outline=(46, 191, 171), width=2)
        rtl("عیب‌یابی  |  نصب نوار  |  تست آب‌بندی", 739, 449, 32, (226, 244, 255), bold=False)
    else:
        rtl("راهنمای گام‌به‌گام", 343, 64, 29, (15, 34, 64))
        rtl("مراحل تعویض", 765, 159, 79, (255, 255, 255))
        rtl("نوار درب", 765, 263, 91, (255, 190, 53))
        rtl("یخچال فریزر", 765, 365, 84, (255, 255, 255))
        d.rounded_rectangle((48, 430, 772, 499), radius=19, fill=(18, 73, 119), outline=(49, 153, 215), width=2)
        rtl("ابزار لازم  |  نصب اصولی  |  تست آب‌بندی", 739, 449, 34, (226, 244, 255), bold=False)
    d.rounded_rectangle((45, 542, 766, 608), radius=21, fill=(255, 255, 255))
    rtl("الو تعمیراتچی", 726, 555, 35, (6, 43, 89))
    d.text((76, 564), "alotamiratchi.ir", font=font(28), fill=(17, 105, 163))
    # Fridge body illustrated in vector with a contrasting new orange gasket.
    d.rounded_rectangle((857, 56, 1133, 574), radius=27, fill=(235, 243, 249), outline=(138, 183, 206), width=9)
    d.rounded_rectangle((883, 90, 1109, 298), radius=15, fill=(220, 234, 241), outline=(143, 166, 178), width=5)
    d.rounded_rectangle((883, 315, 1109, 539), radius=15, fill=(220, 234, 241), outline=(143, 166, 178), width=5)
    d.line((890, 310, 1104, 310), fill=(113, 138, 157), width=5)
    d.rounded_rectangle((908, 115, 1084, 280), radius=11, outline=(255, 155, 27), width=12)
    d.rounded_rectangle((908, 338, 1084, 516), radius=11, outline=(255, 155, 27), width=12)
    d.rounded_rectangle((896, 156, 911, 252), radius=7, fill=(87, 130, 159))
    d.rounded_rectangle((896, 377, 911, 481), radius=7, fill=(87, 130, 159))
    d.ellipse((1030, 82, 1062, 114), fill=(255, 167, 36))
    # Small rubber-gasket close-up with directional pointer.
    d.rounded_rectangle((1010, 463, 1186, 611), radius=19, fill=(12, 45, 91), outline=(255, 184, 40), width=5)
    d.arc((1034, 481, 1161, 603), 185, 455, fill=(255, 199, 62), width=18)
    d.ellipse((1128, 496, 1155, 523), fill=(255, 255, 255))
    budget = int(spec.get("max_bytes", 30720))
    if not 20480 <= budget <= 30720:
        raise ValueError("Banner budget must be 20-30 KiB")
    best = None
    for target_width in (1120, 1060, 1000, 950, 900):
        reduced = img.resize((target_width, round(height * target_width / width)), Image.Resampling.LANCZOS)
        for quality in (83, 76, 68, 60, 52, 44, 35, 27, 20, 14):
            buf = BytesIO()
            reduced.save(buf, "WEBP", quality=quality, method=6)
            data = buf.getvalue()
            if len(data) <= budget and len(data) >= 6000:
                best = data
                break
        if best is not None:
            break
    if best is None:
        raise RuntimeError("Could not produce a banner under the requested byte limit")
    return base64.b64encode(best).decode("ascii"), len(best)


def make_lg_fridge_rudsar_banner(spec):
    """Distinct illustrated LG refrigerator service feature image, <=30 KiB."""
    from PIL import Image, ImageDraw, ImageFont
    width, height = 1120, 588
    im = Image.new("RGB", (width, height), (15, 43, 78))
    draw = ImageDraw.Draw(im)
    draw.polygon([(650, 0), (1120, 0), (1120, 588), (835, 588)], fill=(20, 91, 132))
    for y in range(44, 588, 64):
        draw.line((14, y, 750, y), fill=(26, 62, 93), width=2)
    draw.rounded_rectangle((38, 34, 460, 89), radius=16, fill=(235, 184, 78))
    fbase = ROOT / "public/src/fonts"
    fbold = fbase / "Vazir-Black.ttf"
    fmedium = fbase / "Vazir-Medium.ttf"
    if not fbold.exists():
        fbold = Path("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf")
        fmedium = fbold
    def font(sz, bold=True):
        return ImageFont.truetype(str(fbold if bold else fmedium), sz)
    def rtl(text, x, y, sz, color, bold=True):
        draw.text((x, y), text, font=font(sz, bold), direction="rtl", anchor="ra", fill=color)
    rtl("راهنمای تخصصی خدمات یخچال", 436, 45, 25, (26, 42, 67))
    rtl("تعمیرکار یخچال", 734, 147, 63, (255, 255, 255))
    rtl("ال‌جی در رودسر", 734, 252, 67, (255, 201, 105))
    rtl("عیب‌یابی  |  انتخاب متخصص", 728, 382, 32, (218, 243, 250), False)
    draw.rounded_rectangle((38, 492, 737, 558), radius=20, fill=(237, 247, 250))
    rtl("الو تعمیراتچی", 707, 508, 35, (11, 60, 107))
    draw.text((57, 510), "alotamiratchi.ir", fill=(12, 89, 132), font=font(27))
    # Modern refrigerator, separate side-by-side cabinets, doors and handles.
    draw.rounded_rectangle((788, 68, 1074, 536), radius=21, fill=(225, 233, 242), outline=(99, 145, 176), width=5)
    draw.rounded_rectangle((804, 86, 924, 515), radius=10, fill=(245, 249, 249), outline=(130, 166, 191), width=4)
    draw.rounded_rectangle((935, 86, 1057, 515), radius=10, fill=(230, 240, 248), outline=(130, 166, 191), width=4)
    draw.rounded_rectangle((901, 161, 910, 289), radius=5, fill=(86, 119, 152))
    draw.rounded_rectangle((951, 161, 960, 289), radius=5, fill=(86, 119, 152))
    draw.rounded_rectangle((963, 107, 1040, 160), radius=6, fill=(34, 70, 108))
    draw.line((975, 122, 1028, 122), fill=(119, 219, 234), width=3)
    draw.line((975, 138, 1015, 138), fill=(119, 219, 234), width=3)
    # Discrete cooling icon, without using or forging the LG trademark.
    draw.ellipse((840, 344, 892, 396), outline=(26, 142, 175), width=5)
    draw.line((867, 350, 867, 390), fill=(26, 142, 175), width=4)
    draw.line((847, 370, 887, 370), fill=(26, 142, 175), width=4)
    budget = int(spec.get("max_bytes", 30720))
    if not 20480 <= budget <= 30720:
        raise ValueError("Banner budget must be 20-30 KiB")
    for target_w in (1120, 1040, 960, 880):
        scaled = im if target_w == width else im.resize(
            (target_w, round(height * target_w / width)), Image.Resampling.LANCZOS)
        for quality in (82, 72, 60, 48, 36, 26, 18):
            buf = BytesIO()
            scaled.save(buf, "WEBP", quality=quality, method=6)
            raw = buf.getvalue()
            if 6000 <= len(raw) <= budget:
                return base64.b64encode(raw).decode("ascii"), len(raw)
    raise RuntimeError("LG Rudsar editorial image exceeded the byte budget")

def make_refrigerator_topic_banner(spec):
    """Three original topic-specific illustrations, compressed below 30 KiB."""
    from PIL import Image, ImageDraw, ImageFont
    from io import BytesIO
    import base64
    topic = spec.get("topic")
    if topic == "washing-detergent-drawer":
        return make_washing_drawer_banner(spec)
    if topic not in ("water-leak", "compressor", "error-codes", "airflow", "washing-drain", "washing-buying", "washing-tear", "fridge-buying", "food-mold", "fridge-noise", "washing-suds"):
        raise ValueError("Unknown refrigerator illustration topic")
    colors = {"water-leak": ((9, 48, 91), (26, 166, 197)),
              "compressor": ((37, 41, 73), (243, 153, 56)),
              "error-codes": ((28, 52, 77), (93, 198, 139)),
              "airflow": ((12, 66, 91), (109, 221, 232)),
              "washing-drain": ((52, 45, 92), (136, 202, 255)),
              "washing-buying": ((22, 58, 77), (255, 196, 87)),
              "washing-tear": ((42, 46, 83), (246, 166, 94)),
              "fridge-buying": ((18, 43, 77), (255, 195, 65)),
              "food-mold": ((31, 58, 46), (244, 185, 76)),
              "fridge-noise": ((27, 38, 76), (255, 177, 69)),
              "washing-suds": ((24, 56, 84), (103, 222, 225))}
    bg, accent = colors[topic]
    im = Image.new("RGB", (1120, 588), bg)
    d = ImageDraw.Draw(im)
    fbase = ROOT / "public/src/fonts"
    fbold = fbase / "Vazir-Black.ttf"
    if not fbold.is_file():
        fbold = Path("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf")
    def rtl(txt, x, y, sz, color):
        d.text((x, y), txt, font=ImageFont.truetype(str(fbold), sz),
               fill=color, direction="rtl", anchor="ra")
    d.rounded_rectangle((35, 35, 742, 100), radius=18, fill=accent)
    rtl("راهنمای تخصصی لباسشویی" if topic.startswith("washing-") else "راهنمای تخصصی یخچال", 707, 49, 30, bg)
    lines = {
        "water-leak": ("آب کف یخچال", "جمع می‌شود؟", "۸ علت نشتی و راهکار"),
        "compressor": ("یخچال یکسره", "کار می‌کند؟", "عیب‌یابی کمپرسور"),
        "error-codes": ("ارورهای یخچال", "التو (التتو)", "کدهای خطای 8410 و 8420"),
        "airflow": ("فریزر سرد است", "یخچال گرم؟", "بررسی فن و گردش هوا"),
        "washing-drain": ("آب لباسشویی", "تخلیه نمی‌شود؟", "بررسی پمپ و فیلتر"),
        "washing-buying": ("راهنمای خرید", "ماشین لباسشویی", "ظرفیت، موتور و مصرف انرژی"),
        "washing-tear": ("چرا لباسشویی", "لباس را پاره می‌کند؟", "۸ علت و راه‌حل ایمن"),
        "fridge-buying": ("راهنمای خرید", "یخچال ۱۴۰۵", "ساید، دوقلو، ظرفیت و مصرف انرژی"),
        "food-mold": ("چرا غذا در یخچال", "کپک می‌زند؟", "۱۰ علت و راه‌های پیشگیری"),
        "fridge-noise": ("صدای تق‌تق", "یا وزوز یخچال؟", "تشخیص صدای طبیعی و خرابی"),
        "washing-suds": ("کف زیاد لباسشویی", "آبکشی ناقص؟", "۹ علت علمی و راهکار")
    }[topic]
    rtl(lines[0], 740, 145, 68, (255,255,255))
    rtl(lines[1], 740, 242, 65, accent)
    rtl(lines[2], 740, 363, 34, (224,239,248))
    d.rounded_rectangle((35, 493, 741, 558), radius=15, fill=(237,246,252))
    rtl("الو تعمیراتچی", 708, 509, 31, bg)
    d.rounded_rectangle((801, 66, 1078, 523), radius=24, fill=(222,235,244), outline=accent, width=7)
    d.line((810, 294, 1068, 294), fill=(120,150,171), width=7)
    d.rounded_rectangle((824, 90, 1056, 273), radius=12, fill=(244,248,250))
    d.rounded_rectangle((824, 313, 1056, 500), radius=12, fill=(239,247,249))
    if topic == "water-leak":
        d.line((840, 461, 1025, 461), fill=accent, width=9)
        for x,y in ((880,486),(946,516),(1010,486)):
            d.ellipse((x-11,y-11,x+11,y+11), fill=accent)
        d.arc((827,442,1064,566), 12, 172, fill=accent, width=10)
    elif topic == "compressor":
        d.rounded_rectangle((857, 376, 1027, 479), radius=32, fill=(58,72,90), outline=accent, width=8)
        d.ellipse((909,397,976,464), outline=accent, width=7)
        d.line((942,376,942,340), fill=accent, width=9)
        d.line((857,427,827,427), fill=accent, width=9)
    elif topic == "airflow":
        for y in (360,405,450):
            d.arc((840,y-35,1038,y+35),180,345,fill=accent,width=9)
        d.ellipse((910,115,988,193),outline=accent,width=9)
        d.line((950,120,950,190),fill=accent,width=5)
    elif topic == "washing-suds":
        d.rounded_rectangle((828, 96, 1052, 512), radius=20, fill=(234,244,249), outline=(116,161,188), width=6)
        d.ellipse((846,185,1034,373), fill=(44,76,112), outline=accent, width=10)
        d.ellipse((864,202,1016,355), fill=(143,194,218))
        for x,y,r in ((891,235,17),(937,224,24),(977,258,21),(916,288,29),(965,317,17)):
            d.ellipse((x-r,y-r,x+r,y+r),fill=(243,251,252),outline=accent,width=3)
        d.rounded_rectangle((852,115,1024,158),radius=8,fill=(42,86,123))
        d.ellipse((980,124,1001,145),fill=accent)
    elif topic == "washing-tear":
        d.rounded_rectangle((825, 94, 1060, 515), radius=24, fill=(231,236,245), outline=(132,152,185), width=7)
        d.ellipse((853, 180, 1032, 359), fill=(50,66,98), outline=(143,169,194), width=10)
        d.ellipse((877, 203, 1009, 336), fill=(159,195,217))
        d.polygon([(894,235),(924,220),(941,239),(964,220),(992,239),(975,307),(911,308)],fill=(245,174,98))
        d.line((923,263,948,284),fill=(94,54,65),width=7)
        d.line((947,262,922,288),fill=(94,54,65),width=7)
        d.rounded_rectangle((855,115,1028,161),radius=9,fill=(63,81,117))
        d.ellipse((981,126,1003,148),fill=accent)
    elif topic == "food-mold":
        # Moldy produce and storage container: purpose-built editorial artwork.
        d.rounded_rectangle((830, 340, 1055, 485), radius=18, fill=(229,241,225), outline=accent, width=6)
        d.ellipse((850, 354, 948, 453), fill=(216,92,73), outline=(157,52,49), width=5)
        d.ellipse((939, 367, 1024, 450), fill=(228,186,85), outline=(154,112,54), width=5)
        for x,y,r in ((880,380,14),(909,405,18),(958,395,11),(985,424,16),(897,434,9)):
            d.ellipse((x-r,y-r,x+r,y+r),fill=(64,122,73),outline=(32,75,51),width=3)
        d.rounded_rectangle((841, 109, 1045, 222), radius=15, fill=(234,247,234))
        d.ellipse((881, 136, 941, 196), fill=(216,92,73))
        for x,y in ((896,152),(913,169),(929,149)):
            d.ellipse((x-9,y-9,x+9,y+9), fill=(61,125,73))
        d.line((971,147,1022,147), fill=(63,122,80), width=9)
        d.line((971,176,1009,176), fill=(63,122,80), width=9)
    elif topic == "fridge-buying":
        # Three distinct refrigerator silhouettes: side-by-side, twin, freezer-on-top.
        d.rounded_rectangle((816, 345, 900, 480), radius=8, fill=(240,248,251), outline=accent, width=5)
        d.line((858, 348, 858, 478), fill=(60,106,138), width=4)
        d.line((847, 376, 847, 413), fill=accent, width=5)
        d.line((868, 376, 868, 413), fill=accent, width=5)
        d.rounded_rectangle((919, 345, 1000, 480), radius=8, fill=(240,248,251), outline=accent, width=5)
        d.line((959, 348, 959, 478), fill=(60,106,138), width=4)
        d.rounded_rectangle((1013, 345, 1065, 480), radius=7, fill=(240,248,251), outline=accent, width=5)
        d.line((1015, 390, 1063, 390), fill=(60,106,138), width=4)
        d.rounded_rectangle((836, 111, 1035, 196), radius=12, fill=(32,76,105))
        d.text((856, 129), "A++", fill=accent, font=ImageFont.truetype(str(fbold), 46))
    elif topic == "washing-buying":
        d.ellipse((843,331,1038,510),outline=accent,width=14)
        d.ellipse((876,365,1005,484),outline=(72,130,158),width=8)
        d.ellipse((866,115,899,148),fill=accent)
        d.rounded_rectangle((925,111,1030,156),radius=9,fill=(34,72,96))
        d.text((942,118),"A++",fill=accent,font=ImageFont.truetype(str(fbold),24))
    elif topic == "washing-drain":
        d.ellipse((848,332,1030,502),outline=accent,width=14)
        d.arc((860,355,1015,490),20,170,fill=accent,width=12)
        d.line((910,480,910,530),fill=accent,width=8)
    else:
        d.rounded_rectangle((852, 111, 1030, 234), radius=12, fill=(23,48,64))
        d.text((875,137), "F1 E2", fill=accent, font=ImageFont.truetype(str(fbold), 39))
        d.text((868,180), "H1 C2", fill=(255,255,255), font=ImageFont.truetype(str(fbold), 34))
        d.line((864,369,1022,369), fill=accent, width=9)
        d.line((864,403,990,403), fill=accent, width=9)
    for width in (1120, 1000, 880, 760, 640):
        scaled=im.resize((width,round(588*width/1120)),Image.Resampling.LANCZOS)
        for quality in (76,65,54,43,32,22):
            out=BytesIO()
            scaled.save(out,"WEBP",quality=quality,method=6)
            if len(out.getvalue())<=30720:
                return base64.b64encode(out.getvalue()).decode("ascii"),len(out.getvalue())
    raise RuntimeError("Unable to compress banner to 30 KiB")


def make_washing_drawer_banner(spec):
    """An original detergent-drawer diagram for the Alo washing machine guide."""
    from PIL import Image, ImageDraw, ImageFont
    width, height = 1120, 588
    im = Image.new("RGB", (width, height), (19, 50, 79))
    d = ImageDraw.Draw(im)
    blue, orange, pale = (35, 108, 150), (255, 180, 76), (237, 248, 250)
    d.polygon([(756, 0), (1120, 0), (1120, 588), (900, 588)], fill=(29, 92, 127))
    for y in range(120, 520, 56):
        d.line((34, y, 745, y), fill=(29, 66, 93), width=2)
    font_dir = ROOT / "public/src/fonts"
    font_path = font_dir / "Vazir-Black.ttf"
    if not font_path.is_file():
        font_path = Path("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf")
    def font(size):
        return ImageFont.truetype(str(font_path), size)
    def rtl(text, x, y, size, fill):
        d.text((x, y), text, font=font(size), fill=fill, anchor="ra", direction="rtl")
    d.rounded_rectangle((38, 39, 730, 100), radius=17, fill=orange)
    rtl("راهنمای عیب‌یابی ماشین لباسشویی", 704, 51, 30, (17, 49, 79))
    rtl("چرا آب در", 728, 141, 72, (255, 255, 255))
    rtl("جاپودری می‌ماند؟", 728, 249, 66, orange)
    rtl("آب کم طبیعی است یا گرفتگی مسیر؟", 727, 368, 35, (226, 246, 251))
    d.rounded_rectangle((36, 493, 740, 558), radius=17, fill=pale)
    rtl("الو تعمیراتچی", 707, 508, 34, (18, 57, 86))
    # A front-loading washing machine with a distinct open detergent drawer.
    d.rounded_rectangle((819, 86, 1074, 526), radius=22, fill=(222, 235, 245), outline=(142, 182, 204), width=6)
    d.rounded_rectangle((838, 105, 1056, 174), radius=10, fill=(57, 102, 135))
    d.rounded_rectangle((848, 122, 976, 158), radius=7, fill=(23, 61, 91))
    d.line((858, 141, 922, 141), fill=(174, 231, 243), width=4)
    d.ellipse((1003, 119, 1040, 156), fill=(207, 223, 233), outline=orange, width=4)
    d.ellipse((851, 215, 1040, 405), fill=(42, 80, 116), outline=(116, 153, 186), width=12)
    d.ellipse((871, 235, 1020, 385), fill=(142, 190, 212))
    d.arc((891, 267, 1012, 368), 18, 175, fill=(219, 241, 249), width=13)
    d.rounded_rectangle((836, 444, 1056, 479), radius=10, fill=(201, 219, 233))
    # Exploded inset showing the detergent tray and residual water (not the drum).
    d.rounded_rectangle((785, 48, 967, 228), radius=16, fill=(248, 252, 253), outline=orange, width=5)
    d.rounded_rectangle((805, 92, 950, 167), radius=8, fill=(197, 220, 237), outline=blue, width=5)
    d.line((851, 96, 851, 163), fill=blue, width=4)
    d.line((900, 96, 900, 163), fill=blue, width=4)
    for x, y in ((824, 151), (870, 147), (919, 151)):
        d.arc((x - 13, y - 9, x + 13, y + 9), 5, 176, fill=blue, width=4)
    d.line((846, 180, 846, 207), fill=orange, width=5)
    d.polygon([(837, 201), (855, 201), (846, 216)], fill=orange)
    budget = int(spec.get("max_bytes", 30720))
    if budget < 20480 or budget > 30720:
        raise ValueError("Banner budget must be between 20 and 30 KiB")
    for target_w in (1120, 1000, 880, 760, 640):
        scaled = im.resize((target_w, round(height * target_w / width)), Image.Resampling.LANCZOS)
        for quality in (78, 68, 58, 48, 38, 28, 18):
            out = BytesIO()
            scaled.save(out, "WEBP", quality=quality, method=6)
            if 6000 <= len(out.getvalue()) <= budget:
                return base64.b64encode(out.getvalue()).decode("ascii"), len(out.getvalue())
    raise RuntimeError("Unable to optimize detergent-drawer banner under 30 KiB")
