<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import DivBarbacoa from "@/Components/Index/DadesBarbacoa.vue";
import CreateBarbaqInput from "@/Components/CreateBarbaqInput/CreateBarbaqInput.vue";
import CreateBarbaqInputNoAuth from "@/Components/CreateBarbaqInput/CreateBarbaqInputNoAuth.vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
authStore.updateUserData();



</script>

<template>

    <MainLayout title="Inici">
        <template #main-content>

            <div v-if="authStore.user">
                <CreateBarbaqInput class="mb-5" />
            </div>
            <div v-else>
                
            </div>




            <DivBarbacoa class="mb-5" />
            <DivBarbacoa class="mb-5" />


        </template>
        <template #right-aside>
            <div class="aside-menu">

            </div>
        </template>
    </MainLayout>
</template>

<style scoped>
.aside-menu {
    width: 100%;
    height: 400px;
    background: white;
    border-radius: 20px;
}
</style>