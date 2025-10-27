<script setup>
import { ref, onMounted, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import { mdiAccount, mdiAsterisk } from '@mdi/js'
import Swal from 'sweetalert2'

// Componentes del formulario
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
// import BaseButton from '@/Components/BaseButton.vue' // <- Ya no usamos BaseButton, usamos un <button> normal
import FormValidationErrors from '@/Components/FormValidationErrors.vue'

// Importamos el NUEVO CSS del login
import '../../css/login.css'

// Prop para recibir mensajes "flash"
const props = defineProps({
  flash: Object
})

// --- Lógica de carga (SIN CAMBIOS) ---
const isLoading = ref(true)
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false
  }, 3000) // Puedes reducir esto a 1500ms (1.5s) para que sea más rápido
})
// ----------------------------------------

// --- Lógica de Notificaciones (SIN CAMBIOS) ---
watch(() => props.flash, (flash) => {
  if (flash && flash.warning) {
    Swal.fire({
      icon: 'warning',
      title: 'Acceso denegado',
      text: flash.warning,
      confirmButtonText: 'OK'
    })
  }
}, { deep: true })
// ------------------------------------

// Lógica del formulario (SIN CAMBIOS)
const form = useForm({
  email: '',
  password: '',
  remember: false
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>

  <Head title="Login" />

  <div id="loader" v-if="isLoading">
    <div class="spinner"></div>
  </div>

  <div id="main-content" v-if="!isLoading" class="login-page">

    <div class="video-background">
      <video autoplay muted loop playsinline poster="/img/gym.mp4">
        <source src="/img/gym.mp4" type="video/mp4">
      </video>
      <div class="video-overlay"></div>
    </div>

    <div class="login-card-container">

      <div class="login-card">

        <div class="logo-container">
          <center>
            <img src="/img/Gym.png" alt="Logo Gym" class="gym-logo" />
          </center>
          <h2 class="mt-4">Acceso Miembros</h2>
        </div>

        <FormValidationErrors class="mb-4" />

        <form @submit.prevent="submit" class="w-100">

          <FormField label="Correo electrónico" label-for="email">
            <FormControl id="email" v-model="form.email" :icon="mdiAccount" type="email" placeholder="tu@correo.com"
              required autocomplete="username" class="text-black placeholder:text-gray-500" />
          </FormField>

          <FormField label="Contraseña" label-for="password">
            <FormControl id="password" v-model="form.password" :icon="mdiAsterisk" type="password"
              placeholder="••••••••" required autocomplete="current-password"
              class="text-black placeholder:text-gray-500" />
          </FormField>

          <button type="submit" class="btn-submit-gym" :disabled="form.processing">
            Ingresar
          </button>
        </form>
      </div>

      <footer class="login-footer">
        © {{ new Date().getFullYear() }} TuGimnasio. Todos los derechos reservados.
      </footer>
    </div>
  </div>
</template>