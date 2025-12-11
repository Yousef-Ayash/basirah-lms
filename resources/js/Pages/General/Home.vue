<script setup>
import { Link } from '@inertiajs/vue3';
// import { Head } from '@inertiajs/vue3';
import {
    BookOpen,
    Users,
    ClipboardList,
    Clock,
    Award,
    UserCheck,
    UserPlus,
} from 'lucide-vue-next';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import OtherLayout from './OtherLayout.vue';
import SeoHead from '@/components/SeoHead.vue';
import JsonLd from '@/components/JsonLd.vue';

const props = defineProps({
    teachers: Array,
});

defineOptions({ layout: OtherLayout });

const materials = [
    {
        title: 'القرآن الكريم وعلومه',
        description:
            'القرآن الكريم هو كلام الله تعالى، فهو أشرف الكلام، وهو المصدر التشريعي الأول، وهو نبع الإيمان والهداية والعلم، لا يستغني مسلم عن التزود من معينه، نتعلم في بصيرة ترتيله وتجويده وتفسيره وعلومه وأحكامه، وندرس أسباب نزوله، ونتأمل في إعجازه البياني والتشريعي، ونستقي من حكمته التي أرشدت البشرية عبر العصور.',
    },
    {
        title: 'الحديث النبوي الشريف وعلومه',
        description:
            'الحديث النبوي الشريف هو المصدر الثاني للتشريع بعد القرآن الكريم، يُستقى منه الهدي النبوي، وتُستنبط الأحكام الشرعية، وتُفهم به مقاصد الآيات، ويُقتدى بسُنّة المصطفى ﷺ في القول والعمل، ويُدرس سنده ومتنُه وفق علم الرواية والدراية، ليظل منارةً للفقه والأخلاق والسلوك القويم.',
    },
    {
        title: 'العقيدة وعلومها',
        description:
            'العقيدة الإسلامية هي الأساس الذي تُبنى عليه سائر الأعمال، تغرس في القلب إيماناً بوحدانية الله، وتُفصّل في أركان الإيمان الستة، وتُدرس أدلتها النقلية والعقلية، ويُحصّن بها المسلم من الشبهات، وتُهذب بها النفس وتُوجه السلوك، لتكون منطلقاً للعبادة، ومصدراً للطمأنينة، وسبيلاً للنجاة في الدنيا والآخرة.',
    },
    {
        title: 'الفقه وأصوله',
        description:
            'الفقه الإسلامي هو العلم الذي يُعنى بفهم الأحكام الشرعية العملية من أدلتها التفصيلية، استنبطه فقهاؤنا وفق قواعد أصولية راسخة ، قسمه العلماء إلى أبواب عديدة: منها العبادات والمعاملات وأحكام الأسرة والبيوع والجنايات والحدود..راعى فيها المشرع احتياجات الناس ومصالحهم في حياتهم.',
    },
    {
        title: 'النحو وعلومه',
        description:
            'اللغة العربية هي وعاء الفكر، ولسان الوحي، وأدلة البيان، ندرسها في أقسامها اللغوية والصرفية والنحوية والبلاغية، فاللغة من أهم أدوات الفهم الشرعي والثقافي، لما تحمله من دقة في المعنى، وثراء في الأسلوب، وعمق في الدلالة، مما يجعلها ركيزةً للهوية، وجسراً للتواصل الحضاري.',
    },
];

const teacherCarouselIndex = ref(0);

const teachersPerSlide = ref(3);

// تحديد عدد البطاقات حسب حجم الشاشة
const updateTeachersPerSlide = () => {
    teachersPerSlide.value = window.innerWidth < 768 ? 1 : 3;
};

onMounted(() => {
    updateTeachersPerSlide();
    window.addEventListener('resize', updateTeachersPerSlide);
});
onBeforeUnmount(() => {
    window.removeEventListener('resize', updateTeachersPerSlide);
});

// تقسيم المعلمين إلى مجموعات حسب عدد الشرائح
const teacherGroups = computed(() => {
    const groups = [];
    for (let i = 0; i < props.teachers.length; i += teachersPerSlide.value) {
        groups.push(props.teachers.slice(i, i + teachersPerSlide.value));
    }
    return groups;
});

const prevTeacherGroup = () => {
    teacherCarouselIndex.value =
        teacherCarouselIndex.value === 0
            ? teacherGroups.value.length - 1
            : teacherCarouselIndex.value - 1;
};

const nextTeacherGroup = () => {
    teacherCarouselIndex.value =
        teacherCarouselIndex.value === teacherGroups.value.length - 1
            ? 0
            : teacherCarouselIndex.value + 1;
};

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'EducationalOrganization',
    name: 'برنامج بصيرة',
    url: 'https://basirahonline.com',
    logo: 'https://basirahonline.com/logo.png',
    description: 'برنامج دراسات إسلامية منهجي لغير المتفرغين.',
};
</script>

<template>
    <div class="bg-gradient-to-b from-green-50 via-green-100 to-white">
        <!-- <Head title="الرئيسية" /> -->
        <SeoHead
            title="الرئيسية"
            description="برنامج بصيرة هو دبلوم دراسات إسلامية لغير المتفرغين، يجمع بين التأصيل الشرعي والمعاصرة، مع شهادة مصدقة ومحاضرات تفاعلية."
        />
        <!-- image="https://basirahonline.com/images/share-image.jpg" -->

        <JsonLd :data="structuredData" />

        <!-- Basirah Program -->
        <div
            class="relative flex flex-col items-start gap-12 text-right md:flex-row"
        >
            <!-- Program Info -->
            <div
                class="relative flex flex-1 flex-col items-center justify-center space-y-6 rounded-3xl bg-gradient-to-br from-green-100 via-green-50 to-green-200 p-10 text-center shadow-2xl md:translate-y-26 md:pr-8"
            >
                <h2 class="mb-4 text-4xl font-bold text-green-600 md:text-5xl">
                    برنامج بصيرة
                </h2>
                <p
                    class="font-segoe-important max-w-md text-lg leading-relaxed font-semibold tracking-wide text-gray-800 sm:text-xl md:text-lg"
                >
                    هو برنامج دراسات إسلامية لغير المتفرغين يجمع بين الثقافة
                    والتأصيل والوضوح والاعتدال، بالإضافة لمحاضرات إثرائية داعمة.
                </p>
                <div
                    class="flex w-full max-w-md flex-col items-center justify-center gap-4 sm:flex-row sm:gap-6"
                >
                    <Link
                        :href="route('login')"
                        class="w-full rounded-xl bg-green-600 px-8 py-4 text-lg font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:bg-green-700 hover:shadow-lg sm:w-1/2"
                    >
                        تسجيل الدخول
                    </Link>
                    <a
                        href="https://forms.gle/rBJK8guhKmSSMd2X6"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:bg-[#4D88C7]hover:shadow-lg w-full rounded-xl bg-[#4D88C7] px-8 py-4 text-lg font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 sm:w-1/2"
                    >
                        انتسب إلى بصيرة
                    </a>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="relative flex-1 space-y-8 md:pl-6">
                <!-- Feature Card 1 -->
                <div class="group flex w-full justify-start">
                    <div
                        class="font-segoe-important mt-5 flex w-full max-w-md flex-col items-center gap-4 rounded-3xl bg-gradient-to-tr from-green-100 via-green-50 to-green-200 p-8 text-center shadow-lg transition-transform duration-500 ease-in-out hover:scale-105 hover:from-green-200 hover:to-green-400 hover:shadow-2xl"
                    >
                        <img
                            src="@i/icons/live-stream.png"
                            alt="Electronic Lecture"
                            class="h-16 w-16 transition-transform duration-500 group-hover:rotate-12"
                        />
                        <p
                            class="font-segoe-important text-lg font-bold text-gray-900"
                        >
                            إمكانية حضور المحاضرات إلكترونيًا
                        </p>
                    </div>
                </div>

                <!-- Feature Card 2 -->
                <div class="group flex w-full justify-end">
                    <div
                        class="font-segoe-important flex w-full max-w-md flex-col items-center gap-4 rounded-3xl bg-gradient-to-tr from-blue-100 via-blue-50 to-blue-200 p-8 text-center shadow-lg transition-transform duration-500 ease-in-out hover:scale-105 hover:from-blue-200 hover:to-blue-400 hover:shadow-2xl"
                    >
                        <img
                            src="@i/icons/certificate.png"
                            alt="Electronic Lecture"
                            class="h-16 w-16 transition-transform duration-500 group-hover:rotate-12"
                        />
                        <p
                            class="font-segoe-important text-lg font-bold text-gray-900"
                        >
                            الحصول على شهادة مصدقة من وزارة الأوقاف
                        </p>
                    </div>
                </div>

                <!-- Feature Card 3 -->
                <div class="group flex w-full justify-start">
                    <div
                        class="font-segoe-important flex w-full max-w-md flex-col items-center gap-4 rounded-3xl bg-gradient-to-tr from-purple-100 via-purple-50 to-purple-200 p-8 text-center shadow-lg transition-transform duration-500 ease-in-out hover:scale-105 hover:from-purple-200 hover:to-purple-400 hover:shadow-2xl"
                    >
                        <img
                            src="@i/icons/group.png"
                            alt="Electronic Lecture"
                            class="h-16 w-16 transition-transform duration-500 group-hover:rotate-12"
                        />
                        <p
                            class="font-segoe-important text-lg font-bold text-gray-900"
                        >
                            +2000 طالب وطالبة
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mainالمحاور -->
        <section class="bg-gray-50 py-12">
            <div class="mx-auto max-w-7xl px-6 text-center">
                <h2 class="mb-4 text-2xl font-bold text-green-600 md:text-4xl">
                    أساسيات برنامج بصيرة
                </h2>
            </div>

            <div
                class="mx-auto grid max-w-7xl grid-cols-1 place-items-stretch gap-8 px-6 sm:grid-cols-2 md:grid-cols-3"
                dir="rtl"
            >
                <!-- المحور الأول -->
                <div
                    class="flex w-full max-w-sm flex-col items-center justify-between gap-5 rounded-xl bg-gradient-to-tr from-purple-50 to-purple-100 p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex flex-col items-center gap-5">
                        <div
                            class="flex items-center justify-center rounded-full bg-purple-100 p-5"
                        >
                            <Clock
                                class="h-14 w-14 text-purple-500 transition-transform duration-300 group-hover:scale-110"
                            />
                        </div>
                        <h3 class="text-center text-xl leading-tight font-bold">
                            المدة الزمنية
                        </h3>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            مدة البرنامج سنتان كاملتان
                        </p>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            مقسمة إلى أربعة مستويات
                        </p>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            مدة كل مستوى ستة أشهر
                        </p>
                    </div>
                </div>

                <!-- المحور الثاني -->
                <div
                    class="flex w-full max-w-sm flex-col items-center justify-between gap-5 rounded-xl bg-gradient-to-tr from-blue-50 to-blue-100 p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex flex-col items-center gap-5">
                        <div
                            class="flex items-center justify-center rounded-full bg-blue-100 p-5"
                        >
                            <Users
                                class="h-14 w-14 text-blue-500 transition-transform duration-300 group-hover:scale-110"
                            />
                        </div>
                        <h3 class="text-center text-xl leading-tight font-bold">
                            دفعة جديدة
                        </h3>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            تبدأ دفعة جديدة
                        </p>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            ومستوى جديد
                        </p>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            كل ستة أشهر
                        </p>
                    </div>
                </div>

                <!-- المحور الثالث -->
                <div
                    class="flex w-full max-w-sm flex-col items-center justify-between gap-5 rounded-xl bg-gradient-to-tr from-red-50 to-red-100 p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex flex-col items-center gap-5">
                        <div
                            class="flex items-center justify-center rounded-full bg-red-100 p-5"
                        >
                            <BookOpen
                                class="h-14 w-14 text-red-500 transition-transform duration-300 group-hover:scale-110"
                            />
                        </div>

                        <h3 class="text-center text-xl leading-tight font-bold">
                            المواد الأساسية
                        </h3>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            القرآن الكريم والحديث الشريف
                        </p>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            العقيدة الإسلامية والفقه الإسلامي
                        </p>
                        <p
                            class="text-base leading-relaxed font-semibold text-gray-700"
                        >
                            اللغة العربية والمواد الداعمة
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Materials -->
        <section class="bg-white py-16" dir="rtl">
            <div class="mx-auto max-w-6xl px-6">
                <div class="mb-12 flex justify-center">
                    <div class="rounded-xl bg-green-50 px-6 py-4">
                        <h2
                            class="text-center text-3xl leading-tight font-bold text-green-600 sm:text-4xl"
                        >
                            المواد الأساسية لبرنامج بصيرة
                        </h2>
                    </div>
                </div>

                <div class="flex flex-col space-y-10">
                    <div
                        v-for="(item, index) in materials"
                        :key="index"
                        :class="[
                            'hover:bg-BLUE-30 flex items-center rounded-xl bg-gray-50 p-8 shadow-lg transition-transform duration-300 hover:scale-105',
                            index % 2 === 0 ? 'justify-start' : 'justify-end',
                        ]"
                    >
                        <div class="w-full text-right">
                            <h3
                                class="mb-2 text-3xl font-semibold text-[#4D88C7]"
                            >
                                {{ item.title }}
                            </h3>
                            <p class="text-lg leading-relaxed text-gray-900">
                                {{ item.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Teachers -->
        <section class="mx-auto max-w-7xl px-6 py-20 text-center" dir="rtl">
            <div class="relative mb-12">
                <Link
                    :href="route('teachers')"
                    class="absolute left-0 hidden rounded-full bg-gradient-to-r from-green-500 to-green-700 px-4 py-2 text-sm text-white shadow transition hover:scale-105 md:inline-block md:text-base"
                >
                    عرض الكل
                </Link>
                <h2
                    class="w-full text-center text-2xl font-bold text-gray-800 md:text-4xl"
                >
                    أعضاء الهيئة التدريسية
                </h2>
                <Link
                    :href="route('teachers')"
                    class="mx-auto mt-8 inline-block rounded-full bg-gradient-to-r from-green-500 to-green-700 px-4 py-2 text-sm text-white shadow transition hover:scale-105 md:hidden"
                >
                    عرض الكل
                </Link>
            </div>
            <!--Desktop Carousel-->
            <div class="relative hidden min-h-[400px] md:block">
                <div class="flex flex-wrap items-center justify-center gap-6">
                    <div
                        v-for="teacher in teacherGroups[teacherCarouselIndex]"
                        :key="teacher.name"
                        class="group flex w-full max-w-sm flex-col items-center justify-start rounded-2xl bg-white p-4 text-center shadow-xl transition-all duration-300 hover:-translate-y-2 hover:bg-green-50 hover:shadow-2xl"
                    >
                        <div
                            class="flex h-64 w-64 items-center justify-center rounded-full bg-green-500 p-1"
                        >
                            <div
                                class="flex h-full w-full items-center justify-center rounded-full bg-white p-1"
                            >
                                <img
                                    :src="teacher.photo_url"
                                    :alt="teacher.name"
                                    class="h-58 w-58 rounded-full object-cover transition-all duration-300 group-hover:scale-105"
                                />
                            </div>
                        </div>
                        <h3
                            class="mt-4 text-xl font-bold text-gray-950 transition-colors duration-300 group-hover:text-green-700"
                        >
                            {{ teacher.name }}
                        </h3>
                    </div>
                </div>

                <button
                    @click="prevTeacherGroup"
                    class="absolute top-1/2 -right-5 z-10 flex -translate-y-1/2 items-center justify-center rounded-full bg-green-600 p-2 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:cursor-pointer hover:bg-green-700 md:-right-10 md:p-2"
                >
                    <svg
                        class="h-6 w-6 md:h-7 md:w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </button>

                <button
                    @click="nextTeacherGroup"
                    class="absolute top-1/2 -left-5 z-10 flex -translate-y-1/2 items-center justify-center rounded-full bg-green-600 p-2 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:cursor-pointer hover:bg-green-700 md:-left-10 md:p-2"
                >
                    <svg
                        class="h-6 w-6 md:h-7 md:w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </button>

                <!-- Pagination Dots -->
                <div class="mt-6 flex justify-center gap-2">
                    <span
                        v-for="(group, i) in teacherGroups"
                        :key="i"
                        :class="[
                            'inline-block h-3 w-3 rounded-full transition-colors duration-300',
                            teacherCarouselIndex === i
                                ? 'bg-green-600'
                                : 'bg-green-200',
                        ]"
                    ></span>
                </div>
            </div>

            <!--Mobile Carousel -->
            <div class="relative md:hidden">
                <div
                    v-for="(group, index) in teacherGroups"
                    :key="index"
                    v-show="teacherCarouselIndex === index"
                    class="transition-all duration-500 ease-in-out"
                >
                    <div
                        v-for="teacher in group"
                        :key="teacher.name"
                        class="group flex flex-col items-center rounded-2xl bg-white p-4 text-center shadow-xl transition-all duration-300 hover:-translate-y-2 hover:bg-green-50 hover:shadow-2xl"
                    >
                        <div
                            class="flex h-64 w-64 items-center justify-center rounded-full bg-green-500 p-1"
                        >
                            <div
                                class="flex h-full w-full items-center justify-center rounded-full bg-white p-1"
                            >
                                <img
                                    :src="teacher.photo_url"
                                    :alt="teacher.name"
                                    class="h-58 w-58 rounded-full object-cover transition-all duration-300 group-hover:scale-105"
                                />
                            </div>
                        </div>
                        <h3
                            class="text-l mt-4 font-bold text-gray-950 transition-colors duration-300 group-hover:text-green-700"
                        >
                            {{ teacher.name }}
                        </h3>
                    </div>
                </div>
                <button
                    @click="prevTeacherGroup"
                    class="absolute top-1/2 -right-5 z-10 flex -translate-y-1/2 items-center justify-center rounded-full bg-green-600 p-2 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:bg-green-700 md:-right-10 md:p-2"
                >
                    <svg
                        class="h-6 w-6 md:h-7 md:w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </button>

                <button
                    @click="nextTeacherGroup"
                    class="absolute top-1/2 -left-5 z-10 flex -translate-y-1/2 items-center justify-center rounded-full bg-green-600 p-2 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:bg-green-700 md:-left-10 md:p-2"
                >
                    <svg
                        class="h-6 w-6 md:h-7 md:w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </button>
            </div>
        </section>

        <!--features-->
        <section class="bg-gray-50 py-16">
            <h2
                class="mx-auto mb-8 max-w-7xl px-6 text-center text-3xl font-bold text-green-600 md:text-4xl"
            >
                ميزات داخل برنامج بصيرة
            </h2>

            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 sm:grid-cols-2 md:grid-cols-4"
                dir="rtl"
            >
                <!--Register-->
                <div
                    class="group flex flex-col items-center gap-6 rounded-2xl bg-gradient-to-tr from-rose-50 to-rose-100 p-6 text-center shadow-md transition-transform duration-500 ease-in-out hover:scale-105 hover:shadow-2xl"
                >
                    <div
                        class="flex items-center justify-center rounded-full bg-rose-100 p-6 transition-transform duration-500 group-hover:rotate-12"
                    >
                        <UserPlus class="h-16 w-16 text-rose-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">التسجيل</h3>
                    <p class="leading-relaxed font-semibold text-gray-700">
                        لا يُشترط أي تحصيل علمي سابق للالتحاق بالبرنامج.
                    </p>
                </div>
                <!-- Attendance-->
                <div
                    class="group flex flex-col items-center gap-6 rounded-2xl bg-gradient-to-tr from-sky-50 to-sky-100 p-6 text-center shadow-md transition-transform duration-500 ease-in-out hover:scale-105 hover:shadow-2xl"
                >
                    <div
                        class="flex items-center justify-center rounded-full bg-sky-100 p-6 transition-transform duration-500 group-hover:rotate-12"
                    >
                        <UserCheck class="h-16 w-16 text-sky-500" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">الحضور</h3>
                    <p class="leading-relaxed font-semibold text-gray-700">
                        يتيح البرنامج الحضور المكاني التفاعلي لكنه غير إلزامي،
                        كما يمكن الحضور ومتابعة المحاضرات إلكترونياً عبر الموقع.
                    </p>
                </div>

                <!--exam-->
                <div
                    class="group flex flex-col items-center gap-6 rounded-2xl bg-gradient-to-tr from-yellow-50 to-yellow-100 p-6 text-center shadow-md transition-transform duration-500 ease-in-out hover:scale-105 hover:shadow-2xl"
                >
                    <div
                        class="flex items-center justify-center rounded-full bg-yellow-100 p-6 transition-transform duration-500 group-hover:rotate-12"
                    >
                        <ClipboardList class="h-16 w-16 text-yellow-500" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">الامتحان</h3>
                    <p class="leading-relaxed font-semibold text-gray-700">
                        التقدُّم للامتحان يتم عبر موقع منصة بصيرة.
                    </p>
                </div>

                <!--certificate-->
                <div
                    class="group flex flex-col items-center gap-6 rounded-2xl bg-gradient-to-tr from-purple-50 to-purple-100 p-6 text-center shadow-md transition-transform duration-500 ease-in-out hover:scale-105 hover:shadow-2xl"
                >
                    <div
                        class="flex items-center justify-center rounded-full bg-purple-100 p-6 transition-transform duration-500 group-hover:rotate-12"
                    >
                        <Award class="h-16 w-16 text-purple-500" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">الشهادة</h3>
                    <p class="leading-relaxed font-semibold text-gray-700">
                        شهادة مصدقّة من وزارة الأوقاف السورية.
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>
