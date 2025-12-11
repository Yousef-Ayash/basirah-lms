<template>
    <div class="min-h-screen space-y-20 bg-neutral-50 px-4 py-16">
        <!-- <Head title="المواد الرئيسية" /> -->
        <SeoHead
            title="المواد الرئيسية"
            description="اكتشف المواد الأساسية في برنامج بصيرة: القرآن الكريم وعلومه، الحديث النبوي الشريف، العقيدة الإسلامية، الفقه وأصوله، واللغة العربية"
        />
        <JsonLd :data="structuredData" />

        <h1
            class="mt-2 text-center text-3xl font-bold text-green-600 md:text-4xl"
        >
            المواد الرئيسية
        </h1>
        <!-- Cards-->
        <div class="container mx-auto flex flex-col gap-10">
            <section
                v-for="(subject, index) in subjects"
                :key="index"
                class="relative flex transform flex-col items-center gap-8 rounded-3xl bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 p-6 shadow-lg transition duration-500 hover:scale-[1.02] hover:shadow-2xl md:flex-row-reverse"
            >
                <div class="flex-shrink-0">
                    <div
                        class="overflow-hidden rounded-2xl border-4 border-green-600 bg-white p-2 shadow-lg"
                    >
                        <img
                            :src="subject.img"
                            :alt="subject.title"
                            class="h-[200px] w-[300px] rounded-xl object-cover transition-transform duration-500 hover:scale-110"
                        />
                    </div>
                </div>
                <div class="flex-1 text-right">
                    <h3
                        class="mb-4 text-2xl font-bold text-green-600 md:text-3xl"
                    >
                        {{ subject.title }}
                    </h3>
                    <p class="text-lg leading-relaxed text-gray-800">
                        {{ subject.text }}
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import OtherLayout from './OtherLayout.vue';
import { Head } from '@inertiajs/vue3';
import SeoHead from '@/components/SeoHead.vue';
import JsonLd from '@/components/JsonLd.vue';

defineOptions({ layout: OtherLayout });

const subjects = [
    {
        title: 'القرآن الكريم وعلومه',
        img: 'images/subjects/القرآن.jpg',
        text: 'لمّا كان كتاب الله تعالى المنهاج والدليل المنزّل على خير الخلق سيدنا محمد صلى الله عليه وسلم، كان لا بدُّ للمسلم من الاغتراف منه ما استطاع، يتعلّم قراءته وتجويده، وبديع معانيه وتفسيره وأسباب نزوله، وإعجازه وحكمته والناسخ منه والمنسوخ، ليتزوّد منه أحسن الزّاد من فهم وعلم وعمل، ويلقى مولاه وهو راض عنه',
        bg: [
            'absolute top-0 left-0 w-32 h-32 bg-green-200 rounded-full opacity-20 blur-3xl -z-10',
            'absolute bottom-0 right-0 w-48 h-48 bg-blue-200 rounded-full opacity-20 blur-3xl -z-10',
        ],
    },
    {
        title: 'الحديث النبوي الشريف',
        img: 'images/subjects/الحديث.jpg',
        text: 'يكتسب هذا العلم الشرف الرفيع من شرف صاحب الحديث سيدنا محمد صلى الله عليه وسلم، فالحديث النبوي مشكاة الأنوار الربانية المنزلة على صاحب الرتبة العلية، وهو ثاني مصادر التشريع وثاني أدلّة الأحكام ومادّة علوم الفقه وأصوله وأصول الدّين، وتبحث علومه في اتّصال السند وعدالة الرّواة، وضبطهم، والسلامة من الشذوذ والعلّة الخلفيّة القادحة، ليصلنا حديث رسولنا عليه الصلاة والسلام صحيحاً فصيحاً يدلّنا على صلاح أمر ديننا ودنيانا وآخرتنا',
        bg: [
            'absolute top-0 left-0 w-32 h-32 bg-yellow-200 rounded-full opacity-20 blur-3xl -z-10',
            'absolute bottom-0 right-0 w-48 h-48 bg-pink-200 rounded-full opacity-20 blur-3xl -z-10',
        ],
    },

    {
        title: 'العقيدة الإسلامية',
        img: 'images/subjects/العقيدة.jpg',
        text: 'صحة الاعتقاد أساس لصحة العمل ونجاة العامل، كما هو حال الشجرة مع جذورها، لذلك كان علم العقيدة الإسلامية عماداً هاماً ليكون منطلق اعتقاد المسلم سليماً ولتكون فطرته نقيّة محصّنة بالأدلة العقلية والنقلية من هجمات أهل الهوى والإلحاد والانحراف، وليكون قادراً على تبليغ النّاس السعادة الرّبانيّة التي أحبها الله تعالى لعباده في الدّارين',
        bg: [
            'absolute top-0 left-0 w-32 h-32 bg-purple-200 rounded-full opacity-20 blur-3xl -z-10',
            'absolute bottom-0 right-0 w-48 h-48 bg-green-200 rounded-full opacity-20 blur-3xl -z-10',
        ],
    },
    {
        title: 'الفقه وأصوله',
        img: 'images/subjects/الفقه.jpg',
        text: 'يتناول علم الفقه الأحكام الشرعية المتعلقة بطهارة الإنسان و حياته ومماته، وعبادته ومعاملاته، زواجه وطلاقه، ومأكله ومشربه وملبسه وجميع شؤونه، لتكون عبوديته لبارئه جل جلاله، وعلاقته بخلق الله تعالى مضبوطة بالشريعة السّمحة الحكيمة.وأما علم أصول الفقه فهو علم يختص بالأدلة الشرعية التي تستمد منها الأحكام الفقهية { واجب، مندوب، مباح، مكروه، حرام، غير ذلك …} ، وطرق ذلك الاستمداد، وحال المجتهد القائم على ذلك العمل العظيم الذي حفظ الله تعالى به دينه، وهو علم بحاجة إلى باقة واسعة من العلوم الأُخرى التي تكون عوناً للمجتهد في سبيل تبيين أحكام الشّارع جلّ جلاله لعباده المؤمنين',
        bg: [
            'absolute top-0 left-0 w-32 h-32 bg-red-200 rounded-full opacity-20 blur-3xl -z-10',
            'absolute bottom-0 right-0 w-48 h-48 bg-indigo-200 rounded-full opacity-20 blur-3xl -z-10',
        ],
    },
    {
        title: 'اللغة العربية',
        img: 'images/subjects/عربي.png',
        text: 'لغة عظيمة، ونعمة جسيمة خص بها الله تعالى العرب واختارها لهم فكانت طرازاً بديعاً جمع اختصار العبارة إلى جمال الإشارة، وسعة المعاني إلى طرب المباني، وحقّ لها أن تتشرف بنزول القرآن بلسانها المبين، وأن تكون لغة أهل الجنة، فلا بدّ لأهل هذه اللغة من تعلّم صرفها ونحوها، بنائها وإعرابها، بيانها وبديعها، عروضها وقوافيها، كما لا يعقل منهم أن يتخلوا عن عنوان شخصيتهم وهويتهم الإسلامية، ورمز ثقافتهم ووعاء علومهم وتاريخهم',
        bg: [
            'absolute top-0 left-0 w-32 h-32 bg-blue-200 rounded-full opacity-20 blur-3xl -z-10',
            'absolute bottom-0 right-0 w-48 h-48 bg-yellow-200 rounded-full opacity-20 blur-3xl -z-10',
        ],
    },
];

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'المواد الرئيسية - برنامج بصيرة',
    description:
        'تعرف على المواد الرئيسية في برنامج بصيرة: القرآن الكريم، الحديث الشريف، العقيدة، الفقه، واللغة العربية',
    url: 'https://basirahonline.com/subjects',
    hasPart: subjects.map((subject, index) => ({
        '@type': 'Course',
        name: subject.title,
        description: subject.text,
        provider: {
            '@type': 'EducationalOrganization',
            name: 'برنامج بصيرة',
            url: 'https://basirahonline.com',
        },
    })),
};
</script>
