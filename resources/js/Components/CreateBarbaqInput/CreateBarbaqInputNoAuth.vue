<script setup>
import { ref, onMounted } from 'vue';
import { createApp } from 'vue'


</script>

<template>
    <div class="dropdown skeleton">

        <div class="flex content ">
            <img src="/assets/img/profile.jpg" alt="Foto de perfil" class="mr-2 h-16 w-16 rounded-full imgprofile">
            <form action="" class="w-full mt-auto mb-auto ">
                <div class="input-container">
                    <h1 class="textdiv">Crear nova barbacoa...</h1>
                </div>
            </form>
            <img src="/assets/svg/lockdiv.svg" alt="Imatge de editar" class="imgprofile opacity-25">
        </div>

    </div>
</template>

<style scoped>
.dropdown {
    position: relative;
    width: 100%;
    background-color: rgb(80, 80, 80);
    border-radius: 20px;
    
    cursor: no-drop;
    
}


.content {
    padding: 10px 20px;
}

.textdiv{
    font-size: 26px;
}
.imgprofile{
    opacity: 0.5;
    z-index: 0;
}

</style>
