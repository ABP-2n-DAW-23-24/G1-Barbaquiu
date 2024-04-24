<script setup>
import { ref } from 'vue';
import 'vue3-carousel/dist/carousel.css';

const currentSlide = ref(0);
const images = ref([
    {
        src: '/assets/img/barbaquiu1.jpg',
        alt: 'Barbaquiu 1'
    },
    {
        src: '/assets/img/barbaquiu2.jpg',
        alt: 'Barbaquiu 2'
    },
    {
        src: '/assets/img/barbaquiu3.jpg',
        alt: 'Barbaquiu 3'
    }
]);

const navigate = (index) => {
    if (index < 0) {
        currentSlide.value = images.value.length - 1;
    } else if (index >= images.value.length) {
        currentSlide.value = 0;
    } else {
        currentSlide.value = index;
    }
};

const selectedImage = ref({ src: '', alt: '' });

function showModal(image) {
    selectedImage.value = image;
    const modal = document.getElementById('modalImages');
    modal.showModal();
}

function closeModal() {
    const modal = document.getElementById('modalImages');
    modal.close();
}

function showMoreComments() {
    var paragraph = document.querySelector('.more-coments');
        
    if (paragraph.textContent.trim() === 'Mostrar els comentaris') {
        paragraph.textContent = 'Mostrar menys comentaris';

        var div = document.createElement('div');

        div.classList.add('more-comments');

        div.style.height = '150px';
        div.style.overflow = 'auto';

        div.innerHTML = `
            <div class="comentari pb-4">
                <div class="flex items-center justify-between pb-2">
                    <div class="left flex items-center">
                        <img src="/assets/svg/user.svg" alt="User" class="w-8 mr-2">
                        <h4 class="font-bold">Joan Paneque</h4>
                    </div>

                    <p class="text-gray-400 text-sm">15 de abril a les 18:39</p>
                </div>

                <p class="text-sm">
                    Jo també m'apunto, tinc moltes ganes de veure-us a tots! 🥳
                </p>
            </div>

            <div class="comentari pb-4">
                <div class="flex items-center justify-between pt-5 pb-2">
                    <div class="left flex items-center">
                        <img src="/assets/svg/user.svg" alt="User" class="w-8 mr-2">
                        <h4 class="font-bold">Joan Paneque</h4>
                    </div>

                    <p class="text-gray-400 text-sm">15 de abril a les 18:39</p>
                </div>

                <p class="text-sm">
                    Jo també m'apunto, tinc moltes ganes de veure-us a tots! 🥳
                </p>
            </div>
        `;

        paragraph.insertAdjacentElement('beforebegin', div);
    } else {
        paragraph.textContent = 'Mostrar els comentaris';

        var addedElement = document.querySelector('.more-comments');
        if (addedElement) {
            addedElement.remove();
        }
    }
}
</script>

<template>
    <div class="container px-6 py-3">
        <div class="header flex items-center justify-between pb-5">
            <div class="left flex items-center">
                <img src="/assets/svg/user.svg" alt="User" class="w-10 mr-2">
                <div>
                    <h3 class="font-bold">Aniol Moreno Batlle</h3>
                    <div class="flex items-center">
                        <img src="/assets/svg/marcador.svg" alt="Marcador" class="w-3 mr-1">
                        <p class="text-sm">L'Estartit</p>
                    </div>
                </div>
            </div>

            <p class="text-gray-400 text-sm">12 de abril a les 21:25</p>
        </div> 

        <div class="content pb-3">
            <h1 class="text-3xl font-bold">Barbacoa dels cardats</h1>

            <p>
                Una barbacoa és una trobada entre amics o família en què es cuina a l'aire lliure, generalment al voltant d'una graella o un forn de carbó. És una tradició popular en moltes cultures, on es preparen diverses menes de carn, verdures i altres aliments que es cuinen lentament sobre les flames, proporcionant un sabor distintiu i aromàtic. És una ocasió per gaudir del bon temps, la companyia i la gastronomia alhora.
            </p>

            <div class="images flex pt-3">
                <div class="w-full">
                    <div class="flex gap-1">
                        <div v-for="(image, index) in images" :key="index" class="w-full md:w-1/2">
                            <img 
                                :src="image.src" 
                                :alt="image.alt" 
                                class="object-cover w-full h-32 cursor-pointer"
                                @click="showModal(image)"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="linia" />

        <div class="bottom">
            <div class="comentaris pt-2">
                <p 
                    class="more-coments font-bold text-xs cursor-pointer w-fit pt-2"
                    @click="showMoreComments"
                >
                    Mostrar els comentaris
                </p>

                <div class="flex items-center py-2">
                    <img src="/assets/svg/user.svg" alt="User" class="w-8 mr-2">
                    
                    <textarea
                        type="text" 
                        id="comentari" 
                        name="coment" 
                        placeholder="Escriu un comentari"
                        class="mr-2 w-full h-[40px] resize-none"
                    />

                    <button class="send">
                        Enviar
                    </button>
                </div>
            </div>
        </div>

        <!-- CAROUSEL IMAGES -->
        <dialog id="modalImages" class="modal">
            <div class="carousel w-5/6 h-5/6">
                 <button 
                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-50 border-inherit bg-white hover:text-white"
                    @click="closeModal"
                > 
                    ✕
                </button>
                <template v-for="(image, index) in images" :key="index">
                    <div :id="'slide' + (index + 1)" class="carousel-item relative w-full" v-show="index === currentSlide">
                        <img :src="image.src" :alt="image.alt" class="w-full object-contain" />
                        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                            <a @click.prevent="navigate(index - 1)" :href="'#slide' + (index === 0 ? images.length : index)" class="btn btn-circle">❮</a> 
                            <a @click.prevent="navigate(index + 1)" :href="'#slide' + (index === images.length - 1 ? 1 : index + 2)" class="btn btn-circle">❯</a>
                        </div>
                    </div>
                </template>
            </div>
        </dialog>
    </div>
</template>

<style scoped>
.container {
    background-color: #fff;
    border-radius: 20px;
}

.images img {
    border-radius: 5px;
}

.linia {
    height: 1px;
    background-color: #CECECE;
}

textarea,
.send {
    background-color: #DEDEDE;
    border-radius: 5px;
    padding: 0.5rem;
}

.modal-box{
    padding: 0;
}
</style>