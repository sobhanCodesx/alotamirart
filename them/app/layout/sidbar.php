<!-- ============================================================ -->
<!-- ===== سایدبار طلایی-مشکی-آبی سه‌بعدی + سئو شده ===== -->
<!-- ============================================================ -->

<aside style="background: linear-gradient(145deg, #0a0e27, #1a1a3e, #0d1b2a); border-radius: 16px; padding: 25px 20px; box-shadow: 0 10px 50px rgba(212, 175, 55, 0.15), inset 0 1px 0 rgba(212, 175, 55, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); margin-bottom: 25px; direction: rtl; font-family: 'IRANSans', 'Tahoma', sans-serif; position: relative; overflow: hidden;">
    
    <!-- ===== افکت پس‌زمینه طلایی ===== -->
    <div style="position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: radial-gradient(ellipse at 30% 20%, rgba(212, 175, 55, 0.03), transparent 60%); pointer-events: none;"></div>
    
    <!-- ===== عنوان ===== -->
    <div style="position: relative; z-index: 1; text-align: center; margin-bottom: 20px;">
        <h3 style="color: #D4AF37; font-size: 20px; font-weight: 800; margin: 0; text-shadow: 0 0 30px rgba(212, 175, 55, 0.3), 0 0 60px rgba(212, 175, 55, 0.1); letter-spacing: 0.5px; position: relative; display: inline-block;">
            <span style="background: linear-gradient(135deg, #D4AF37, #F0D060); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 0 40px rgba(212, 175, 55, 0.2);">آخرین پست‌ها</span>
        </h3>
        <div style="width: 60px; height: 3px; background: linear-gradient(90deg, #D4AF37, #F0D060, #D4AF37); margin: 8px auto 0; border-radius: 10px; box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);"></div>
    </div>
    
    <!-- ===== لیست پست‌ها ===== -->
    <div style="display: flex; flex-wrap: wrap; gap: 15px; position: relative; z-index: 1;">
        <?php foreach ($side_post as $pu) { ?>
            <div style="display: flex; width: 100%; background: rgba(255,255,255,0.02); border-radius: 12px; padding: 10px; transition: all 0.4s ease; border: 1px solid rgba(212, 175, 55, 0.05); align-items: center; gap: 12px; flex-wrap: wrap;"
                 onmouseover="this.style.background='rgba(212, 175, 55, 0.05)'; this.style.borderColor='rgba(212, 175, 55, 0.3)'; this.style.boxShadow='0 4px 25px rgba(212, 175, 55, 0.08)'; this.style.transform='translateY(-2px)';"
                 onmouseout="this.style.background='rgba(255,255,255,0.02)'; this.style.borderColor='rgba(212, 175, 55, 0.05)'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                
                <!-- ===== تصویر ===== -->
                <div style="flex: 0 0 70px; max-width: 70px;">
                    <a style="text-decoration: none; display: block;" title="<?= strip_tags($pu['title']) ?>" href="<?= assets('post/' . $pu['id']) ?>">
                        <img style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px; border: 2px solid #D4AF37; transition: all 0.4s ease; box-shadow: 0 2px 15px rgba(212, 175, 55, 0.1);" 
                             src="<?= assets($pu['img']) ?>" 
                             width="70" height="70" 
                             alt="<?= strip_tags($pu['title']) ?>"
                             loading="lazy"
                             onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 0 30px rgba(212, 175, 55, 0.3)'; this.style.borderColor='#F0D060';"
                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 15px rgba(212, 175, 55, 0.1)'; this.style.borderColor='#D4AF37';">
                    </a>
                </div>
                
                <!-- ===== محتوا ===== -->
                <div style="flex: 1; min-width: 100px;">
                    <a style="text-decoration: none; color: #E0E0E0; font-weight: 700; font-size: 13px; display: block; margin-bottom: 4px; transition: all 0.3s ease; line-height: 1.5;" 
                       title="<?= strip_tags($pu['title']) ?>" 
                       href="<?= assets('post/' . $pu['id']) ?>"
                       onmouseover="this.style.color='#D4AF37'; this.style.textShadow='0 0 20px rgba(212, 175, 55, 0.2)';"
                       onmouseout="this.style.color='#E0E0E0'; this.style.textShadow='none';">
                        <?php echo limit_words(trim(strip_tags($pu['title']), "/  "), 5) ?>
                    </a>
                    <span style="color: #888; font-size: 11px; line-height: 1.6; display: block; margin-bottom: 5px;">
                        <?php echo limit_words(trim(strip_tags($pu['content']), "/  "), 8) ?>
                    </span>
                    <a style="text-decoration: none; display: inline-block; float: left;" 
                       title="<?= strip_tags($pu['title']) ?>" 
                       href="<?= assets('post/' . $pu['id']) ?>">
                        <button style="background: linear-gradient(135deg, #D4AF37, #B8960F); color: #0a0e27; border: none; border-radius: 6px; padding: 3px 14px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 15px rgba(212, 175, 55, 0.15);"
                                onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 25px rgba(212, 175, 55, 0.3)'; this.style.background='linear-gradient(135deg, #F0D060, #D4AF37)';"
                                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 15px rgba(212, 175, 55, 0.15)'; this.style.background='linear-gradient(135deg, #D4AF37, #B8960F)';">
                            ادامه ◄
                        </button>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</aside>

<!-- ============================================================ -->
<!-- ===== سایدبار برندها طلایی-مشکی-آبی سه‌بعدی ===== -->
<!-- ============================================================ -->

<aside style="background: linear-gradient(145deg, #0a0e27, #1a1a3e, #0d1b2a); border-radius: 16px; padding: 25px 20px; box-shadow: 0 10px 50px rgba(212, 175, 55, 0.15), inset 0 1px 0 rgba(212, 175, 55, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); margin-top: 25px; direction: rtl; font-family: 'IRANSans', 'Tahoma', sans-serif; position: relative; overflow: hidden;">
    
    <!-- ===== افکت ===== -->
    <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(ellipse at 70% 80%, rgba(212, 175, 55, 0.03), transparent 60%); pointer-events: none;"></div>
    
    <!-- ===== عنوان ===== -->
    <div style="position: relative; z-index: 1; text-align: center; margin-bottom: 20px;">
        <h3 style="color: #D4AF37; font-size: 20px; font-weight: 800; margin: 0; text-shadow: 0 0 30px rgba(212, 175, 55, 0.3), 0 0 60px rgba(212, 175, 55, 0.1); letter-spacing: 0.5px;">
            <span style="background: linear-gradient(135deg, #D4AF37, #F0D060); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 0 40px rgba(212, 175, 55, 0.2);">پست‌های برندها</span>
        </h3>
        <div style="width: 60px; height: 3px; background: linear-gradient(90deg, #D4AF37, #F0D060, #D4AF37); margin: 8px auto 0; border-radius: 10px; box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);"></div>
    </div>
    
    <!-- ===== لیست برندها ===== -->
    <div style="display: flex; flex-wrap: wrap; gap: 15px; position: relative; z-index: 1;">
        <?php foreach ($side_brand as $pu) { ?>
            <div style="display: flex; width: 100%; background: rgba(255,255,255,0.02); border-radius: 12px; padding: 10px; transition: all 0.4s ease; border: 1px solid rgba(212, 175, 55, 0.05); align-items: center; gap: 12px; flex-wrap: wrap;"
                 onmouseover="this.style.background='rgba(212, 175, 55, 0.05)'; this.style.borderColor='rgba(212, 175, 55, 0.3)'; this.style.boxShadow='0 4px 25px rgba(212, 175, 55, 0.08)'; this.style.transform='translateY(-2px)';"
                 onmouseout="this.style.background='rgba(255,255,255,0.02)'; this.style.borderColor='rgba(212, 175, 55, 0.05)'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                
                <!-- ===== تصویر ===== -->
                <div style="flex: 0 0 70px; max-width: 70px;">
                    <a style="text-decoration: none; display: block;" title="<?= strip_tags($pu['title']) ?>" href="<?= assets($pu['slug']."/" . $pu['id']) ?>">
                        <img style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px; border: 2px solid #D4AF37; transition: all 0.4s ease; box-shadow: 0 2px 15px rgba(212, 175, 55, 0.1);" 
                             src="<?= assets($pu['img']) ?>" 
                             width="70" height="70" 
                             alt="<?= strip_tags($pu['title']) ?>"
                             loading="lazy"
                             onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 0 30px rgba(212, 175, 55, 0.3)'; this.style.borderColor='#F0D060';"
                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 15px rgba(212, 175, 55, 0.1)'; this.style.borderColor='#D4AF37';">
                    </a>
                </div>
                
                <!-- ===== محتوا ===== -->
                <div style="flex: 1; min-width: 100px;">
                    <a style="text-decoration: none; color: #E0E0E0; font-weight: 700; font-size: 13px; display: block; margin-bottom: 4px; transition: all 0.3s ease; line-height: 1.5;" 
                       title="<?= strip_tags($pu['title']) ?>" 
                       href="<?= assets($pu['slug']."/" . $pu['id']) ?>"
                       onmouseover="this.style.color='#D4AF37'; this.style.textShadow='0 0 20px rgba(212, 175, 55, 0.2)';"
                       onmouseout="this.style.color='#E0E0E0'; this.style.textShadow='none';">
                        <?php echo limit_words(trim(strip_tags($pu['title']), "/  "), 5) ?>
                    </a>
                    <span style="color: #888; font-size: 11px; line-height: 1.6; display: block; margin-bottom: 5px;">
                        <?php echo limit_words(trim(strip_tags($pu['content']), "/  "), 8) ?>
                    </span>
                    <a style="text-decoration: none; display: inline-block; float: left;" 
                       title="<?= strip_tags($pu['title']) ?>" 
                       href="<?= assets($pu['slug']."/" . $pu['id']) ?>">
                        <button style="background: linear-gradient(135deg, #D4AF37, #B8960F); color: #0a0e27; border: none; border-radius: 6px; padding: 3px 14px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 15px rgba(212, 175, 55, 0.15);"
                                onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 25px rgba(212, 175, 55, 0.3)'; this.style.background='linear-gradient(135deg, #F0D060, #D4AF37)';"
                                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 15px rgba(212, 175, 55, 0.15)'; this.style.background='linear-gradient(135deg, #D4AF37, #B8960F)';">
                            ادامه ◄
                        </button>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</aside>