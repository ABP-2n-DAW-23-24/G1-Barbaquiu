<script setup>
import { defineProps } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import AuthAccount from '@/Components/Auth/AuthAccount.vue';
import MainLeftAside from '@/Components/Asides/MainLeftAside.vue';

const props = defineProps({
    title: {
        type: String,
        required: false
    }
});

</script>

<template>
    <div class="main-layout-container">
        <div class="main-layout-header">
            <div class="main-layout-application-logo">
                <ApplicationLogo />
            </div>
            <div class="main-layout-title">{{ title ?? 'Barbeq' }}</div>
            <div class="main-layout-profile">
                <AuthAccount />
            </div>
        </div>
        <div class="main-layout-content">
            <div class="main-layout-left-aside">
                <MainLeftAside />
            </div>
            <div class="main-layout-content-wrapper">
                <slot name="main-content"></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>
.main-layout-header {
    padding-inline: 12%;
    grid-template-columns: 270px auto 350px;
}
.main-layout-content {
    padding-inline: 12%;
    grid-template-columns: 270px auto;
}

.main-layout-application-logo {
    display: flex;
    align-items: center;

}

.main-layout-container {
    height: 100vh;
    background: #E9E9E9;
}

.main-layout-header {
    display: grid;
    grid-template-rows: 100%;
    height: 90px;
    background: #fff;
    position: fixed;
    width: 100%;
    z-index: 100;
}

.main-layout-content {
    --gap: 20px;
    display: grid;
    height: 100%;
    gap: var(--gap);
    grid-template-rows: 100%;
    padding-top: calc(90px + var(--gap));
   
}

.main-layout-title {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
}

.main-layout-content-wrapper {
    height: 100%;
}

.main-layout-profile {
    display: flex;
    justify-content: center;
    align-items: center;
} 
</style>