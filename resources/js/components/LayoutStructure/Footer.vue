<template>
    <footer
        class="bottom-0 mt-6 border-t border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-900"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between px-4 py-6 text-sm text-gray-600 md:flex-row dark:text-gray-400"
        >
            <p>
                جميع الحقوق محفوظة | لمركز بصيرة &copy;
                {{ new Date().getFullYear() }}
            </p>

            <div class="mt-3 flex space-x-4 md:mt-0">
                <a :href="route('about')" class="hover:text-[#61CE70]"
                    >عن بصيرة</a
                >
                <a href="#" class="hover:text-[#61CE70]">تواصل معنا</a>
            </div>
        </div>
    </footer>
</template>
