<?php
/** Homepage-only visual mapping. Never changes canonical category names or routing. */
declare(strict_types=1);

/** @return array{image:string,icon:string,description:string} */
function aloHomeVisualForBrand(string $title): array
{
    $rules = [
        ['~یخچال|فریزر~u','refrigerator','❄','تعمیر انواع یخچال و فریزر'],
        ['~لباسشویی|لباس‌شویی|لباس شویی~u','washer','⚙','عیب‌یابی و تعمیر انواع لباسشویی'],
        ['~تلویزیون|تلوزیون~u','television','✦','آموزش و تعمیر مشکلات تلویزیون'],
        ['~پشتیبانی|مشاوره~u','support','☏','راهنمایی و ارتباط با پشتیبانی'],
        ['~پکیج|آبگرمکن~u','boiler','♨','عیب‌یابی و تعمیر انواع پکیج'],
        ['~کولر|تهویه~u','air-conditioner','❋','نصب، سرویس و تعمیر کولر گازی'],
        ['~طراحی سایت|طراحی وب|o3am~iu','website','⌘','طراحی و راه‌اندازی سایت حرفه‌ای'],
        ['~متخصص|کارشناس~u','expert','▤','معرفی متخصصان و مطالب تخصصی'],
        ['~تبلیغات|آگهی~u','advertising','⌁','معرفی خدمات و کسب‌وکارها'],
        ['~آموزش|یادگیری~u','education','▣','دوره‌ها و راهنماهای کاربردی'],
        ['~مجلات|مجله|مقالات علمی~u','science','⚗','مطالب علمی و مجلات معتبر'],
        ['~خبر|اخبار~u','news','▥','خبرها و رویدادهای روز کشور'],
    ];
    foreach ($rules as [$pattern, $name, $icon, $description]) {
        if (preg_match($pattern, $title) === 1) {
            return [
                'image' => 'public/src/img/home-premium/' . $name . '.webp',
                'icon' => $icon,
                'description' => $description,
            ];
        }
    }
    return [
        'image' => 'public/src/img/home-premium/website.webp',
        'icon' => '↗',
        'description' => 'آشنایی با خدمات و مقالات مرتبط',
    ];
}
