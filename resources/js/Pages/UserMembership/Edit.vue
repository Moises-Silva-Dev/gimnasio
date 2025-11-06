<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import {
    mdiCardAccountDetails,
    mdiAccount,
    mdiTicket,
    mdiNoteText,
    mdiCheckboxMarked,
    mdiPencil,
    mdiCancel,
    mdiDumbbell,
    mdiCalendar,
    mdiAlertCircle
} from "@mdi/js";

const props = defineProps({
    title: String,
    routeName: String,
    userMembership: Object,
});

const form = useForm({
    remaining_sessions: props.userMembership.remaining_sessions,
    status: props.userMembership.status,
    notes: props.userMembership.notes || '',
});

const update = () => {
    form.put(route(`${props.routeName}update`, props.userMembership.id));
};

const getStatusColor = computed(() => {
    const colors = {
        'active': 'bg-green-50 border-green-200',
        'expired': 'bg-red-50 border-red-200',
        'cancelled': 'bg-gray-50 border-gray-200'
    };
    return colors[form.status] || 'bg-gray-50 border-gray-200';
});

const canEditSessions = computed(() => {
    return form.status === 'active';
});
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="title" main :icon="mdiPencil" />

        <NotificationBar
            v-if="$page.props.flash.success"
            color="success"
            :icon="mdiCheckboxMarked"
            :outline="false"
        >
            {{ $page.props.flash.success }}
        </NotificationBar>

        <!-- Información No Editable -->
        <CardBox class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Información de la Membresía</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-1">
                        <span class="mdi" :class="mdiAccount"></span>
                        <span>Usuario</span>
                    </label>
                    <p class="text-base font-semibold text-gray-900 mt-1">
                        {{ userMembership.user?.name }} {{ userMembership.user?.last_name }}
                    </p>
                    <p class="text-sm text-gray-600">{{ userMembership.user?.email }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-1">
                        <span class="mdi" :class="mdiTicket"></span>
                        <span>Tipo de Membresía</span>
                    </label>
                    <p class="text-base font-semibold text-gray-900 mt-1">
                        {{ userMembership.membership?.name }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-1">
                        <span class="mdi" :class="mdiDumbbell"></span>
                        <span>Gimnasio</span>
                    </label>
                    <p class="text-base font-semibold text-gray-900 mt-1">
                        {{ userMembership.membership?.gyms?.name || 'N/A' }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-1">
                        <span class="mdi" :class="mdiCalendar"></span>
                        <span>Fecha de Inicio</span>
                    </label>
                    <p class="text-base font-semibold text-gray-900 mt-1">
                        {{ new Date(userMembership.start_date).toLocaleDateString('es-MX', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 flex items-center space-x-1">
                        <span class="mdi" :class="mdiCalendar"></span>
                        <span>Fecha de Fin</span>
                    </label>
                    <p class="text-base font-semibold text-gray-900 mt-1">
                        {{ new Date(userMembership.end_date).toLocaleDateString('es-MX', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Días Restantes</label>
                    <p class="text-2xl font-bold mt-1"
                       :class="{
               'text-green-600': userMembership.days_remaining > 30,
               'text-yellow-600': userMembership.days_remaining > 7 && userMembership.days_remaining <= 30,
               'text-red-600': userMembership.days_remaining <= 7
             }">
                        {{ userMembership.days_remaining }}
                    </p>
                </div>
            </div>
        </CardBox>

        <!-- Formulario de Edición -->
        <CardBox form @submit.prevent="update">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Editar Información</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Estado de la Membresía -->
                <FormField :error="form.errors.status" label="Estado de la Membresía" required>
                    <FormControl
                        v-model="form.status"
                        type="select"
                        :options="[
              { id: 'active', name: 'Activa' },
              { id: 'expired', name: 'Expirada' },
              { id: 'cancelled', name: 'Cancelada' }
            ]"
                        label-key="name"
                        value-key="id"
                    />

                    <div class="mt-3 p-3 rounded-lg border-2" :class="getStatusColor">
                        <div class="flex items-center space-x-2">
                            <span class="mdi text-lg" :class="mdiAlertCircle"></span>
                            <span class="text-sm font-medium">
                Estado actual: <strong>{{
                                    form.status === 'active' ? 'Activa' :
                                        form.status === 'expired' ? 'Expirada' :
                                            'Cancelada'
                                }}</strong>
              </span>
                        </div>
                    </div>
                </FormField>

                <!-- Sesiones Restantes -->
                <FormField
                    :error="form.errors.remaining_sessions"
                    label="Sesiones Restantes"
                    :required="canEditSessions"
                >
                    <FormControl
                        v-model="form.remaining_sessions"
                        type="number"
                        min="0"
                        :max="userMembership.membership?.sessions"
                        placeholder="Número de sesiones"
                        :disabled="!canEditSessions"
                    />

                    <div class="mt-2 text-sm text-gray-600">
                        <p>Total de sesiones: <strong>{{ userMembership.membership?.sessions }}</strong></p>
                        <p>Sesiones usadas: <strong>{{ (userMembership.membership?.sessions || 0) - userMembership.remaining_sessions }}</strong></p>
                    </div>

                    <div v-if="!canEditSessions" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800 flex items-center space-x-2">
                        <span class="mdi" :class="mdiAlertCircle"></span>
                        <span>Las sesiones solo pueden editarse cuando la membresía está activa</span>
                    </div>

                    <!-- Barra de progreso de sesiones -->
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="h-2 rounded-full transition-all duration-300"
                                :class="{
                  'bg-green-500': (form.remaining_sessions / userMembership.membership?.sessions) > 0.5,
                  'bg-yellow-500': (form.remaining_sessions / userMembership.membership?.sessions) > 0.2 && (form.remaining_sessions / userMembership.membership?.sessions) <= 0.5,
                  'bg-red-500': (form.remaining_sessions / userMembership.membership?.sessions) <= 0.2
                }"
                                :style="{ width: `${(form.remaining_sessions / userMembership.membership?.sessions) * 100}%` }"
                            ></div>
                        </div>
                    </div>
                </FormField>
            </div>

            <BaseDivider />

            <!-- Notas -->
            <FormField :error="form.errors.notes" label="Notas Adicionales">
                <FormControl
                    v-model="form.notes"
                    type="textarea"
                    placeholder="Agrega notas sobre cambios, ajustes o información relevante..."
                    :icon="mdiNoteText"
                    rows="4"
                />
                <div class="text-xs text-gray-500 mt-1">
                    Máximo 500 caracteres ({{ form.notes?.length || 0 }}/500)
                </div>
            </FormField>

            <!-- Advertencias -->
            <div class="mt-6 space-y-3">
                <div v-if="form.status === 'cancelled'" class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <span class="mdi text-red-600 text-xl" :class="mdiCancel"></span>
                        <div>
                            <p class="font-medium text-red-800">Cancelar Membresía</p>
                            <p class="text-sm text-red-700 mt-1">
                                Al cancelar esta membresía, el usuario ya no podrá usar las sesiones restantes.
                                Esta acción no se puede revertir automáticamente.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="form.status === 'expired'" class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <span class="mdi text-yellow-600 text-xl" :class="mdiAlertCircle"></span>
                        <div>
                            <p class="font-medium text-yellow-800">Marcar como Expirada</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                Esta membresía será marcada como expirada y el usuario no podrá usarla.
                            </p>
                        </div>
                    </div>
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
                        label="Actualizar Membresía"
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
