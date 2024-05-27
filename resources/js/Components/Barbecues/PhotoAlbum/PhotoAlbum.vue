<script setup>
import { ref } from 'vue';
import { useBarbecueStore } from "@/stores/barbecue";
import Gallery from "@/Components/Galleries/Gallery.vue";
import axios from 'axios';

const barbecueStore = useBarbecueStore();
const barbecue = barbecueStore.barbecue;
const selectedImage = ref(null);
const openedImage = ref(false);

const openimgpath = ref('');

const openImageModal = (image) => {
    selectedImage.value = image;
    openedImage.value = true;
    openimgpath.value = selectedImage.value.path;

};

const closeImageModal = () => {
    openedImage.value = false;
    selectedImage.value = null;
};

const deleteImage = async (imageId) => {
    try {
        await axios.delete(`/barbecues/${barbecue.id}/images/${imageId}`);

        barbecue.images = barbecue.images.filter(image => image.id !== imageId);
    } catch (error) {
        console.error('Error eliminant la imatge:', error);
    }
};

</script>

<template>
    <div class="flex justify-center items-center cursor-default">
        <svg width="50px" height="50px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg"
            stroke="#000000" stroke-width="0.00024000000000000003">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                stroke-width="0.048"></g>
            <g id="SVGRepo_iconCarrier">
                <path
                    d="M18.5 3.75H8.5C7.77065 3.75 7.07118 4.03973 6.55546 4.55546C6.03973 5.07118 5.75 5.77065 5.75 6.5V6.75H5.5C4.77065 6.75 4.07118 7.03973 3.55546 7.55546C3.03973 8.07118 2.75 8.77065 2.75 9.5V17.5C2.75 18.2293 3.03973 18.9288 3.55546 19.4445C4.07118 19.9603 4.77065 20.25 5.5 20.25H15.5C16.2293 20.25 16.9288 19.9603 17.4445 19.4445C17.9603 18.9288 18.25 18.2293 18.25 17.5V17.25H18.5C19.2293 17.25 19.9288 16.9603 20.4445 16.4445C20.9603 15.9288 21.25 15.2293 21.25 14.5V6.5C21.25 5.77065 20.9603 5.07118 20.4445 4.55546C19.9288 4.03973 19.2293 3.75 18.5 3.75ZM7.25 6.5C7.25 6.16848 7.3817 5.85054 7.61612 5.61612C7.85054 5.3817 8.16848 5.25 8.5 5.25H18.5C18.8315 5.25 19.1495 5.3817 19.3839 5.61612C19.6183 5.85054 19.75 6.16848 19.75 6.5V12.7L17.48 10.79C17.4061 10.7257 17.3201 10.6768 17.2271 10.646C17.1341 10.6152 17.036 10.6032 16.9383 10.6106C16.8406 10.6181 16.7454 10.6448 16.6581 10.6893C16.5709 10.7339 16.4933 10.7953 16.43 10.87L15.36 12.13L11.36 8.25C11.2961 8.17377 11.2166 8.11204 11.127 8.06893C11.0373 8.02582 10.9395 8.00232 10.84 8C10.737 8.00389 10.6358 8.02898 10.5429 8.07372C10.45 8.11845 10.3673 8.18187 10.3 8.26L7.25 11.89V6.5ZM8.5 15.75C8.16848 15.75 7.85054 15.6183 7.61612 15.3839C7.3817 15.1495 7.25 14.8315 7.25 14.5V14.2L10.92 9.88L14.38 13.27L12.28 15.75H8.5ZM16.75 17.5C16.75 17.8315 16.6183 18.1495 16.3839 18.3839C16.1495 18.6183 15.8315 18.75 15.5 18.75H5.5C5.16848 18.75 4.85054 18.6183 4.61612 18.3839C4.3817 18.1495 4.25 17.8315 4.25 17.5V9.5C4.25 9.16848 4.3817 8.85054 4.61612 8.61612C4.85054 8.3817 5.16848 8.25 5.5 8.25H5.75V14.5C5.75 15.2293 6.03973 15.9288 6.55546 16.4445C7.07118 16.9603 7.77065 17.25 8.5 17.25H16.75V17.5ZM18.5 15.75H14.25L17.09 12.41L19.72 14.64C19.6903 14.9433 19.549 15.2247 19.3236 15.4298C19.0982 15.6349 18.8047 15.749 18.5 15.75Z"
                    fill="#000000"></path>
            </g>
        </svg>
        <p class="font-extrabold">Album de imatges</p>
    </div>


    <dialog id="my_modal_2" class="modal">
        <div class="modal-box1" v-if="barbecueStore">
            <div class=" gap-2 imgsection"
                :class="{
                    'columns-1': barbecue.images.length < 1,
                    'columns-2': barbecue.images.length > 1 && barbecue.images.length < 3,
                    'columns-3': barbecue.images.length > 2 && barbecue.images.length < 5, 'columns-4': barbecue.images.length > 4
                }"
                v-if="barbecue.images.length > 0">
                <div v-for="image in barbecue.images" :key="image.id" class="break-inside-avoid mb-2 h-full w-full">
                    <img 
                        :src="image.path" 
                        class="h-full max-w-full rounded-lg object-cover fit-content"
                        @click="openImageModal(image)" 
                        alt=""
                    >

                    <img 
                        src="/assets/svg/deletebbq.svg" 
                        class="absolute top-2 right-2 w-5 h-5 cursor-pointer"
                        title="Suprimir la imatge"
                        @click="() => deleteImage(image.id)" alt="Eliminar Imatge"
                    >
                </div>

            </div>
            <div v-if="openedImage" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
                @click="closeImageModal">
                <div>
                    <img :src="openimgpath" alt="">
                </div>
            </div>
        </div>

        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>

    </dialog>

</template>

<style>
.modal-box1 {
    width: 1000px !important;
}

.imgsection {
    height: 350px;
}
</style>