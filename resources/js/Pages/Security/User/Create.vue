<script setup>
import { ref, defineProps, computed } from 'vue';
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
  mdiShieldAccount, mdiAccountPlus
} from "@mdi/js";

const props = defineProps({
  titulo: String,
  routeName: String,
  roles: Array,
  gyms: Array,
  departamentos: Array,
});

// Computed properties seguras
const safeRoles = computed(() => props.roles || []);
const safeGyms = computed(() => props.gyms || []);
const safeDepartamentos = computed(() => props.departamentos || []);

// Computed para verificar si hay roles de miembro seleccionados
const selectedRoles = computed(() => {
  return form.roles.map(roleId => 
    safeRoles.value.find(role => role.id === roleId)?.name
  ).filter(Boolean);
});

const hasMemberRole = computed(() => {
  return selectedRoles.value.includes('Member');
});

// Form con todos los campos necesarios
const form = useForm({
  name: '',
  last_name: '',
  mother_last_name: '',
  phone: '',
  email: '',
  password: '',
  departamento_id: null,
  gym_id: null,
  status: true,
  roles: [],
});

const submit = () => {
  form.post(route(`${props.routeName}store`), {
    onSuccess: () => {
      form.reset();
    },
    preserveScroll: true
  });
};

// Función para manejar selección única de rol si es necesario
const toggleRole = (roleId) => {
  const index = form.roles.indexOf(roleId);
  if (index > -1) {
    form.roles.splice(index, 1);
  } else {
    // Para selección única, descomenta esta línea:
    // form.roles = [roleId];
    // Para selección múltiple, mantén esta:
    form.roles.push(roleId);
  }
};
</script>

<template>
  <LayoutMain :title="titulo">
    <SectionTitleLineWithButton :title="titulo" main :icon="mdiAccountPlus" />

    <NotificationBar 
      v-if="$page.props.flash.message" 
      color="success" 
      :icon="mdiCheckboxMarked" 
      :outline="false"
    >
      {{ $page.props.flash.message }}
    </NotificationBar>

    <CardBox form @submit.prevent="submit">
      <!-- Información Personal en Grid -->
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

      <!-- Información de Cuenta -->
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
        
        <FormField :error="form.errors.password" label="Contraseña" required>
          <FormControl
            v-model="form.password"
            type="password"
            placeholder="Mínimo 8 caracteres"
            :icon="mdiLock"
          />
        </FormField>
      </div>

      <!-- Departamento (si existe) -->
      <FormField 
        v-if="safeDepartamentos.length > 0" 
        label="Departamento" 
        :error="form.errors.departamento_id"
      >
        <FormControl
          v-model="form.departamento_id"
          :options="safeDepartamentos"
          type="select"
          label-key="nombre"
          value-key="id"
          :icon="mdiOfficeBuilding"
          placeholder="Selecciona un departamento"
        />                            
      </FormField>

      <BaseDivider />

      <!-- Selección de Roles -->
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

      <!-- Campos específicos para miembros -->
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
            @click="submit" 
            type="submit" 
            color="info"            
            outline
            label="Guardar Usuario" 
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