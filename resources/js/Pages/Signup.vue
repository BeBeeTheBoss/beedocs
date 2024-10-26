<template>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="col-5 d-flex justify-content-center align-items-center">
            <form class="form">
                <p class="title">Register to BeeDocs</p>
                <p class="message">Signup now and create your docs free. </p>
                <label>
                    <input v-model="form.name" class="input" type="text" placeholder="" required="">
                    <span>Username</span>
                </label>

                <!-- <label>
                        <input class="input" type="text" placeholder="" required="">
                        <span>Lastname</span>
                    </label> -->

                <label>
                    <input v-model="form.email" class="input" type="email" placeholder="" required="">
                    <span>Email</span>
                </label>

                <label>
                    <input v-model="form.password" class="input" type="password" placeholder="" required="">
                    <span>Password</span>
                </label>
                <label>
                    <input v-model="form.confirm_password" class="input" type="password" placeholder="" required="">
                    <span>Confirm password</span>
                </label>
                <button type="button" @click="submit" class="submit">Submit</button>
                <p class="signin">Already have an acount ?
                    <Link :href="'/login'" style="color:white">Login</Link>
                </p>
            </form>
        </div>

    </div>

</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";

const toast = useToast();

const form = useForm({
    name: '',
    email: '',
    password: '',
    confirm_password: '',
});


const submit = () => {
    if(form.name == '' || form.email == '' || form.password == '' || form.confirm_password == ''){
        toast.warning('All fields are required');
        return;
    }

    if(!form.email.includes('@gmail.com')){
        toast.warning('Invalid email');
        return;
    }

    if(form.password != form.confirm_password){
        toast.warning('Passwords do not match');
        return;
    }

    form.post('/signup');

}

</script>

<style scoped>
/* From Uiverse.io by ammarsaa */
.form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 350px;
    padding: 20px;
    border-radius: 20px;
    position: relative;
    color: #fff;
}

.title {
    font-size: 28px;
    font-weight: 600;
    letter-spacing: -1px;
    position: relative;
    display: flex;
    align-items: center;
    padding-left: 30px;
    color: #A78BFA;
}

.title::before {
    width: 18px;
    height: 18px;
}

.title::after {
    width: 18px;
    height: 18px;
    animation: pulse 1s linear infinite;
}

.title::before,
.title::after {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    border-radius: 50%;
    left: 0px;
    background-color: #A78BFA;
}

.message,
.signin {
    font-size: 14.5px;
    color: rgba(255, 255, 255, 0.7);
}

.signin {
    text-align: center;
}

.signin a:hover {
    text-decoration: underline royalblue;
}

.signin a {
    color: #00bfff;
}

.flex {
    display: flex;
    width: 100%;
    gap: 6px;
}

.form label {
    position: relative;
}

.form label .input {
    background-color: #111827;
    color: #fff;
    width: 100%;
    padding: 20px 05px 05px 10px;
    outline: 0;
    border: 1px solid rgba(105, 105, 105, 0.397);
    border-radius: 10px;
}

.form label .input+span {
    color: rgba(255, 255, 255, 0.5);
    position: absolute;
    left: 10px;
    top: 0px;
    font-size: 0.9em;
    cursor: text;
    transition: 0.3s ease;
}

.form label .input:placeholder-shown+span {
    top: 12.5px;
    font-size: 0.9em;
}

.form label .input:focus+span,
.form label .input:valid+span {
    color: #00bfff;
    top: 0px;
    font-size: 0.7em;
    font-weight: 600;
}

.input {
    font-size: medium;
}

.submit {
    border: none;
    outline: none;
    padding: 10px;
    border-radius: 10px;
    color: #fff;
    font-size: 16px;
    transform: .3s ease;
    background-color: #A78BFA;
}

.submit:hover {
    background-color: #7a50fa;
}

@keyframes pulse {
    from {
        transform: scale(0.9);
        opacity: 1;
    }

    to {
        transform: scale(1.8);
        opacity: 0;
    }
}
</style>
