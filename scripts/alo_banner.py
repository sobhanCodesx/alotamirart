#!/usr/bin/env python3
"""Render a Persian editorial banner as a small WebP for the MCP article image."""
import base64
from io import BytesIO
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def make_banner(spec):
    if not isinstance(spec, dict):
        raise ValueError("banner must be an object")
    if spec.get("template") != "refrigerator-gasket":
        raise ValueError("Unsupported editorial banner template")
    from PIL import Image, ImageDraw, ImageFont
    width, height = 1200, 630
    img = Image.new("RGB", (width, height))
    px = img.load()
    for y in range(height):
        for x in range(width):
            t = (x / width) * .35 + (y / height) * .65
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
