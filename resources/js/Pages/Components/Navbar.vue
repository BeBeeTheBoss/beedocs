<template>
    <nav class="navbar">
        <div class="container-fluid px-4 py-2 d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white d-flex align-items-center" href="/projects">
                <img src="https://cdn-icons-png.flaticon.com/128/809/809052.png" alt="Logo" width="30" height="24"
                    class="d-inline-block align-text-top me-2">
                BeeDocs
            </a>
            <div class="text-white ms-5 ps-5">
                <span style="">Base URL :</span><span class="text-white p-2 rounded rounded-3 fw-bold"
                    style="color:#C38AEA !important">{{ props.base_url }}</span>
            </div>
            <div class="text-white d-flex align-items-center dropdown" style="cursor:pointer">
                <select v-model="selectedProjectId" @change="change" name="" class="form-select me-3 bg-black text-white" :value="selectedProjectId" style="width:200px;border:none;outline:none">
                    <option v-for="project in projects" :key="project" :value="project.id">{{ project.name }}</option>
                </select>
                <img src="https://cdn-icons-png.flaticon.com/128/7915/7915354.png" class="img-thumbnail" data-bs-toggle="dropdown"
                    style="width:30px;border:solid 1px white;padding:2px;border-radius:50%">
                <ul class="dropdown-menu bg-black" style="margin-left:80px">
                    <li @click="logout" class="dropdown-item text-white btn bg-black">
                        Logout
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { useToast } from 'vue-toastification';
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const toast = useToast();

const props = defineProps({
    base_url: String,
    selected: Number,
    projects: Object
})

const selectedProjectId = ref(props.selected);

const change = () => {
    router.post('/projects/set-active/'+selectedProjectId.value);
}

const logout = () => {
    router.post('/logout');
}

</script>

<style></style>
