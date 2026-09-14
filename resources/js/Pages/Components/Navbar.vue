<template>
    <nav class="navbar">
        <div class="container-fluid px-4 py-2 d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white d-flex align-items-center" href="/projects">
                <img src="https://cdn-icons-png.flaticon.com/128/809/809052.png" alt="Logo" width="30" height="24"
                    class="d-inline-block align-text-top me-2">
                BeeDocs
            </a>
            <div class="base-url-display text-white">
                <span style="margin-left:300px">Base URL :</span><span class="text-white p-2 rounded rounded-3 fw-bold"
                    style="color:#C38AEA !important;">{{ props.base_url }}</span>
            </div>
            <div class="text-white d-flex align-items-center gap-2" style="cursor:pointer">
                <div class="dropdown project-dropdown">
                    <button class="project-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="project-mark">P</span>
                        <span class="project-name">{{ selectedProject?.name || 'Select project' }}</span>
                        <span class="project-chevron">⌄</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end project-menu">
                        <div class="project-menu-label">Switch project</div>
                        <button v-for="project in projects" :key="project.id" type="button" class="project-option" :class="{ active: project.id === selectedProjectId }" @click="switchProject(project.id)">
                            <span class="option-mark">{{ project.name?.charAt(0)?.toUpperCase() }}</span>
                            <span class="option-copy"><strong>{{ project.name }}</strong><small>{{ project.base_url || 'No base URL' }}</small></span>
                            <span v-if="project.id === selectedProjectId" class="check">✓</span>
                        </button>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="profile-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open profile menu">{{ userInitial }}</button>
                    <div class="dropdown-menu dropdown-menu-end profile-menu">
                        <div class="profile-summary"><div class="profile-avatar">{{ userInitial }}</div><div class="profile-copy"><strong>{{ user?.name || 'BeeDocs user' }}</strong><span>{{ user?.email || '' }}</span></div></div>
                        <div class="profile-divider"></div>
                        <button class="profile-action" type="button" @click="logout"><span>Sign out</span><span aria-hidden="true">→</span></button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { useToast } from 'vue-toastification';
import { computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const toast = useToast();
const page = usePage();
const user = computed(() => page.props.auth?.user);
const userInitial = computed(() => user.value?.name?.trim()?.charAt(0)?.toUpperCase() || 'U');

const props = defineProps({
    base_url: String,
    selected: Number,
    projects: Object
})

const selectedProjectId = ref(props.selected);
const selectedProject = computed(() => props.projects?.find(project => project.id === selectedProjectId.value));

const switchProject = (id) => {
    if (id === selectedProjectId.value) return;
    selectedProjectId.value = id;
    router.get('/projects/' + id + '/view');
}

const logout = () => {
    router.post('/logout');
}

</script>

<style scoped>
.base-url-display{position:absolute;left:50%;max-width:46%;transform:translateX(-50%);text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.base-url-display span{display:inline}.base-url-display span:last-child{overflow-wrap:anywhere}@media(max-width:800px){.base-url-display{display:none}}
.project-trigger{display:flex;width:210px;height:42px;align-items:center;gap:9px;padding:0 10px;border:1px solid rgba(215,188,255,.16);border-radius:11px;background:rgba(9,3,15,.62);color:#fff;text-align:left;box-shadow:inset 0 1px 0 rgba(255,255,255,.035)}.project-trigger:hover,.project-trigger[aria-expanded="true"]{border-color:rgba(167,139,250,.48);background:rgba(35,15,48,.78)}.project-mark,.option-mark{display:grid;place-items:center;width:25px;height:25px;flex:0 0 25px;border-radius:8px;background:rgba(167,139,250,.16);color:#d8b4fe;font-size:10px;font-weight:800}.project-name{overflow:hidden;flex:1;font-size:12px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}.project-chevron{color:#8e7c9b;font-size:14px;transform:translateY(-2px)}.project-menu{width:280px;margin-top:10px!important;padding:8px!important;background:linear-gradient(145deg,#241132,#14071d)!important;border:1px solid rgba(215,188,255,.16)!important;border-radius:15px!important;box-shadow:0 24px 65px rgba(3,0,8,.55)!important}.project-menu-label{padding:7px 9px 9px;color:#8f7e9b;font-size:9px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.project-option{display:flex;width:100%;align-items:center;gap:10px;padding:9px;border:0;border-radius:10px;background:transparent;color:#ddd4e2;text-align:left}.project-option:hover{background:rgba(167,139,250,.1);color:#fff}.project-option.active{background:rgba(167,139,250,.13);color:#fff}.option-copy{display:flex;min-width:0;flex:1;flex-direction:column}.option-copy strong,.option-copy small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.option-copy strong{font-size:12px}.option-copy small{margin-top:2px;color:#8f8298;font-size:9px}.check{display:grid;place-items:center;width:20px;height:20px;border-radius:7px;background:rgba(52,211,153,.11);color:#6ee7b7;font-size:10px;font-weight:800}@media(max-width:600px){.project-trigger{width:150px}.project-mark{display:none}}
.profile-trigger{display:grid;place-items:center;width:42px;height:42px;padding:0;border:2px solid #a78bfa;border-radius:13px;background:linear-gradient(145deg,#c4b5fd,#7c3aed);color:white;font-weight:800;box-shadow:0 8px 24px rgba(124,58,237,.3)}
.profile-menu{width:280px;margin-top:12px!important;padding:10px!important;background:#1b0d29!important;border:1px solid rgba(215,188,255,.14)!important;border-radius:16px!important;box-shadow:0 24px 70px rgba(4,0,10,.48)!important}.profile-summary{display:flex;align-items:center;gap:12px;padding:10px}.profile-avatar{display:grid;place-items:center;flex:0 0 42px;height:42px;border-radius:12px;background:rgba(167,139,250,.16);color:#d8b4fe;font-weight:800}.profile-copy{display:flex;min-width:0;flex-direction:column}.profile-copy strong,.profile-copy span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.profile-copy strong{color:#fff;font-size:14px}.profile-copy span{color:#aa9bb8;font-size:12px}.profile-divider{height:1px;margin:6px 0;background:rgba(215,188,255,.1)}.profile-action{display:flex;width:100%;align-items:center;justify-content:space-between;padding:10px 12px;border:0;border-radius:9px;background:transparent;color:#d8cfdf;font-size:13px;font-weight:700}.profile-action:hover{background:rgba(251,113,133,.1);color:#fb7185}
</style>
