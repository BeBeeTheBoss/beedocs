<template>
  <div class="json-editor" :class="{ invalid: value && !isValid }">
    <div class="editor-toolbar">
      <div class="editor-label"><span class="json-badge">JSON</span><strong>{{ label }}</strong></div>
      <div class="editor-actions">
        <span v-if="value" class="validity" :class="{ valid: isValid }">{{ isValid ? 'Valid JSON' : 'Invalid JSON' }}</span>
        <button type="button" @click="formatJson" :disabled="!isValid">Format</button>
      </div>
    </div>
    <div class="editor-body">
      <div class="line-number">1</div>
      <pre ref="highlightLayer" class="highlight-layer" aria-hidden="true" v-html="highlightedJson"></pre>
      <textarea
        ref="textarea"
        :value="value"
        :placeholder="placeholder"
        spellcheck="false"
        autocomplete="off"
        @input="$emit('update:modelValue', $event.target.value)"
        @keydown.tab.prevent="insertTab"
        @scroll="syncScroll"
        @blur="$emit('blur')"
      ></textarea>
    </div>
    <div v-if="value && !isValid" class="editor-error">{{ errorMessage }}</div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
const props = defineProps({ modelValue: { type: String, default: '' }, label: String, placeholder: String })
const emit = defineEmits(['update:modelValue', 'blur'])
const textarea = ref(null)
const highlightLayer = ref(null)
const value = computed(() => props.modelValue)
const validation = computed(() => {
  if (!value.value.trim()) return { valid: false, message: '' }
  try { JSON.parse(value.value); return { valid: true, message: '' } }
  catch (error) { return { valid: false, message: error.message.replace(/^JSON\.parse: /, '') } }
})
const isValid = computed(() => validation.value.valid)
const errorMessage = computed(() => validation.value.message)
const highlightedJson = computed(() => {
  const escaped = value.value
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')

  return escaped.replace(
    /(&quot;(?:\\.|[^&])*?&quot;)(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?/g,
    (token, stringToken, colon, literal) => {
      if (stringToken) {
        const type = colon ? 'json-key' : 'json-string'
        return `<span class="${type}">${stringToken}</span>${colon || ''}`
      }
      if (literal === 'true' || literal === 'false') return `<span class="json-boolean">${token}</span>`
      if (literal === 'null') return `<span class="json-null">${token}</span>`
      return `<span class="json-number">${token}</span>`
    }
  ) + '\n'
})
const formatJson = () => {
  if (isValid.value) emit('update:modelValue', JSON.stringify(JSON.parse(value.value), null, 2))
}
const insertTab = event => {
  const input = event.target, start = input.selectionStart, end = input.selectionEnd
  const next = value.value.slice(0, start) + '  ' + value.value.slice(end)
  emit('update:modelValue', next)
  requestAnimationFrame(() => { input.selectionStart = input.selectionEnd = start + 2 })
}
const syncScroll = event => {
  if (!highlightLayer.value) return
  highlightLayer.value.scrollTop = event.target.scrollTop
  highlightLayer.value.scrollLeft = event.target.scrollLeft
}
</script>

<style scoped>
.json-editor{overflow:hidden;border:1px solid rgba(215,188,255,.16);border-radius:13px;background:#0d0b16;transition:border-color .2s,box-shadow .2s}.json-editor:focus-within{border-color:rgba(167,139,250,.65);box-shadow:0 0 0 4px rgba(139,92,246,.1)}.json-editor.invalid{border-color:rgba(251,113,133,.42)}
.editor-toolbar{display:flex;min-height:48px;align-items:center;justify-content:space-between;padding:0 13px;border-bottom:1px solid rgba(215,188,255,.1);background:rgba(255,255,255,.025)}.editor-label,.editor-actions{display:flex;align-items:center;gap:9px}.editor-label strong{color:#e9e1ee;font-size:12px}.json-badge{padding:3px 6px;border-radius:5px;background:rgba(167,139,250,.16);color:#c4b5fd;font:700 9px monospace;letter-spacing:.08em}.validity{color:#fb7185;font-size:10px;font-weight:700}.validity.valid{color:#34d399}.editor-actions button{padding:5px 9px;border:1px solid rgba(215,188,255,.13);border-radius:7px;background:rgba(167,139,250,.08);color:#cfc1d9;font-size:10px;font-weight:700}.editor-actions button:hover:not(:disabled){background:rgba(167,139,250,.18);color:#fff}.editor-actions button:disabled{cursor:not-allowed;opacity:.4}
.editor-body{position:relative;min-height:220px;padding-left:38px}.line-number{position:absolute;z-index:2;top:0;bottom:0;left:0;width:38px;padding-top:16px;border-right:1px solid rgba(215,188,255,.08);color:#5f5368;font:11px/1.65 monospace;text-align:center;user-select:none}.highlight-layer,.editor-body textarea{position:absolute;inset:0 0 0 38px;width:calc(100% - 38px)!important;height:100%!important;min-height:220px!important;box-sizing:border-box!important;margin:0!important;padding:14px 16px!important;border:0!important;border-radius:0!important;font-family:"Fira Code","DM Mono",monospace!important;font-size:13px!important;font-weight:400!important;font-style:normal!important;line-height:21.45px!important;letter-spacing:0!important;text-align:left!important;text-indent:0!important;text-transform:none!important;tab-size:2!important;white-space:pre!important;word-break:normal!important;overflow-wrap:normal!important;overflow:auto!important}.highlight-layer{z-index:0;pointer-events:none;background:transparent;color:#d8cfe1}.editor-body textarea{z-index:1;outline:0!important;background:transparent!important;color:transparent!important;caret-color:#fff!important;-webkit-text-fill-color:transparent!important;box-shadow:none!important;resize:none!important}.editor-body textarea::selection{background:rgba(124,58,237,.42);-webkit-text-fill-color:transparent}.editor-body textarea::placeholder{color:#63586d!important;-webkit-text-fill-color:#63586d!important}.highlight-layer :deep(.json-key){color:#7dd3fc}.highlight-layer :deep(.json-string){color:#f0abfc}.highlight-layer :deep(.json-number){color:#fbbf24}.highlight-layer :deep(.json-boolean){color:#34d399;font-weight:600}.highlight-layer :deep(.json-null){color:#fb7185;font-style:italic}.editor-error{padding:8px 13px;border-top:1px solid rgba(251,113,133,.15);background:rgba(251,113,133,.06);color:#fb8da0;font:10px/1.5 monospace}
</style>
