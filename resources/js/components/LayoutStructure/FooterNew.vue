<script setup>
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';
import { Facebook, MapPin, MessageSquare, Phone, Youtube } from 'lucide-vue-next';

const { __ } = useTranslations();

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <footer class="relative bg-[#61CE70] px-6 py-10 text-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 md:grid-cols-3">
            <div class="space-y-4 text-start">
                <h3 class="text-2xl font-bold">{{ __('footer.basirah_title') }}</h3>
                <p class="text-sm leading-relaxed">
                    {{ __('footer.basirah_description') }}
                </p>
                <ul class="space-y-2 text-sm">
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.about_basirah') }}</Link>
                    </li>
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.internal_system') }}</Link>
                    </li>
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.scientific_curriculum') }}</Link>
                    </li>
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.contact_us') }}</Link>
                    </li>
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.privacy_policy') }}</Link>
                    </li>
                </ul>
            </div>

            <div class="space-y-4 text-start md:text-center">
                <h3 class="text-2xl font-bold">{{ __('footer.links_title') }}</h3>
                <ul class="space-y-2 text-sm">
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.studies_program') }}</Link>
                    </li>
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.main_subjects') }}</Link>
                    </li>
                    <li>
                        <Link href="#" class="hover:underline">{{ __('footer.faculty') }}</Link>
                    </li>
                </ul>
            </div>

            <div class="space-y-6 text-start md:text-end">
                <div class="flex items-center justify-start gap-3 md:justify-end">
                    <span>{{ __('footer.phone_number') }}</span>
                    <Phone class="h-5 w-5" />
                </div>
                <div class="flex items-center justify-start gap-4 md:justify-end">
                    <a href="#" aria-label="Facebook" class="hover:opacity-80"><Facebook /></a>
                    <a href="#" aria-label="WhatsApp" class="hover:opacity-80"><MessageSquare /></a>
                    <a href="#" aria-label="YouTube" class="hover:opacity-80"><Youtube /></a>
                </div>
                <div class="flex items-center justify-start gap-3 md:justify-end">
                    <span class="text-lg">{{ __('footer.address') }}</span>
                    <MapPin class="h-6 w-6" />
                </div>
            </div>
        </div>

        <div class="mt-10 border-t border-white/20 pt-6 text-center text-xs">
            <p>{{ __('footer.copyright') }}</p>
        </div>

        <button
            @click="scrollToTop"
            class="absolute start-6 bottom-6 rounded-full bg-white p-2 text-gray-700 shadow-lg transition hover:bg-gray-200"
            aria-label="Scroll to top"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="lucide lucide-arrow-up"
            >
                <path d="m5 12 7-7 7 7" />
                <path d="M12 19V5" />
            </svg>
        </button>
    </footer>
</template>
