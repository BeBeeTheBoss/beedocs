<template>
  <div class="json-viewer">
    <div class="json-toolbar">
      <div><span class="json-badge">JSON</span><strong>{{ label }}</strong></div>
      <button type="button" @click="copy" :class="{ failed: copyFailed }">{{ copyFailed ? 'Copy failed' : copied ? 'Copied!' : 'Copy' }}</button>
    </div>
    <pre v-html="formatted"></pre>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
const props = defineProps({ json: { type: String, default: '' }, label: String })
const copied = ref(false)
const copyFailed = ref(false)
const formatted = computed(() => {
  let source = props.json || ''
  try { source = JSON.stringify(JSON.parse(source), null, 2) } catch {}
  const escaped = source.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
  return escaped.replace(/(&quot;(?:\\.|[^&])*?&quot;)(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?/g, (token, stringToken, colon, literal) => {
    if (stringToken) return '<span class="' + (colon ? 'json-key' : 'json-string') + '">' + stringToken + '</span>' + (colon || '')
    if (literal === 'true' || literal === 'false') return '<span class="json-boolean">' + token + '</span>'
    if (literal === 'null') return '<span class="json-null">' + token + '</span>'
    return '<span class="json-number">' + token + '</span>'
  })
})
const copy = async () => {
  copyFailed.value = false
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(props.json || '')
    } else {
      const textarea = document.createElement('textarea')
      textarea.value = props.json || ''
      textarea.setAttribute('readonly', '')
      textarea.style.cssText = 'position:fixed;left:-9999px;top:0'
      document.body.appendChild(textarea)
      textarea.select()
      const success = document.execCommand('copy')
      document.body.removeChild(textarea)
      if (!success) throw new Error('Copy command failed')
    }
    copied.value = true
    window.setTimeout(() => { copied.value = false }, 1400)
  } catch {
    copyFailed.value = true
    window.setTimeout(() => { copyFailed.value = false }, 1800)
  }
}
</script>
