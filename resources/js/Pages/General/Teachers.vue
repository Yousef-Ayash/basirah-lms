<template>
    <div
        class="container mx-auto max-w-7xl space-y-16 bg-neutral-50 px-4 py-16"
    >
        <Head title="الهيئة التدريسية" />

        <section class="relative px-6 py-16" dir="rtl">
            <div class="mb-14 text-center">
                <h2 class="mt-2 text-3xl font-bold text-green-600 md:text-4xl">
                    أعضاء الهيئة التدريسية في برنامج بصيرة
                </h2>
                <div class="mt-3 text-xl font-semibold text-[#4D88C7]">
                    ( أُمةً وسطاً )
                </div>
            </div>

            <div
                class="grid grid-cols-1 justify-items-center gap-x-10 gap-y-14 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="teacher in teachers"
                    :key="teacher.name"
                    class="relative flex min-h-[380px] transform flex-col items-center overflow-hidden rounded-3xl border border-gray-100 bg-[#FAFAF9] p-6 shadow-md transition duration-500 hover:scale-105 hover:shadow-xl"
                >
                    <div
                        class="relative flex h-44 w-44 items-center justify-center overflow-hidden rounded-full bg-white/80 p-2 shadow-sm ring-4 ring-green-600"
                    >
                        <div
                            class="absolute inset-0 rounded-full bg-gradient-to-tr from-green-400 to-green-200 opacity-0 transition-opacity duration-500 hover:opacity-40"
                        ></div>
                        <img
                            :src="teacher.photo_url"
                            :alt="teacher.name"
                            class="relative z-10 h-40 w-40 rounded-full object-cover"
                        />
                    </div>

                    <h2
                        class="mt-5 text-center text-lg font-bold text-green-700 md:text-xl"
                    >
                        {{ teacher.name }}
                    </h2>
                    <h5
                        class="mt-1 text-center text-sm font-medium text-gray-800 md:text-base"
                    >
                        {{ teacher.bio }}
                    </h5>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import OtherLayout from './OtherLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({ layout: OtherLayout });

const props = defineProps({
    teachers: Array,
});

console.log(props.teachers);

// const teachers = [
//     {
//         name: 'فضيلة الشيخ المحدّث  محمد نعيم عرقسوسي',
//         title: 'المُقرئ الجامع المحدّث، علّامة الشّام حفظه الله تعالى.',
//         image: 'images/teachers/شيخ نعيم.jpg',
//     },
//     {
//         name: 'فضيلة الشيخ الدكتور الداعية محمد خير الشعال',
//         title: 'داعية، علّامة، محدّث، دكتوراه في علوم الحديث، خطيب جامع أنس بن مالك.',
//         image: 'images/teachers/الشيخ الشعال.jpg',
//     },
//     {
//         name: 'الشيخ الدكتور بسام عجك',
//         title: 'نائب المشرف العام على مجمّع الشيخ أحمد كفتارو، دكتوراه في العقيدة والأديان، مدرّس في كليّات مجمّع الشيخ أحمد كفتارو، عميد كليّة الدعوة سابقاً.',
//         image: 'images/teachers/بسام عجك.jpg',
//     },
//     {
//         name: 'الشيخ الدكتور بسام حمزاوي',
//         title: 'محدّث في مجالس الرواية في جامع أبو النور وغيره، دكتوراه في علوم الحديث، مدرّس في كليّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/الشيخ-حمزاوي.jpg',
//     },
//     {
//         name: 'الشيخ الحافظ المقرئ عماد الدين الهبول',
//         title: 'إجازة في القراءات العشر، مدرّس التفسير والقراءات والتجويد في ثانويّات وكليّات مجمّع الشيخ أحمد كفتارو، مدير دار القرآن الكريم وعلومه.',
//         image: 'images/teachers/شيخ عماد.jpg',
//     },
//     {
//         name: 'الشيخ الدكتور محمد صلاح تقوى',
//         title: 'دكتوراه في العقيدة الإسلامية مدرّس في كليّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/محمد تقوى.jpg',
//     },
//     {
//         name: 'الشيخ أحمد البرشة',
//         title: 'عضو مجلس إدارة مجمع الشيخ أحمد كفتارو، خطيب جامع العفيف في دمشق، مدير ثانوية الغد المشرق، مدرّس في ثانويّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/الشيخ أحمد البرشة.JPG',
//     },
//     {
//         name: 'الشيخ الدكتور أسامة الحمصي',
//         title: 'مدير دار الحديث الشريف والسّنة النبويّة في مجمّع الشيخ أحمد كفتارو، دكتوراه في علوم الحديث، مدرّس في كليّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/الشيخ اسامة.jpg',
//     },
//     {
//         name: 'الشيخ محمد ضياء عبد الفتاح',
//         title: 'عضو مجلس أمناء مجمع الشيخ أحمد كفتارو، إمام وخطيب جامع الخليل في الهامة في ريف دمشق، مدرّس الفقه في ثانوية الغد المشرق.',
//         image: 'images/teachers/الشيخ محمد ضياء عبد الفتاح.JPG',
//     },
//     {
//         name: 'الشيخ الدكتور جميل الكل',
//         title: 'عضو مجلس إدارة مجمّع الشيخ أحمد كفتارو، دكتوراه في أصول الفقه، إمام جامع المحمدي وخطيب جامع الكوثر في المزة في دمشق، مدرّس في ثانويّات وكليّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/جميل.jpg',
//     },
//     {
//         name: 'الشيخ الدكتور نمر السيد مصطفى',
//         title: 'مدير المكتبة المركزية في مجمّع الشيخ أحمد كفتارو، دكتوراه في أصول الفقه تخصص مقاصد الشريعة، مدرّس في كليّات المجمع.',
//         image: 'images/teachers/د, نمر السيد مصطفى.JPG',
//     },
//     {
//         name: 'الشيخ أسامة أبو شعر',
//         title: 'خطيب جامع أبو النور، إمام جامع الحسنين في دمشق، مدرّس في كليّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/osama.jpg',
//     },
//     {
//         name: 'الشيخ الحافظ المقرئ محمد كرم زليطة',
//         title: 'إجازة في القراءات العشر، ماجستير في الفقه الإسلامي، إمام وخطيب جامع البظنا في دمشق.',
//         image: 'images/teachers/كرم.jpg',
//     },
//     {
//         name: 'الشيخ الدكتور محمد مليحي',
//         title: 'عميد كلية الدعوة والدراسات الإسلامية في جامعة بلاد الشام، دكتوراه في التفسير وعلوم القرآن، مدرّس في كليّات مجمّع الشيخ أحمد كفتارو.',
//         image: 'images/teachers/مليحي.jpg',
//     },
//     {
//         name: 'الأستاذ عبدالله المغربي',
//         title: 'مدرّس اللغة العربية في ثانوية الغد المشرق وغيرها من ثانويّات دمشق.',
//         image: 'images/teachers/مغربي.jpg',
//     },
// ];
</script>
