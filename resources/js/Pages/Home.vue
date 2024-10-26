<template>
    <NavbarVue :selected="project.id" :base_url="project.base_url" :projects="projects" />

    <div class="row w-100">
        <div class="col-2" style="height: 92vh;overflow-y: scroll">
            <SidebarVue @selected="selected" :project_id="project.id" :groups="project.groups" />
        </div>
        <div class="col-8 d-flex justify-content-center" style="height: 92vh;overflow: scroll">
            <ApiDetailsVue :api="selectedApi" :selectedId="selectedId" />
        </div>
    </div>

</template>

<script setup>
import ApiDetailsVue from "./Components/ApiDetails.vue";
import NavbarVue from "./Components/Navbar.vue";
import SidebarVue from "./Components/Sidebar.vue";
import { usePage } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";
import { onMounted, onUpdated, ref } from 'vue';
import axios from "axios";

const toast = useToast();
const $page = usePage();
const selectedId = ref(null);
const selectedApi = ref(null);

const props = defineProps({
    projects: Object,
    project: Object,
    api : Object
})

onMounted(() => {

    if(props.api){
        selected(props.api);
    }

    if ($page.props.flash.message != null) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});

onUpdated(() => {

    if(props.api){
        selected(props.api);
    }

    if ($page.props.flash.message != null) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});

const selected = (api) => {
    selectedId.value = api.id;
    axios.get('/api/docs/' + api.id).then((response) => {
        selectedApi.value = response.data.data
        console.log(response.data.data);

    })

}




</script>

<style></style>
