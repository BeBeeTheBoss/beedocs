<template>
  <aside class="docs-sidebar">
    <a href="/projects" class="back-projects"><font-awesome-icon icon="fa-solid fa-arrow-left" /> Back to projects</a>
    <div class="sidebar-heading">
      <div>
        <span class="eyebrow">Navigation</span>
        <h2>API collections</h2>
      </div>
      <button v-if="!readonly" class="icon-button primary" @click="dialog = true" aria-label="Create group">
        <font-awesome-icon icon="fa-solid fa-plus" />
      </button>
    </div>

    <div class="sidebar-meta">
      <span>{{ groups.length }} {{ groups.length === 1 ? 'group' : 'groups' }}</span>
      <span>{{ apiCount }} endpoints</span>
    </div>

    <div class="group-list">
      <section v-for="(group, index) in groups" :key="group.id" class="group-card">
        <div class="group-row">
          <button class="group-toggle" @click="openGroup(index)">
            <font-awesome-icon icon="fa-solid fa-angle-down" class="chevron" :class="{ open: isOpen[index] }" />
            <span class="group-name">{{ group.name }}</span>
            <span class="count">{{ group.apis?.length || 0 }}</span>
          </button>
          <div v-if="!readonly" class="dropdown">
            <button class="more-button" data-bs-toggle="dropdown" aria-label="Group actions">
              <font-awesome-icon icon="fa-solid fa-ellipsis-vertical" />
            </button>
            <div class="dropdown-menu dropdown-menu-end action-menu">
              <button class="menu-action" @click="openApiDialog(group.id)">
                <font-awesome-icon icon="fa-solid fa-plus" /> Add endpoint
              </button>
              <button class="menu-action danger" @click="askDelete('group', group)">
                <font-awesome-icon icon="fa-solid fa-trash-can" /> Delete group
              </button>
            </div>
          </div>
        </div>

        <div v-show="isOpen[index]" class="api-list">
          <button
            v-for="api in group.apis"
            :key="api.id"
            class="api-row"
            :class="{ active: selectedId === api.id }"
            @click="$emit('selected', api); selectedApi(api.id)"
          >
            <span class="api-name">{{ api.name }}</span>
            <button v-if="!readonly" class="delete-api" @click.stop="askDelete('api', api)" aria-label="Delete API">
              <font-awesome-icon icon="fa-solid fa-trash-can" />
            </button>
          </button>
          <button v-if="!readonly && !group.apis?.length" class="empty-group" @click="openApiDialog(group.id)">
            <font-awesome-icon icon="fa-solid fa-plus" /> Add your first endpoint
          </button>
        </div>
      </section>
    </div>

    <div v-if="groups.length === 0" class="empty-sidebar">
      <div class="empty-mark"><font-awesome-icon icon="fa-solid fa-plus" /></div>
      <strong>No collections yet</strong>
      <span>Create a group to organize your APIs.</span>
      <button v-if="!readonly" @click="dialog = true">Create group</button>
    </div>

    <v-dialog v-model="dialog" width="auto">
      <v-card width="500" title="Create new group" class="px-5" @keydown.enter.prevent="createGroup">
        <v-text-field v-model="form.name" label="Group name" variant="outlined"></v-text-field>
        <template #actions>
          <v-btn text="Cancel" @click="dialog = false"></v-btn>
          <v-btn text="Create group" @click="createGroup"></v-btn>
        </template>
      </v-card>
    </v-dialog>

    <v-dialog v-model="apiDialog" width="auto">
      <v-card width="500" title="Create new API" class="px-5" @keydown.enter.prevent="createApi">
        <v-text-field v-model="apiForm.name" label="API name" variant="outlined"></v-text-field>
        <template #actions>
          <v-btn text="Cancel" @click="apiDialog = false"></v-btn>
          <v-btn text="Create API" @click="createApi"></v-btn>
        </template>
      </v-card>
    </v-dialog>

    <v-dialog v-model="confirmDialog" width="auto">
      <v-card width="430" class="confirm-card px-5">
        <div class="danger-icon"><font-awesome-icon icon="fa-solid fa-trash-can" /></div>
        <h3>Delete {{ deleteTarget.type === 'group' ? 'group' : 'API' }}?</h3>
        <p>
          <strong>{{ deleteTarget.name }}</strong> will be permanently deleted.
          <span v-if="deleteTarget.type === 'group'"> All APIs and documentation inside it will also be removed.</span>
        </p>
        <template #actions>
          <v-btn text="Cancel" @click="confirmDialog = false"></v-btn>
          <v-btn class="confirm-delete" text="Delete permanently" @click="confirmDelete"></v-btn>
        </template>
      </v-card>
    </v-dialog>
  </aside>
</template>

<script setup>
import { computed, onMounted, onUpdated, ref, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

const props = defineProps({ project_id: Number, groups: Object, selected_id: Number, readonly: { type: Boolean, default: false } })
defineEmits(['selected'])

const toast = useToast()
const page = usePage()
const dialog = ref(false)
const apiDialog = ref(false)
const confirmDialog = ref(false)
const deleteTarget = ref({ type: '', id: null, name: '' })
const isOpen = ref([])
const selectedId = ref(null)
const openGroupsKey = computed(() => `beedocs:project:${props.project_id}:open-groups`)
const apiCount = computed(() => props.groups.reduce((total, group) => total + (group.apis?.length || 0), 0))
const form = useForm({ project_id: props.project_id, name: '' })
const apiForm = useForm({ group_id: '', name: '' })

const showFlash = () => {
  if (page.props.flash.message) {
    toast.success(page.props.flash.message)
    page.props.flash.message = null
  }
}

onMounted(() => {
  restoreOpenGroups()
  selectedId.value = props.selected_id
  showFlash()
})
onUpdated(showFlash)

watch(() => props.selected_id, id => { selectedId.value = id })

const restoreOpenGroups = () => {
  const stored = localStorage.getItem(openGroupsKey.value)
  let savedIds = []
  try { savedIds = JSON.parse(stored || '[]') } catch {}
  isOpen.value = props.groups.map(group => stored === null ? true : savedIds.includes(group.id))
}

const saveOpenGroups = () => {
  const ids = props.groups.filter((group, index) => isOpen.value[index]).map(group => group.id)
  localStorage.setItem(openGroupsKey.value, JSON.stringify(ids))
}

const selectedApi = id => {
  selectedId.value = id
}
const openGroup = index => {
  isOpen.value[index] = !isOpen.value[index]
  saveOpenGroups()
}
const openApiDialog = groupId => {
  apiForm.group_id = groupId
  apiDialog.value = true
}
const createGroup = () => {
  if (!form.name.trim()) return toast.warning('Group name is required')
  form.post('/groups', { onSuccess: () => { dialog.value = false; form.reset('name') } })
}
const createApi = () => {
  if (!apiForm.name.trim()) return toast.warning('API name is required')
  apiForm.post('/apis', { onSuccess: () => { apiDialog.value = false; apiForm.reset('name') } })
}
const askDelete = (type, item) => {
  deleteTarget.value = { type, id: item.id, name: item.name }
  confirmDialog.value = true
}
const confirmDelete = () => {
  const { type, id } = deleteTarget.value
  const url = type === 'group' ? '/groups/' + id : '/apis/' + id
  router.delete(url, { onFinish: () => { confirmDialog.value = false } })
}
</script>

<style scoped>
.docs-sidebar{min-height:100%;padding:12px 16px 26px;color:#fff;background:transparent}
.back-projects{display:inline-flex;height:34px;align-items:center;gap:8px;margin:0 4px 16px;padding:0 11px;border:1px solid rgba(215,188,255,.1);border-radius:9px;background:rgba(167,139,250,.045);color:#a999b3;font-size:10px;font-weight:750;line-height:1;text-decoration:none}.back-projects svg{width:11px;font-size:11px}.back-projects:hover{border-color:rgba(196,181,253,.28);background:rgba(167,139,250,.11);color:#fff;transform:translateX(-2px)}
.sidebar-heading{display:flex;align-items:center;justify-content:space-between;padding:0 4px}.eyebrow{display:block;margin-bottom:5px;color:#b69fc8;font-size:10px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}.sidebar-heading h2{margin:0;color:#fff;font-size:17px;font-weight:800;letter-spacing:-.025em}
.icon-button{display:grid;place-items:center;width:36px;height:36px;border:1px solid rgba(196,181,253,.28);border-radius:10px;color:#fff;background:rgba(139,92,246,.22)}.icon-button.primary{border-color:rgba(196,181,253,.38);background:linear-gradient(145deg,rgba(139,92,246,.35),rgba(109,40,217,.28));color:#f3e8ff}.icon-button:hover{transform:translateY(-1px);background:rgba(139,92,246,.42);box-shadow:0 8px 22px rgba(124,58,237,.2)}
.sidebar-meta{display:flex;justify-content:space-between;margin:20px 4px 10px;color:#8f7b9f;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em}
.group-list{display:flex;flex-direction:column;gap:8px}.group-card{overflow:visible;border:1px solid rgba(215,188,255,.09);border-radius:13px;background:rgba(255,255,255,.035)}.group-card:hover{border-color:rgba(196,181,253,.2);background:rgba(255,255,255,.05)}
.group-row{display:flex;align-items:center;padding:5px}.group-toggle{display:flex;min-width:0;flex:1;align-items:center;gap:10px;padding:9px;border:0;background:transparent;color:#eee7f5;text-align:left}.chevron{color:#aa95ba;font-size:11px;transform:rotate(-90deg);transition:transform .2s}.chevron.open{transform:rotate(0)}.group-name{overflow:hidden;flex:1;font-size:13px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.count{display:grid;place-items:center;min-width:22px;height:20px;padding:0 6px;border-radius:7px;background:rgba(139,92,246,.25);color:#e9d5ff;font-size:10px;font-weight:800}
.more-button{display:grid;place-items:center;width:30px;height:30px;border:0;border-radius:8px;background:transparent;color:#a996b7}.more-button:hover{background:rgba(255,255,255,.06);color:#fff}.action-menu{width:190px;padding:7px!important;background:linear-gradient(145deg,#241132,#16091f)!important;border:1px solid rgba(215,188,255,.16)!important;border-radius:12px!important;box-shadow:0 18px 45px rgba(3,0,8,.55)!important}.menu-action{display:flex;width:100%;align-items:center;gap:10px;padding:10px 11px;border:0;border-radius:8px;background:transparent!important;color:#e3d8e9!important;font-size:12px;font-weight:700;text-align:left}.menu-action svg{color:#bda5ce}.menu-action:hover{background:rgba(167,139,250,.14)!important;color:#fff!important}.menu-action.danger{color:#d9cadf!important}.menu-action.danger:hover{background:rgba(251,113,133,.12)!important;color:#fb7185!important}.menu-action.danger:hover svg{color:#fb7185}
.api-list{padding:0 7px 8px 7px}.api-row{position:relative;display:flex;width:100%;align-items:center;gap:9px;margin-top:2px;padding:10px 10px 10px 14px;border:0;border-radius:9px;background:transparent;color:#c2b6cb;text-align:left}.api-row::before{position:absolute;top:8px;bottom:8px;left:0;width:3px;border-radius:0 4px 4px 0;background:transparent;content:""}.api-row:hover{background:rgba(255,255,255,.06);color:#fff}.api-row.active{background:linear-gradient(90deg,rgba(139,92,246,.3),rgba(139,92,246,.12));color:#fff;box-shadow:inset 0 0 0 1px rgba(196,181,253,.09)}.api-row.active::before{background:#c4b5fd;box-shadow:0 0 12px rgba(167,139,250,.65)}.api-name{overflow:hidden;flex:1;font-size:12px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}.delete-api{visibility:hidden;padding:4px;border:0;background:transparent;color:#a996b7;font-size:10px;opacity:0}.api-row:hover .delete-api{visibility:visible;opacity:1}.delete-api:hover{color:#fb7185}
.empty-group{width:100%;padding:9px 8px 9px 18px;border:0;background:transparent;color:#766b80;font-size:11px;text-align:left}.empty-group:hover{color:#c4b5fd}
.empty-sidebar{display:flex;flex-direction:column;align-items:center;padding:44px 18px;text-align:center}.empty-mark{display:grid;place-items:center;width:44px;height:44px;margin-bottom:14px;border:1px solid rgba(167,139,250,.25);border-radius:13px;background:rgba(167,139,250,.1);color:#c4b5fd}.empty-sidebar strong{font-size:13px}.empty-sidebar span{margin:6px 0 16px;color:var(--muted);font-size:11px;line-height:1.6}.empty-sidebar button{padding:8px 12px;border:1px solid var(--line);border-radius:9px;background:rgba(255,255,255,.04);color:#fff;font-size:11px;font-weight:700}
.confirm-card{padding-top:30px!important;text-align:center}.danger-icon{display:grid;place-items:center;width:54px;height:54px;margin:0 auto 17px;border:1px solid rgba(251,113,133,.25);border-radius:16px;background:rgba(251,113,133,.1);color:#fb7185;font-size:18px}.confirm-card h3{margin:0;color:#fff;font-size:20px;font-weight:800;letter-spacing:-.025em}.confirm-card p{margin:10px auto 8px;max-width:340px;color:#b9aebf;font-size:12px;line-height:1.7}.confirm-card p strong{color:#f4eafa}.confirm-card :deep(.v-card-actions){justify-content:center!important}.confirm-card :deep(.confirm-delete){background:linear-gradient(135deg,#e11d48,#be123c)!important;color:#fff!important;box-shadow:0 8px 22px rgba(225,29,72,.22)!important}
</style>
