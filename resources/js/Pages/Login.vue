<template>
  <main class="auth-page">
    <div class="auth-glow glow-one"></div><div class="auth-glow glow-two"></div>
    <section class="auth-shell">
      <div class="auth-intro">
        <a href="/" class="brand"><span>B</span> BeeDocs</a>
        <div class="intro-copy">
          <span class="eyebrow">API documentation workspace</span>
          <h1>Document APIs.<br><em>Share with clarity.</em></h1>
          <p>Keep endpoints, payloads and responses beautifully organized in one focused workspace.</p>
        </div>
        <div class="preview-card">
          <div class="preview-top"><i></i><i></i><i></i><span>api/reference</span></div>
          <div class="preview-endpoint"><b>GET</b><code>/v1/members</code></div>
          <div class="preview-lines"><span></span><span></span><span></span></div>
        </div>
        <span class="copyright">© {{ new Date().getFullYear() }} BeeDocs</span>
      </div>

      <div class="auth-form-side">
        <form class="auth-form" @submit.prevent="submit">
          <div class="mobile-brand"><span>B</span> BeeDocs</div>
          <span class="eyebrow">Welcome back</span>
          <h2>Sign in to BeeDocs</h2>
          <p class="form-lead">Continue to your API documentation workspace.</p>
          <div v-if="errorMessage" class="form-error-alert"><span>!</span><p>{{ errorMessage }}</p></div>

          <label class="field">
            <span>Email address</span>
            <input v-model="form.email" type="email" autocomplete="email" placeholder="you@example.com" />
            <small v-if="form.errors.email">{{ form.errors.email }}</small>
          </label>
          <label class="field">
            <span>Password</span>
            <input v-model="form.password" type="password" autocomplete="current-password" placeholder="Enter your password" />
            <small v-if="form.errors.password">{{ form.errors.password }}</small>
          </label>

          <button class="auth-submit" type="submit" :disabled="form.processing">
            <span>{{ form.processing ? 'Signing in…' : 'Sign in' }}</span><span aria-hidden="true">→</span>
          </button>
          <p class="auth-switch">New to BeeDocs? <Link href="/signup">Create an account</Link></p>
        </form>
      </div>
    </section>
  </main>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
const toast = useToast()
const form = useForm({ email: '', password: '' })
const localError = ref('')
const errorMessage = computed(() => localError.value || form.errors.message || form.errors.email || form.errors.password || '')
const submit = () => {
  localError.value = ''
  if (!form.email || !form.password) { localError.value = 'Email and password are required.'; return }
  form.post('/login', { onError: errors => toast.error(errors.message || 'Please check your details') })
}
</script>

<style scoped>
.auth-page{position:relative;display:grid;min-height:100vh;place-items:center;overflow:hidden;padding:28px;background:#08030d}.auth-glow{position:absolute;border-radius:50%;filter:blur(10px);pointer-events:none}.glow-one{top:-240px;left:-180px;width:600px;height:600px;background:radial-gradient(circle,rgba(124,58,237,.22),transparent 68%)}.glow-two{right:-220px;bottom:-260px;width:650px;height:650px;background:radial-gradient(circle,rgba(88,28,135,.2),transparent 68%)}.auth-shell{position:relative;display:grid;width:min(1040px,100%);min-height:650px;grid-template-columns:1.05fr .95fr;overflow:hidden;border:1px solid rgba(215,188,255,.13);border-radius:26px;background:rgba(14,7,21,.78);box-shadow:0 35px 100px rgba(2,0,7,.5);backdrop-filter:blur(22px)}.auth-intro{position:relative;display:flex;overflow:hidden;flex-direction:column;padding:42px;background:radial-gradient(circle at 20% 0,rgba(139,92,246,.22),transparent 25rem),linear-gradient(145deg,rgba(46,18,65,.75),rgba(17,7,27,.7));border-right:1px solid rgba(215,188,255,.1)}.brand,.mobile-brand{display:flex;align-items:center;gap:10px;color:#fff;font-size:17px;font-weight:800;text-decoration:none}.brand>span,.mobile-brand>span{display:grid;width:34px;height:34px;place-items:center;border-radius:10px;background:linear-gradient(135deg,#a78bfa,#7c3aed);box-shadow:0 9px 25px rgba(124,58,237,.3);font-size:12px}.intro-copy{margin-top:72px}.eyebrow{display:block;color:#b794f4;font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.intro-copy h1{margin:14px 0 16px;color:#fff;font-size:42px;font-weight:800;letter-spacing:-.055em;line-height:1.1}.intro-copy h1 em{color:#c4b5fd;font-style:normal}.intro-copy p{max-width:430px;color:#aa9bb8;font-size:13px;line-height:1.8}.preview-card{width:88%;margin-top:40px;overflow:hidden;border:1px solid rgba(215,188,255,.14);border-radius:15px;background:rgba(8,3,13,.56);box-shadow:0 22px 50px rgba(3,0,8,.25);transform:rotate(-1deg)}.preview-top{display:flex;align-items:center;gap:5px;padding:11px;border-bottom:1px solid rgba(215,188,255,.08)}.preview-top i{width:6px;height:6px;border-radius:50%;background:#6f5d7a}.preview-top i:first-child{background:#a78bfa}.preview-top span{margin-left:auto;color:#796b82;font:8px monospace}.preview-endpoint{display:flex;align-items:center;gap:10px;padding:17px 17px 8px}.preview-endpoint b{padding:4px 7px;border-radius:6px;background:rgba(52,211,153,.12);color:#6ee7b7;font:700 8px monospace}.preview-endpoint code{color:#cfc3d5;font-size:10px}.preview-lines{display:flex;flex-direction:column;gap:8px;padding:8px 17px 18px}.preview-lines span{width:70%;height:5px;border-radius:9px;background:rgba(196,181,253,.13)}.preview-lines span:nth-child(2){width:45%}.preview-lines span:nth-child(3){width:58%}.copyright{margin-top:auto;color:#6f6377;font-size:9px}.auth-form-side{display:grid;place-items:center;padding:46px}.auth-form{width:min(360px,100%)}.mobile-brand{display:none}.auth-form h2{margin:10px 0 6px;color:#fff;font-size:29px;font-weight:800;letter-spacing:-.045em}.form-lead{margin:0 0 30px;color:#97899f;font-size:12px}.field{display:flex;flex-direction:column;gap:8px;margin-bottom:17px}.field>span{color:#d6cbdc;font-size:11px;font-weight:700}.field input{height:50px;padding:0 14px;border:1px solid rgba(215,188,255,.14);border-radius:11px;outline:0;background:rgba(255,255,255,.025);color:#fff;font-size:13px}.field input::placeholder{color:#665a6e}.field input:focus{border-color:rgba(167,139,250,.65);box-shadow:0 0 0 4px rgba(139,92,246,.1)}.field small{color:#fb8da0;font-size:9px}.auth-submit{display:flex;width:100%;height:50px;align-items:center;justify-content:space-between;margin-top:7px;padding:0 16px;border:0;border-radius:11px;background:linear-gradient(135deg,#a78bfa,#7c3aed);box-shadow:0 12px 30px rgba(124,58,237,.26);color:#fff;font-size:12px;font-weight:800}.auth-submit:hover:not(:disabled){filter:brightness(1.08);transform:translateY(-1px)}.auth-submit:disabled{cursor:wait;opacity:.65}.auth-switch{margin:22px 0 0;color:#8f8297;font-size:11px;text-align:center}.auth-switch a{color:#c4b5fd;font-weight:750;text-decoration:none}.auth-switch a:hover{color:#e9d5ff}.auth-switch a{text-decoration:underline;text-underline-offset:3px}@media(max-width:800px){.auth-shell{max-width:480px;grid-template-columns:1fr}.auth-intro{display:none}.auth-form-side{padding:42px 30px}.mobile-brand{display:flex;margin-bottom:48px}}@media(max-width:480px){.auth-page{padding:0}.auth-shell{min-height:100vh;border:0;border-radius:0}.auth-form-side{padding:28px 22px}}
.form-error-alert{display:flex;align-items:flex-start;gap:10px;margin:0 0 18px;padding:11px 12px;border:1px solid rgba(251,113,133,.2);border-radius:10px;background:rgba(251,113,133,.07);color:#fda4af}.form-error-alert>span{display:grid;width:19px;height:19px;flex:0 0 19px;place-items:center;border-radius:6px;background:rgba(251,113,133,.14);font-size:10px;font-weight:800}.form-error-alert p{margin:1px 0 0;font-size:10px;line-height:1.55}
</style>
