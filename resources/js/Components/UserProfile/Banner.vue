<script setup>
import { useAuthStore } from "@/stores/auth";
import { Head, Link, useForm } from '@inertiajs/vue3';


const authStore = useAuthStore();


</script>

<template>

    <div v-if="authStore.user">
        <div class="w-full bg-cover bg-no-repeat bg-center rounded-t-[20px]" style="height: 150px; background: rgb(131,58,180);
        background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(253,29,29,1) 50%, rgba(252,176,69,1) 100%);">
        </div>
        <div class="bg-white rounded-b-[20px]">

            <div class="p-4">
                <div class="relative flex w-full">
                    <div class="flex flex-1 pl-4">
                        <div style="margin-top: -6rem;">
                            <div style="height:9rem; width:9rem;" class="md rounded-full relative avatar">
                                <img style="height:9rem; width:9rem;"
                                    class="md rounded-full relative border-4 border-white bg-white"
                                    :src="authStore.user.image" alt="avatar">
                                <div class="absolute"></div>
                            </div>
                        </div>
                        <div class="flex flex-col text-left">
                            <h1 class="text-2xl font-bold text-gray-800">{{ authStore.user.name }} {{
                                authStore.user.surnames }}</h1>
                            <div class="flex items-center content-center">

                                <img src="/assets/svg/marcador.svg" alt="Marcador" class="w-3 mr-1">
                                <p class="text-sm ">L'Estartit</p>

                            </div>
                        </div>

                        <div class="flex flex-col text-right ml-auto">
                            <button>
                                <Link :href="route('profile.edit', { profile: authStore.user.id })">
                                <img src="/assets/svg/editpen.svg" alt="Editar" class="w-5">
                                </Link>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped></style>