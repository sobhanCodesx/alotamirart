<footer style="background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%); color: #fff; padding: 60px 0 30px; margin-top: 50px; border-top: 4px solid #D4AF37; direction: rtl; font-family: 'IRANSans', 'Tahoma', sans-serif;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between;">
            
            <!-- ===== ستون اول: درباره ما ===== -->
            <div style="flex: 1; min-width: 250px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span style="color: #D4AF37; font-size: 28px;">✦</span>
                    <h3 style="color: #D4AF37; font-size: 22px; font-weight: 800; margin: 0; text-shadow: 0 0 30px rgba(212, 175, 55, 0.2);">درباره ما</h3>
                </div>
                <div style="width: 60px; height: 3px; background: linear-gradient(90deg, #D4AF37, #F0D060); margin-bottom: 18px; border-radius: 10px;"></div>
                <p style="color: #c0c0c0; font-size: 15px; line-height: 2; text-align: justify; margin: 0;">
                    <?php echo isset($dataFooter['about_description']) ? strip_tags($dataFooter['about_description']) : 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است.'; ?>
                </p>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <a href="#" style="color: #D4AF37; font-size: 22px; transition: all 0.3s ease; display: inline-block;">📱</a>
                    <a href="#" style="color: #D4AF37; font-size: 22px; transition: all 0.3s ease; display: inline-block;">📧</a>
                    <a href="#" style="color: #D4AF37; font-size: 22px; transition: all 0.3s ease; display: inline-block;">💬</a>
                </div>
            </div>
            
            <!-- ===== ستون دوم: لینک‌های سریع ===== -->
            <div style="flex: 1; min-width: 180px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span style="color: #D4AF37; font-size: 28px;">◆</span>
                    <h3 style="color: #D4AF37; font-size: 22px; font-weight: 800; margin: 0; text-shadow: 0 0 30px rgba(212, 175, 55, 0.2);">دسترسی سریع</h3>
                </div>
                <div style="width: 60px; height: 3px; background: linear-gradient(90deg, #D4AF37, #F0D060); margin-bottom: 18px; border-radius: 10px;"></div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 12px;"><a href="#" style="color: #c0c0c0; text-decoration: none; font-size: 15px; transition: all 0.3s ease; display: inline-block; padding: 5px 0; border-bottom: 1px solid transparent;">🏠 صفحه اصلی</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: #c0c0c0; text-decoration: none; font-size: 15px; transition: all 0.3s ease; display: inline-block; padding: 5px 0; border-bottom: 1px solid transparent;">📖 وبلاگ</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: #c0c0c0; text-decoration: none; font-size: 15px; transition: all 0.3s ease; display: inline-block; padding: 5px 0; border-bottom: 1px solid transparent;">📞 تماس با ما</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: #c0c0c0; text-decoration: none; font-size: 15px; transition: all 0.3s ease; display: inline-block; padding: 5px 0; border-bottom: 1px solid transparent;">📄 قوانین</a></li>
                </ul>
            </div>
            
            <!-- ===== ستون سوم: اطلاعات تماس ===== -->
            <div style="flex: 1; min-width: 220px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span style="color: #D4AF37; font-size: 28px;">✧</span>
                    <h3 style="color: #D4AF37; font-size: 22px; font-weight: 800; margin: 0; text-shadow: 0 0 30px rgba(212, 175, 55, 0.2);">تماس با ما</h3>
                </div>
                <div style="width: 60px; height: 3px; background: linear-gradient(90deg, #D4AF37, #F0D060); margin-bottom: 18px; border-radius: 10px;"></div>
                
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <span style="background: rgba(212, 175, 55, 0.15); padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#D4AF37" viewBox="0 0 16 16">
                            <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                        </svg>
                    </span>
                    <span style="color: #c0c0c0; font-size: 14px;"><?php echo isset($dataFooter['email']) ? strip_tags($dataFooter['email']) : 'info@site.com'; ?></span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <span style="background: rgba(212, 175, 55, 0.15); padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#D4AF37" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                        </svg>
                    </span>
                    <span style="color: #c0c0c0; font-size: 14px;"><?php echo isset($dataFooter['phon']) ? strip_tags($dataFooter['phon']) : '0912 123 4567'; ?></span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <span style="background: rgba(212, 175, 55, 0.15); padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#D4AF37" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
                        </svg>
                    </span>
                    <span style="color: #c0c0c0; font-size: 14px;"><?php echo isset($dataFooter['instagram']) ? '@' . strip_tags($dataFooter['instagram']) : '@instagram'; ?></span>
                </div>
            </div>
            
            <!-- ===== ستون چهارم: خبرنامه ===== -->
            <div style="flex: 1; min-width: 220px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span style="color: #D4AF37; font-size: 28px;">✉</span>
                    <h3 style="color: #D4AF37; font-size: 22px; font-weight: 800; margin: 0; text-shadow: 0 0 30px rgba(212, 175, 55, 0.2);">خبرنامه</h3>
                </div>
                <div style="width: 60px; height: 3px; background: linear-gradient(90deg, #D4AF37, #F0D060); margin-bottom: 18px; border-radius: 10px;"></div>
                <p style="color: #c0c0c0; font-size: 14px; line-height: 1.8; margin: 0 0 15px 0;">
                    برای دریافت آخرین مطالب و تخفیف‌ها عضو شوید.
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="email" placeholder="ایمیل خود را وارد کنید..." style="flex: 1; min-width: 150px; padding: 12px 16px; border: 2px solid #D4AF37; border-radius: 10px; background: rgba(255,255,255,0.05); color: #fff; outline: none; transition: all 0.3s ease; font-size: 14px;">
                    <button style="background: linear-gradient(135deg, #D4AF37, #F0D060); color: #0a0e27; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.3s ease; white-space: nowrap;">عضویت</button>
                </div>
            </div>
            
        </div>
        
        <!-- ===== خط پایین فوتر ===== -->
        <div style="border-top: 1px solid rgba(212, 175, 55, 0.2); margin-top: 40px; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <p style="color: #888; font-size: 14px; margin: 0;">
                © 1404 تمامی حقوق محفوظ است | طراحی و توسعه توسط <span style="color: #D4AF37; font-weight: 700;">تیم طلایی</span>
            </p>
            <div style="display: flex; gap: 15px;">
                <a href="#" style="color: #888; text-decoration: none; font-size: 13px; transition: all 0.3s ease;">حریم خصوصی</a>
                <a href="#" style="color: #888; text-decoration: none; font-size: 13px; transition: all 0.3s ease;">|</a>
                <a href="#" style="color: #888; text-decoration: none; font-size: 13px; transition: all 0.3s ease;">قوانین</a>
                <a href="#" style="color: #888; text-decoration: none; font-size: 13px; transition: all 0.3s ease;">|</a>
                <a href="#" style="color: #888; text-decoration: none; font-size: 13px; transition: all 0.3s ease;">پشتیبانی</a>
            </div>
        </div>
        
    </div>
</footer>