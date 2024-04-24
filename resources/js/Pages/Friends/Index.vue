<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
const props = defineProps({
  friends: {
    type: Object,
    required: true,
  },
});
function DeleteFriend(friendId) {
    axios.post(route('friends.destroy', { id: friendId }), {
        _method: 'DELETE'
    })
    .then(response => {
        window.location.reload();
    })
}
</script>
<template>
    <MainLayout title="Inici">
        <template #main-content>
            <div class="grid grid-friends">
                <div v-for="friend in friends" :key="friends.id"
                    class="friend-card w-40 bg-white border border-gray-200 rounded-2xl dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex justify-end px-2 pt-3">
                    </div>
                    <div class="flex flex-col items-center pb-5">
                        <img class="w-32 h-32 mb-3 rounded-full shadow-lg" src="assets/img/user.png" alt="User Image" />
                        <h5 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">{{friend.name}}</h5>
                        <div class="flex md:mt-2">
                            <button @click="DeleteFriend(friend.id)"
                                class="inline-flex items-center px-4 py-1 text-sm font-medium text-center text-white bg-red-700 rounded-2xl hover:bg-red-800 focus:ring-2 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Eliminar
                                amic</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <button type="button"
                    class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Mostrar
                    més</button>
            </div>
        </template>
        <template #right-aside>
            <div class="aside-menu">
                <span class="text-2xl text-gray-800 dark:text-white font-bold">Gent a prop teu</span>
                <div v-for="(person, index) in [0, 0, 0, 0, 0]" :key="index" class="flex aside-friend">
                    <img class="w-12 h-12 rounded-full shadow-lg" src="assets/img/user.png" alt="User Image" />
                    <span class="user text-xl text-gray-800 dark:text-white p-2.5">Marcos Cardat</span>
                    <div class="plus pl-4 flex justify-end">
                        <button class="w-8 h-8 rounded-full border-transparent hover:bg-gray-300">
                            <img src="assets/img/plus.png">
                        </button>
                    </div>
                </div>
                <div class="people-nearby text-center">
                    <button>
                        <span class="text-lg text-gray-800 dark:text-white font-bold">Mostrar més</span>
                    </button>
                </div>
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
    padding: 26px 50px;
}

.grid-friends {
    grid-template-columns: repeat(auto-fit, minmax(9rem, 1fr));
    gap: 20px;
}

.friend-card {
    width: 100%;
}

.aside-friend {
    align-items: center;
    padding: 5px 0px;
}
</style>
