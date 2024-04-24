<script setup>
import { useAuthStore } from "@/stores/auth";
import { Link } from "@inertiajs/vue3";

const authStore = useAuthStore();
</script>

<template>
    <div class="auth-account-container" v-if="authStore.user">
        <div class="auth-account-image">
            <img :src="authStore.user.image" alt="Icon" />
        </div>
        <div class="auth-account-naming-wrapper">
            <div class="auth-account-naming">
                <span class="auth-account-name">{{ authStore.user.name }}</span>
                <span class="auth-account-surnames">{{ authStore.user.surnames }}</span>
            </div>
        </div>
        <div class="auth-account-settings">
            <div class="auth-account-settings-bubble">
                <img src="/assets/svg/settings.svg" alt="User settings" />
            </div>
        </div>
    </div>
    <div class="no-auth-account-container flex gap-2" v-else>
        <div class="flex items-center justify-center dark:bg-gray-800 ">
            <Link href="/register"
                class="px-4 py-2 border flex gap-2 border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 hover:border-slate-400 dark:hover:border-slate-500 hover:text-slate-900 dark:hover:text-slate-300 hover:shadow transition duration-150 bg-gray-50">

            <span class="font-bold">Registrar-me</span>
            </Link>
        </div>
        <div class="flex items-center justify-center dark:bg-gray-800">
            <Link href="/login"
                class="px-4 py-2 border flex gap-2 border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 hover:border-slate-400 dark:hover:border-slate-500 hover:text-slate-900 dark:hover:text-slate-300 hover:shadow transition duration-150 bg-gray-50">

            <span class="font-bold">Iniciar sessió</span>
            </Link>
        </div>


    </div>

</template>

<style scoped>
.btnregister:hover {
    background-color: rgb(224, 224, 224);
}

.btnlogin:hover {
    background-color: rgb(224, 224, 224);
}

.btnregister {

    color: rgb(0, 0, 0);
    font-weight: bold;
    border: 0.5px solid rgb(212, 212, 212);
    padding: 5px 10px;
    border-radius: 7px;
    background-color: rgb(236, 236, 236);
}

.btnlogin {
    color: rgb(0, 0, 0);

    border: 0.5px solid rgb(212, 212, 212);
    padding: 5px 10px;
    border-radius: 7px;
    background-color: rgb(236, 236, 236);
}

.auth-account-container:hover {
    background: #f2f2f2;
    border-radius: 3000px;
    cursor: pointer;
}

.auth-account-container {
    display: grid;
    grid-template-columns: 50px auto 50px;
    height: fit-content;
    width: 270px;
    gap: 12px;
}

.auth-account-image {
    width: 100%;
    height: 100%;
    overflow: hidden;
    display: flex;
    align-items: center;
    margin-left: 5px;
}

.auth-account-image img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.auth-account-naming {
    display: grid;
    height: 60px;
    align-items: center;
}

.auth-account-naming span {
    display: block;
}

.auth-account-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: -5px;
}

.auth-account-surnames {
    font-size: 14px;
    margin-top: -20px;
}

.auth-account-naming-wrapper {
    display: flex;
    align-items: center;
}

.auth-account-settings {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
}

.auth-account-settings-bubble {
    width: 40px;
    height: 40px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-account-settings-bubble img {
    width: 20px;
    height: 20px;
    transition: transform 0.3s;
}

.auth-account-settings:hover {
    cursor: pointer;
}

.auth-account-settings:hover .auth-account-settings-bubble {
    background: #ccc;
}

.auth-account-settings:hover .auth-account-settings-bubble img {
    transform: rotate(90deg);
    transition: transform 0.3s;
}
</style>