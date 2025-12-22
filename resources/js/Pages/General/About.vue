<script setup>
import OtherLayout from './OtherLayout.vue';
// import { Head } from '@inertiajs/vue3';

import SeoHead from '@/components/SeoHead.vue';
import JsonLd from '@/components/JsonLd.vue';

defineOptions({ layout: OtherLayout });

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'AboutPage',
    name: 'عن بصيرة - برنامج الدراسات الإسلامية',
    description:
        'تعرف على برنامج بصيرة للدراسات الإسلامية، رؤيتنا، رسالتنا، وأهدافنا في تقديم تعليم شرعي ميسر لجميع الشرائح',
    url: 'https://basirahonline.com/about',
    mainEntity: {
        '@type': 'EducationalOrganization',
        name: 'برنامج بصيرة',
        url: 'https://basirahonline.com',
        logo: 'https://basirahonline.com/logo.png',
        description:
            'برنامج دراسات إسلامية متكاملة تجمع بين التأصيل والمعاصرة في بيئة تعليمية مرنة',
    },
};
</script>

<template>
    <div class="relative space-y-15">
        <!-- <Head title="عن بصيرة" /> -->

        <SeoHead
            title="عن بصيرة - برنامج الدراسات الإسلامية"
            description="تعرف على برنامج بصيرة للدراسات الإسلامية، رؤيتنا في نشر التعليم الشرعي الوسطي، رسالتنا وأهدافنا في بناء طالب علم راسخ"
        />
        <JsonLd :data="structuredData" />

        <!--about-->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 py-12 sm:py-20"
        >
            <div
                class="container mx-auto flex flex-col-reverse items-center gap-6 px-6 sm:gap-10 lg:flex-row"
            >
                <div
                    class="space-y-4 text-center sm:space-y-6 lg:w-1/2 lg:text-right"
                >
                    <h1 class="text-2xl font-bold text-green-600 sm:text-4xl">
                        من نحن؟
                    </h1>
                    <p class="text-lg leading-relaxed text-gray-800 sm:text-xl">
                        نحن برنامج
                        <span class="font-semibold text-green-600">بصيرة</span>،
                        نسعى لتقديم دراسات إسلامية متكاملة تجمع بين التأصيل
                        والمعاصرة في بيئة تعليمية مرنة
                    </p>
                    <Link
                        :href="route('join')"
                        class="mx-auto block w-full rounded-full bg-green-600 px-6 py-3 text-center text-lg font-semibold text-white shadow transition hover:scale-105 hover:bg-green-700 sm:w-auto sm:text-sm lg:mx-0 lg:inline-block"
                    >
                        انضم إلينا
                    </Link>
                </div>
                <div class="mx-auto w-full sm:w-3/4 lg:w-1/2">
                    <div
                        class="overflow-hidden rounded-3xl border-4 border-green-600 bg-white shadow-lg transition-transform hover:scale-105"
                    >
                        <img
                            src="@i/aboutbasirah/دبلوم6.jpg"
                            alt="عن بصيرة"
                            class="h-64 w-full object-contain sm:h-80"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- -->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-green-50 via-green-100 to-neutral-50 py-20"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 lg:grid-cols-2"
            >
                <div class="space-y-12">
                    <div class="flex flex-col items-center text-center">
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="inline-flex transform items-center justify-center text-3xl transition-transform duration-500 hover:scale-110 hover:rotate-12"
                            >
                                📖
                            </div>
                            <h2
                                class="text-lg font-bold text-[#4D88C7] sm:text-2xl"
                            >
                                رؤية برنامج بصيرة
                            </h2>
                        </div>
                        <p
                            class="max-w-2xl text-base leading-relaxed font-semibold text-gray-800 sm:text-lg"
                        >
                            تعليم شرعي ميسر لجميع الشرائح ضمن برامج ومستويات
                            محددة واضحة وتأصيلية.
                        </p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="inline-flex transform items-center justify-center text-3xl transition-transform duration-500 hover:scale-110 hover:rotate-12"
                            >
                                👥
                            </div>
                            <h2
                                class="text-lg font-bold text-[#4D88C7] sm:text-2xl"
                            >
                                الشريحة المستهدفة
                            </h2>
                        </div>
                        <p
                            class="max-w-2xl text-base leading-relaxed font-semibold text-gray-800 sm:text-lg"
                        >
                            كل مسلم حريص على تحصين نفسه عن طريق تعلم ما يجب
                            معرفته من العلم الشرعي، وكل عامل في مجال الدعوة راغب
                            في التأصيل الشرعي وزيادة المعرفة في أي مكان وزمان.
                        </p>
                    </div>
                </div>
                <div class="relative flex items-center justify-center px-4">
                    <div
                        class="aspect-[16/9] w-full max-w-4xl overflow-hidden rounded-2xl border-4 border-green-600 shadow-lg"
                    >
                        <img
                            src="@i/aboutbasirah/صور للموقع.jpg"
                            alt="رؤية البرنامج والشريحة المستهدفة"
                            class="h-full w-full object-cover object-top transition-transform duration-500 hover:scale-105"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- -->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-green-50 via-green-100 to-neutral-50 py-12 sm:py-20"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-6 px-6 sm:gap-10 lg:grid-cols-2"
            >
                <div class="space-y-4 sm:space-y-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="mb-6 inline-flex items-center justify-center rounded-full bg-green-100 p-4 text-3xl shadow-lg"
                        >
                            📜
                        </div>
                        <h2
                            class="text-lg font-bold text-[#4D88C7] sm:text-2xl"
                        >
                            الرسالة
                        </h2>

                        <p
                            class="max-w-2xl text-base leading-relaxed font-semibold text-gray-800 sm:text-lg"
                        >
                            أن يكون العلم الشرعي الضروري مؤمّن بين أيديكم أينما
                            كنتم وكيف ما يسهل عليكم.
                        </p>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <div
                            class="mb-6 inline-flex items-center justify-center rounded-full bg-green-100 p-4 text-3xl shadow-lg"
                        >
                            🌟
                        </div>
                        <h2
                            class="text-lg font-bold text-[#4D88C7] sm:text-2xl"
                        >
                            القيم
                        </h2>
                        <p
                            class="max-w-2xl text-base leading-relaxed font-semibold text-gray-800 sm:text-lg"
                        >
                            بناء صحيح، منهج سليم، فكر وسطي في مواجهة الفكر
                            المتطرف والأفكار التفريطية، ترسيخ قيم المجتمع والعيش
                            المشترك عن طريق نهج فكري صحيح.
                        </p>
                    </div>
                </div>
                <div class="relative flex items-center justify-center">
                    <div
                        class="max-h-72 w-full overflow-hidden rounded-2xl border-4 border-[#4D88C7] bg-white p-0 shadow-lg sm:max-h-80"
                    >
                        <img
                            src="@i/aboutbasirah/رسالة.png"
                            alt="الرسالة"
                            class="h-auto w-full rounded-2xl"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!--Goals-->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 py-20"
            dir="rtl"
        >
            <div class="mx-auto w-full px-6 lg:px-24">
                <div
                    class="mx-auto max-w-5xl space-y-6 text-center lg:text-right"
                >
                    <div
                        class="mb-4 flex items-center justify-center gap-4 lg:justify-start lg:gap-2"
                    >
                        <div
                            class="inline-flex items-center justify-center rounded-full bg-green-100 p-4 text-3xl shadow-lg"
                        >
                            ✔️
                        </div>
                        <h2
                            class="text-xl font-bold text-[#4D88C7] sm:text-3xl"
                        >
                            الأهداف
                        </h2>
                    </div>

                    <p
                        class="mb-6 text-lg leading-relaxed font-semibold text-gray-800"
                    >
                        نشر التعليم الشرعي الوسطي بالاستفادة من ميزات الانترنت
                        في ساحة وسائل التواصل التي تغزوها المدارس الفطرية
                        المشوهة. المشكلة تبدأ عندما يتسلل الخلل إلى الأفكار،
                        والآن ميادين الأفكار ساحاتها واسعة، وقفزاتها كبيرة،
                        وضوابطها معدومة ورجالاتها الحقة قليلة، فكان لابد من
                        العمل في ميدانها وهي ساحات التواصل التي تطرق كل باب
                        وتخترق كل حصن سليم، فتشبعت سبيل الحق، وأشكلت وتعقدت ولا
                        سبيل لحلها سوى قوله تعالى: ( قُلْ هَٰذِهِ سَبِيلِي
                        أَدْعُو إِلَى اللَّهِ ۚ عَلَىٰ بَصِيرَةٍ أَنَا وَمَنِ
                        اتَّبَعَنِي ۖ ) فلزم :
                    </p>

                    <ul
                        class="list-disc space-y-3 pr-6 text-right text-lg font-semibold text-gray-700"
                    >
                        <li>
                            تدرج واضح سليم سلس بين الثقافة الشرعية العامة
                            والتأصيل الشرعي.
                        </li>
                        <li>إعداد صحيح ومنهاج تأصيلي مبسط.</li>
                        <li>
                            معلومات شرعية لابد منها لكل مسلم أو مهتم في العمل
                            الدعوي.
                        </li>
                        <li>
                            رفع سوية العاملين في الدعوة لتحقيق مكنة علمية أعلى.
                        </li>
                        <li>
                            مهارات حياتية ووسائل جديدة لفن الدعوة وإيصال
                            المعلومات.
                        </li>
                        <li>تحقيق فرصة الدراسة الشرعية لغير المتفرغين.</li>
                        <li>
                            الاستفادة من وسائل التواصل في عملية التعليم ونشرها
                            في أبعد مدى.
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- ProgramBasirah1-->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 py-12 sm:py-16"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-start gap-8 px-6 lg:grid-cols-2 lg:gap-12"
            >
                <div class="space-y-4 text-center lg:text-right">
                    <div
                        class="flex items-center justify-center gap-4 lg:justify-start"
                    >
                        <div
                            class="inline-flex transform items-center justify-center text-3xl transition-transform duration-500 hover:scale-110 hover:rotate-12"
                        >
                            💡
                        </div>
                        <h1
                            class="text-xl font-bold text-[#4D88C7] sm:text-3xl"
                        >
                            عن برنامج بصيرة
                        </h1>
                    </div>
                    <p
                        class="text-lg leading-relaxed font-semibold text-gray-800"
                    >
                        يتألف برنامج بصيرة من أربعة مستويات دراسية تندرج من
                        المعلومات المفتاحية البسيطة لكل علم ضمن مستويات أربعة،
                        إلى أن نصل إلى البناء التأصيلي الشرعي، فيتمكن طالب العلم
                        من العلوم الشرعية الأساسية.
                    </p>
                </div>
                <div class="space-y-4 text-center lg:text-right">
                    <div
                        class="flex items-center justify-center gap-4 lg:justify-start"
                    >
                        <div
                            class="inline-flex transform items-center justify-center text-3xl transition-transform duration-500 hover:scale-110 hover:rotate-12"
                        >
                            📚
                        </div>
                        <h1
                            class="text-xl font-bold text-[#4D88C7] sm:text-3xl"
                        >
                            ماذا يتضمن برنامج بصيرة؟
                        </h1>
                    </div>
                    <p
                        class="text-lg leading-relaxed font-semibold text-gray-800"
                    >
                        يتضمن البرنامج خمسة علوم رئيسية: (القرآن الكريم وعلومه –
                        العقيدة وعلومها – الحديث الشريف وعلومه – الفقه وأصوله –
                        والنحو وعلومه)، بالإضافة للمواد الداعمة والقضايا الفكرية
                        التي تُعنى ببناء شخصية سليمة مؤثرة وواعية ذات منهج صحيح
                        وفكر متوازن للمتغيرات المعاصرة.
                    </p>
                </div>
            </div>
        </section>

        <!-- ProgramBasirah2-->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 py-12"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-start gap-12 px-6 lg:grid-cols-2 lg:items-start"
            >
                <div
                    class="relative flex items-center justify-center lg:order-first"
                >
                    <div
                        class="w-full max-w-lg overflow-hidden rounded-2xl border-4 border-[#4D88C7] bg-white shadow-lg"
                    >
                        <img
                            src="@i/aboutbasirah/2.jpg"
                            alt="عن برنامج بصيرة"
                            class="h-auto w-full object-contain"
                        />
                    </div>
                </div>
                <div class="space-y-6 text-center lg:text-right">
                    <h1
                        class="mb-4 text-center text-xl font-bold text-[#4D88C7] sm:text-3xl lg:text-right"
                    >
                        على ماذا يعتمد برنامج بصيرة؟
                    </h1>
                    <div
                        class="max-w-2xl space-y-4 text-lg leading-relaxed font-semibold text-gray-800"
                    >
                        <p>
                            يعتمد البرنامج على محاضرات مكانية يقدمها أهل
                            الاختصاص من العلماء ممن لديهم اهتمام بالعلم الشرعي
                            وربطه بالجوانب التربوية وغيرهم من المربين والدعاة
                            المتميزين، كل حسب تخصصه
                        </p>
                        <p>
                            حيث يقدم بواقع محاضرتين أسبوعياً لمدة ساعة ونصف
                            تقريباً، وهكذا كل كتاب وما يليه ضمن خطة أكاديمية
                            واضحة وتسلسل منهجي صحيح ومعتمد
                        </p>
                        <p>
                            وقد رُوعي في هذا المنهج بناء الإنسان في الجانب
                            الشرعي والتربوي والنفسي والأسري، لذلك تم إضافة
                            الدورات الإثرائية الداعمة
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
