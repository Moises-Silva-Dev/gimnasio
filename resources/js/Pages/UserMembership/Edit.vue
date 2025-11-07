<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from '@/Components/NotificationBar.vue';

import {
  mdiAccount,
  mdiCalendar,
  mdiCurrencyUsd,
  mdiOfficeBuilding,
  mdiCardAccountDetails,
  mdiPlaylistCheck,
  mdiInformation,
  mdiArrowLeft,
  mdiAlertCircle
} from "@mdi/js";

const props = defineProps({
  title: String,
  routeName: String,
  userMembership: Object,
  memberships: Array,
  gyms: Array,
  users: Array,
});

const form = useForm({
  gym_id: props.userMembership.gym_id,
  user_id: props.userMembership.user_id,
  membership_id: props.userMembership.membership_id,
  payment_id: props.userMembership.payment_id,
  start_date: props.userMembership.start_date,
  end_date: props.userMembership.end_date,
  amount_paid: Number(props.userMembership.amount_paid),
  status: props.userMembership.status,
});

const selectedMembership = ref(null);
const originalData = ref({});

// Inicializar datos
onMounted(() => {
  selectedMembership.value = props.memberships.find(m => m.id == form.membership_id);

  // Guardar datos originales para comparación
  originalData.value = {
    membership_id: form.membership_id,
    start_date: form.start_date,
    end_date: form.end_date,
    amount_paid: form.amount_paid,
    status: form.status,
  };
});

// Filtrar membresías por gimnasio seleccionado
const filteredMemberships = computed(() => {
  if (!form.gym_id) return [];
  return props.memberships.filter(m => m.gym_id == form.gym_id);
});

// Detectar cambios en el formulario
const hasChanges = computed(() => {
  return form.membership_id !== originalData.value.membership_id ||
      form.start_date !== originalData.value.start_date ||
      form.end_date !== originalData.value.end_date ||
      form.amount_paid !== originalData.value.amount_paid ||
      form.status !== originalData.value.status;
});

watch(() => form.membership_id, (newMembershipId) => {
  if (newMembershipId) {
    selectedMembership.value = filteredMemberships.value.find(m => m.id == newMembershipId);
    if (selectedMembership.value) {
      // Sugerir precio de la membresía seleccionada
      if (form.membership_id !== originalData.value.membership_id) {
        form.amount_paid = Number(selectedMembership.value.price);
      }
      if (form.start_date) {
        calculateEndDate();
      }
    }
  } else {
    selectedMembership.value = null;
  }
});

watch(() => form.start_date, (newStartDate) => {
  if (newStartDate && selectedMembership.value) {
    calculateEndDate();
  }
});

const calculateEndDate = () => {
  if (!form.start_date || !selectedMembership.value?.duration_days) return;

  const startDate = new Date(form.start_date);
  const endDate = new Date(startDate);
  endDate.setDate(startDate.getDate() + selectedMembership.value.duration_days);

  form.end_date = endDate.toISOString().split('T')[0];
};

const handleSubmit = () => {
  form.put(route(`${props.routeName}update`, props.userMembership.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Actualizar datos originales después de guardar exitosamente
      originalData.value = {
        membership_id: form.membership_id,
        start_date: form.start_date,
        end_date: form.end_date,
        amount_paid: form.amount_paid,
        status: form.status,
      };
    }
  });
};

const handleReset = () => {
  form.membership_id = originalData.value.membership_id;
  form.start_date = originalData.value.start_date;
  form.end_date = originalData.value.end_date;
  form.amount_paid = originalData.value.amount_paid;
  form.status = originalData.value.status;
  selectedMembership.value = props.memberships.find(m => m.id == form.membership_id);
};

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Pendiente',
    'active': 'Activa',
    'expired': 'Expirada',
    'cancelled': 'Cancelada'
  };
  return labels[status] || status;
};

const getStatusBadge = (status) => {
  const badges = {
    'active': { class: 'bg-green-100 text-green-800', text: 'Activa' },
    'pending': { class: 'bg-yellow-100 text-yellow-800', text: 'Pendiente' },
    'expired': { class: 'bg-red-100 text-red-800', text: 'Expirada' },
    'cancelled': { class: 'bg-gray-100 text-gray-800', text: 'Cancelada' }
  };
  return badges[status] || badges['pending'];
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// Computed para verificar si el formulario es válido
const isFormValid = computed(() => {
  return Boolean(
      form.gym_id &&
      form.user_id &&
      form.membership_id &&
      form.start_date &&
      form.end_date &&
      form.amount_paid &&
      form.amount_paid > 0
  );
});
</script>

<template>
  <LayoutMain :title="title">
    <SectionTitleLineWithButton :title="title" main :icon="mdiCardAccountDetails">
      <BaseButton
          :href="route(`${routeName}index`)"
          :icon="mdiArrowLeft"
          label="Volver al Listado"
          color="contrast"
          outline
      />
    </SectionTitleLineWithButton>

    <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation">
      {{ $page.props.flash.error }}
    </NotificationBar>

    <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation">
      {{ $page.props.flash.success }}
    </NotificationBar>

    <!-- Información Actual de la Membresía -->
    <CardBox class="mb-6">
      <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-l-4 border-blue-500">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
            </div>
          </div>
          <div class="flex-1">
            <h3 class="text-xl font-bold text-blue-900 mb-4">Información Actual de la Membresía</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <!-- Usuario -->
              <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  Usuario
                </p>
                <p class="text-base font-bold text-gray-900">
                  {{ userMembership.user?.name }} {{ userMembership.user?.last_name }}
                </p>
                <p class="text-sm text-gray-600">{{ userMembership.user?.email }}</p>
              </div>

              <!-- Gimnasio -->
              <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                  </svg>
                  Gimnasio
                </p>
                <p class="text-base font-bold text-gray-900">{{ userMembership.gym?.name }}</p>
              </div>

              <!-- Estado -->
              <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Estado Actual</p>
                <span
                    :class="getStatusBadge(userMembership.status).class"
                    class="inline-flex px-3 py-1 rounded-full text-sm font-semibold"
                >
                                    {{ getStatusBadge(userMembership.status).text }}
                                </span>
                <p class="text-xs text-gray-500 mt-2">
                  Creada: {{ new Date(userMembership.created_at).toLocaleDateString('es-MX') }}
                </p>
              </div>

              <!-- Membresía Original -->
              <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Tipo de Membresía</p>
                <p class="text-base font-bold text-gray-900">{{ userMembership.membership?.name }}</p>
                <p class="text-sm text-gray-600">{{ userMembership.membership?.duration_days }} días</p>
              </div>

              <!-- Vigencia Original -->
              <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Vigencia Original</p>
                <p class="text-sm text-gray-900">{{ formatDate(userMembership.start_date) }}</p>
                <p class="text-sm text-gray-600">hasta {{ formatDate(userMembership.end_date) }}</p>
              </div>

              <!-- Monto Original -->
              <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Monto Pagado</p>
                <p class="text-2xl font-bold text-green-600">{{ formatCurrency(userMembership.amount_paid) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </CardBox>

    <!-- Formulario de Edición -->
    <CardBox is-form @submit.prevent="handleSubmit" class="mb-6">
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
          Editar Datos de la Membresía
        </h3>

        <!-- Nota informativa -->
        <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="text-sm font-semibold text-amber-800">Nota Importante</p>
              <p class="text-sm text-amber-700 mt-1">
                El gimnasio y el usuario no pueden ser modificados. Si necesitas cambiar estos datos, debes crear una nueva membresía.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Campos no editables -->
      <div class="mb-8">
        <h4 class="text-sm font-semibold text-gray-600 uppercase mb-4">Datos No Modificables</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField label="Gimnasio" help="Este campo no puede ser modificado">
            <FormControl
                :model-value="userMembership.gym?.name"
                type="text"
                disabled
                :icon="mdiOfficeBuilding"
                class="bg-gray-50"
            />
          </FormField>

          <FormField label="Usuario" help="Este campo no puede ser modificado">
            <FormControl
                :model-value="`${userMembership.user?.name} ${userMembership.user?.last_name || ''}`"
                type="text"
                disabled
                :icon="mdiAccount"
                class="bg-gray-50"
            />
          </FormField>
        </div>
      </div>

      <!-- Campos editables -->
      <div class="mb-6">
        <h4 class="text-sm font-semibold text-gray-600 uppercase mb-4">Datos Editables</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <!-- Tipo de Membresía -->
          <FormField :error="form.errors.membership_id" label="Tipo de Membresía *" help="Selecciona el plan de membresía">
            <FormControl
                v-model="form.membership_id"
                type="select"
                required
                :icon="mdiCardAccountDetails"
                :options="filteredMemberships"
                value-select="id"
            />
          </FormField>

          <!-- Fecha de Inicio -->
          <FormField :error="form.errors.start_date" label="Fecha de Inicio *" help="Fecha en que inicia la membresía">
            <FormControl
                v-model="form.start_date"
                type="date"
                required
                :icon="mdiCalendar"
            />
          </FormField>

          <!-- Fecha de Fin -->
          <FormField :error="form.errors.end_date" label="Fecha de Fin *" help="Fecha de vencimiento">
            <FormControl
                v-model="form.end_date"
                type="date"
                required
                :icon="mdiCalendar"
                :min="form.start_date"
            />
            <p v-if="selectedMembership" class="text-xs text-green-600 mt-1 flex items-center gap-1">
              <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
              Duración: {{ selectedMembership.duration_days }} días
            </p>
          </FormField>

          <!-- Monto Pagado -->
          <FormField :error="form.errors.amount_paid" label="Monto Pagado *" help="Monto que pagó el usuario">
            <FormControl
                v-model="form.amount_paid"
                type="number"
                step="0.01"
                min="0"
                required
                :icon="mdiCurrencyUsd"
                placeholder="0.00"
            />
            <p v-if="selectedMembership?.price" class="text-xs text-blue-600 mt-1">
              Precio regular: {{ formatCurrency(selectedMembership.price) }}
            </p>
          </FormField>

          <!-- Estado -->
          <FormField :error="form.errors.status" label="Estado *" help="Estado de la membresía">
            <FormControl
                v-model="form.status"
                type="select"
                required
                :icon="mdiPlaylistCheck"
                :options="[
                                { id: 'pending', name: 'Pendiente' },
                                { id: 'active', name: 'Activa' },
                                { id: 'expired', name: 'Expirada' },
                                { id: 'cancelled', name: 'Cancelada' }
                            ]"
                value-select="id"
            />
          </FormField>
        </div>
      </div>

      <!-- Alerta de cambios -->
      <div v-if="hasChanges" class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          <div>
            <p class="text-sm font-semibold text-blue-800">Has realizado cambios</p>
            <p class="text-sm text-blue-700 mt-1">
              Revisa los datos en el resumen antes de guardar. Los cambios se aplicarán inmediatamente.
            </p>
          </div>
        </div>
      </div>

      <!-- Resumen de cambios -->
      <div v-if="selectedMembership && form.start_date && form.end_date"
           class="mt-6 p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 shadow-sm">
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-900">
                    <span :class="`w-3 h-3 rounded-full ${
                        form.status === 'active' ? 'bg-green-500' :
                        form.status === 'pending' ? 'bg-yellow-500' :
                        form.status === 'cancelled' ? 'bg-gray-500' : 'bg-red-500'
                    }`"></span>
          Vista Previa de los Cambios
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white p-4 rounded-lg shadow-sm">
            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Membresía</p>
            <p class="text-base font-semibold text-gray-900">{{ selectedMembership.name }}</p>
            <p class="text-sm text-gray-600">{{ selectedMembership.duration_days }} días</p>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm">
            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Vigencia</p>
            <p class="text-sm text-gray-900">{{ formatDate(form.start_date) }}</p>
            <p class="text-sm text-gray-600">hasta {{ formatDate(form.end_date) }}</p>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm">
            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Monto</p>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(form.amount_paid) }}</p>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm">
            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Estado</p>
            <span
                :class="getStatusBadge(form.status).class"
                class="inline-flex px-3 py-1 rounded-full text-xs font-semibold"
            >
                            {{ getStatusBadge(form.status).text }}
                        </span>
          </div>
        </div>
      </div>

      <template #footer>
        <BaseButtons>
          <BaseButton
              type="submit"
              color="success"
              :label="form.processing ? 'Guardando Cambios...' : 'Guardar Cambios'"
              :disabled="form.processing || !hasChanges || !isFormValid"
              class="font-semibold"
          />
          <BaseButton
              v-if="hasChanges"
              type="button"
              @click="handleReset"
              color="warning"
              outline
              label="Deshacer Cambios"
          />
          <BaseButton
              :href="route(`${routeName}show`, userMembership.id)"
              type="button"
              color="info"
              outline
              label="Ver Detalles Completos"
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

<style scoped>
.bg-gray-50 {
  cursor: not-allowed;
}
</style>
