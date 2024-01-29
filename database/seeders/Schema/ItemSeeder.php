<?php

namespace Database\Seeders\Schema;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    private $englishId;
    private $arabicId;
    private $order = 1;
    private $items = [];

    public function __construct()
    {
        $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;

        $this->order = 1;
    }


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # 1 , 13
        $this->homeItems();


        # 14 , 24
        $this->aboutItems();


        # 25 , 31
        $this->contactItems();


        # 32 , 33
        $this->serviceItems();




        ###############################################################
        #                           Creating                          #
        ###############################################################

        foreach ($this->items as $item)
            $this->createItem($item['fields'], $item['details'], $item['pageGroupIds'] ?? [], $item['groupId'] ?? null, $item['parentId'] ?? null);
    }



    private function createItem($fields, $details, $pageGroupIds, $parentId = null)
    {
        // create item
        $item = Item::create($fields);

        // prepare details
        foreach ($details as $key => $detail) {
            if (is_null($detail))
                continue;

            $details[$key] = [
                'itemId' => $item->id,
                'languageId' => $detail['languageId'],
                'valueType' => $detail['valueType'],
                'key' => $detail['key'],
                'value' => $detail['value'],
                'order' => $this->order++,
            ];
        }

        // attaching details
        $item->details()->createMany($details);

        // add page group if there is no any parentId available
        if (is_null($parentId))
            foreach ($pageGroupIds as $pageGroupId)
                $item->pageGroups()->attach($pageGroupId);

        // add parent if exist
        if (!is_null($parentId))
            ItemHierarchy::create([
                'childId' => $item->id,
                'parentId' => $parentId,
            ]);
    }

    private function translationGenerator($type, $key, $value_en, $value_ar)
    {
        $translations = [];

        // English
        $translations[] = [
            'languageId' => $this->englishId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_en,
        ];

        // Arabic
        $translations[] = [
            'languageId' => $this->arabicId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_ar,
        ];


        return $translations;
    }

    private function itemFieldsGenerator($schemaId, $featureTitle, $pageGroupIds, $details)
    {
        $_details = [];

        foreach ($details as $key => $detail)
            $_details = array_merge($_details, $this->translationGenerator($detail['type'], $key, $detail['en'], $detail['ar']));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $_details,

            'pageGroupIds' => $pageGroupIds
        ];
    }



    private function homeItems()
    {
        /**
         * Slider
         */
        # ---> 1
        $this->itemFieldsGenerator(1, "slider", [1], [
            'title' => ['type' => "long_text", 'en' => "Your Trusted Partner for Businesses Seeking Reliable", 'ar' => "شريكك الموثوق به للشركات التي تبحث عن الموثوقية"],
            'video' => ['type' => "attachment", 'en' => "video/ac9b8dc1-b061-4de5-8f24-11a948f4059d.mp4", 'ar' => "video/ac9b8dc1-b061-4de5-8f24-11a948f4059d.mp4"],
        ]);
        # ---> 2
        $this->itemFieldsGenerator(1, "slider", [1], [
            'title' => ['type' => "long_text", 'en' => "Your Trusted Partner for Businesses Seeking Reliable", 'ar' => "شريكك الموثوق به للشركات التي تبحث عن الموثوقية"],
            'video' => ['type' => "attachment", 'en' => "video/ac9b8dc1-b061-4de5-8f24-11a948f4059d.mp4", 'ar' => "video/ac9b8dc1-b061-4de5-8f24-11a948f4059d.mp4"],
        ]);
        # ---> 3
        $this->itemFieldsGenerator(1, "slider", [1], [
            'title' => ['type' => "long_text", 'en' => "Your Trusted Partner for Businesses Seeking Reliable", 'ar' => "شريكك الموثوق به للشركات التي تبحث عن الموثوقية"],
            'video' => ['type' => "attachment", 'en' => "video/ac9b8dc1-b061-4de5-8f24-11a948f4059d.mp4", 'ar' => "video/ac9b8dc1-b061-4de5-8f24-11a948f4059d.mp4"],
        ]);


        /**
         * Feature Banner
         */
        $title_en = "A DISTINGUISHED LEADER IN THE TRANSPORTATION INDUSTRY";
        $title_ar = "قائد متميز في صناعة النقل";
        $description_en = "Daro Trust Company's expertise encompasses a holistic understanding of the transportation industry, spanning logistics modes, global operations, innovation, reliability, customer satisfaction, and environmental responsibility. Our commitment to excellence ensures that we are well-equipped to meet the diverse and evolving needs of our clients in the dynamic world of transportation.";
        $description_ar = "تشمل خبرة شركة Daro Trust فهمًا شاملاً لصناعة النقل، بما في ذلك الوسائط اللوجستية والعمليات العالمية والابتكار والموثوقية ورضا العملاء والمسؤولية البيئية. يضمن التزامنا بالتميز أننا مجهزون جيدًا لتلبية الاحتياجات المتنوعة والمتطورة لعملائنا في عالم النقل الديناميكي.";
        # ---> 4
        $this->itemFieldsGenerator(2, "banner", [2], [
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
        ]);


        /**
         * Features
         */
        $banner_en = "Our Expertise";
        $banner_ar = "خبرتنا";
        $title_en = "Proven Expertise";
        $title_ar = "خبرة مثبتة";
        $summary_en = "Global Logistics Understanding";
        $summary_ar = "الفهم اللوجستي العالمي";
        $description_en = "With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency. With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency.With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency. With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency. With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency.";
        $description_ar = "بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يجلب المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. مع ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust Company خبرتها، واكتسبت فهمًا عميقًا للديناميكيات المعقدة من الخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة.";
        # ---> 5
        $this->itemFieldsGenerator(3, "feature", [2], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'image' => ['type' => "image", 'en' => "img/loading-of-cargo-containers-to-airplane-at-airport-2023-11-27-05-31-46-utc.jpg", 'ar' => "img/loading-of-cargo-containers-to-airplane-at-airport-2023-11-27-05-31-46-utc.jpg"],
            'summary' => ['type' => "short_text", 'en' => $summary_en, 'ar' => $summary_ar],
            'description' => ['type' => "editor_basic", 'en' => $description_en, 'ar' => $description_ar],
        ]);
        $summary_en = "Industry Knowledge and Experience";
        $summary_ar = "المعرفة والخبرة الصناعية";
        # ---> 6
        $this->itemFieldsGenerator(3, "feature", [2], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'image' => ['type' => "image", 'en' => "img/Image-7.jpg", 'ar' => "img/Image-7.jpg"],
            'summary' => ['type' => "short_text", 'en' => $summary_en, 'ar' => $summary_ar],
            'description' => ['type' => "editor_basic", 'en' => $description_en, 'ar' => $description_ar],
        ]);
        $summary_en = "Reliability and Punctuality";
        $summary_ar = "الموثوقية والالتزام بالمواعيد";
        # ---> 7
        $this->itemFieldsGenerator(3, "feature", [2], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'image' => ['type' => "image", 'en' => "img/industrial-crane-view-from-below-moving-cargo-2023-11-27-05-03-24-utc.jpg", 'ar' => "img/industrial-crane-view-from-below-moving-cargo-2023-11-27-05-03-24-utc.jpg"],
            'summary' => ['type' => "short_text", 'en' => $summary_en, 'ar' => $summary_ar],
            'description' => ['type' => "editor_basic", 'en' => $description_en, 'ar' => $description_ar],
        ]);


        /**
         * About us
         */
        # ---> 8
        $this->itemFieldsGenerator(4, "banner", [3], [
            'title' => ['type' => "long_text", 'en' => "About us", 'ar' => "معلومات عنا"],
        ]);

        $title_en = "Proven Expertise";
        $title_ar = "خبرة مثبتة";
        $description_en = "With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency. With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency.With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency. With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency. With six years of experience in the transportation field, Daro Trust Company has honed its expertise, gaining a deep understanding of the intricate dynamics of logistics. Our seasoned professionals bring a wealth of knowledge to the table, ensuring that your transportation needs are met with precision and efficiency.";
        $description_ar = "بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يجلب المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. مع ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust Company خبرتها، واكتسبت فهمًا عميقًا للديناميكيات المعقدة من الخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة. بفضل ست سنوات من الخبرة في مجال النقل، عززت شركة Daro Trust خبرتها واكتسبت فهمًا عميقًا للديناميكيات المعقدة للخدمات اللوجستية. يقدم المتخصصون المتمرسون لدينا ثروة من المعرفة إلى الطاولة، مما يضمن تلبية احتياجات النقل الخاصة بك بدقة وكفاءة.";
        $image = "img/Image-6.jpg";
        $button_text_en = "Read More";
        $button_text_ar = "اقرأ أكثر";
        $button_url = "/about";
        # ---> 9
        $this->itemFieldsGenerator(5, "about", [3], [
            'title' => ['type' => "short_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
            'button_text' => ['type' => "long_text", 'en' => $button_text_en, 'ar' => $button_text_ar],
            'button_url' => ['type' => "long_text", 'en' => $button_url, 'ar' => $button_url],
        ]);
        
        $title_en = "Global Perspective";
        $title_ar = "المنظور العالمي";
        $description_en = "Recognizing the global nature of trade and commerce, Daro Trust Company is well-equipped to handle international logistics. Our expansive network and strategic partnerships enable us to provide seamless transportation services on a global scale, facilitating the smooth flow of goods across borders.";
        $description_ar = "إدراكًا للطبيعة العالمية للتجارة والتبادل التجاري، فإن شركة Daro Trust Company مجهزة تجهيزًا جيدًا للتعامل مع الخدمات اللوجستية الدولية. تمكننا شبكتنا الموسعة وشراكاتنا الإستراتيجية من تقديم خدمات نقل سلسة على نطاق عالمي، مما يسهل التدفق السلس للبضائع عبر الحدود.";
        $image = "img/cargo-stock-on-sea-port-2023-11-27-04-53-47-utc.jpg";
        $button_text_en = "Read More";
        $button_text_ar = "اقرأ أكثر";
        $button_url = "/about";
        # ---> 10
        $this->itemFieldsGenerator(5, "about", [3], [
            'title' => ['type' => "short_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
            'button_text' => ['type' => "long_text", 'en' => $button_text_en, 'ar' => $button_text_ar],
            'button_url' => ['type' => "long_text", 'en' => $button_url, 'ar' => $button_url],
        ]);


        /**
         * Services
         */
        # ---> 11
        $this->itemFieldsGenerator(6, "banner", [4], [
            'title' => ['type' => "long_text", 'en' => "Our Services", 'ar' => "خدماتنا"],
        ]);
        
        $banner_en = "Our Expertise";
        $banner_ar = "خبرتنا";
        $title_en = "Multimodal Transportation: Integrated Transportation Solutions That Combine Multiple Modes For Optimal Efficiency.";
        $title_ar = "النقل متعدد الوسائط: حلول النقل المتكاملة التي تجمع بين وسائط متعددة لتحقيق الكفاءة المثلى.";
        $description_en = "Recognizing the global nature of trade and commerce, Daro Trust Company is well-equipped to handle international logistics. Our expansive network and strategic partnerships enable us to provide seamless transportation services on a global scale, facilitating the smooth flow of goods across borders.";
        $description_ar = "إدراكًا للطبيعة العالمية للتجارة والتبادل التجاري، فإن شركة Daro Trust Company مجهزة تجهيزًا جيدًا للتعامل مع الخدمات اللوجستية الدولية. تمكننا شبكتنا الموسعة وشراكاتنا الإستراتيجية من تقديم خدمات نقل سلسة على نطاق عالمي، مما يسهل التدفق السلس للبضائع عبر الحدود.";
        $image = "/img/loading-cargo-into-the-ship-in-harbor-2023-11-27-04-56-46-utc.jpg";
        # ---> 12
        $this->itemFieldsGenerator(7, "about", [5, 4], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "short_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
        ]);
        
        $banner_en = "Our Expertise";
        $banner_ar = "خبرتنا";
        $title_en = "Specialized Logistics Services : Special Handling For Perishable Goods, Hazardous Materials, And Oversized Shipments.";
        $title_ar = "الخدمات اللوجستية المتخصصة: التعامل الخاص مع البضائع القابلة للتلف والمواد الخطرة والشحنات كبيرة الحجم.";
        $description_en = "Recognizing the global nature of trade and commerce, Daro Trust Company is well-equipped to handle international logistics. Our expansive network and strategic partnerships enable us to provide seamless transportation services on a global scale, facilitating the smooth flow of goods across borders.";
        $description_ar = "إدراكًا للطبيعة العالمية للتجارة والتبادل التجاري، فإن شركة Daro Trust Company مجهزة تجهيزًا جيدًا للتعامل مع الخدمات اللوجستية الدولية. تمكننا شبكتنا الموسعة وشراكاتنا الإستراتيجية من تقديم خدمات نقل سلسة على نطاق عالمي، مما يسهل التدفق السلس للبضائع عبر الحدود.";
        $image = "img/industrial-equipment-2023-11-27-05-30-46-utc.jpg";
        # ---> 13
        $this->itemFieldsGenerator(7, "about", [5, 4], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "short_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
        ]);
    }

    private function aboutItems()
    {
        /**
         * Banner
         */
        $title_en = "About us";
        $title_ar = "معلومات عنا";
        $summary_en = "Discover Daro Trust Company's Story";
        $summary_ar = "اكتشف قصة شركة Daro Trust";
        $image = "img/loading-of-cargo-containers-to-airplane-at-airport-2023-11-27-05-31-46-utc.jpg";
        # ---> 14
        $this->itemFieldsGenerator(8, "banner", [6], [
            'title' => ['type' => "short_text", 'en' => $title_en, 'ar' => $title_ar],
            'summary' => ['type' => "long_text", 'en' => $summary_en, 'ar' => $summary_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
        ]);



        /**
         * Article
         */
        $banner_en = "Daro Trust Company";
        $banner_ar = "شركة دارو ترست";
        $title_en = "Our Story";
        $title_ar = "قصتنا";
        $description_en = "Welcome to Daro Trust Company, a leading transportation company committed to delivering seamless and efficient logistics solutions. With a rich legacy spanning six years in the transportation field, we have established ourselves as a trusted partner in the dynamic world of logistics. With six years of hands-on experience, we bring a wealth of knowledge and expertise to the transportation industry. Our team is composed of skilled professionals who are committed to navigating the complexities of logistics with precision and efficiency. We leverage our in-depth understanding of various transportation modes, including road, rail, air, sea, and pipelines, to offer comprehensive and customized solutions for our clients. Goods transportation is the backbone of the global economy, and Daro Trust Company is proud to contribute to this essential process. We recognize the interconnectedness of supply chains, businesses, and consumers on local, national. and international scales. Through our strategic and reliable logistics services, we play a crucial role in driving economic activities and facilitating the seamless flow of goods across borders.";
        $description_ar = "مرحبًا بكم في شركة دارو تراست، شركة النقل الرائدة الملتزمة بتقديم حلول لوجستية سلسة وفعالة. بفضل إرث غني يمتد لستة أعوام في مجال النقل، نجحنا في ترسيخ أنفسنا كشريك موثوق به في عالم الخدمات اللوجستية الديناميكي. مع ست سنوات من الخبرة العملية، نجلب ثروة من المعرفة والخبرة إلى صناعة النقل. يتكون فريقنا من محترفين ماهرين ملتزمين بالتعامل مع التعقيدات اللوجستية بدقة وكفاءة. نحن نستفيد من فهمنا المتعمق لمختلف وسائل النقل، بما في ذلك الطرق والسكك الحديدية والجو والبحر وخطوط الأنابيب، لتقديم حلول شاملة ومخصصة لعملائنا. يعتبر نقل البضائع العمود الفقري للاقتصاد العالمي، وتفخر شركة دارو ترست بالمساهمة في هذه العملية الأساسية. نحن ندرك الترابط بين سلاسل التوريد والشركات والمستهلكين على المستوى المحلي والوطني. والمقاييس الدولية. ومن خلال خدماتنا اللوجستية الاستراتيجية والموثوقة، نلعب دورًا حاسمًا في دفع الأنشطة الاقتصادية وتسهيل التدفق السلس للبضائع عبر الحدود.";
        $image = "img/industrial-crane-view-from-below-moving-cargo-2023-11-27-05-03-24-utc.jpg";
        # ---> 15
        $this->itemFieldsGenerator(9, "article", [6], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "short_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "editor_basic", 'en' => $description_en, 'ar' => $description_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
        ]);



        /**
         * Quote
         */
        $mask = "img/logo-mask.svg";
        $content_en = "At Daro Trust Company, our mission is to redefine the standards of goods transportation by prioritizing reliability, fostering innovation, and ensuring unparalleled customer satisfaction. We understand the pivotal role transportation plays in the global economy, and we are dedicated to providing top-notch services that facilitate the smooth movement of products from manufacturers to consumers.";
        $content_ar = "مهمتنا في شركة دارو ترست هي إعادة تعريف معايير نقل البضائع من خلال إعطاء الأولوية للموثوقية وتعزيز الابتكار وضمان رضا العملاء الذي لا مثيل له. نحن ندرك الدور المحوري الذي يلعبه النقل في الاقتصاد العالمي، ونحن ملتزمون بتقديم خدمات رفيعة المستوى تسهل الحركة السلسة للمنتجات من الشركات المصنعة إلى المستهلكين.";
        # ---> 16
        $this->itemFieldsGenerator(10, "quote", [6], [
            'mask' => ['type' => "image", 'en' => $mask, 'ar' => $mask],
            'content' => ['type' => "textarea", 'en' => $content_en, 'ar' => $content_ar],
        ]);



        /**
         * Commitment
         */
        $title_en = "Our Commitment";
        $title_ar = "التزامنا";
        $description_en = "At the heart of [DARO TRUST] lies a commitment to reliability, innovation, and unparalleled customer service. We understand that every shipment holds a unique story, and we are here to ensure that each journey, short or long, is marked hy efficiency safety and satistaction";
        $description_ar = "في قلب [DARO TRUST] يكمن الالتزام بالموثوقية والابتكار وخدمة العملاء التي لا مثيل لها. نحن ندرك أن كل شحنة تحمل قصة فريدة من نوعها، ونحن هنا للتأكد من أن كل رحلة، قصيرة أو طويلة، تتميز بكفاءة السلامة والرضا";
        $image = "img/professional-business-marketing-teamwork-concept-2023-11-27-05-28-48-utc.jpg";
        # ---> 17
        $this->itemFieldsGenerator(11, "banner", [7], [
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "editor_basic", 'en' => $description_en, 'ar' => $description_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
        ]);

        # ---> 18
        $this->itemFieldsGenerator(12, "commitment", [7], [
            'commitment' => ['type' => "short_text", 'en' => "Multimodal Transportation", 'ar' => "النقل المتعدد الوسائط"],
        ]);

        # ---> 19
        $this->itemFieldsGenerator(12, "commitment", [7], [
            'commitment' => ['type' => "short_text", 'en' => "Multimodal Transportation", 'ar' => "النقل المتعدد الوسائط"],
        ]);

        # ---> 20
        $this->itemFieldsGenerator(12, "commitment", [7], [
            'commitment' => ['type' => "short_text", 'en' => "Multimodal Transportation", 'ar' => "النقل المتعدد الوسائط"],
        ]);

        # ---> 21
        $this->itemFieldsGenerator(12, "commitment", [7], [
            'commitment' => ['type' => "short_text", 'en' => "Multimodal Transportation", 'ar' => "النقل المتعدد الوسائط"],
        ]);

        # ---> 22
        $this->itemFieldsGenerator(12, "commitment", [7], [
            'commitment' => ['type' => "short_text", 'en' => "Multimodal Transportation", 'ar' => "النقل المتعدد الوسائط"],
        ]);
        # ---> 23
        $this->itemFieldsGenerator(13, "link", [7], [
            'title' => ['type' => "long_text", 'en' => "Proven Expertise", 'ar' => "خبرة مثبتة"],
            'url' => ['type' => "long_text", 'en' => "/about", 'ar' => "/about"],
        ]);

        # ---> 24
        $this->itemFieldsGenerator(13, "link", [7], [
            'title' => ['type' => "long_text", 'en' => "GLOBAL PERSPECTIVE", 'ar' => "منظور عالمي"],
            'url' => ['type' => "long_text", 'en' => "/about", 'ar' => "/about"],
        ]);
    }

    private function contactItems()
    {
        /**
         * Banner
         */
        $banner_en = "Contact Us";
        $banner_ar = "اتصل بنا";
        $title_en = "CONNECT WITH DARO TRUST COMPANY";
        $title_ar = "تواصل مع شركة دارو ترست";
        $image = "img/semi-truck-with-cargo-trailer-driving-on-highway-h-2023-11-27-05-09-51-utc.jpg";
        # ---> 25
        $this->itemFieldsGenerator(14, "banner", [8], [
            'banner' => ['type' => "short_text", 'en' => $banner_en, 'ar' => $banner_ar],
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'image' => ['type' => "image", 'en' => $image, 'ar' => $image],
        ]);


        /**
         * Message
         */
        $title_en = "Send Message";
        $title_ar = "أرسل رسالة";
        $description_en = "Feel free to reach out to us using the contact form below or through the provided email and phone number.";
        $description_ar = "لا تتردد في التواصل معنا باستخدام نموذج الاتصال أدناه أو من خلال البريد الإلكتروني ورقم الهاتف المقدم.";
        # ---> 26
        $this->itemFieldsGenerator(15, "content", [8], [
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'name_placeholder' => ['type' => "short_text", 'en' => "Name", 'ar' => "اسم"],
            'email_placeholder' => ['type' => "short_text", 'en' => "Email", 'ar' => "بريد إلكتروني"],
            'subject_placeholder' => ['type' => "short_text", 'en' => "Subject", 'ar' => "موضوع"],
            'message_placeholder' => ['type' => "short_text", 'en' => "Message", 'ar' => "رسالة"],
            'send_button_text' => ['type' => "short_text", 'en' => "Send Message", 'ar' => "أرسل رسالة"],
        ]);


        /**
         * Detail
         */
        $title_en = "Contact Detail";
        $title_ar = "تفاصيل الإتصال";
        $description_en = "Your Gateway to Seamless Logistics Solutions and Unmatched Service Excellence";
        $description_ar = "بوابتك إلى الحلول اللوجستية السلسة والتميز في الخدمة الذي لا مثيل له";
        # ---> 27
        $this->itemFieldsGenerator(16, "detail", [8], [
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "editor_basic", 'en' => $description_en, 'ar' => $description_ar],
        ]);


        /**
         * Contacts
         */
        # ---> 28
        $this->itemFieldsGenerator(17, "contact", [9], [
            'icon' => ['type' => "icon", 'en' => "ooui:map-pin", 'ar' => "ooui:map-pin"],
            'value' => ['type' => "long_text", 'en' => "101 Shorsh alley - str. No. 87 / Sulaymaniyah - Iraq", 'ar' => "101 الزقاق السادس عشر - المستوى. لا. 87 / السليمانية – العراق"],
        ]);

        # ---> 29
        $this->itemFieldsGenerator(17, "contact", [9], [
            'icon' => ['type' => "icon", 'en' => "tabler:mail-filled", 'ar' => "tabler:mail-filled"],
            'value' => ['type' => "long_text", 'en' => "info@darotrust.co", 'ar' => "info@darotrust.co"],
        ]);

        # ---> 30
        $this->itemFieldsGenerator(17, "contact", [9], [
            'icon' => ['type' => "icon", 'en' => "carbon:phone-filled", 'ar' => "carbon:phone-filled"],
            'value' => ['type' => "long_text", 'en' => "+964 770 601 8484", 'ar' => "+964 770 101 8484"],
        ]);


        /**
         * Article
         */
        $title_en = "Find us on the map";
        $title_ar = "تابعنا على الخريطة";
        $description_en = "Discover the physical location of Daro Trust Company through the interactive map below. Our office is strategically situated for your convenience.";
        $description_ar = "اكتشف الموقع الفعلي لشركة Daro Trust من خلال الخريطة التفاعلية أدناه. يقع مكتبنا في موقع استراتيجي لراحتك.";
        $location = "35.563181,45.3364555";
        # ---> 31
        $this->itemFieldsGenerator(18, "article", [10], [
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'location' => ['type' => "map", 'en' => $location, 'ar' => $location],
        ]);
    }

    private function serviceItems()
    {
        /**
         * Banner
         */
        # ---> 32
        $this->itemFieldsGenerator(19, "banner", [11], [
            'title' => ['type' => "long_text", 'en' => "Services", 'ar' => "خدمات"],
        ]);

        $title_en = "Our Values are everything";
        $title_ar = "قيمنا هي كل شيء";
        $description_en = "Return to our home page to explore more about Daro Trust Company and discover the full range of logistics solutions we offer.";
        $description_ar = "ارجع إلى صفحتنا الرئيسية لاستكشاف المزيد عن شركة Daro Trust واكتشف المجموعة الكاملة من الحلول اللوجستية التي نقدمها.";
        $button_text_en = "About us";
        $button_text_ar = "معلومات عنا";
        $button_url = "/about";
        # ---> 33
        $this->itemFieldsGenerator(20, "value", [11], [
            'title' => ['type' => "long_text", 'en' => $title_en, 'ar' => $title_ar],
            'description' => ['type' => "textarea", 'en' => $description_en, 'ar' => $description_ar],
            'button_text' => ['type' => "long_text", 'en' => $button_text_en, 'ar' => $button_text_ar],
            'button_url' => ['type' => "long_text", 'en' => $button_url, 'ar' => $button_url],
        ]);
    }
}
