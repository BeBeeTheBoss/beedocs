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
                            <v-card width="500" prepend-icon="mdi-update" title="Create new project" @keydown.enter.prevent="createProject"
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
                <div class="dropdown">
                    <button class="profile-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open profile menu">
                        {{ userInitial }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-end profile-menu">
                        <div class="profile-summary">
                            <div class="profile-avatar">{{ userInitial }}</div>
                            <div class="profile-copy">
                                <strong>{{ user?.name || 'BeeDocs user' }}</strong>
                                <span>{{ user?.email || '' }}</span>
                            </div>
                        </div>
                        <div class="profile-divider"></div>
                        <button class="profile-action" type="button" @click="logout">
                            <span>Sign out</span><span aria-hidden="true">→</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="container-fluid px-5">
        <div v-if="props.projects.length === 0" class="projects-empty">
            <div class="empty-project-visual" aria-hidden="true">
                <div class="empty-window"><div class="empty-window-bar"><i></i><i></i><i></i></div><div class="empty-window-body"><span></span><span></span><span></span></div></div>
                <div class="empty-plus">+</div>
            </div>
            <span class="section-eyebrow">Your workspace is ready</span>
            <h1>Create your first project</h1>
            <p>Start by creating a project, then organize your API endpoints into clear documentation collections.</p>
            <button type="button" class="empty-create-button" @click="dialog = true">Create project <FontAwesomeIcon icon="fa-solid fa-plus" /></button>
        </div>
        <div v-else class="container projects-container">
            <div class="projects-heading">
                <div><span class="section-eyebrow">Workspace</span><h1>My projects</h1><p>Manage your APIs and collaborate with your viewers.</p></div>
                <span class="project-total">{{ props.projects.length }} {{ props.projects.length === 1 ? 'project' : 'projects' }}</span>
            </div>
            <div class="projects-grid">
                <article class="project-card" v-for="project in props.projects" :key="project.id" @click="enterProject(project.id)">
                    <div class="card-top">
                        <div class="project-icon">{{ project.name?.charAt(0)?.toUpperCase() }}</div>
                        <span class="access-badge" :class="{ viewer: !project.can_edit }">{{ project.can_edit ? 'Owner' : 'Viewer' }}</span>
                        <div>
                            <div class="project-name">{{ project.name }}</div>
                            <div class="project-url">{{ project.base_url || 'No base URL configured' }}</div>
                        </div>
                        <div v-if="project.can_edit" class="dropdown ms-auto" @click.stop>
                            <button data-bs-toggle="dropdown" class="project-more" aria-label="Project actions">
                                <FontAwesomeIcon icon="fa-solid fa-ellipsis-vertical" />
                            </button>
                            <div class="dropdown-menu dropdown-menu-end project-actions">
                                <button @click="openViewers(project)">
                                    <FontAwesomeIcon icon="fa-solid fa-plus" class="me-2" /> Manage viewers
                                </button>
                                <button @click="editProjectDialog(project)">
                                    <FontAwesomeIcon icon="fa-solid fa-pen-to-square" class="me-2" /> Edit
                                </button>
                                <button class="danger" @click="askDeleteProject(project)">
                                    <FontAwesomeIcon icon="fa-solid fa-trash-can" class="me-2" /> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer-meta">
                        <span><b>{{ project.groups_count }}</b> {{ project.groups_count === 1 ? 'group' : 'groups' }}</span>
                        <span v-if="project.can_edit"><b>{{ project.viewers_count }}</b> {{ project.viewers_count === 1 ? 'viewer' : 'viewers' }}</span>
                        <span class="open-project">Open <span>→</span></span>
                    </div>
                        <template>
                            <v-dialog v-if="project.can_edit" v-model="editDialog" width="auto">
                                <v-card width="500" prepend-icon="mdi-update" title="Edit project" @keydown.enter.prevent="updateProject"
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
                </article>
            </div>
        </div>
    </div>

    <v-dialog v-model="viewersDialog" width="auto">
        <v-card width="520" title="Project viewers" class="px-5" @keydown.enter.prevent="saveViewer">
            <p class="viewer-help">Add an existing BeeDocs account by email. Viewers can read this project's documentation but cannot make changes.</p>
            <div class="viewer-add-row">
                <v-text-field v-model="viewerForm.email" :label="editingViewer ? 'New viewer email' : 'Viewer email'" type="email" variant="outlined" hide-details></v-text-field>
                <v-btn class="add-viewer-button" :text="editingViewer ? 'Save changes' : 'Add viewer'" @click="saveViewer"></v-btn>
            </div>
            <button v-if="editingViewer" type="button" class="cancel-viewer-edit" @click="cancelViewerEdit">Cancel editing {{ editingViewer.name }}</button>
            <div class="viewer-list">
                <div v-for="viewer in activeProject?.viewers || []" :key="viewer.id" class="viewer-row">
                    <span class="viewer-avatar">{{ viewer.name?.charAt(0)?.toUpperCase() }}</span>
                    <span class="viewer-info"><strong>{{ viewer.name }}</strong><small>{{ viewer.email }}</small></span>
                    <div class="viewer-actions"><button type="button" class="edit-viewer" @click="editViewer(viewer)">Edit</button><button type="button" @click="removeViewer(viewer.id)">Remove</button></div>
                </div>
                <div v-if="!activeProject?.viewers?.length" class="no-viewers">No viewers have been added yet.</div>
            </div>
            <template #actions><v-btn text="Done" @click="viewersDialog = false"></v-btn></template>
        </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" width="auto">
        <v-card width="430" class="delete-project-card px-5">
            <div class="delete-project-icon"><FontAwesomeIcon icon="fa-solid fa-trash-can" /></div>
            <h3>Delete project?</h3>
            <p><strong>{{ deleteTarget?.name }}</strong> and all of its groups, endpoints and documentation will be permanently deleted.</p>
            <template #actions>
                <v-btn text="Cancel" @click="deleteDialog = false"></v-btn>
                <v-btn class="delete-confirm-button" text="Delete permanently" @click="confirmDeleteProject"></v-btn>
            </template>
        </v-card>
    </v-dialog>

</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUpdated, watch } from 'vue';
import { usePage } from '@inertiajs/vue3'
import { useToast } from "vue-toastification";
import { computed } from 'vue';

const toast = useToast();
const $page = usePage();
const user = computed(() => $page.props.auth?.user);
const userInitial = computed(() => user.value?.name?.trim()?.charAt(0)?.toUpperCase() || 'U');
const dialog = ref(false);
const editDialog = ref(false);
const viewersDialog = ref(false);
const activeProject = ref(null);
const editingViewer = ref(null);
const deleteDialog = ref(false);
const deleteTarget = ref(null);

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
const viewerForm = useForm({ email: '' });

const openViewers = (project) => {
    activeProject.value = project;
    editingViewer.value = null;
    viewerForm.reset();
    viewersDialog.value = true;
}

const saveViewer = () => {
    if (!viewerForm.email.trim()) return toast.warning('Viewer email is required');
    const url = editingViewer.value
        ? `/projects/${activeProject.value.id}/viewers/${editingViewer.value.id}`
        : `/projects/${activeProject.value.id}/viewers`;
    const method = editingViewer.value ? 'put' : 'post';
    viewerForm[method](url, {
        preserveScroll: true,
        onSuccess: () => { viewerForm.reset(); editingViewer.value = null; },
        onError: errors => toast.error(errors.email || 'Unable to add viewer')
    });
}

const removeViewer = (userId) => router.delete(`/projects/${activeProject.value.id}/viewers/${userId}`, { preserveScroll: true });
const editViewer = viewer => { editingViewer.value = viewer; viewerForm.email = viewer.email; };
const cancelViewerEdit = () => { editingViewer.value = null; viewerForm.reset(); };

watch(() => props.projects, projects => {
    if (activeProject.value) activeProject.value = projects.find(project => project.id === activeProject.value.id) || null;
}, { deep: true });

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

const askDeleteProject = project => { deleteTarget.value = project; deleteDialog.value = true; }
const confirmDeleteProject = () => {
    if (!deleteTarget.value) return;
    router.delete('/projects/' + deleteTarget.value.id, {
        onFinish: () => { deleteDialog.value = false; deleteTarget.value = null; }
    });
}

const logout = () => router.post('/logout');

const enterProject = (id) => {
    router.get('/projects/' + id + '/view');

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
.profile-trigger{display:grid;place-items:center;width:42px;height:42px;padding:0;border:2px solid #a78bfa;border-radius:13px;background:linear-gradient(145deg,#c4b5fd,#7c3aed);color:white;font-weight:800;box-shadow:0 8px 24px rgba(124,58,237,.3)}
.profile-trigger:hover{transform:translateY(-1px);box-shadow:0 12px 30px rgba(124,58,237,.42)}
.profile-menu{width:280px;margin-top:12px!important;padding:10px!important;background:#1b0d29!important;border:1px solid rgba(215,188,255,.14)!important;border-radius:16px!important;box-shadow:0 24px 70px rgba(4,0,10,.48)!important}
.profile-summary{display:flex;align-items:center;gap:12px;padding:10px}.profile-avatar{display:grid;place-items:center;flex:0 0 42px;height:42px;border-radius:12px;background:rgba(167,139,250,.16);color:#d8b4fe;font-weight:800}.profile-copy{display:flex;min-width:0;flex-direction:column}.profile-copy strong{overflow:hidden;color:#fff;font-size:14px;text-overflow:ellipsis;white-space:nowrap}.profile-copy span{overflow:hidden;color:#aa9bb8;font-size:12px;text-overflow:ellipsis;white-space:nowrap}.profile-divider{height:1px;margin:6px 0;background:rgba(215,188,255,.1)}.profile-action{display:flex;width:100%;align-items:center;justify-content:space-between;padding:10px 12px;border:0;border-radius:9px;background:transparent;color:#d8cfdf;font-size:13px;font-weight:700;text-align:left}.profile-action:hover{background:rgba(251,113,133,.1);color:#fb7185}
.projects-container{max-width:1180px;padding-top:64px}.projects-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:30px}.section-eyebrow{display:block;margin-bottom:7px;color:#b794f4;font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.projects-heading h1{margin:0;color:#fff;font-size:32px;font-weight:800;letter-spacing:-.045em}.projects-heading p{margin:8px 0 0;color:#9f91a8;font-size:13px}.project-total{padding:7px 10px;border:1px solid rgba(215,188,255,.12);border-radius:9px;background:rgba(167,139,250,.06);color:#bbaac5;font-size:10px;font-weight:700}.projects-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.project-card{position:relative;overflow:visible;min-height:205px;padding:20px;border:1px solid rgba(215,188,255,.12);border-radius:18px;background:linear-gradient(145deg,rgba(45,18,64,.74),rgba(15,6,24,.72));box-shadow:0 18px 45px rgba(3,0,8,.2),inset 0 1px 0 rgba(255,255,255,.04);cursor:pointer;transition:transform .22s,border-color .22s,box-shadow .22s}.project-card::after{position:absolute;right:-30px;bottom:-45px;width:130px;height:130px;border-radius:50%;background:rgba(139,92,246,.08);filter:blur(12px);content:"";pointer-events:none}.project-card:hover{z-index:2;border-color:rgba(167,139,250,.38);box-shadow:0 24px 60px rgba(3,0,8,.35),0 0 0 1px rgba(139,92,246,.08);transform:translateY(-4px)}.card-top{display:flex;align-items:flex-start;gap:11px}.project-icon{display:grid;width:42px;height:42px;flex:0 0 42px;place-items:center;border:1px solid rgba(196,181,253,.2);border-radius:12px;background:linear-gradient(145deg,rgba(139,92,246,.28),rgba(88,28,135,.18));color:#e9d5ff;font-size:15px;font-weight:800}.access-badge{position:absolute;top:20px;right:52px;padding:4px 7px;border-radius:6px;background:rgba(52,211,153,.1);color:#6ee7b7;font-size:8px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.access-badge.viewer{background:rgba(96,165,250,.1);color:#93c5fd}.project-name{max-width:170px;margin-top:2px;overflow:hidden;color:#fff;font-size:16px;font-weight:800;text-overflow:ellipsis;white-space:nowrap}.project-url{max-width:190px;margin-top:6px;overflow:hidden;color:#9b8ba5;font:9px monospace;text-overflow:ellipsis;white-space:nowrap}.project-more{display:grid;width:30px;height:30px;place-items:center;border:0;border-radius:8px;background:transparent;color:#a08cab}.project-more:hover{background:rgba(255,255,255,.06);color:#fff}.project-actions{width:170px;padding:6px!important;background:linear-gradient(145deg,#241132,#14071d)!important;border:1px solid rgba(215,188,255,.16)!important;border-radius:12px!important;box-shadow:0 20px 50px rgba(3,0,8,.55)!important}.project-actions button{display:block;width:100%;padding:9px 10px;border:0;border-radius:8px;background:transparent;color:#ddd3e3;font-size:11px;font-weight:700;text-align:left}.project-actions button:hover{background:rgba(167,139,250,.11);color:#fff}.project-actions button.danger:hover{background:rgba(251,113,133,.1);color:#fb7185}.card-footer-meta{position:absolute;right:20px;bottom:18px;left:20px;display:flex;align-items:center;gap:14px;padding-top:14px;border-top:1px solid rgba(215,188,255,.09);color:#94849f;font-size:9px}.card-footer-meta b{color:#d5c6de}.open-project{z-index:1;margin-left:auto;color:#c4b5fd;font-weight:700}.open-project span{display:inline-block;margin-left:4px;transition:transform .2s}.project-card:hover .open-project span{transform:translateX(3px)}
.projects-empty{display:flex;min-height:calc(100vh - 72px);align-items:center;justify-content:center;flex-direction:column;padding:40px;text-align:center}.empty-project-visual{position:relative;width:210px;height:150px;margin-bottom:26px}.empty-window{position:absolute;inset:7px 17px 8px 0;overflow:hidden;border:1px solid rgba(196,181,253,.23);border-radius:18px;background:linear-gradient(145deg,rgba(42,17,59,.74),rgba(12,5,20,.72));box-shadow:0 22px 55px rgba(3,0,8,.32);transform:rotate(-2deg)}.empty-window-bar{display:flex;gap:5px;padding:13px;border-bottom:1px solid rgba(215,188,255,.09)}.empty-window-bar i{width:6px;height:6px;border-radius:50%;background:#6d5a78}.empty-window-bar i:first-child{background:#a78bfa}.empty-window-body{display:flex;flex-direction:column;gap:11px;padding:23px}.empty-window-body span{width:72%;height:6px;border-radius:8px;background:rgba(196,181,253,.16)}.empty-window-body span:nth-child(2){width:44%}.empty-window-body span:nth-child(3){width:60%}.empty-plus{position:absolute;right:0;bottom:0;display:grid;width:58px;height:58px;place-items:center;border:1px solid rgba(216,180,254,.3);border-radius:17px;background:linear-gradient(145deg,#9333ea,#6d28d9);box-shadow:0 15px 38px rgba(109,40,217,.38);color:#fff;font-size:25px}.projects-empty h1{margin:0;color:#fff;font-size:28px;font-weight:800;letter-spacing:-.045em}.projects-empty>p{max-width:470px;margin:10px 0 22px;color:#9f91a8;font-size:12px;line-height:1.75}.empty-create-button{display:inline-flex;height:43px;align-items:center;gap:9px;padding:0 16px;border:1px solid rgba(196,181,253,.24);border-radius:11px;background:linear-gradient(135deg,#a78bfa,#7c3aed);box-shadow:0 12px 30px rgba(124,58,237,.28);color:#fff;font-size:11px;font-weight:800}.empty-create-button:hover{filter:brightness(1.08);transform:translateY(-2px)}.delete-project-card{padding-top:30px!important;text-align:center}.delete-project-icon{display:grid;width:54px;height:54px;margin:0 auto 17px;place-items:center;border:1px solid rgba(251,113,133,.24);border-radius:16px;background:rgba(251,113,133,.09);color:#fb7185;font-size:17px}.delete-project-card h3{margin:0;color:#fff;font-size:20px;font-weight:800}.delete-project-card p{max-width:340px;margin:10px auto;color:#aa9bb8;font-size:11px;line-height:1.7}.delete-project-card p strong{color:#f4eafa}.delete-project-card :deep(.v-card-actions){justify-content:center!important}.delete-project-card :deep(.delete-confirm-button){background:linear-gradient(135deg,#e11d48,#be123c)!important;color:#fff!important;box-shadow:0 8px 22px rgba(225,29,72,.22)!important}
@media(max-width:1000px){.projects-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:650px){.projects-container{padding-top:35px}.projects-heading{align-items:flex-start;flex-direction:column}.projects-grid{grid-template-columns:1fr}}
.viewer-help{margin:0 0 18px;color:#aa9bb8;font-size:12px;line-height:1.65;text-align:left}.viewer-add-row{display:grid;grid-template-columns:1fr auto;align-items:center;gap:10px}.add-viewer-button{height:48px!important;background:linear-gradient(135deg,#a78bfa,#7c3aed)!important;color:#fff!important}.cancel-viewer-edit{margin:10px 0 0;border:0;background:transparent;color:#bca9c8;font-size:10px;text-decoration:underline;text-underline-offset:3px}.viewer-list{display:flex;max-height:260px;flex-direction:column;gap:6px;margin-top:20px;overflow:auto}.viewer-row{display:flex;align-items:center;gap:10px;padding:10px;border:1px solid rgba(215,188,255,.1);border-radius:11px;background:rgba(255,255,255,.025);text-align:left}.viewer-avatar{display:grid;place-items:center;width:34px;height:34px;border-radius:10px;background:rgba(167,139,250,.14);color:#d8b4fe;font-weight:800}.viewer-info{display:flex;min-width:0;flex:1;flex-direction:column}.viewer-info strong{font-size:12px}.viewer-info small{overflow:hidden;color:#998ba3;font-size:10px;text-overflow:ellipsis}.viewer-actions{display:flex;align-items:center;gap:4px}.viewer-row button{padding:5px 7px;border:0;border-radius:6px;background:transparent;color:#fb8da0;font-size:10px;font-weight:700}.viewer-row button.edit-viewer{color:#c4b5fd}.viewer-row button:hover{background:rgba(251,113,133,.09)}.viewer-row button.edit-viewer:hover{background:rgba(167,139,250,.1)}.no-viewers{padding:24px;color:#8e8295;font-size:11px;text-align:center}
</style>
