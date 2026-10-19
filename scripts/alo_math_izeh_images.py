#!/usr/bin/env python3
"""Build three original, low-size topic-specific WebP illustrations for math tutoring."""
import io,math,random
from pathlib import Path
from PIL import Image,ImageDraw,ImageFont
ROOT=Path(__file__).resolve().parents[1]
FONTS=ROOT/"public/src/fonts"
W,H=960,530
NAVY=(15,39,65);ORANGE=(237,120,37);BLUE=(43,134,204);MILK=(248,250,250)
def f(size,bold=False):return ImageFont.truetype(str(FONTS/("Vazir-Black.ttf" if bold else "Vazir-Medium.ttf")),size)
def rtl(s):
    try:
        import arabic_reshaper
        from bidi.algorithm import get_display
        return get_display(arabic_reshaper.reshape(s))
    except ImportError:
        return s
def label(d,s,anchor,y,size,fill,bold=False):
    line=rtl(s)
    d.text(anchor,line,font=f(size,bold),fill=fill,anchor="ra")

def card(d,xy,fill=(255,255,255),radius=19):
    d.rounded_rectangle(xy,radius=radius,fill=fill,outline=(203,220,232),width=2)

def background():
    im=Image.new("RGB",(W,H),(247,250,252))
    d=ImageDraw.Draw(im)
    for y in range(H):
        t=y/H
        d.line((0,y,W,y),fill=(int(244-12*t),int(249-19*t),int(252-21*t)))
    d.rounded_rectangle((17,16,943,515),radius=26,outline=(195,215,227),width=2)
    d.rectangle((19,16,941,27),fill=ORANGE)
    d.text((40,43),"ALOTAMIRATCHI.IR",font=f(21,True),fill=NAVY)
    return im,d

def scene(kind):
    im,d=background()
    if kind==0:
        # Geometry board and study-table, visually distinct featured image.
        d.rounded_rectangle((40,108,565,452),radius=23,fill=(29,58,80),outline=(20,43,64),width=5)
        for k in range(18):
            x=53+k*27
            d.line((x,116,x,443),fill=(42,74,93),width=1)
        for y in range(120,451,27):d.line((48,y,557,y),fill=(42,74,93),width=1)
        d.arc((125,177,260,323),25,335,fill=(135,214,234),width=6)
        d.line((135,332,284,236),fill=(247,182,109),width=8)
        d.polygon([(175,300),(275,189),(332,330)],outline=(236,235,211))
        d.line((175,300,275,189,332,330,175,300),fill=(245,235,218),width=6)
        d.text((102,143),"x² + 5x + 6",font=f(36,True),fill=(244,246,240))
        d.text((375,181),"a + b = c",font=f(27),fill=(255,189,114))
        d.text((380,235),"sin θ",font=f(30),fill=(153,217,247))
        d.text((98,365),"π  3.14",font=f(35),fill=(247,187,123))
        d.rounded_rectangle((587,119,917,446),radius=23,fill=(255,255,255))
        label(d,"کلاس خصوصی ریاضی",(890,179),155,34,NAVY,True)
        label(d,"در ایذه",(891,244),213,43,ORANGE,True)
        d.line((650,280,891,280),fill=(217,229,237),width=3)
        label(d,"آموزش مفهومی و رفع اشکال",(889,335),307,22,NAVY)
        label(d,"برنامه‌ریزی متناسب با پایه",(891,380),356,20,(68,98,120))
        d.rounded_rectangle((635,395,895,425),radius=13,fill=(236,245,251))
        d.text((652,399),"09168949864",font=f(24,True),fill=NAVY)
    elif kind==1:
        # Learning method: work through geometric and algebraic steps.
        label(d,"یادگیری مرحله‌ای ریاضی",(915,104),75,38,NAVY,True)
        card(d,(49,132,469,445))
        card(d,(487,132,910,445))
        d.text((89,165),"y = 2x + 1",font=f(42,True),fill=NAVY)
        ox,oy=250,365
        d.line((96,oy,410,oy),fill=(91,136,159),width=4)
        d.line((ox,425,ox,206),fill=(91,136,159),width=4)
        for v in (-2,-1,1,2):
            x=ox+v*52;y=oy-(2*v+1)*27
            if 210<y<421:
                d.ellipse((x-7,y-7,x+7,y+7),fill=ORANGE)
        d.line((153,419,315,241),fill=BLUE,width=6)
        d.polygon([(540,360),(715,179),(863,353)],outline=BLUE)
        d.line((540,360,715,179,863,353,540,360),fill=BLUE,width=8)
        d.line((715,179,715,353),fill=ORANGE,width=6)
        for cx,cy in ((540,360),(715,179),(863,353)):
            d.ellipse((cx-7,cy-7,cx+7,cy+7),fill=NAVY)
        d.text((685,380),"a² + b² = c²",font=f(32,True),fill=NAVY)
        label(d,"جبر و معادله",(414,440),398,21,NAVY)
        label(d,"هندسه و حل مسئله",(868,440),398,21,NAVY)
    else:
        # Four-stage learning plan, unique from the other two drawings.
        label(d,"نقشه راه یک کلاس خصوصی مؤثر",(911,111),79,37,NAVY,True)
        steps=[
            ("۱","ارزیابی اولیه","شناخت پیش‌نیازها"),
            ("۲","تدریس مفهومی","نمونه و مثال"),
            ("۳","حل مستقل","تمرین و بازخورد"),
            ("۴","سنجش پیشرفت","اصلاح خطاها")
        ]
        xstarts=(45,272,500,727)
        for i,(number,heading,subtitle) in enumerate(steps):
            x=xstarts[i]
            card(d,(x,161,x+195,416),fill=(255,255,255),radius=22)
            d.ellipse((x+56,182,x+140,266),fill=ORANGE if i==2 else NAVY)
            d.text((x+96,227),rtl(number),font=f(41,True),fill=(255,255,255),anchor="mm")
            label(d,heading,(x+176,345),310,23,NAVY,True)
            label(d,subtitle,(x+176,389),369,17,(98,119,135))
            if i!=3:d.line((x+198,277,x+225,277),fill=ORANGE,width=6)
        d.rounded_rectangle((315,448,656,489),radius=17,fill=(229,240,248))
        d.text((348,455),"09168949864",font=f(25,True),fill=NAVY)
    return im

def build():
    out=[]
    for kind in range(3):
        im=scene(kind)
        b=None
        for width in (960,850,740,650,560):
            thumb=im.resize((width,round(H*width/W)),Image.Resampling.LANCZOS)
            for quality in (68,56,44,34,24,16):
                bio=io.BytesIO()
                thumb.save(bio,"WEBP",quality=quality,method=6)
                if len(bio.getvalue())<=29500:
                    b=bio.getvalue();break
            if b is not None:break
        if b is None:raise RuntimeError("Unable to compress image")
        out.append(b)
        print("MATH_VISUAL",kind,"bytes",len(b))
    return out
if __name__=="__main__":
    assert len(build())==3
