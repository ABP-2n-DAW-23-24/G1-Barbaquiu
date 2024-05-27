<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useBarbecueStore } from "@/stores/barbecue";
import { useAuthStore } from '@/stores/auth';
import { defineProps } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import BarbaqUserProfile from '../UserProfile/BarbaqUserProfile.vue';
import axios from 'axios';
import PhotoAlbum from '../Barbecues/PhotoAlbum/PhotoAlbum.vue';

import AddProductModal from '../Barbecues/AddProductModal.vue';
import { parse } from 'vue/compiler-sfc';

const props = defineProps({
    friends: {
        type: Object,
        required: true,
    },
});


const highlightedArea = ref(null);
const showAddUsers = ref(false);

function highlightArea(area) {
    if (showAddUsers.value === false) {
        showAddUsers.value = true;
    } else {
        showAddUsers.value = false;
    }

    if (highlightedArea.value !== 'baskets') {
        if (highlightedArea.value === area) {
            highlightedArea.value = null;
            console.log('highlightedArea', highlightedArea.value);
        } else {
            highlightedArea.value = area;
            console.log('highlightedArea', highlightedArea.value);
        }
    }
}

function resetHighlight() {
    highlightedArea.value = null;
    console.log('highlightedArea', highlightedArea.value);
}

const barbecueStore = useBarbecueStore();
const authStore = useAuthStore();
const barbecue = barbecueStore.barbecue;
console.log('Barbecue', barbecue);

const coordinates = ref([barbecue.latitude, barbecue.longitude]);

const form = useForm({
    user_id: null,
});

const inviteUser = (friendId) => {
    form.user_id = friendId;
    form.post('/sendinvitation/' + barbecue.id);
    highlightArea('usersinvite');
}


function removeMemberFromBBQ(barbecueId, userId) {
    axios.delete(route('rejectbarbecuejoinrequest', [barbecueId, userId])).then(res => {
        authStore.updateUserData();
        barbecueStore.removeMember(userId);
    });
}

const deleteMember = (memberId) => {
    form.user_id = memberId;
    // form.delete(route('destroyfriendship', { id: barbecue.id }));
    removeMemberFromBBQ(barbecue.id, memberId);
    // axios.delete(route('destroyfriendship'), {
    //     data: {
    //         user_id: memberId,
    //         barbecue_id: barbecue.id,
    //     }
    // })
    //     .then(response => {
    //         console.log('Member deleted', response.data);
    //         barbecueStore.removeMember(memberId);
    //     })
    //     .catch(error => {
    //         console.error('Error deleting member:', error);
    //     });
    // console.log('Delete member', memberId);
}

function addEvent() {
    var eventTitle = barbecue.title;
    var eventDescription = barbecue.content;
    var dateString = barbecue.date;
    var date = parseFormatDateFromString(dateString);
    var longitude = barbecue.longitude;
    var latitude = barbecue.latitude;

    const startDate = date;
    const endDate = new Date(date);
    endDate.setHours(endDate.getHours() + 2);
    const summary = `${eventTitle}`;
    const description = eventDescription;
    const location = `${latitude},${longitude}`;

    const encodedSummary = encodeURIComponent(summary);
    const encodedDescription = encodeURIComponent(description);
    const encodedLocation = encodeURIComponent(location);

    const googleCalendarURL = `https://calendar.google.com/calendar/render?action=TEMPLATE&dates=${changeFormatDate(startDate)}/${changeFormatDate(endDate)}&text=${encodedSummary}&details=${encodedDescription}&location=${encodedLocation}`;

    window.open(googleCalendarURL);
}

function parseFormatDateFromString(dateString) {
    const [datePart, timePart] = dateString.split(' ');
    const [day, month, year] = datePart.split('/');
    const [hours, minutes] = timePart.slice(0, -1).split(':');

    const date = new Date(year, month - 1, day, hours, minutes);

    const dateFormatted = date.toISOString();

    const fullDate = dateFormatted.split('T')[0] + 'T' + hours + ':' + minutes + ':00Z';

    return fullDate;
}

function changeFormatDate(date) {
    const dateObject = new Date(date);
    const dateWithoutSeconds = dateObject.toISOString().slice(0, -5);
    const addedZ = dateWithoutSeconds + 'CET';

    return addedZ.replace(/[-:.]/g, '');
}

const formProduct = useForm({
    member_id: null,
    product_id: null,
    basket_product_id: null,
});

const assignProduct = (productId, member, basketProductId) => {
    formProduct.product_id = productId;
    formProduct.member_id = member.id;
    formProduct.basket_product_id = basketProductId;
    axios.post(route('assignproduct', { id: barbecue.id }), {
        product_id: formProduct.product_id,
        member_id: formProduct.member_id,
        basket_product_id: formProduct.basket_product_id,
    })
        .then(response => {
            console.log('Product assigned', response.data);
            barbecueStore.setUserBasketProduct(productId, member);
        })
        .catch(error => {
            console.error('Error assigning product:', error);
        });
}

const formProductQuantity = useForm({
    product_name: '',
    product_price: '',
    product_id: null,
});
const addOldProduct = (product) => {
    axios.post(route('addproduct', { id: barbecueStore.barbecue.id }), {
        product_name: product.name,
        product_price: product.price,
    })
        .then(response => {
            barbecueStore.setBasketProduct(product.id, response.data.quantity);
            highlightArea('baskets');
        })
        .catch(error => {
            console.error('Error adding product:', error);
        });
};


const minusProduct = (product) => {
    axios.post(route('minusproduct', { id: barbecueStore.barbecue.id }), {
        product_id: product.id,
    })
        .then(response => {
            if (response.data.deleted) {
                console.log('Product deleted');
                barbecueStore.removeBasketProduct(product.id);
            } else {
                barbecueStore.setBasketProduct(product.id, response.data.quantity);
            }
            highlightArea('baskets');
        })
        .catch(error => {
            console.error('Error removing product:', error);
            console.log('Product', product);
        });
};

</script>

<template>
    <div class="grid-aside" :style="{ display: highlightedArea === null ? 'grid' : 'block' }">
        <div class="baskets" @click="highlightArea('baskets')" :class="{
            'selected': highlightedArea === 'baskets',
            'notSelected': highlightedArea !== 'baskets' && highlightedArea !== null
        }">
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <h1>
                        CISTELLA</h1>
                    <p class="relative text">
                        Descobreix tots els productes
                    </p>
                </div>
                <div v-if="highlightedArea === 'baskets'" class="flex items-center mr-2">
                    <button class="p-2  hover:bg-orange-500 rounded-full" @click.stop="resetHighlight">
                        <img src="/assets/svg/deshacer.svg" alt="Desfer" class="img-fluid" style="width: 20px;">
                    </button>
                    <AddProductModal @click="highlightArea('baskets')" />
                </div>
            </div>

            <div v-if="highlightedArea !== 'baskets'">
                <div class="img">
                    <img src="/assets/img/foodbaskets.png" alt="Hamburguesa" class="img-fluid">
                </div>
            </div>
            <div v-if="highlightedArea === 'baskets'">
                <div class="grid-baskets"
                    v-if="barbecue && barbecue.basket && barbecue.basket.basket_product && barbecue.basket.basket_product.length > 0">
                    <div class="title-grid-baskets">
                        <p class="product">Productes</p>
                        <p class="quantity">Quantitat</p>
                        <p class="price">Preu</p>
                        <p class="price"></p>
                    </div>

                    <div class="grid-info">
                        <div class="producte1"
                            v-for="item in barbecue.basket.basket_product.filter((product, index, self) => self.findIndex(t => t.product.id === product.product.id) === index)"
                            :key="item.product.id" @click.stop>
                            <p class="w-2/5">
                                {{ item.product.name }}</p>
                            <div class="flex items-center gap-1">
                                <div class="minus" @click.stop="minusProduct(item.product)">
                                    <img src="/assets/svg/menosmini.svg" alt="Menys" class="img-fluid"
                                        style="width: 17px;">
                                </div>
                                <p>{{ item.quantity }}</p>
                                <div class="plus" @click.stop="addOldProduct(item.product)">
                                    <img src="/assets/svg/plusmini.svg" alt="Més" class="img-fluid"
                                        style="width: 17px;">
                                </div>
                            </div>
                            <!-- item * quantity -->
                            <p class=" text-right w-1/3 mr-4">
                                {{ (parseFloat(item.product.price) * item.quantity).toFixed(2) }}
                                €</p>

                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="" @click.stop>
                                    <img :src="item.user.image" alt="Usuari" class="img-fluid rounded-full "
                                        style="width: 25px; height: 25px;">
                                </div>
                                <div tabindex="0"
                                    class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-[300px]">
                                    <p class="title text-center color-[#ff5e00] mt-1">
                                        Asigna el producte a un membre del grup</p>

                                    <div class="flex items-center gap-2 bg-white p-1 mb-2 mt-2 w-full ">

                                        <Link
                                            class="flex items-center bg-white gap-2 rounded-full w-full cursor-pointer">
                                        <img :src="item.user.image ? item.user.image : '/assets/img/user.png'" alt=""
                                            class="fit-content !h-10 !w-10 rounded-full object-cover">
                                        <p>{{ item.user.name }} </p>



                                        </Link>


                                        <div class="flex justify-end w-full">
                                            <div
                                                class="badge badge-outline border-transparent text-[#ff5e00] justify-end">
                                                Asignat
                                            </div>

                                        </div>
                                    </div>

                                    <div class="members" v-for="member in $page.props.members" :key="member.id">
                                        <form @click="assignProduct(item.product.id, member, item.id)"
                                            v-if="member.id !== item.user.id">
                                            <div
                                                class="flex items-center gap-2 bg-white p-1 rounded-full mb-2 w-full members-asignar">
                                                <div
                                                    class="flex items-center bg-white gap-2 rounded-full  w-full cursor-pointer hover-members">
                                                    <img :src="member.image" alt=""
                                                        class="fit-content !h-10 !w-10 rounded-full object-cover">
                                                    <p>{{ member.name }} </p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid-baskets !align-left" v-else>
                    <h1 class="!pl-0">
                        No hi ha productes a la cistella</h1>
                </div>
                <div class="total" v-if="barbecue && barbecue.basket && barbecue.basket.basket_product">
                    <p class="!text-lg">
                        Total: </p>

                    <h1>
                        {{
                            barbecue.basket.basket_product.reduce(
                                (total, item) => total + parseFloat(item.product.price) * item.quantity,
                                0
                            ).toFixed(2)
                        }}
                        <span>€</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="dates" :class="{
            'notSelected': highlightedArea !== 'dates' && highlightedArea !== null
        }" @click="addEvent">
            <h2 v-if="barbecue.date">{{ barbecue.date.split(' ')[0] }}</h2>
            <h1 v-if="barbecue.date">{{ barbecue.date.split(' ')[1] }}</h1>
            <h2 class="" v-else="barbecue.date">No hi ha cap data programada!!</h2>
        </div>
        <div class="album" onclick="my_modal_2.showModal()" :class="{
            'notSelected': highlightedArea !== 'album' && highlightedArea !== null
        }">
            <PhotoAlbum />
        </div>

        <div class="maps" :class="{
            'selected': highlightedArea === 'maps',
            'notSelected': highlightedArea !== 'maps' && highlightedArea !== null
        }">
            <img src="/assets/img/map.png" alt="Mapa" class="img-fluid hidden" @click="handleMapClick">

            <iframe class="w-full h-50 mapagoogle"
                :src="'https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d877.7659237744726!2d' + barbecue.longitude + '!3d' + barbecue.latitude + '!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses!2ses!4v1715779032596!5m2!1ses!2ses'"
                width="" height="200" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <div class="costs" :class="{

            'notSelected': highlightedArea !== 'costs' && highlightedArea !== null
        }">
            <div class="top">
                <h1>COSTOS</h1>

                <div class="total">
                    <p>Total</p>
                    <!-- plus all items, and items * quantity too -->
                    <h1 v-if="barbecue.basket?.basket_product">{{
                        barbecue.basket.basket_product.reduce(
                            (total, item) => total + parseFloat(item.product.price) * item.quantity,
                            0
                        ).toFixed(2)
                    }}<span>€</span></h1>
                    <h1 v-else>0 €</h1>
                </div>
            </div>

            <div class="bottom">

                <img src="/assets/img/money.png" alt="Targeta de crèdit" class="img-fluid">

                <div class="pay">
                    <p>A pagar</p>
                    <!-- my items assogned to my * quantity -->
                    <h1 v-if="barbecue.basket?.basket_product">
                        {{
                            (barbecue.basket.basket_product.reduce(
                                (total, item) => total + parseFloat(item.product.price) * item.quantity,
                        0
                        ) / $page.props.members.length).toFixed(2)
                        }}<span>€</span>
                    </h1>
                    <h1 v-else>0.00 €</h1>


                </div>
            </div>
        </div>

        <div class="users" :class="{
            'selected': highlightedArea === 'users' || highlightedArea === 'usersinvite',
            'notSelected': highlightedArea !== 'users' && highlightedArea !== null && highlightedArea !== 'usersinvite' && highlightedArea !== null
        }">
            <h1>USUARIS</h1>
            <div class="usersbarbaq" @click="highlightArea('users')" :class="{
                'selected': highlightedArea === 'users',
                'notSelected': highlightedArea !== 'users' && highlightedArea !== null
            }">


                <div class="usersdiv">
                    <p>
                        Veure tots els usuaris inscrits
                    </p>
                    <img src="/assets/svg/arrow-right.svg" alt="Fletxa dreta" class="img-fluid bounce-left">
                </div>

                <div v-if="highlightedArea === 'users'" v-for="member in barbecueStore.barbecue.members" :key="member.id">
                    <div class="flex items-center gap-2 bg-white p-1 rounded-xl mb-2 w-full mt-2">
                        <Link :href="route('profile.show', member.id)"
                            class="flex items-center bg-white gap-2 rounded-xl  w-full cursor-pointer">

                        <img :src="member.image" alt="" class="fit-content h-10 w-10 rounded-full object-cover">
                        <div class="flex flex-row items-center gap-1 w-full">
                            <p>
                                {{ member.name }}
                            </p>
                            <div class="flex intolerances">
                                <div v-if="member.vegetarian"
                                    class=" w-auto flex justify-center items-center ml-2 p-1 rounded-md ">
                                    <img src="/assets/img/intolerance/vegetarian.png" class="h-auto max-h-7"
                                        alt="vegetarian">
                                </div>
                                <div v-if="member.lactose"
                                    class=" w-auto flex justify-center items-center ml-2 p-1 rounded-md tooltip "
                                    data-tip="Intolerant a la lactosa">
                                    <img src="/assets/img/intolerance/lactose.png" class="h-auto max-h-7" alt="lactose">
                                </div>
                                <div v-if="member.gluten"
                                    class=" w-auto flex justify-center items-center ml-2 p-1 rounded-md tooltip "
                                    data-tip="Intolerant al gluten">
                                    <img src="/assets/img/intolerance/gluten.png" class="h-auto max-h-7" alt="gluten">
                                </div>
                                <div v-if="member.spicy"
                                    class=" w-auto flex justify-center items-center ml-2 p-1 rounded-md tooltip "
                                    data-tip="Intolerant al picant">
                                    <img src="/assets/img/intolerance/spicy.png" class="h-auto max-h-7" alt="spicy">
                                </div>
                                <div v-if="member.halal"
                                    class=" w-auto flex justify-center items-center ml-2 p-1 rounded-md ">
                                    <img src="/assets/img/intolerance/halal.png" class="h-auto max-h-7" alt="halal">
                                </div>

                            </div>

                        </div>

                        </Link>
                        <div class="flex justify-end w-1/2" v-if="member.id !== barbecue.user_id">
                            <Link @click="deleteMember(member.id)" class="flex items-center pr-1">
                            <button title="Add New" class="group cursor-pointer outline-none hover:rotate-[135deg]
                                duration-300 rotate-45">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24"
                                    class="stroke-pink-400 fill-none group-hover:fill-pink-800 group-active:stroke-pink-200 group-active:fill-pink-600 group-active:duration-0 duration-300">
                                    <path
                                        d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                                        stroke-width="1.5"></path>
                                    <path d="M8 12H16" stroke-width="1.5"></path>
                                    <path d="M12 16V8" stroke-width="1.5"></path>
                                </svg>
                            </button>
                            </Link>
                        </div>

                        <div v-if="member.id === barbecue.user_id || barbecue.friendships.find(friendship => friendship.user_id === member.id && friendship.is_admin === 1)"
                            class="flex justify-end w-1/3">

                            <div class="badge badge-outline border-transparent text-[#ff5e00] ">
                                Admin</div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="inviteusers"
                v-if="authStore?.user?.id === barbecue.user_id || barbecue.friendships.find(friendship => friendship.user_id === authStore.user?.id && friendship.is_admin === 1)"
                @click="highlightArea('usersinvite')" :class="{
                    'selected': highlightedArea === 'usersinvite',
                    'notSelected': highlightedArea !== 'usersinvite' && highlightedArea !== null
                }">
                <div class="usersdiv mb-2">
                    <p>
                        Invitar als teus amics
                    </p>
                    <img src="/assets/svg/arrow-right.svg" alt="Fletxa dreta" class="img-fluid bounce-left">
                </div>
                <div v-for="friend in friends" :key="friend.id" v-if="highlightedArea === 'usersinvite'"
                    class="overflow-y-auto cursor-default">
                    <div class="flex items-center gap-2 bg-white p-1 rounded-xl mb-2 w-full ">

                        <Link :href="route('profile.show', friend.id)" @click="highlightArea('usersinvite')"
                            class="flex items-center bg-white gap-2 rounded-xl  w-full cursor-pointer">
                        <img :src="friend.image" alt="" class="fit-content h-10 w-10 rounded-full object-cover">
                        <p>{{ friend.name }} </p>
                        </Link>

                        <div class="flex justify-end w-full">
                            <Link @click="inviteUser(friend.id)" class="flex items-center pr-1">
                            <button title="Add New"
                                class="group cursor-pointer outline-none hover:rotate-90 duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24"
                                    class="stroke-pink-400 fill-none group-hover:fill-pink-800 group-active:stroke-pink-200 group-active:fill-pink-600 group-active:duration-0 duration-300">
                                    <path
                                        d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                                        stroke-width="1.5"></path>
                                    <path d="M8 12H16" stroke-width="1.5"></path>
                                    <path d="M12 16V8" stroke-width="1.5"></path>
                                </svg>
                            </button>

                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');



.baskets {
    grid-area: baskets;
    width: 100%;
    background: #f2f2f2;
    border-radius: 10px;
    cursor: pointer;

    h1 {
        font-size: 1.5rem;
        color: #ff5e00;
        margin-left: 5px;
        padding-left: 15px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        padding-top: 10px;
    }

    .text {
        font-size: 0.7rem;
        color: #ff5e00;
        font-weight: 500;
        padding-left: 20px;
        font-family: 'Montserrat', sans-serif;
    }

    img {
        object-fit: cover;
        width: 240px;
    }

    .img {
        display: flex;
        justify-content: flex-end;
        padding-right: 10px;
        padding-bottom: 10px;
        height: 110px;
    }

    .grid-baskets {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 10px;
    }

    .title-grid-baskets {

        color: #9B3B00;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        padding: 10px;
        width: 100%;
    }


    .grid-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
    }



    .producte1 {
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        width: 100%;
        padding: 5px;
        margin-bottom: 10px;
        align-items: center;

        p {
            align-items: center !important;
        }
    }

    .producte1:hover {
        background-color: #ffffff;
    }

    .total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        color: #9B3B00;
        font-weight: bold;
        background-color: rgba(255, 255, 255, 0.5);
        font-size: 1.5rem;
    }

    .total h1 {
        font-size: 1.5rem;
        color: #9B3B00;
        font-weight: bold;
        padding: 0;
    }

    .product,
    .quantity,
    .price {
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0;
        color: #9B3B00;
    }
}

.dates {
    grid-area: dates;
    background: #f2f2f2;
    padding: 10px;
    width: auto;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    height: 100%;

    h1 {
        font-size: 1.4rem;
        color: #020202;
        font-weight: 600;
        text-align: center;
        font-family: 'Montserrat', sans-serif;
    }

    h2 {
        display: flex;

        font-size: 0.9rem;
        color: #ff5e00;
        font-weight: bold;
    }


}

.album {
    grid-area: album;
    background: #f2f2f2;

    width: auto;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    height: 100%;
}

.maps {
    grid-area: maps;
    border-radius: 10px;
    cursor: pointer;
    width: 100%;
    height: 100%;
}

.costs {
    grid-area: costs;
    background: #f2f2f2;
    padding: 10px;
    width: 100%;
    border-radius: 10px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;

    .top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 10px;
        line-height: 1em;

        h1 {
            font-size: 1.5rem;
            color: #ff5e00;
            font-weight: 600;
        }

        .total {
            display: flex;
            flex-direction: column;
            align-items: center;

            p {
                font-size: 0.9rem;
                color: #ff5e00;
                font-weight: 600;
            }

            h1 {
                font-size: 1rem;
                color: #000000;
                font-weight: bolder;
            }
        }

    }

    .bottom {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 10px;
        padding-right: 40px;
        padding-bottom: 10px;
        line-height: 2em;
        gap: 30px;


        .pay {
            display: flex;
            flex-direction: column;
            align-items: center;

            p {
                font-size: 1rem;
                color: #ff5e00;
                font-weight: 600;
                padding-right: 35px;
            }

            h1 {
                font-size: 3rem;
                color: #000000;
                font-weight: bolder;
            }

        }

        img {
            rotate: -15deg;
            padding-left: 30px;
            width: 120px;
        }

    }
}

.users {
    grid-area: users;
    font-family: 'Montserrat', sans-serif;
    background: #f2f2f2;
    padding: 10px;
    width: 100%;
    border-radius: 10px;
    cursor: pointer;

    h1 {
        font-size: 1.5rem;
        color: #ff5e00;
        font-weight: 600;
        text-align: start;
        padding-left: 10px;
    }

    .usersdiv {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding: 10px 0px 10px 10px;
        gap: 10px;

        p {
            font-size: 1rem;
            color: #000000;
            font-weight: 500;
        }

        img {
            width: 20px;
        }
    }

    .usersdiv:hover {
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
    }
}


.grid-aside {
    display: grid;
    grid-template-areas:
        'baskets baskets'
        'dates album'
        'maps maps'
        'costs costs'
        'users users';
    grid-gap: 10px;
    padding: 10px;
    width: 100%;
    height: max-content;
}

.notSelected {
    display: none;
}

.mapagoogle {
    border-radius: 10px;
}

.members-asignar:hover {
    background-color: #f0f0f0;

    .hover-members {
        background-color: #f0f0f0;
    }
}

.minus:hover,
.plus:hover {
    background-color: #e0e0e0;
    border-radius: 50%;
}
</style>