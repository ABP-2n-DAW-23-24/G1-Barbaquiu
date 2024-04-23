<script setup>
import CreateBarbaqInput from "@/Components/CreateBarbaqInput/CreateBarbaqInput.vue";
import DadesBarbacoa from "../Index/DadesBarbacoa.vue";

import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();
</script>

<template>
    <div class="barbaq-user-template">
    <div class="left">
    <CreateBarbaqInput />
    <DadesBarbacoa />
</div>

<div class="rigth">

</div>
</div>
</template>

<style scoped>
.barbaq-user-template {
    display: grid;
    grid-template-columns: auto 320px;
    gap: 20px;
}

.left {
    display: grid;
    gap: 20px;
}

.rigth {
   background-color: red;
}

</style>