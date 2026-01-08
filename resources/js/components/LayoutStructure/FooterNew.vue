<script setup>
import { Link } from '@inertiajs/vue3';
import YoutubeIcon from '../Icons/YoutubeIcon.vue';
import WhatsAppIcon from '../Icons/WhatsAppIcon.vue';
import FacebookIcon from '../Icons/FacebookIcon.vue';
import PhoneIcon from '../Icons/PhoneIcon.vue';
import MapPinIcon from '../Icons/MapPinIcon.vue';
</script>

<template>
    <footer class="bg-[#61CE70] text-white" dir="rtl" aria-label="تذييل الموقع">
        <!-- Main footer content -->
        <div
            class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 py-14 md:grid-cols-3"
        >
            <!-- Brand / Description -->
            <div class="space-y-4 text-right">
                <h3 class="text-3xl font-extrabold tracking-tight">بصيرة</h3>
                <p class="text-sm leading-relaxed text-white/90">
                    هو برنامج دراسات إسلامية لغير المتفرغين، حيث يجمع بين
                    الثقافة والتأصيل والوضوح والاعتدال، بالإضافة إلى مقررات
                    داعمة ومحاضرات إثرائية.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4 text-right md:text-center">
                <h4 class="text-xl font-bold">روابط سريعة</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <Link
                            :href="route('i-sys')"
                            class="hover:text-white hover:underline"
                            >النظام الداخلي</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('teachers')"
                            class="hover:text-white hover:underline"
                            >الهيئة التدريسية</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('subjects')"
                            class="hover:text-white hover:underline"
                            >المواد الرئيسية</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('about')"
                            class="hover:text-white hover:underline"
                            >عن بصيرة</Link
                        >
                    </li>
                </ul>
            </div>

            <!-- Contact & Social -->
            <div class="space-y-6 text-right md:text-end">
                <!-- Phone -->
                <div class="flex items-center justify-end gap-3">
                    <a
                        href="tel:+963930101566"
                        class="transition hover:text-white"
                        target="_blank"
                        dir="ltr"
                        >+963 930 101 566</a
                    >
                    <div class="h-5 w-5">
                        <PhoneIcon class="fill-white" />
                    </div>
                </div>

                <!-- Social icons -->
                <div class="flex items-center justify-end gap-4">
                    <a
                        href="https://www.facebook.com/basirahonline"
                        aria-label="Facebook — بصيرة"
                        target="_blank"
                        class="h-8 w-8 rounded-full bg-white p-1 transition-transform hover:scale-110"
                    >
                        <FacebookIcon />
                    </a>
                    <a
                        href="https://wa.me/+963930101566"
                        aria-label="WhatsApp — بصيرة"
                        target="_blank"
                        class="h-8 w-8 rounded-full bg-white p-1 transition-transform hover:scale-110"
                    >
                        <WhatsAppIcon />
                    </a>
                    <a
                        href="https://www.youtube.com/@basirahonline"
                        aria-label="YouTube — بصيرة"
                        target="_blank"
                        class="h-8 w-8 rounded-full bg-white p-1 transition-transform hover:scale-110"
                    >
                        <YoutubeIcon />
                    </a>
                </div>

                <!-- Address -->
                <div class="flex items-center justify-end gap-3">
                    <a
                        href="https://maps.app.goo.gl/dMBSPWCsRvA4w6My9"
                        class="text-sm leading-relaxed"
                        target="_blank"
                        >دمشق - ركن الدين، مجمع الشيخ أحمد كفتارو</a
                    >
                    <div class="h-5 w-5">
                        <MapPinIcon class="fill-white" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-white/20"></div>

        <!-- Bottom legal bar -->
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-4 text-xs md:flex-row"
        >
            <p class="text-white/70">
                © {{ new Date().getFullYear() }} بصيرة. جميع الحقوق محفوظة.
            </p>
            <p class="text-white/60">
                تصميم وتطوير بواسطة
                <a
                    href="https://www.github.com/Yousef-Ayash"
                    target="_blank"
                    class="font-semibold transition hover:text-white"
                    >فريق بصيرة التقني</a
                >
            </p>
        </div>
    </footer>
</template>

<style scoped>
/* Optional: subtle hover effect for links */
a:hover {
    text-decoration: underline;
    transition:
        color 0.2s ease,
        transform 0.2s ease;
}
</style>
