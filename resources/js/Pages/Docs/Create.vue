<template>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="col-10 d-flex justify-content-center align-items-center">
            <form class="form">
                <div class="d-flex justify-content-start">
                    <p class="title">Create new Api docs</p>
                </div>
                <!-- <p class="message">Signup now and create your docs free. </p> -->
                <div class="d-flex align-items-center justify-content-end">
                    <span class="me-2 text-white" style="font-size: 15px;">
                        Authorization: Bearer $token
                    </span>
                    <Toggle @toggle="form.is_authorized = !form.is_authorized"/>
                </div>
                <div class="d-flex">
                    <label class="col-2 pe-2">
                    <select v-model="form.method" class="input form-select" type="email" placeholder="" required="">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                    <span>Method</span>
                </label>
                <label class="col-10">
                    <input v-model="form.endpoints" class="input" type="email" placeholder="" required="">
                    <span>End Points</span>
                </label>
                </div>
                <label>
                    <textarea v-model="form.description" class="input" type="text" placeholder="" required=""></textarea>
                    <span>Description</span>
                </label>

                <label>
                    <textarea v-model="form.request" class="input" type="text" placeholder="" required=""></textarea>
                    <span>Request</span>
                </label>

                <label>
                    <textarea v-model="form.response" class="input" type="text" placeholder="" required=""></textarea>
                    <span>Response</span>
                </label>
                <div v-for="error,index in errors" :key="error" class="d-flex">
                    <label class="col-3 pe-2">
                        <input v-model="error.status_code" class="input" type="email" placeholder="" required="">
                        <span>Status Code</span>
                </label>
                <label class="col-8">
                    <input v-model="error.message" class="input" type="email" placeholder="" required="">
                    <span>Message</span>
                </label>
                <CloseButton @click="removeError(index)"/>
                </div>
                <div class="d-flex">
                    <label class="col-3 pe-2">
                        <input v-model="status_code" class="input" type="email" placeholder="" required="">
                        <span>Status Code</span>
                </label>
                <label class="col-7">
                    <input v-model="message" class="input" type="email" placeholder="" required="">
                    <span>Message</span>
                </label>
                <AddButton @click="addError" class="col-2 ms-1"/>
                </div>
                <button type="button" @click="submit" class="submit mt-2 w-25">Create</button>

            </form>
        </div>

    </div>

</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";
import AddButton from '../Components/AddButton.vue';
import Toggle from '../Components/Toggle.vue';
import CloseButton from '../Components/CloseButton.vue';
import {ref} from 'vue';

const toast = useToast();
const props = defineProps({
    apiId : Number
})

const status_code = ref(null);
const message = ref(null);
const errors = ref([]);

const form = useForm({
    'api_id' : props.apiId,
    'method' : 'GET',
    'endpoints' : '',
    'description' : '',
    'is_authorized' : false,
    'request' : '',
    'response' : '',
    'error_responses' : [],
});

const addError = () => {

    if(status_code.value == null || message.value == null){
        toast.warning('Status code and message are required');
        return;
    }

    errors.value.push({
        status_code : status_code.value,
        message : message.value
    })

    status_code.value = null;
    message.value = null;

}

const removeError = (index) => {

    errors.value.splice(index, 1);
}

const submit = () => {

    if(form.endpoints == '' || form.description == '' || form.request == '' || form.response == ''){
        toast.warning('All fields are required');
        return;
    }

    form.error_responses = errors.value;

    form.post('/docs/store',{
        onError: (error) => toast.error(error.message)
    });

}

</script>

<style scoped>
.form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
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
    background-color: #7a50fa;
}

.submit:hover {
    background-color: #A78BFA;
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
