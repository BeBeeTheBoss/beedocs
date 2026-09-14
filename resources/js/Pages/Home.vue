<template>
    <NavbarVue :selected="project.id" :base_url="project.base_url" :projects="projects" />

    <div class="row w-100 m-0">
        <div class="col-2 sidebar-column" style="height: 92vh;overflow-y: auto">
            <SidebarVue @selected="selected" :project_id="project.id" :groups="project.groups" :selected_id="selectedId" :readonly="!canEdit" />
        </div>
        <div class="col-10 content-column" style="height: 92vh;overflow: auto">
            <ApiDetailsVue :api="selectedApi" :selectedId="selectedId" :readonly="!canEdit" />
        </div>
    </div>

</template>

<script setup>
import ApiDetailsVue from "./Components/ApiDetails.vue";
import NavbarVue from "./Components/Navbar.vue";
import SidebarVue from "./Components/Sidebar.vue";
import { usePage } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";
import { onMounted, onUpdated, ref, watch } from 'vue';
import axios from "axios";

const toast = useToast();
const $page = usePage();
const selectedId = ref(null);
const selectedApi = ref(null);

const props = defineProps({
    projects: Object,
    project: Object,
    api : Object,
    canEdit: { type: Boolean, default: true }
})

const storageKey = () => `beedocs:project:${props.project.id}:selected-api`;
const projectApis = () => props.project.groups.flatMap(group => group.apis || []);
const restoreSelection = () => {
    const savedId = Number(localStorage.getItem(storageKey()));
    const savedApi = projectApis().find(api => api.id === savedId);
    const fallbackApi = props.api && projectApis().find(api => api.id === props.api.id);
    const api = savedApi || fallbackApi;
    if (api) selected(api);
    else { selectedId.value = null; selectedApi.value = null; }
}

onMounted(() => {
    restoreSelection();

    if ($page.props.flash.message != null) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});

onUpdated(() => {
    if ($page.props.flash.message != null) {
        toast.success($page.props.flash.message);
        $page.props.flash.message = null;
    }
});

const selected = (api) => {
    selectedId.value = api.id;
    localStorage.setItem(storageKey(), String(api.id));
    window.history.replaceState({}, '', `/projects/${props.project.id}/apis/${api.id}`);
    axios.get(`/projects/${props.project.id}/apis/${api.id}/data`).then((response) => {
        selectedApi.value = response.data.data
        console.log(response.data.data);

    })

}

watch(() => props.project.id, restoreSelection);




</script>

<style>
.sidebar-column{background:transparent;border-right:1px solid rgba(215,188,255,.1)}
.content-column{position:relative;display:flex;justify-content:center;min-width:0;padding:0!important}
@media(max-width:900px){.sidebar-column{width:280px!important}}
</style>
