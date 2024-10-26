<template>
    <div class="sidebar ms-4 ps-1">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h5>Docs Groups</h5>
            <div>
                <button @click="dialog = true"
                    class="btn text-white d-flex justify-content-center align-items-center mb-1" style="border:none">
                    <font-awesome-icon icon="fa-solid fa-plus" style="font-size: 15px;" />
                </button>
                <template>
                    <v-dialog v-model="dialog" width="auto">
                        <v-card width="500" prepend-icon="mdi-update" title="Create new group" class="text-center px-5"
                            style="background-color:black;color:white">
                            <v-text-field v-model="form.name" label="Group name" variant="outlined"></v-text-field>
                            <template v-slot:actions>
                                <v-btn text="Cancel" @click="dialog = false"></v-btn>
                                <v-btn text="Create" @click="createGroup"></v-btn>
                            </template>
                        </v-card>
                    </v-dialog>
                </template>
            </div>
        </div>
        <div class="mt-2">
            <div v-for="(group, index) in groups" :key="group.id">
                <div class="d-flex">
                    <button class="btn my-3 text-white my-1 d-flex justify-content-between align-items-center"
                        style="border:solid 1px grey;width:90%" @click="openGroup(index)">
                        <span>
                            <font-awesome-icon icon="fa-solid fa-angle-down" class="me-1" />
                            {{ group.name }}
                        </span>
                    </button>
                    <div class="dropright d-flex align-items-center" style="width:10%">
                        <button class="ms-3" data-bs-toggle="dropdown" style="width:100%">
                            <font-awesome-icon icon="fa-solid fa-ellipsis-vertical"/>
                        </button>
                        <ul class="dropdown-menu bg-black">
                            <li class="dropdown-item bg-black cursor-pointer">
                                <div @click="openApiDialog(group.id)" class="text-white p-2 rounded-pill text-center" style="border:solid 0.5px grey">
                                    <font-awesome-icon icon="fa-solid fa-plus" /> Create
                                </div>
                                <template>
                                    <v-dialog v-model="apiDialog" width="auto">
                                        <v-card width="500" prepend-icon="mdi-update" title="Create new api"
                                            class="text-center px-5" style="background-color:black;color:white">
                                            <v-text-field v-model="apiForm.name" label="Api name"
                                                variant="outlined"></v-text-field>
                                            <template v-slot:actions>
                                                <v-btn text="Cancel" @click="apiDialog = false"></v-btn>
                                                <v-btn text="Delete" @click="createApi"></v-btn>
                                            </template>
                                        </v-card>
                                    </v-dialog>
                                </template>
                                <div @click="deleteGroup(group.id)" class="mt-2 text-white p-2 rounded-pill text-center" style="border:solid 0.5px grey">
                                    <font-awesome-icon icon="fa-solid fa-trash-can" class="me-1" style="font-size:135x" /> Delete
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-if="isOpen[index]">
                    <div v-for="api in group.apis" :key="api">
                        <button @click="$emit('selected', api), selectedApi(api.id)"
                            class="btn text-white my-1 d-flex justify-content-between align-items-center ms-5 ps-4"
                            style="width:91%;">
                            <span>
                                <span v-if="selectedId == api.id" style="color:#A78BFA">{{ api.name }}</span>
                                <span v-else>{{ api.name }}</span>
                                <span v-if="selectedId == api.id" class="selected"></span>
                            </span>
                            <font-awesome-icon @click="deleteApi(api.id)" icon="fa-solid fa-trash-can" style="font-size:135x" />
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="groups.length == 0" class="text-center mt-5 pt-5">
                No groups
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUpdated } from 'vue'
import { useForm, usePage,router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import axios from 'axios';

const toast = useToast();
const $page = usePage();
const dialog = ref(false)
const apiDialog = ref(false)
const isOpen = ref([]);
const selectedId = ref(null);

const selectedApi = (id) => {
    selectedId.value = id
    axios.post('/api/set-active-api', {
        headers: { Authorization: `Bearer ${token}` },
        params: {
            'api_id': id
        }
    }).then((response) => {
        console.log(response);
    })
}

const props = defineProps({
    project_id: Number,
    groups: Object
})

const form = useForm({
    'project_id': props.project_id,
    'name': ""
});

const apiForm = useForm({
    'group_id': "",
    'name': ""
})

onMounted(() => {

    props.groups.forEach((group) => {
        isOpen.value.push(false)
    })


    if ($page.props.flash.message) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});


onUpdated(() => {
    if ($page.props.flash.message) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});

const openApiDialog = (groupId) => {
    apiDialog.value = true;
    apiForm.group_id = groupId
}

const createGroup = () => {

    if (form.name == "") {
        toast.warning('Group name is required');
        return;
    }

    form.post('/groups', {
        onSuccess: (response) => {
            dialog.value = false;
        },
        onError: (error) => toast.error(error.message)
    });

}

const createApi = () => {

    if (apiForm.name == "") {
        toast.warning('Api name is required');
        return;
    }

    apiForm.post('/apis', {
        onSuccess: (response) => {
            apiDialog.value = false;
        },
        onError: (error) => toast.error(error.message)
    })

}

const openGroup = (index) => {

    isOpen.value[index] = !isOpen.value[index]
}

const deleteGroup = (groupId) => {
    router.delete('/groups/'+groupId);
}

const deleteApi = (apiId) => {
    router.delete('/apis/'+apiId);
}

</script>

<style scoped>
.sidebar {
    color: white
}

.selected::before {
    width: 18px;
    height: 18px;
}

.selected::after {
    width: 18px;
    height: 18px;
    animation: pulse 1s linear infinite;
}

.selected::before,
.selected::after {
    position: absolute;
    content: "";
    height: 10px;
    width: 10px;
    border-radius: 50%;
    margin-top: 7px;
    left: 40px;
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
