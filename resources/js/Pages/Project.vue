<template>
    <nav class="navbar">
        <div class="container-fluid px-4 py-2 d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white d-flex align-items-center" href="#">
                <img src="https://cdn-icons-png.flaticon.com/128/809/809052.png" alt="Logo" width="30" height="24"
                    class="d-inline-block align-text-top me-2">
                BeeDocs
            </a>
            <div class="text-white d-flex align-items-center" style="cursor:pointer">
                <button @click="dialog = true" class="btn bg-black text-white me-3">
                    Create project
                    <FontAwesomeIcon class="ms-1" icon="fa-solid fa-plus" />
                </button>
                <template>
                    <div class="text-center pa-4">
                        <v-dialog v-model="dialog" width="auto">
                            <v-card width="500" prepend-icon="mdi-update" title="Create new project"
                                class="text-center px-5" style="background-color:black;color:white">
                                <v-text-field v-model="form.name" label="Project name"
                                    variant="outlined"></v-text-field>
                                <v-text-field v-model="form.base_url" label="Base URL"
                                    variant="outlined"></v-text-field>
                                <template v-slot:actions>
                                    <v-btn text="Cancel" @click="dialog = false"></v-btn>
                                    <v-btn text="Create" @click="createProject"></v-btn>
                                </template>
                            </v-card>
                        </v-dialog>
                    </div>
                </template>
                <img src="https://cdn-icons-png.flaticon.com/128/7915/7915354.png" class="img-thumbnail"
                    style="width:30px;border:solid 1px white;padding:2px;border-radius:50%">
            </div>
        </div>
    </nav>
    <div class="container-fluid px-5">
        <div v-if="props.projects.length == 0" class="row d-flex justify-content-center align-items-center"
            style="height: 90vh">
            <div class="col-2 text-center">
                <img src="../../../public/images/empty.svg" alt="">
                <span class="text-white fs-5">No projects</span>
            </div>
        </div>
        <div class="container">
            <h5 class="text-white fw-bold mt-5 pt-5">My projects</h5>
            <div class="row mt-5">
                <div class="col-3" v-for="project in props.projects" :key="project.id">
                    <div
                        class="card-sm p-3 my-3 text-white project rounded-3 d-flex justify-content-between align-items-center"
                        style="background-color:#210134">
                        <div>
                            <div class="card-title fw-bold fs-5 h-100 mb-2 projectLink" @click="enterProject(project.id)">{{ project.name }}</div>
                            <div class="text-white text-muted" style="font-size:13px">{{ project.base_url || 'http://192.168.100.13:800/api' }}</div>
                        </div>
                        <div class="dropdown">
                            <div data-bs-toggle="dropdown" class="d-flex justify-content-center align-items-center"
                                style="width:30px;height:30px;cursor:pointer">
                                <FontAwesomeIcon icon="fa-solid fa-ellipsis-vertical" />
                            </div>
                            <ul class="dropdown-menu bg-dark">
                                <button class="btn bg-dark w-100 text-white text-start"
                                    @click="editProjectDialog(project)">
                                    <FontAwesomeIcon icon="fa-solid fa-pen-to-square" class="me-2" /> Edit
                                </button>
                                <button class="btn bg-dark w-100 text-white text-start"
                                    @click="deleteProject(project.id)">
                                    <FontAwesomeIcon icon="fa-solid fa-trash-can" class="me-2" /> Delete
                                </button>
                            </ul>
                        </div>
                        <template>
                            <v-dialog v-model="editDialog" width="auto">
                                <v-card width="500" prepend-icon="mdi-update" title="Edit project"
                                    class="text-center px-5" style="background-color:black;color:white">
                                    <v-text-field v-model="editForm.name" label="Project name"
                                        variant="outlined"></v-text-field>
                                    <v-text-field v-model="editForm.base_url" label="Base URL"
                                        variant="outlined"></v-text-field>
                                    <template v-slot:actions>
                                        <v-btn text="Cancel" @click="editDialog = false"></v-btn>
                                        <v-btn text="Update" @click="updateProject"></v-btn>
                                    </template>
                                </v-card>
                            </v-dialog>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUpdated } from 'vue';
import { usePage } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";

const toast = useToast();
const $page = usePage();
const dialog = ref(false);
const editDialog = ref(false);

const form = useForm({
    name: '',
    base_url: '',
});

const props = defineProps({
    projects: Object
})

const editForm = useForm({
    id: "",
    name: "",
    base_url: "",
})

const editProjectDialog = (project) => {

    editForm.id = project.id;
    editForm.name = project.name;
    editForm.base_url = project.base_url;
    editDialog.value = true;

}

const createProject = () => {

    if (form.name == '') {
        toast.warning('Project name is required');
        return;
    }

    form.post('/projects', {
        onSuccess: (response) => {
            dialog.value = false;
        },
        onError: (error) => toast.error(error.message)
    });

}

const updateProject = () => {
    if (editForm.name == '') {
        toast.warning('Project name is required');
        return;
    }


    editForm.post('/projects/update', {
        onSuccess: (response) => {
            editDialog.value = false;
        },
        onError: (error) => toast.error(error.message)
    });

}

const deleteProject = (id) => {

    router.delete('/projects/' + id);

}

const enterProject = (id) => {

    router.post('/projects/set-active/' + id);

}

onMounted(() => {
    if ($page.props.flash.message) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
})

onUpdated(() => {

    if ($page.props.flash.message) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});

</script>

<style scoped>
.project {
    background: #210133;
    box-shadow: 15px 15px 30px rgba(147, 147, 147, 0.051),
        -15px -15px 30px rgba(147, 147, 147, 0.051);
}
.projectLink:hover{
    text-decoration: underline;
    cursor: pointer;
}
</style>
