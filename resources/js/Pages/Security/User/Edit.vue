<script setup>
import { ref, defineProps, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import FormControlCheckbox from "@/Components/FormControlCheckbox.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import {
  mdiAccount,
  mdiAccountCircle,
  mdiAccountTie,
  mdiPhone,
  mdiMail,
  mdiLock,
  mdiOfficeBuilding,
  mdiDumbbell,
  mdiCheckboxMarked,
  mdiShieldAccount,
  mdiAccountEdit,
  mdiKey,
  mdiRefresh
} from "@mdi/js";

const props = defineProps({
  titulo: String,
  routeName: String,
  roles: Array,
  gyms: Array,
  departamentos: Array,
  user: Object,
});

const safeRoles = computed(() => props.roles || []);
const safeGyms = computed(() => props.gyms || []);

const selectedRoles = computed(() => {
  return form.roles.map(roleId => 
    safeRoles.value.find(role => role.id === roleId)?.name
  ).filter(Boolean);
});

const hasMemberRole = computed(() => {
  return selectedRoles.value.includes('Member');
});

const showPasswordField = ref(false);
const showGeneratedPassword = ref(false);

const form = useForm({
  name: props.user?.name || '',
  last_name: props.user?.last_name || '',
  mother_last_name: props.user?.mother_last_name || '',
  phone: props.user?.phone || '',
  email: props.user?.email || '',
  password: '', // Vacío 
  departamento_id: props.user?.departamento_id || null,
  gym_id: props.user?.gym_id || null,
  status: Boolean(props.user?.status), // BOOLEAN
  roles: props.user?.roles?.map(role => role.id) || [],
});

const update = () => { 
  form.put(route(`${props.routeName}update`, props.user?.id))
};

const generatePassword = () => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
  let password = '';
  for (let i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  form.password = password;
  showPasswordField.value = true;
  showGeneratedPassword.value = true;
};

const regeneratePassword = () => {
  generatePassword();
};

// Selección de roles
const toggleRole = (roleId) => {
  const index = form.roles.indexOf(roleId);
  if (index > -1) {
    form.roles.splice(index, 1);
  } else {
    form.roles.push(roleId);
  }
};
</script>

<template>
  <LayoutMain :title="titulo">
    <SectionTitleLineWithButton :title="titulo" main :icon="mdiAccountEdit" />

    <NotificationBar 
      v-if="$page.props.flash.message" 
      color="success" 
      :icon="mdiCheckboxMarked" 
      :outline="false"
    >
      {{ $page.props.flash.message }}
    </NotificationBar>

    <CardBox form @submit.prevent="update">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <FormField :error="form.errors.name" label="Nombre" required>
          <FormControl 
            v-model="form.name" 
            type="text" 
            placeholder="Ingresa el nombre"
            :icon="mdiAccount" 
          />
        </FormField>

        <FormField :error="form.errors.phone" label="Número Telefónico" required>
          <FormControl
            v-model="form.phone"
            type="tel"
            placeholder="7771234567"
            maxlength="10"
            :icon="mdiPhone"
          />
        </FormField>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <FormField :error="form.errors.last_name" label="Apellido Paterno" required>
          <FormControl 
            v-model="form.last_name" 
            type="text" 
            placeholder="Apellido paterno"
            :icon="mdiAccountCircle" 
          />
        </FormField>

        <FormField :error="form.errors.mother_last_name" label="Apellido Materno" required>
          <FormControl 
            v-model="form.mother_last_name" 
            type="text" 
            placeholder="Apellido materno"
            :icon="mdiAccountTie" 
          />
        </FormField>
      </div>

      <BaseDivider />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <FormField :error="form.errors.email" label="Correo Electrónico" required>
          <FormControl 
            v-model="form.email" 
            type="email" 
            placeholder="usuario@ejemplo.com"
            :icon="mdiMail" 
          />
        </FormField>
        
        <FormField label="Contraseña">
          <div class="space-y-3">
            <!-- Botones para gestionar contraseña -->
            <div class="flex flex-wrap gap-2">
              <BaseButton
                @click="showPasswordField = !showPasswordField"
                type="button"
                color="info"
                small
                outline
                :icon="mdiKey"
                :label="showPasswordField ? 'Ocultar campo contraseña' : 'Cambiar contraseña'"
              />
              
              <BaseButton
                @click="generatePassword"
                type="button"
                color="warning"
                small
                outline
                :icon="mdiKey"
                label="Generar nueva contraseña"
              />
            </div>
            
            <div v-if="showPasswordField" class="space-y-3">
              <FormControl
                v-model="form.password"
                type="password"
                placeholder="Dejar vacío para mantener la contraseña actual"
                :icon="mdiLock"
                :error="form.errors.password"
              />
              
              <BaseButton
                v-if="form.password && showGeneratedPassword"
                @click="regeneratePassword"
                type="button"
                color="info"
                small
                outline
                :icon="mdiRefresh"
                label="Regenerar contraseña"
              />
              
              <div 
                v-if="showGeneratedPassword && form.password" 
                class="p-3 bg-green-50 border border-green-200 rounded-lg"
              >
                <p class="text-sm font-medium text-green-800 mb-1">
                  ✅ Nueva contraseña generada:
                </p>
                <div class="flex items-center justify-between">
                  <code class="text-green-700 font-mono text-sm bg-green-100 px-2 py-1 rounded">
                    {{ form.password }}
                  </code>
                  <span class="text-xs text-green-600">
                    Copia esta contraseña
                  </span>
                </div>
                <p class="text-xs text-green-600 mt-2">
                  <strong>Nota:</strong> Esta contraseña se mostrará solo una vez. Asegúrate de guardarla.
                </p>
              </div>
              
              <div v-if="form.password && !showGeneratedPassword" class="text-xs">
                <div 
                  v-if="form.password.length < 8" 
                  class="text-red-600 font-medium"
                >
                  ⚠ La contraseña debe tener al menos 8 caracteres
                </div>
                <div 
                  v-else 
                  class="text-green-600 font-medium"
                >
                  ✅ Contraseña válida
                </div>
              </div>
              
              <p v-if="!form.password" class="text-xs text-gray-500">
                ⓘ Deja este campo vacío si no deseas cambiar la contraseña actual.
              </p>
            </div>
          </div>
        </FormField>
      </div>

      <BaseDivider />


      <FormField label="Asignar Roles" :error="form.errors.roles">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <label 
            v-for="role in safeRoles" 
            :key="role.id" 
            class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
            :class="{ 'bg-blue-50 border-blue-200': form.roles.includes(role.id) }"
          >
            <input
              type="checkbox"
              :value="role.id"
              v-model="form.roles"
              class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
            />
            <div class="flex flex-col">
              <span class="font-medium text-gray-900">{{ role.name }}</span>
              <span class="text-sm text-gray-500">{{ role.guard_name }}</span>
            </div>
          </label>
        </div>
                
        <!-- Roles seleccionados -->
        <div v-if="form.roles.length > 0" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
          <p class="text-sm font-medium text-blue-800 mb-2 flex items-center">
            <span class="mdi" :class="mdiShieldAccount"></span>
            Roles seleccionados ({{ form.roles.length }}):
          </p>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="roleId in form.roles"
              :key="roleId"
              class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium border border-blue-200"
            >
              {{ safeRoles.find(r => r.id === roleId)?.name }}
              <button
                type="button"
                @click="toggleRole(roleId)"
                class="ml-2 text-blue-600 hover:text-blue-800"
              >
                ×
              </button>
            </span>
          </div>
        </div>
      </FormField>

      <div v-if="hasMemberRole" class="space-y-4 mt-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 flex items-center">
          <span class="mdi" :class="mdiDumbbell"></span>
          Información del Miembro
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField label="Estado del Miembro">
            <FormControlCheckbox 
              v-model="form.status" 
              label="Miembro activo"
            />
          </FormField>

          <FormField label="Gimnasio" :error="form.errors.gym_id" required>
            <FormControl
              v-model="form.gym_id"
              :options="safeGyms"
              type="select"
              label-key="name"
              value-key="id"
              :icon="mdiDumbbell"
              placeholder="Selecciona un gimnasio"
            />
          </FormField>
        </div>
      </div>

      <template #footer>
        <BaseButtons>
          <BaseButton 
            @click="update" 
            type="submit" 
            color="info" 
            :loading="form.processing"
            :disabled="form.processing"
            outline
            label="Actualizar Usuario" 
          />
          <BaseButton 
            :href="route(`${routeName}index`)" 
            type="button" 
            color="danger" 
            outline 
            label="Cancelar" 
          />
        </BaseButtons>
      </template>
    </CardBox>
  </LayoutMain>
</template>