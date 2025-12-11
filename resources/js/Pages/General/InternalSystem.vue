<script setup>
import OtherLayout from './OtherLayout.vue';
// import { Head } from '@inertiajs/vue3';
import SeoHead from '@/components/SeoHead.vue';
import JsonLd from '@/components/JsonLd.vue';

defineOptions({ layout: OtherLayout });

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: 'النظام الداخلي - برنامج بصيرة',
    description:
        'تعرف على النظام الداخلي لبرنامج بصيرة، شروط التسجيل والانتساب، الدوام والحضور، الدراسة والامتحانات',
    url: 'https://basirahonline.com/internal-system',
    breadcrumb: {
        '@type': 'BreadcrumbList',
        itemListElement: [
            {
                '@type': 'ListItem',
                position: 1,
                name: 'الرئيسية',
                item: 'https://basirahonline.com',
            },
            {
                '@type': 'ListItem',
                position: 2,
                name: 'النظام الداخلي',
                item: 'https://basirahonline.com/internal-system',
            },
        ],
    },
};
</script>

<template>
    <div
        class="from-green-20 relative min-h-screen space-y-20 bg-gradient-to-br via-green-50 to-neutral-100 px-4 py-16"
    >
        <!-- <Head title="النظام الداخلي" /> -->

        <SeoHead
            title="النظام الداخلي"
            description="تعرف على النظام الداخلي لبرنامج بصيرة: التسجيل والانتساب، الدوام والحضور المكاني والإلكتروني، نظام الترفع والرسوب، والدراسة والامتحانات"
        />
        <JsonLd :data="structuredData" />

        <div class="mb-12 text-center">
            <h1 class="text-3xl font-bold text-green-600 md:text-4xl">
                النظام الداخلي
            </h1>
        </div>

        <!--Register-->
        <section
            class="relative bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 px-6 py-16 lg:px-16"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 lg:grid-cols-2"
            >
                <div class="space-y-6 text-center lg:text-right">
                    <h2 class="text-2xl font-bold text-[#4D88C7] md:text-4xl">
                        التسجيل والانتساب
                    </h2>

                    <p
                        class="text-lg leading-relaxed font-semibold text-gray-800"
                    >
                        التسجيل في
                        <span class="font-bold text-green-600"
                            >برنامج بصيرة</span
                        >
                        متاح من خلال زيارة مقر المركز مباشرة، أو عبر تعبئة نموذج
                        التسجيل الإلكتروني بالضغط على الزر أدناه:
                    </p>

                    <div class="flex justify-center lg:justify-start">
                        <a
                            href="https://forms.gle/rBJK8guhKmSSMd2X6"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-full bg-[#4D88C7] px-6 py-3 text-base font-semibold text-white shadow-lg transition-transform duration-300 hover:scale-105 hover:bg-green-700"
                        >
                            انتسب إلى بصيرة
                        </a>
                    </div>

                    <p
                        class="text-lg leading-relaxed font-semibold text-gray-800"
                    >
                        التواصل يتم بشكل رئيسي ضمن مجموعات
                        <span class="text-green-700">الواتساب</span> لتسهيل
                        المتابعة والتفاعل.
                    </p>
                </div>

                <div class="flex justify-center">
                    <div
                        class="relative max-w-md overflow-hidden rounded-3xl shadow-xl transition-transform duration-500 hover:scale-105"
                    >
                        <img
                            src="@i/internal/انتساب.png"
                            alt="التسجيل في بصيرة"
                            class="h-auto w-full rounded-3xl object-contain"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- -->
        <section
            class="relative rounded-2xl bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 px-6 py-16 shadow-lg"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 lg:grid-cols-2"
            >
                <div class="order-1 flex justify-center lg:order-none">
                    <img
                        src="@i/internal/الحضور6.jpg"
                        alt="الدوام والحضور"
                        class="w-full max-w-lg rounded-xl object-cover shadow-lg transition-transform duration-500 hover:scale-105"
                    />
                </div>
                <div class="space-y-6 text-right">
                    <h2 class="text-2xl font-bold text-[#4D88C7] md:text-3xl">
                        الدوام والحضور
                    </h2>
                    <p class="leading-relaxed font-semibold text-gray-800">
                        يتاح للطالب الحضور المكاني التفاعلي بينه وبين المدرس،
                        كما يتاح الحضور الإلكتروني ضمن الموقع والبث المباشر لكل
                        محاضرة.
                    </p>
                    <p class="leading-relaxed font-semibold text-gray-800">
                        لا يشترط للطالب النجاح في كل المواد للانتقال للمستوى
                        الذي يليه.
                    </p>
                    <p class="leading-relaxed font-semibold text-gray-800">
                        يسمح للطالب الحضور في كل المستويات حتى لو لم يتقدم
                        للامتحانات، حيث يشارك كمشاهد ولا يحصل على حضور ولا نجاح
                        في المستوى.
                    </p>
                </div>
            </div>
        </section>

        <!--Success-->
        <section
            class="relative rounded-2xl bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 px-6 py-16 shadow-lg"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 lg:grid-cols-2"
            >
                <div class="space-y-6 text-right">
                    <h2 class="text-2xl font-bold text-[#4D88C7] md:text-3xl">
                        الترفع والرسوب
                    </h2>
                    <p class="leading-relaxed font-semibold text-gray-800">
                        يترفع الطالب للمستوى الأعلى إذا لم يتجاوز عدد المواد
                        الراسبة أربع مواد كحدًّ أقصى، ويمكنه أن يقدم المواد
                        الراسبة مع طلاب المستوى الأدنى.
                    </p>
                    <p class="leading-relaxed font-semibold text-gray-800">
                        يحصل الطالب في نهاية الدبلوم على شهادة مصدقة من وزارة
                        الأوقاف السورية.
                    </p>
                </div>
                <div class="flex justify-center">
                    <img
                        src="@i/internal/نجاح.jpg"
                        alt="النجاح والوثائق"
                        class="w-full max-w-lg rounded-xl object-cover shadow-lg transition-transform duration-500 hover:scale-105"
                    />
                </div>
            </div>
        </section>

        <!--exams-->
        <section
            class="relative rounded-2xl bg-gradient-to-r from-green-50 via-green-100 to-neutral-100 px-6 py-16 shadow-lg"
            dir="rtl"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 lg:grid-cols-2"
            >
                <div class="space-y-6 text-right">
                    <h2
                        class="gap-10 text-2xl font-bold text-[#4D88C7] md:text-4xl"
                    >
                        الدراسة والامتحانات
                    </h2>
                    <ul
                        class="space-y-4 leading-relaxed font-semibold text-gray-800"
                    >
                        <li class="flex items-start gap-3">
                            ✅
                            <p>
                                يُقسّم المستوى إلى عدد من الفترات التدريسية،
                                ويتم تدريس عدد من المواد في كل فترة.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            ✅
                            <p>
                                يجري في نهاية الفترة التدريسية امتحان بالمواد
                                التي تم تدريسها، على ألّا يكون في الأسبوع أكثر
                                من مادة واحدة، ويكون الامتحان لكل مادة في أسبوع
                                بشكل منفصل.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            ✅
                            <p>
                                عند انتهاء المحاضرات في مادة من المواد يُعطى
                                الطالب أسبوعين للدراسة، ثم يفتح امتحان المادة
                                على موقع بصيرة، ويبقى الامتحان متاحاً أسبوعاً
                                كاملاً، يحق للطالب أن يتقدم مرة واحدة فقط خلال
                                هذا الأسبوع.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            ✅
                            <p>
                                بعد انتهاء المستوى يُتاح شهر امتحاني لكامل
                                المواد السابقة، يحق للطالب خلالها أن يتقدم لأي
                                مادة مرتين، على ألّا يكون بينهما أقل من أسبوع
                                واحد، وبذلك يكون لكل مادة ثلاث فرص امتحانية.
                            </p>
                        </li>
                    </ul>
                </div>
                <div class="order-1 flex justify-center lg:order-none">
                    <img
                        src="@i/internal/ملحقات.png"
                        alt="الدراسة"
                        class="w-full max-w-lg rounded-xl object-cover shadow-lg transition-transform duration-500 hover:scale-105"
                    />
                </div>
            </div>
        </section>
    </div>
</template>
