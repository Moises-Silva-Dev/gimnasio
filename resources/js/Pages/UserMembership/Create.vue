<script setup>
import { ref, computed, watch } from 'vue';
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
    mdiAccount,
    mdiCardAccountDetails,
    mdiCalendar,
    mdiCash,
    mdiCreditCard,
    mdiNoteText,
    mdiCheckboxMarked,
    mdiTicket,
    mdiDumbbell
} from "@mdi/js";

const props = defineProps({
    title: String,
    routeName: String,
    users: Array,
    memberships: Array,
});

const form = useForm({
    user_id: null,
    membership_id: null,
    start_date: new Date().toISOString().split('T')[0],
    payment_amount: '',
    payment_method: 'cash',
    notes: '',
});

const selectedMembership = computed(() => {
    if (!form.membership_id) return null;
    return props.memberships.find(m => m.id === form.membership_id);
});

const calculatedEndDate = computed(() => {
    if (!form.start_date || !selectedMembership.value) return '';
    const start = new Date(form.start_date);
    const end = new Date(start);
    end.setDate(end.getDate() + selectedMembership.value.duration_days);
    return end.toLocaleDateString('es-MX');
});

const submit = () => {
    form.post(route(`${props.routeName}store`));
};

// Auto-rellenar precio cuando se selecciona membresía
const onMembershipChange = () => {
    if (selectedMembership.value) {
        form.payment_amount = selectedMembership.value.price;
    }
};
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="title" main :icon="mdiCardAccountDetails" />

        <NotificationBar
            v-if="$page.props.flash.message"
            color="success"
            :icon="mdiCheckboxMarked"
            :outline="false"
        >
            {{ $page.props.flash.message }}
        </NotificationBar>

        <CardBox form @submit.prevent="submit">
            <!-- Selección de Usuario -->
            <FormField :error="form.errors.user_id" label="Seleccionar Usuario" required>
                <FormControl
                    v-model="form.user_id"
                    :options="users"
                    type="select"
                    label-key="name"
                    value-key="id"
                    :icon="mdiAccount"
                    placeholder="Elige un usuario"
                />
                <div class="text-xs text-gray-500 mt-1">
                    Solo se muestran usuarios con rol "Member"
                </div>
            </FormField>

            <BaseDivider />

            <!-- Selección de Membresía -->
            <FormField :error="form.errors.membership_id" label="Seleccionar Membresía" required>
                <FormControl
                    v-model="form.membership_id"
                    :options="memberships"
                    type="select"
                    label-key="name"
                    value-key="id"
                    :icon="mdiTicket"
                    placeholder="Elige una membresía"
                    @change="onMembershipChange"
                />
            </FormField>

            <!-- Información de la membresía seleccionada -->
            <div v-if="selectedMembership" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-medium text-blue-900 mb-2 flex items-center">
                    <span class="mdi" :class="mdiDumbbell"></span>
                    Detalles de la Membresía
                </h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600">Gimnasio:</span>
                        <span class="ml-2 font-medium">{{ selectedMembership.gym_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Duración:</span>
                        <span class="ml-2 font-medium">{{ selectedMembership.duration_days }} días</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Sesiones:</span>
                        <span class="ml-2 font-medium">{{ selectedMembership.sessions }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Precio:</span>
                        <span class="ml-2 font-medium text-green-600">${{ selectedMembership.price }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600">Descripción:</span>
                        <p class="ml-2 text-gray-800">{{ selectedMembership.description }}</p>
                    </div>
                </div>
            </div>

            <BaseDivider />

            <!-- Fecha de inicio y cálculo de fecha fin -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <FormField :error="form.errors.start_date" label="Fecha de Inicio" required>
                    <FormControl
                        v-model="form.start_date"
                        type="date"
                        :icon="mdiCalendar"
                    />
                </FormField>

                <FormField label="Fecha de Finalización (calculada)">
                    <div class="p-3 bg-gray-100 rounded border border-gray-300 flex items-center">
            <span class="text-gray-700 font-medium">
              {{ calculatedEndDate || 'Selecciona membresía y fecha' }}
            </span>
                    </div>
                </FormField>
            </div>

            <BaseDivider />

            <!-- Información de pago -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <FormField :error="form.errors.payment_amount" label="Monto Pagado" required>
                    <FormControl
                        v-model="form.payment_amount"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        :icon="mdiCash"
                    />
                </FormField>

                <FormField :error="form.errors.payment_method" label="Método de Pago">
                    <FormControl
                        v-model="form.payment_method"
                        type="select"
                        :options="[
              { id: 'cash', name: 'Efectivo' },
              { id: 'card', name: 'Tarjeta' },
              { id: 'transfer', name: 'Transferencia' },
              { id: 'other', name: 'Otro' }
            ]"
                        label-key="name"
                        value-key="id"
                        :icon="mdiCreditCard"
                    />
                </FormField>
            </div>

            <!-- Notas adicionales -->
            <FormField :error="form.errors.notes" label="Notas Adicionales (opcional)">
                <FormControl
                    v-model="form.notes"
                    type="textarea"
                    placeholder="Agrega cualquier nota relevante..."
                    :icon="mdiNoteText"
                />
            </FormField>

            <template #footer>
                <BaseButtons>
                    <BaseButton
                        @click="submit"
                        type="submit"
                        color="info"
                        :loading="form.processing"
                        :disabled="form.processing"
                        outline
                        label="Asignar Membresía"
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
