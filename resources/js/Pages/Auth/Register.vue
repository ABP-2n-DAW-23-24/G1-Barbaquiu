<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const googleLogin = () => {
    window.location.href = '/auth/google';
};
</script>

<template>
    <Head title="Register" />

    <div class="center">

        <form @submit.prevent="submit" class="form">
            <div class="flex-column">
                <label for="name">Nom</label>
            </div>
            <div class="inputForm">
                <img src="/assets/svg/user.svg" alt="Arrova" class="w-5">
                <input
                    id="name"
                    type="text"
                    class="input" 
                    placeholder="Escriu el teu Nom" 
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                >
            </div>

            <div class="flex-column">
                <label for="email">Email</label>
            </div>
            <div class="inputForm">
                <img src="/assets/svg/arroba.svg" alt="Arrova" class="w-5">
                <input
                    id="email"
                    type="email"
                    class="input" 
                    placeholder="Escriu el teu Email" 
                    v-model="form.email"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="flex-column">
                <label for="password">Contrasenya</label>
            </div>
            <div class="inputForm">
                <img src="/assets/svg/lock.svg" alt="Lock" class="w-5">
                <input 
                    id="password"
                    type="password"
                    class="input"
                    placeholder="Escriu la teva Contrasenya"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                >
            </div>

             <div class="flex-column">
                <label for="password_confirmation">Repeteix la contrasenya</label>
            </div>
            <div class="inputForm">
                <img src="/assets/svg/lock.svg" alt="Lock" class="w-5">
                <input 
                    id="password_confirmation"
                    type="password"
                    class="input"
                    placeholder="Repeteix la Contrasenya"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="password_confirmation"
                >
            </div>

            <button 
                class="button-submit" 
                :class="{ 'opacity-25': form.processing }" 
                :disabled="form.processing"
            >
                Registra't
            </button>
            
            <p class="p">
                Ja tens compte? <Link :href="route('login')" class="span">Incia Sessió</Link>
            </p>

            <p class="p line">O amb</p>

            <div class="flex-row">
                <button 
                    class="btn google"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="googleLogin"
                >
                    <img src="/assets/svg/google_icon.svg" alt="Google" class="w-8">
                    Google
                </button>
            </div>
        </form>
    </div>
</template>


<style scoped>
.center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background-color: #ffffff;
    padding: 30px;
    width: 450px;
    border-radius: 20px;
    box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.3);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

::placeholder {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.form button {
    align-self: flex-end;
}

.flex-column>label {
    color: #151717;
    font-weight: 600;
}

.inputForm {
    border: 1.5px solid #ecedec;
    border-radius: 10px;
    height: 50px;
    display: flex;
    align-items: center;
    padding-left: 10px;
    transition: 0.2s ease-in-out;
}

.input {
    border-radius: 10px;
    border: none;
    width: 100%;
    height: 100%;
    padding-left: 10px;
}

.input:focus {
    outline: none;
    --tw-ring-shadow: none !important;
}

.inputForm:focus-within {
    border: 1.5px solid #2d79f3;
}

.flex-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    justify-content: space-between;
}

.flex-row > div > label {
    font-size: 14px;
    color: black;
    font-weight: 400;
    padding-left: 10px;
}

.span {
    font-size: 14px;
    margin-left: 5px;
    color: #2d79f3;
    font-weight: 500;
    cursor: pointer;
}

.button-submit {
    margin: 20px 0 10px 0;
    background-color: #151717;
    border: none;
    color: white;
    font-size: 15px;
    font-weight: 500;
    border-radius: 10px;
    height: 50px;
    width: 100%;
    cursor: pointer;
}

.p {
    text-align: center;
    color: black;
    font-size: 14px;
    margin: 5px 0;
}

.btn {
    margin-top: 10px;
    width: 100%;
    height: 50px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 500;
    gap: 10px;
    border: 1px solid #ededef;
    background-color: white;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}

.btn:hover {
    border: 1px solid #2d79f3;
}
</style>