<script setup>
import OtherLayout from './OtherLayout.vue';
import Logo from '@/components/Logo.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: OtherLayout });
</script>

<template>
    <div class="relative min-h-screen space-y-20 bg-neutral-50 px-4 py-16">
        <Head title="انضم إلينا" />

        <div class="mb-12 text-center">
            <h1 class="text-3xl font-bold text-green-600 md:text-4xl">
                أهلاً بك في دبلوم الدراسات الإسلامية بصيرة
            </h1>
        </div>
        <!--Affiliation-->
        <section class="flex justify-center bg-gray-100 px-4 py-16 sm:px-6">
            <div
                class="relative grid w-full max-w-7xl grid-cols-1 items-center gap-8 overflow-hidden rounded-4xl bg-gradient-to-br from-green-50 via-white to-green-100 p-8 shadow-2xl shadow-green-200/50 sm:gap-12 sm:p-12 lg:grid-cols-2 lg:p-16"
                dir="rtl"
            >
                <div
                    class="order-1 mb-8 flex justify-center lg:order-2 lg:mb-0"
                >
                    <Logo
                        class="w-3/4 max-w-md rounded-3xl object-cover shadow-xl ring-2 ring-green-200 transition-transform duration-500 hover:scale-105 sm:w-2/3 lg:w-full"
                    />
                </div>
                <div
                    class="order-2 space-y-6 text-center lg:order-1 lg:text-right"
                >
                    <h1
                        class="text-right text-xl leading-snug font-semibold whitespace-nowrap text-[#4D88C7] sm:text-3xl md:text-4xl"
                    >
                        ✨ الانتساب إلى برنامج بصيرة
                    </h1>

                    <p
                        class="text-base leading-relaxed font-medium text-gray-700 sm:text-lg"
                    >
                        نرحّب بانضمامك إلى
                        <span class="font-bold text-green-600"
                            >برنامج بصيرة</span
                        >
                        التابع لدار القرآن الكريم، الهادف إلى بناء طالب علمٍ
                        راسخ يجمع بين الفهم العميق والمنهج المتوازن والبصيرة في
                        التعلم والعمل.
                    </p>

                    <p
                        class="text-base leading-relaxed font-medium text-gray-700 sm:text-lg"
                    >
                        يسعى البرنامج إلى تأصيل العلوم الشرعية الأساسية، وتكوين
                        قاعدة معرفية متينة تُهيّئ الدارس للتميّز في طلب العلم
                        بإشراف نخبة من الأساتذة والمتخصصين.
                    </p>

                    <p
                        class="text-base leading-relaxed font-medium text-gray-700 sm:text-lg"
                    >
                        ندعوك لتكون جزءًا من هذه المسيرة العلمية المباركة
                        بالانتساب إلى الدفعة الجديدة من طلاب
                        <span class="font-bold text-green-600">بصيرة</span>.
                    </p>
                    <div class="flex justify-center pt-4 lg:justify-start">
                        <a
                            href="https://forms.gle/rBJK8guhKmSSMd2X6"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-[#4D88C7] px-6 py-4 text-lg font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg sm:w-3/4 md:w-1/2"
                        >
                            انتسب إلى بصيرة
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
