<template>
  <div v-if="api" class="docs-detail">
    <header class="detail-header">
      <div>
        <span class="eyebrow">Endpoint documentation</span>
        <h1>{{ api.description }}</h1>
        <p>Review the endpoint configuration, payload examples and possible error responses.</p>
      </div>
      <div class="detail-actions">
        <button type="button" class="share-button" @click="copyShareUrl">{{ shareStatus }} <span aria-hidden="true">↗</span></button>
        <Link v-if="!readonly" :href="'/docs/edit/' + api.id" class="edit-button">
          Edit documentation <font-awesome-icon icon="fa-solid fa-pen-to-square" />
        </Link>
      </div>
    </header>

    <section class="endpoint-card">
      <div class="endpoint-main">
        <span class="method-badge" :class="'method-' + api.method.toLowerCase()">{{ api.method }}</span>
        <code>{{ api.endpoints }}</code>
      </div>
      <div class="endpoint-meta">
        <span class="status-dot"></span>
        <span>Endpoint configured</span>
      </div>
    </section>

    <section class="docs-section">
      <div class="section-title">
        <div><span class="section-number">01</span><h2>Headers</h2></div>
        <span class="section-note">Request configuration</span>
      </div>
      <div class="headers-card">
        <div class="header-row header-labels"><span>Key</span><span>Value</span></div>
        <div class="header-row"><code>Content-Type</code><code>application/json</code></div>
        <div v-if="api.is_authorized" class="header-row"><code>Authorization</code><code>Bearer &lt;token&gt;</code></div>
      </div>
      <div class="auth-note" :class="{ disabled: !api.is_authorized }">
        <span class="auth-icon">{{ api.is_authorized ? '✓' : '—' }}</span>
        {{ api.is_authorized ? 'Bearer token authorization is required' : 'This endpoint does not require authorization' }}
      </div>
    </section>

    <section class="docs-section">
      <div class="section-title"><div><span class="section-number">02</span><h2>Request</h2></div><span class="section-note">Example payload</span></div>
      <JsonViewer label="Request body" :json="api.request" />
    </section>

    <section class="docs-section">
      <div class="section-title"><div><span class="section-number">03</span><h2>Response</h2></div><span class="section-note">Success payload</span></div>
      <JsonViewer label="Response body" :json="api.response" />
    </section>

    <section class="docs-section error-section" style="padding-bottom: 40px;background-color: #0d0b1600;">
      <div class="section-title"><div><span class="section-number">04</span><h2>Error responses</h2></div><span class="section-note">{{ api.error_responses?.length || 0 }} defined</span></div>
      <div v-if="api.error_responses?.length" class="error-list">
        <div v-for="error in api.error_responses" :key="error.id || error.status_code" class="error-row">
          <span class="error-code">{{ error.status_code }}</span>
          <span>{{ error.message }}</span>
        </div>
      </div>
      <div v-else class="no-errors"><span>✓</span><div><strong>No error responses</strong><p>No custom errors have been documented for this endpoint.</p></div></div>
    </section>
  </div>

  <div v-else class="empty-state">
    <div class="empty-visual" aria-hidden="true"><div class="code-window"><div class="window-bar"><span></span><span></span><span></span></div><div class="code-lines"><i></i><i></i><i></i></div></div><div class="floating-brace">{ }</div></div>
    <span class="eyebrow">API workspace</span>
    <h2>{{ selectedId ? 'This endpoint needs documentation' : 'Choose an endpoint' }}</h2>
    <p>{{ selectedId ? 'Add request details, responses and error codes to make this endpoint ready for your team.' : 'Select an API from the sidebar to view its documentation.' }}</p>
    <Link v-if="selectedId && !readonly" :href="'/docs/' + selectedId" class="edit-button">Create documentation <font-awesome-icon icon="fa-solid fa-plus" /></Link>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import JsonViewer from './JsonViewer.vue'

const props = defineProps({ selectedId: Number, api: Object, readonly: { type: Boolean, default: false } })
const shareStatus = ref('Share')

const copyShareUrl = async () => {
  const url = window.location.href
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(url)
    } else {
      const textarea = document.createElement('textarea')
      textarea.value = url
      textarea.setAttribute('readonly', '')
      textarea.style.cssText = 'position:fixed;left:-9999px;top:0'
      document.body.appendChild(textarea)
      textarea.select()
      const success = document.execCommand('copy')
      document.body.removeChild(textarea)
      if (!success) throw new Error('Copy command failed')
    }
    shareStatus.value = 'Link copied!'
  } catch {
    shareStatus.value = 'Copy failed'
  }
  window.setTimeout(() => { shareStatus.value = 'Share' }, 1700)
}
</script>

<style scoped>
.docs-detail{width:min(920px,calc(100% - 48px));margin:0 auto;padding:52px 0 90px;color:#f8f4fb}.detail-header{display:flex;align-items:flex-start;justify-content:space-between;gap:28px;margin-bottom:28px}.eyebrow{display:block;margin-bottom:9px;color:#b794f4;font-size:10px;font-weight:800;letter-spacing:.17em;text-transform:uppercase}.detail-header h1{max-width:650px;margin:0;color:#fff;font-size:clamp(25px,3vw,36px);font-weight:800;letter-spacing:-.045em;line-height:1.25}.detail-header p{max-width:600px;margin:10px 0 0;color:#a99caf;font-size:13px;line-height:1.7}.detail-actions{display:flex;flex:0 0 auto;align-items:center;gap:8px}.share-button,.edit-button{display:inline-flex;height:42px;align-items:center;justify-content:center;gap:9px;padding:0 15px;border-radius:11px;color:#fff;font-size:12px;font-weight:750;text-decoration:none}.share-button{border:1px solid rgba(196,181,253,.2);background:rgba(167,139,250,.08);color:#ddd0e7}.share-button:hover{border-color:rgba(196,181,253,.42);background:rgba(167,139,250,.16);color:#fff}.edit-button{border:1px solid rgba(196,181,253,.25);background:linear-gradient(135deg,#a78bfa,#7c3aed);box-shadow:0 12px 30px rgba(124,58,237,.25)}.edit-button:hover{color:#fff;transform:translateY(-2px)}
.endpoint-card{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 20px;border:1px solid rgba(215,188,255,.14);border-radius:14px;background:linear-gradient(145deg,rgba(34,14,48,.72),rgba(12,7,19,.7));box-shadow:0 15px 38px rgba(3,0,8,.2)}.endpoint-main{display:flex;min-width:0;align-items:center;gap:12px}.endpoint-main code{overflow:hidden;color:#e8dff0;font:500 13px "Fira Code",monospace;text-overflow:ellipsis;white-space:nowrap}.method-badge{padding:6px 9px;border-radius:8px;font:800 10px monospace;letter-spacing:.05em}.method-get{background:rgba(52,211,153,.15);color:#6ee7b7}.method-post{background:rgba(251,191,36,.14);color:#fcd34d}.method-put{background:rgba(96,165,250,.15);color:#93c5fd}.method-delete{background:rgba(251,113,133,.14);color:#fb7185}.endpoint-meta{display:flex;align-items:center;gap:8px;color:#8f8298;font-size:10px;white-space:nowrap}.status-dot{width:7px;height:7px;border-radius:50%;background:#34d399;box-shadow:0 0 0 4px rgba(52,211,153,.1)}
.docs-section{margin-top:40px}.section-title{display:flex;align-items:end;justify-content:space-between;margin-bottom:14px}.section-title>div{display:flex;align-items:center;gap:10px}.section-title h2{margin:0;color:#fff;font-size:17px;font-weight:800;letter-spacing:-.02em}.section-number{color:#826a94;font:700 10px monospace}.section-note{color:#827588;font-size:10px}
.headers-card{overflow:hidden;border:1px solid rgba(215,188,255,.13);border-radius:13px;background:rgba(10,6,16,.55)}.header-row{display:grid;grid-template-columns:1fr 1.2fr;border-top:1px solid rgba(215,188,255,.09)}.header-row:first-child{border-top:0}.header-row span,.header-row code{padding:12px 15px}.header-row code{color:#d5cadc;font:11px "Fira Code",monospace}.header-row code+code{border-left:1px solid rgba(215,188,255,.09);color:#d8b4fe}.header-labels{background:rgba(167,139,250,.05);color:#8f8298;font-size:9px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.header-labels span+span{border-left:1px solid rgba(215,188,255,.09)}.auth-note{display:flex;align-items:center;gap:8px;margin-top:11px;color:#8adfbe;font-size:10px}.auth-note.disabled{color:#96899e}.auth-icon{display:grid;place-items:center;width:18px;height:18px;border-radius:6px;background:rgba(52,211,153,.1);font-size:9px}
:deep(.json-viewer){overflow:hidden;border:1px solid rgba(215,188,255,.14);border-radius:14px;background:#0d0b16}:deep(.json-toolbar){display:flex;align-items:center;justify-content:space-between;min-height:46px;padding:0 13px;border-bottom:1px solid rgba(215,188,255,.09);background:rgba(255,255,255,.022)}:deep(.json-toolbar>div){display:flex;align-items:center;gap:9px}:deep(.json-toolbar strong){font-size:11px}:deep(.json-badge){padding:3px 6px;border-radius:5px;background:rgba(167,139,250,.16);color:#c4b5fd;font:700 8px monospace}:deep(.json-toolbar button){padding:5px 9px;border:1px solid rgba(215,188,255,.12);border-radius:7px;background:rgba(167,139,250,.07);color:#bcaec6;font-size:9px;font-weight:700}:deep(.json-toolbar button:hover){background:rgba(167,139,250,.16);color:#fff}:deep(.json-viewer pre){max-height:420px;margin:0;padding:18px 20px;overflow:auto;color:#d8cfe1;font:12px/1.7 "Fira Code",monospace;white-space:pre}:deep(.json-key){color:#7dd3fc}:deep(.json-string){color:#f0abfc}:deep(.json-number){color:#fbbf24}:deep(.json-boolean){color:#34d399}:deep(.json-null){color:#fb7185;font-style:italic}
.error-list{overflow:hidden;border:1px solid rgba(251,113,133,.13);border-radius:13px}.error-row{display:flex;align-items:center;gap:14px;padding:13px 15px;border-top:1px solid rgba(215,188,255,.08);color:#cfc3d5;font-size:12px}.error-row:first-child{border-top:0}.error-code{min-width:45px;padding:4px 7px;border-radius:7px;background:rgba(251,113,133,.1);color:#fb8da0;font:700 10px monospace;text-align:center}.no-errors{display:flex;align-items:center;gap:13px;padding:18px;border:1px solid rgba(52,211,153,.12);border-radius:13px;background:rgba(52,211,153,.035)}.no-errors>span{display:grid;place-items:center;width:34px;height:34px;border-radius:10px;background:rgba(52,211,153,.1);color:#6ee7b7}.no-errors strong{font-size:12px}.no-errors p{margin:3px 0 0;color:#8e8295;font-size:10px}
.empty-state{display:flex;width:100%;min-height:100%;align-items:center;justify-content:center;flex-direction:column;padding:40px;text-align:center}.empty-state h2{margin:0;color:#fff;font-size:25px;font-weight:800}.empty-state>p{max-width:440px;margin:10px 0 22px;color:#a99caf;font-size:12px}.empty-visual{position:relative;width:190px;height:135px;margin-bottom:26px}.code-window{position:absolute;inset:0 12px 7px 0;border:1px solid rgba(196,181,253,.25);border-radius:17px;background:rgba(35,14,50,.65);transform:rotate(-2deg)}.window-bar{display:flex;gap:5px;padding:12px;border-bottom:1px solid rgba(215,188,255,.09)}.window-bar span{width:6px;height:6px;border-radius:50%;background:#765f85}.code-lines{display:flex;flex-direction:column;gap:10px;padding:19px}.code-lines i{width:75%;height:5px;border-radius:5px;background:rgba(196,181,253,.2)}.code-lines i:nth-child(2){width:45%}.floating-brace{position:absolute;right:0;bottom:0;display:grid;place-items:center;width:54px;height:54px;border-radius:16px;background:linear-gradient(145deg,#6d28d9,#9333ea);box-shadow:0 15px 35px rgba(109,40,217,.35);font:700 17px monospace}
@media(max-width:700px){.docs-detail{width:calc(100% - 28px);padding-top:28px}.detail-header{flex-direction:column}.detail-actions{width:100%}.share-button,.edit-button{flex:1}.endpoint-card{align-items:flex-start;flex-direction:column}.section-note{display:none}.header-row{grid-template-columns:1fr}.header-row code+code,.header-labels span+span{border-top:1px solid rgba(215,188,255,.09);border-left:0}}
</style>
