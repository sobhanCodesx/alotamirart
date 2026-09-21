<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | ویرایش پست</title>

    <script src="//cdn.ckeditor.com/4.25.1-lts/full/ckeditor.js"></script>

    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
    
    <style>
        /* ===== استایل طلایی-آبی ===== */
        body {
            background: #0a0e27 !important;
        }
        
        .content-wrapper {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%);
            padding: 30px 0;
            min-height: 100vh;
        }
        
        .form-container {
            background: rgba(10, 14, 39, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid #FFD700;
            border-radius: 15px;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.2), inset 0 0 50px rgba(0, 100, 255, 0.05);
            padding: 30px;
        }
        
        .form-label {
            color: #FFD700 !important;
            font-weight: 700 !important;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
            margin-top: 20px !important;
        }
        
        .form-control-custom {
            background: rgba(10, 14, 39, 0.8) !important;
            color: #E0E0E0 !important;
            border: 2px solid #FFD700 !important;
            border-radius: 10px !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }
        
        .form-control-custom:focus {
            border-color: #4A90D9 !important;
            box-shadow: 0 0 50px rgba(74, 144, 217, 0.2), 0 0 30px rgba(255, 215, 0, 0.1) !important;
            outline: none !important;
        }
        
        .form-control-custom::placeholder {
            color: #666 !important;
        }
        
        .form-control-custom option {
            background: #0a0e27 !important;
            color: #E0E0E0 !important;
        }
        
        .upload-section {
            background: rgba(255, 215, 0, 0.05);
            border: 2px dashed #FFD700 !important;
            border-radius: 16px !important;
            padding: 25px !important;
            margin-top: 20px !important;
            transition: all 0.3s ease;
        }
        
        .upload-section:hover {
            background: rgba(255, 215, 0, 0.08);
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.05);
        }
        
        .upload-section h5 {
            color: #FFD700 !important;
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
            font-weight: 700 !important;
            margin-bottom: 15px !important;
        }
        
        .btn-upload {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            color: #0a0e27 !important;
            border: none !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3) !important;
            padding: 10px 30px !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-upload:hover {
            transform: scale(1.05);
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.5) !important;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #4A90D9, #2E6BB0) !important;
            color: #FFFFFF !important;
            border: none !important;
            box-shadow: 0 0 30px rgba(74, 144, 217, 0.3) !important;
            padding: 12px !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
            margin-top: 20px !important;
        }
        
        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 0 50px rgba(74, 144, 217, 0.5) !important;
        }
        
        .upload-status {
            color: #FFD700 !important;
            margin-right: 15px !important;
            font-weight: 600 !important;
        }
        
        .preview-box {
            background: rgba(10, 14, 39, 0.8) !important;
            border: 2px solid #FFD700 !important;
            border-radius: 10px !important;
            padding: 15px !important;
            text-align: center !important;
        }
        
        .preview-box img {
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            border: 2px solid #4A90D9;
        }
        
        .preview-box p {
            color: #4A90D9 !important;
            font-size: 12px !important;
            margin-top: 5px !important;
        }
        
        /* ===== CKEditor استایل ===== */
        .cke {
            border: 2px solid #FFD700 !important;
            border-radius: 10px !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.05) !important;
        }
        
        .cke_top {
            background: rgba(10, 14, 39, 0.9) !important;
            border-bottom: 1px solid #FFD700 !important;
        }
        
        .cke_bottom {
            background: rgba(10, 14, 39, 0.9) !important;
            border-top: 1px solid #FFD700 !important;
        }
        
        .cke_toolgroup {
            background: rgba(10, 14, 39, 0.8) !important;
            border: 1px solid #FFD700 !important;
        }
        
        .cke_button {
            color: #FFD700 !important;
        }
        
        .cke_button:hover {
            background: rgba(255, 215, 0, 0.1) !important;
        }
        
        .cke_button_icon {
            filter: brightness(0) invert(1) sepia(1) saturate(5) hue-rotate(360deg) !important;
        }
        
        .cke_contents {
            background: rgba(10, 14, 39, 0.8) !important;
            color: #E0E0E0 !important;
        }
        
        /* ===== اسکرول ===== */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #0a0e27, #1a1a3e);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #FFD700, #FFA500);
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4A90D9, #FFD700);
            box-shadow: 0 0 50px rgba(74, 144, 217, 0.5);
        }
        
        /* ===== انیمیشن ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-container {
            animation: fadeIn 0.5s ease;
        }
        
        /* ===== ریسپانسیو ===== */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px !important;
            }
            .upload-section {
                padding: 15px !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
    </div>
    
    <div class="content-wrapper">
        <div class="container">
            <div class="form-container">
                
                <form action="<?= assets('admin/posts/updated/' . $post['id']) ?>" method="POST" enctype="multipart/form-data">
                    
                    <!-- ===== عنوان ===== -->
                    <label class="form-label" for="title">
                        <i class="fas fa-heading" style="color: #FFD700;"></i> عنوان مقاله
                    </label>
                    <input value="<?= htmlspecialchars($post['title']) ?>" 
                           type="text" 
                           name="title" 
                           id="title"
                           class="form-control form-control-custom" 
                           placeholder="عنوان مقاله را وارد کنید..."
                           required>

                    <!-- ===== چکیده ===== -->
                    <label class="form-label" for="description">
                        <i class="fas fa-paragraph" style="color: #FFD700;"></i> چکیده
                    </label>
                    <input value="<?= htmlspecialchars($post['description']) ?>" 
                           type="text" 
                           name="description" 
                           id="description"
                           class="form-control form-control-custom" 
                           placeholder="خلاصه مطلب را وارد کنید...">

                    <!-- ===== شماره تماس ===== -->
                    <label class="form-label" for="contact_number">
                        <i class="fas fa-phone" style="color: #FFD700;"></i> شماره تماس دلخواه
                    </label>
                    <input type="text" 
                           name="contact_number" 
                           id="contact_number" 
                           class="form-control form-control-custom" 
                           placeholder="مثال: 09121234567" 
                           value="<?= isset($post['contact_number']) ? htmlspecialchars($post['contact_number']) : '' ?>">

                    <!-- ===== تصویر ===== -->
                    <label class="form-label" for="img">
                        <i class="fas fa-image" style="color: #FFD700;"></i> انتخاب تصویر شاخص
                    </label>
                    <input type="file" 
                           name="img" 
                           id="img"
                           class="form-control form-control-custom" 
                           accept="image/*">

                    <!-- ============================================================ -->
                    <!-- بخش آپلود عکس اختصاصی                                        -->
                    <!-- ============================================================ -->
                    <div class="upload-section">
                        <h5><i class="fas fa-cloud-upload-alt" style="color: #4A90D9;"></i> آپلود عکس برای مقاله</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 14px;">انتخاب عکس:</label>
                                <input type="file" id="uploadImage" class="form-control form-control-custom" accept="image/*">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size: 14px;">عرض (پیکسل):</label>
                                <input type="number" id="imageWidth" class="form-control form-control-custom" value="800" min="50">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" style="font-size: 14px;">ارتفاع (پیکسل):</label>
                                <input type="number" id="imageHeight" class="form-control form-control-custom" value="600" min="50">
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 14px;">متن Alt (سئو):</label>
                                <input type="text" id="imageAlt" class="form-control form-control-custom" placeholder="توضیح عکس برای سئو...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 14px;">تراز:</label>
                                <select id="imageAlign" class="form-control form-control-custom">
                                    <option value="center">وسط</option>
                                    <option value="right">راست</option>
                                    <option value="left">چپ</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size: 14px;">لینک (اختیاری):</label>
                                <input type="text" id="imageLink" class="form-control form-control-custom" placeholder="https://...">
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-upload" onclick="uploadAndInsertImage()">
                                <i class="fas fa-upload"></i> آپلود و درج در مقاله
                            </button>
                            <span id="uploadStatus" class="upload-status"></span>
                        </div>
                        
                        <div id="imagePreview" class="preview-box mt-3" style="display:none;">
                            <img id="previewImg" src="" alt="پیش‌نمایش عکس">
                            <p><i class="fas fa-eye"></i> پیش‌نمایش عکس</p>
                        </div>
                    </div>

                    <!-- ===== ادیتور ===== -->
                    <label class="form-label" for="editor1">
                        <i class="fas fa-edit" style="color: #FFD700;"></i> محتوای مقاله
                    </label>
                    <div class="mt-2">
                        <textarea name="content" id="editor1">
                            <?= htmlspecialchars($post['content']) ?>
                        </textarea>
                    </div>

                    <!-- ===== تگ‌ها ===== -->
                    <label class="form-label" for="tags">
                        <i class="fas fa-tags" style="color: #FFD700;"></i> کلمات مرتبط (تگ‌ها)
                    </label>
                    <input type="text" 
                           value="<?= htmlspecialchars($post['tags']) ?>" 
                           name="tags" 
                           id="tags"
                           placeholder="تگ‌ها را با کاما جدا کنید..." 
                           class="form-control form-control-custom">

                    <!-- ===== دسته‌بندی ===== -->
                    <label class="form-label" for="post_id">
                        <i class="fas fa-folder" style="color: #FFD700;"></i> دسته‌بندی
                    </label>
                    <select name="post_id" id="post_id" class="form-control form-control-custom">
                        <?php foreach ($menus as $m) { ?>
                            <option value="<?= $m['id'] ?>" <?php if ($post['post_id'] == $m['id']) { ?>selected<?php } ?>>
                                <?= htmlspecialchars($m['title']) ?>
                            </option>
                        <?php } ?>
                    </select>

                    <!-- ===== دکمه ارسال ===== -->
                    <button type="submit" class="btn btn-submit btn-block">
                        <i class="fas fa-save"></i> بروزرسانی مقاله
                    </button>
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        // =============================================
        // راه‌اندازی CKEditor
        // =============================================
        CKEDITOR.replace('editor1', {
            language: 'fa',
            contentsLangDirection: 'rtl',
            height: 500,
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'Undo', 'Redo'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'] },
                { name: 'align', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'lists', items: ['NumberedList', 'BulletedList'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Table', 'HorizontalRule', 'SpecialChar'] },
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });

        // =============================================
        // آپلود و درج عکس در ادیتور
        // =============================================
        function uploadAndInsertImage() {
            const fileInput = document.getElementById('uploadImage');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('لطفاً یک عکس انتخاب کنید.');
                return;
            }

            const status = document.getElementById('uploadStatus');
            status.textContent = '⏳ در حال آپلود...';
            status.style.color = '#4A90D9';

            const formData = new FormData();
            formData.append('upload', file);
            formData.append('width', document.getElementById('imageWidth').value);
            formData.append('height', document.getElementById('imageHeight').value);
            formData.append('alt', document.getElementById('imageAlt').value);
            formData.append('align', document.getElementById('imageAlign').value);
            formData.append('link', document.getElementById('imageLink').value);

            fetch('<?= assets('admin/upload-image-ajax.php') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    status.textContent = '✅ آپلود موفق!';
                    status.style.color = '#FFD700';
                    
                    document.getElementById('previewImg').src = data.url;
                    document.getElementById('imagePreview').style.display = 'block';
                    
                    const editor = CKEDITOR.instances.editor1;
                    editor.insertHtml(data.html);
                    
                    fileInput.value = '';
                    document.getElementById('imageAlt').value = '';
                    document.getElementById('imageLink').value = '';
                    
                    setTimeout(() => {
                        status.textContent = '';
                    }, 3000);
                } else {
                    status.textContent = '❌ ' + data.error;
                    status.style.color = '#FF4444';
                }
            })
            .catch(error => {
                status.textContent = '❌ خطا در ارتباط با سرور';
                status.style.color = '#FF4444';
                console.error(error);
            });
        }

        // =============================================
        // پیش‌نمایش عکس
        // =============================================
        document.getElementById('uploadImage').addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>