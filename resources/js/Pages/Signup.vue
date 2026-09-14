<template>
  <main class="auth-page">
    <div class="auth-glow"></div>
    <section class="auth-shell">
      <div class="auth-intro">
        <a href="/" class="brand"><span>B</span> BeeDocs</a>
        <div class="intro-copy">
          <span class="eyebrow">Start documenting</span>
          <h1>One workspace.<br><em>Every endpoint.</em></h1>
          <p>Create structured API references, keep payload examples readable and invite viewers when you're ready to share.</p>
        </div>
        <div class="feature-list">
          <div><span>01</span><p><strong>Organized collections</strong><small>Group endpoints by project and feature.</small></p></div>
          <div><span>02</span><p><strong>Beautiful JSON</strong><small>Readable payloads with syntax highlighting.</small></p></div>
          <div><span>03</span><p><strong>Private sharing</strong><small>Invite read-only viewers by account.</small></p></div>
        </div>
      </div>

      <div class="auth-form-side">
        <form class="auth-form" @submit.prevent="submit">
          <div class="mobile-brand"><span>B</span> BeeDocs</div>
          <span class="eyebrow">Create an account</span>
          <h2>Join BeeDocs</h2>
          <p class="form-lead">Set up your documentation workspace in a minute.</p>
          <div v-if="errorMessage" class="form-error-alert"><span>!</span><p>{{ errorMessage }}</p></div>
          <label class="field"><span>Your name</span><input v-model="form.name" type="text" autocomplete="name" placeholder="Jane Doe" /><small v-if="form.errors.name">{{ form.errors.name }}</small></label>
          <label class="field"><span>Email address</span><input v-model="form.email" type="email" autocomplete="email" placeholder="you@example.com" /><small v-if="form.errors.email">{{ form.errors.email }}</small></label>
          <div class="password-grid">
            <label class="field"><span>Password</span><input v-model="form.password" type="password" autocomplete="new-password" placeholder="••••••••" /><small v-if="form.errors.password">{{ form.errors.password }}</small></label>
            <label class="field"><span>Confirm</span><input v-model="form.confirm_password" type="password" autocomplete="new-password" placeholder="••••••••" /></label>
          </div>
          <button class="auth-submit" type="submit" :disabled="form.processing"><span>{{ form.processing ? 'Creating account…' : 'Create account' }}</span><span>→</span></button>
          <p class="auth-switch">Already have an account? <Link href="/login">Sign in</Link></p>
        </form>
      </div>
    </section>
  </main>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
const toast=useToast()
const form=useForm({name:'',email:'',password:'',confirm_password:''})
const localError=ref('')
const errorMessage=computed(()=>localError.value||form.errors.name||form.errors.email||form.errors.password||form.errors.message||'')
const submit=()=>{
  localError.value=''
  if(!form.name||!form.email||!form.password||!form.confirm_password){localError.value='All fields are required.';return}
  if(form.password!==form.confirm_password){localError.value='Passwords do not match.';return}
  form.post('/signup')
}
</script>

<style scoped>
.auth-page{position:relative;display:grid;min-height:100vh;place-items:center;overflow:hidden;padding:28px;background:#08030d}.auth-glow{position:absolute;inset:-250px auto auto -180px;width:650px;height:650px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.23),transparent 68%)}.auth-shell{position:relative;display:grid;width:min(1040px,100%);min-height:680px;grid-template-columns:1fr 1fr;overflow:hidden;border:1px solid rgba(215,188,255,.13);border-radius:26px;background:rgba(14,7,21,.8);box-shadow:0 35px 100px rgba(2,0,7,.5);backdrop-filter:blur(22px)}.auth-intro{display:flex;flex-direction:column;padding:42px;background:radial-gradient(circle at 20% 0,rgba(139,92,246,.22),transparent 25rem),linear-gradient(145deg,rgba(46,18,65,.75),rgba(17,7,27,.7));border-right:1px solid rgba(215,188,255,.1)}.brand,.mobile-brand{display:flex;align-items:center;gap:10px;color:#fff;font-size:17px;font-weight:800;text-decoration:none}.brand>span,.mobile-brand>span{display:grid;width:34px;height:34px;place-items:center;border-radius:10px;background:linear-gradient(135deg,#a78bfa,#7c3aed);font-size:12px}.intro-copy{margin-top:65px}.eyebrow{display:block;color:#b794f4;font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase}.intro-copy h1{margin:14px 0 16px;color:#fff;font-size:40px;font-weight:800;letter-spacing:-.055em;line-height:1.1}.intro-copy h1 em{color:#c4b5fd;font-style:normal}.intro-copy>p{max-width:430px;color:#aa9bb8;font-size:13px;line-height:1.8}.feature-list{display:flex;flex-direction:column;gap:10px;margin-top:32px}.feature-list>div{display:flex;align-items:center;gap:13px;padding:12px;border:1px solid rgba(215,188,255,.09);border-radius:12px;background:rgba(255,255,255,.025)}.feature-list>div>span{color:#a78bfa;font:700 9px monospace}.feature-list p{display:flex;flex-direction:column;margin:0}.feature-list strong{color:#e9e1ed;font-size:11px}.feature-list small{margin-top:3px;color:#85778e;font-size:9px}.auth-form-side{display:grid;place-items:center;padding:42px}.auth-form{width:min(380px,100%)}.mobile-brand{display:none}.auth-form h2{margin:10px 0 6px;color:#fff;font-size:29px;font-weight:800;letter-spacing:-.045em}.form-lead{margin:0 0 25px;color:#97899f;font-size:12px}.field{display:flex;flex-direction:column;gap:7px;margin-bottom:14px}.field>span{color:#d6cbdc;font-size:10px;font-weight:700}.field input{height:48px;padding:0 13px;border:1px solid rgba(215,188,255,.14);border-radius:11px;outline:0;background:rgba(255,255,255,.025);color:#fff;font-size:12px}.field input::placeholder{color:#665a6e}.field input:focus{border-color:rgba(167,139,250,.65);box-shadow:0 0 0 4px rgba(139,92,246,.1)}.field small{color:#fb8da0;font-size:9px}.password-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.auth-submit{display:flex;width:100%;height:49px;align-items:center;justify-content:space-between;margin-top:5px;padding:0 16px;border:0;border-radius:11px;background:linear-gradient(135deg,#a78bfa,#7c3aed);box-shadow:0 12px 30px rgba(124,58,237,.26);color:#fff;font-size:12px;font-weight:800}.auth-submit:hover:not(:disabled){filter:brightness(1.08);transform:translateY(-1px)}.auth-submit:disabled{opacity:.65}.auth-switch{margin:20px 0 0;color:#8f8297;font-size:11px;text-align:center}.auth-switch a{color:#c4b5fd;font-weight:750;text-underline-offset:3px}@media(max-width:800px){.auth-shell{max-width:500px;grid-template-columns:1fr}.auth-intro{display:none}.auth-form-side{padding:38px 30px}.mobile-brand{display:flex;margin-bottom:38px}}@media(max-width:480px){.auth-page{padding:0}.auth-shell{min-height:100vh;border:0;border-radius:0}.auth-form-side{padding:25px 20px}.password-grid{grid-template-columns:1fr}}
.auth-form,.field,.password-grid{min-width:0}.field input{width:100%!important;min-width:0!important;box-sizing:border-box!important}.password-grid{width:100%;grid-template-columns:repeat(2,minmax(0,1fr))!important}.password-grid .field{width:100%;min-width:0}@media(max-width:480px){.password-grid{grid-template-columns:1fr!important}}
.form-error-alert{display:flex;align-items:flex-start;gap:10px;margin:0 0 16px;padding:10px 11px;border:1px solid rgba(251,113,133,.2);border-radius:10px;background:rgba(251,113,133,.07);color:#fda4af}.form-error-alert>span{display:grid;width:19px;height:19px;flex:0 0 19px;place-items:center;border-radius:6px;background:rgba(251,113,133,.14);font-size:10px;font-weight:800}.form-error-alert p{margin:1px 0 0;font-size:10px;line-height:1.55}
</style>
