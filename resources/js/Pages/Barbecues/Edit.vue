<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { defineProps, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useBarbecueStore } from "@/stores/barbecue";
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PhotoAlbum from '@/Components/Barbecues/PhotoAlbum/PhotoAlbum.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import axios from "axios";
import FindFriendsRightAside from "@/Components/Asides/FindFriendsRightAside.vue";

const authStore = useAuthStore();
authStore.updateUserData();

const quillContent = ref();
const props = defineProps({
    barbecue: {
        type: Object,
        required: true
    },
    friends: {
        type: Object,
        required: true,
    },
});

const formQuill = ref({
    content: ''
});
let originalContent = '';   
const barbecueStore = useBarbecueStore();
barbecueStore.setBarbecue(props.barbecue);

const barbecue = {
    id: props.barbecue.id,
    title: props.barbecue.title,
    content: props.barbecue.content,
    address: props.barbecue.address,
    date: props.barbecue.date,
};

const form = useForm({
    title: barbecue.title,
    content: barbecue.content,
    address: barbecue.address,
    date: barbecue.date,
    dateFormatted: ref('')
});

const submitForm = () => {
    if (form.date) {
        form.dateFormatted = formatDateToSend(form.date);
    }

    form.patch(route('barbecues.update', { id: barbecue.id }), {
        onSuccess: () => {
            barbecue.title = form.title;
            barbecue.content = form.content;
            barbecue.address = form.address;
            barbecue.date = form.date;
        }
    });

    if (imagesForm.image.length > 0) {
        const barbecueId = barbecue.id;
        const formData = new FormData();
        for (const image of imagesForm.image) {
            formData.append('images[]', image);
        }

        axios.post(`/barbecues/${barbecueId}/images`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        .then(() => {
            window.location.reload();
            imagesForm.image = [];
        })
        .catch((error) => {
            console.error('Error al pujar les imatges:', error);
        });
    }
};


const formatDateToSend = (date) => {
    if (!date) {
        return '';
    }

    const formattedDate = new Date(date);

    const day = formattedDate.getDate().toString().padStart(2, '0');
    const month = (formattedDate.getMonth() + 1).toString().padStart(2, '0');
    const year = formattedDate.getFullYear();
    const hour = formattedDate.getHours().toString().padStart(2, '0');
    const minute = formattedDate.getMinutes().toString().padStart(2, '0');

    return `Dia ${day}/${month}/${year} a les ${hour}:${minute}h`;
};

const updateDate = (date) => {
    if (date) {
        form.dateFormatted = formatDateToSend(date);
    } else {
        form.dateFormatted = '';
    }
};

updateDate(barbecue.date);

const modal = ref(null);
const modalOpened = ref(false);
const openModal = () => {
    modalOpened.value = true;
    modal.value.showModal();

    originalContent = form.content;
};
const closeModal = () => {
    const newContent = quillContent.value ? quillContent.value.getHTML().trim() : '';
    if (newContent === '') {
        form.content = originalContent;
    }

    modal.value.close();
};
const saveContent = () => {
    const newContent = quillContent.value ? quillContent.value.getHTML().trim() : '';
    if (newContent !== '') {
        form.content = newContent;
    }
    closeModal();
};


const highlightedArea = ref(null);

const showModalImages = ref(false);

function openModalImages() {
    showModalImages.value = true;
}

function closeModalImages() {
    showModalImages.value = false;
}

function cancelImages() {
    imagesForm.image = [];
    const imagesNamesElement = document.getElementById('imagesNames');
    imagesNamesElement.innerHTML = '';

    closeModalImages();
}

const imagesForm = useForm({
    image: [],
});

const handleImagesChange = (event) => {
    imagesForm.image = event.target.files;

    const fileList = Array.from(imagesForm.image);
    const fileNames = fileList.map(file => file.name).join(', ');
    const imagesNamesElement = document.getElementById('imagesNames');
    imagesNamesElement.innerHTML = `<p>${fileNames}</p>`;
};
</script>

<template>
    <MainLayout :title="barbecue.title">
        <template #main-content>
            <div class="flex justify-center bg-white rounded-2xl p-8 mx-auto w-full max-w-screen-2xl md:max-w-3xl">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="flex flex-col">
                        <div class="mb-6">
                            <InputLabel for="title" value="Titol" />
                            <TextInput id="title" type="text"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                v-model="form.title" />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div class="mb-6 relative">
                            <InputLabel for="content" value="Descripció" />
                            <div class="relative">
                                <div id="content"
                                    class="editable-content mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    v-html="form.content">
                                </div>
                                <button type="button" class="absolute top-1/2 right-3 transform -translate-y-1/2"
                                    @click="openModal">
                                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="8.000000pt"
                                        height="8.000000pt" viewBox="0 0 512.000000 512.000000"
                                        preserveAspectRatio="xMidYMid meet">

                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                                            fill="#000000" stroke="none">
                                            <path d="M4100 5111 c-70 -23 -125 -70 -377 -323 l-273 -273 533 -533 532
-532 277 277 c180 181 284 293 298 322 31 62 36 135 15 207 -16 58 -23 65
-393 437 -259 259 -391 384 -420 399 -47 22 -151 33 -192 19z" />
                                            <path d="M1572 2637 l-1572 -1572 0 -533 0 -532 533 0 532 0 1575 1575 1575
1575 -530 530 c-291 291 -532 530 -535 530 -3 0 -713 -708 -1578 -1573z" />
                                        </g>
                                    </svg>
                                </button>
                            </div>
                            <dialog id="my_modal_1" class="modal" ref="modal">
                                <div class="modal-box" @click.stop>
                                    <QuillEditor theme="snow" class="rounded-b-lg min-h-24" ref="quillContent"
                                        v-html="form.content" />
                                    <div class="quill-container" ref="quillContainer"></div>
                                    <div class="modal-action">
                                        <button type="button" class="btn" @click.stop="closeModal">Close</button>
                                        <button type="button" class="btn" @click.stop="saveContent">Guardar</button>
                                    </div>
                                </div>
                            </dialog>
                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="address" value="Adreça" />
                            <TextInput id="address" type="text"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                v-model="form.address" />
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>
                        <div class="mb-6">
                            <InputLabel for="date" value="Data" />
                            <input id="date" type="datetime-local"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                v-model="form.date" />
                            <InputError class="mt-2" :message="form.errors.date" />
                        </div>

                        <div class="flex gap-2 h-16">
                            <div
                                class="album w-[70%]" 
                                :class="{
                                    'notSelected': highlightedArea !== 'album' && highlightedArea !== null
                                }"
                                onclick="my_modal_2.showModal()"
                            >
                                <PhotoAlbum />
                            </div>

                            <div class="album w-[30%]" @click="openModalImages">
                                <img 
                                    src="/assets/svg/addphoto2.svg" 
                                    alt="Imatge insertar foto/s" 
                                    class="add-info cursor-pointer w-[40px]"
                                    
                                >
                                <h4 class="font-bold">Afegir imatges</h4>
                            </div>

                            <dialog id="my_modal_3" class="modal cursor-auto" :open="showModalImages" @click.self="closeModalImages">
                                <div class="modal-box w-auto min-w-[30%] max-w-5xl">
                                    <form
                                        @submit.prevent="closeModalImages"
                                        class="flex flex-col items-center gap-4"
                                    >
                                        <button 
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                                            @click="cancelImages"
                                        >
                                            ✕
                                        </button>
                                    
                                        <h3 class="font-bold text-lg mb-2">Selecciona la imatge/s</h3>

                                        <label class="custum-file-upload" for="images">
                                            <div class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                                                    <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                                                    <g 
                                                        stroke-linejoin="round" 
                                                        stroke-linecap="round"
                                                        id="SVGRepo_tracerCarrier">
                                                    </g>

                                                    <g id="SVGRepo_iconCarrier">
                                                        <path 
                                                            fill=""
                                                            d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                                                            clip-rule="evenodd" fill-rule="evenodd">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </div>

                                            <div class="text">
                                                <span>Clica per seleccionar una imatge</span>
                                            </div>

                                            <div class="text" id="imagesNames">
                                            </div>
                                            
                                            <input 
                                                type="file" 
                                                id="images" 
                                                name="image" 
                                                multiple
                                                accept="image/*" 
                                                @change="handleImagesChange"
                                            />
                                        </label>

                                        <div class="flex justify-center mt-4 space-x-4">
                                            <button class="btn" type="submit">Guardar</button>
                                            <button class="btn" @click="cancelImages">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="btn hover:bg-[#c84c00] hover:text-white bg-[#FF6100] text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF6100] focus:ring-offset-white active:bg-[#FF6100] active:text-white px-4 py-2 rounded-md transition ease-in-out duration-150"
                            :disabled="form.processing">
                            Guardar
                        </button>

                        <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Guardat.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </template>

        <template #right-aside>
            <div class="aside-menu">
                <FindFriendsRightAside />
            </div>
        </template>
    </MainLayout>
</template>
<style scoped>
.aside-menu {
    width: 100%;
    background: white;
    border-radius: 20px;
    padding: 26px 30px;
}

.album {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    height: 100%;
    padding: 10px;
    border-radius: 10px;
    background: #f2f2f2;
    cursor: pointer;
}

.modal {
    background-color: rgba(0, 0, 0, 0.5);
}

.custum-file-upload {
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: space-between;
    gap: 10px;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    border: 2px dashed #cacaca;
    background-color: rgba(255, 255, 255, 1);
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0px 48px 35px -48px rgba(0, 0, 0, 0.1);
}

.custum-file-upload .icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custum-file-upload .icon svg {
    height: 80px;
    fill: rgba(75, 85, 99, 1);
}

.custum-file-upload .text {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custum-file-upload .text span {
    font-weight: 400;
    color: rgba(75, 85, 99, 1);
}

.custum-file-upload input {
    display: none;
}

.custum-file-upload svg {
    transition: transform 0.3s, filter 0.3s;
}

.custum-file-upload:hover svg {
    transform: scale(1.2);
    filter: brightness(0%);
}

</style>
