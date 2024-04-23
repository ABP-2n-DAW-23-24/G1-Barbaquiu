<script setup>
    import ProfileLayout from "@/Layouts/ProfileLayout.vue";
    import Banner from "@/Components/UserProfile/Banner.vue";
    import BarbaqUserProfile from "@/Components/UserProfile/BarbaqUserProfile.vue";
    import { useAuthStore } from "@/stores/auth";

    const authStore = useAuthStore();
    authStore.updateUserData();
</script>

<template>
    <ProfileLayout title="Inici">
        <template #main-content >
            <Banner class="banner-profile"/>
            <BarbaqUserProfile />
        </template>
    </ProfileLayout>
</template>

<style scoped>
    .aside-menu {
        width: 100%;
        height: 400px;
        background: white;
        border-radius: 20px;
    }

    .banner-profile{
        padding-bottom: 20px;
    }
    
</style>