<script setup>
import { computed } from 'vue';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import BaseDivider from '@/Components/BaseDivider.vue';
import {
    mdiCardAccountDetails,
    mdiArrowLeft,
    mdiAccount,
    mdiTicket,
    mdiDumbbell,
    mdiCalendar,
    mdiCash,
    mdiCreditCard,
    mdiNoteText,
    mdiClockOutline,
    mdiCheckCircle,
    mdiAlertCircle,
    mdiCancel,
    mdiPencil
} from '@mdi/js';

const props = defineProps({
    title: String,
    userMembership: Object,
    routeName: String,
});

const getStatusConfig = computed(() => {
    const membership = props.userMembership;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const startDate = new Date(membership.start_date);
    startDate.setHours(0, 0, 0, 0);

    // Si aún no ha comenzado
    if (startDate > today) {
        return {
            color: 'bg-blue-100 text-blue-800 border-blue-200',
            icon: mdiClockOutline,
            text: 'Programada'
        };
    }

    if (membership.is_active) {
        return {
            color: 'bg-green-100 text-green-800 border-green-200',
            icon: mdiCheckCircle,
            text: 'Activa'
        };
    } else if (membership.status === 'expired') {
        return {
            color: 'bg-red-100 text-red-800 border-red-200',
            icon: mdiAlertCircle,
            text: 'Expirada'
        };
    } else if (membership.status === 'cancelled') {
        return {
            color: 'bg-gray-100 text-gray-800 border-gray-200',
            icon: mdiCancel,
            text: 'Cancelada'
        };
    }
    return {
        color: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        icon: mdiAlertCircle,
        text: 'Inactiva'
    };
});

const getDaysRemainingColor = computed(() => {
    const days = props.userMembership.days_remaining;
    if (days === null) return 'text-blue-600';
    if (days > 30) return 'text-green-600';
    if (days > 7) return 'text-yellow-600';
    return 'text-red-600';
});

const getDaysRemainingDisplay = computed(() => {
    const membership = props.userMembership;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const startDate = new Date(membership.start_date);
    startDate.setHours(0, 0, 0, 0);

    // Si aún no ha comenzado
    if (startDate > today) {
        return {
            value: membership.days_until_start,
            label: 'Días hasta el inicio'
        };
    }

    return {
        value: membership.days_remaining,
        label: 'Días Restantes'
    };
});

const getSessionsColor = computed(() => {
    const remaining = props.userMembership.remaining_sessions;
    const total = props.userMembership.membership?.sessions || 1;
    const percentage = (remaining / total) * 100;

    if (percentage > 50) return 'text-green-600';
    if (percentage > 20) return 'text-yellow-600';
    return 'text-red-600';
});

const paymentMethodLabel = computed(() => {
    const methods = {
        'cash': 'Efectivo',
        'card': 'Tarjeta',
        'transfer': 'Transferencia',
        'other': 'Otro'
    };
    return methods[props.userMembership.payment_method] || props.userMembership.payment_method || 'No especificado';
});
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="title" main :icon="mdiCardAccountDetails">
            <BaseButton
                :href="route(`${routeName}index`)"
                :icon="mdiArrowLeft"
                label="Volver"
                color="white"
                rounded-full
                small
            />
        </SectionTitleLineWithButton>

        <!-- Estado de la Membresía -->
        <CardBox class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div
                        class="px-4 py-2 rounded-lg border-2 flex items-center space-x-2"
                        :class="getStatusConfig.color"
                    >
                        <span class="mdi text-2xl" :class="getStatusConfig.icon"></span>
                        <span class="font-bold text-lg">{{ getStatusConfig.text }}</span>
                    </div>
                </div>

                <BaseButton
                    :href="route(`${routeName}edit`, userMembership.id)"
                    :icon="mdiPencil"
                    label="Editar Membresía"
                    color="info"
                    outline
                />
            </div>
        </CardBox>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Información del Usuario -->
            <CardBox>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="mdi text-2xl text-blue-600" :class="mdiAccount"></span>
                    <h3 class="text-xl font-bold text-gray-800">Información del Usuario</h3>
                </div>

                <BaseDivider />

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ userMembership.user?.name }}
                            {{ userMembership.user?.last_name }}
                            {{ userMembership.user?.mother_last_name }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Correo Electrónico</label>
                        <p class="text-base text-gray-900">{{ userMembership.user?.email }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Teléfono</label>
                        <p class="text-base text-gray-900">{{ userMembership.user?.phone || 'No registrado' }}</p>
                    </div>
                </div>
            </CardBox>

            <!-- Información de la Membresía -->
            <CardBox>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="mdi text-2xl text-purple-600" :class="mdiTicket"></span>
                    <h3 class="text-xl font-bold text-gray-800">Detalles de la Membresía</h3>
                </div>

                <BaseDivider />

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tipo de Membresía</label>
                        <p class="text-lg font-semibold text-gray-900">{{ userMembership.membership?.name }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Descripción</label>
                        <p class="text-base text-gray-700">{{ userMembership.membership?.description || 'Sin descripción' }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Gimnasio</label>
                        <div class="flex items-center space-x-2">
                            <span class="mdi" :class="mdiDumbbell"></span>
                            <p class="text-base font-medium text-gray-900">
                                {{ userMembership.gym?.name || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </CardBox>
        </div>

        <!-- Vigencia y Sesiones -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Vigencia -->
            <CardBox>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="mdi text-2xl text-green-600" :class="mdiCalendar"></span>
                    <h3 class="text-xl font-bold text-gray-800">Vigencia</h3>
                </div>

                <BaseDivider />

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Inicio</label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ new Date(userMembership.start_date).toLocaleDateString('es-MX', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Fecha de Fin</label>
                            <p class="text-base font-semibold text-gray-900">
                                {{ new Date(userMembership.end_date).toLocaleDateString('es-MX', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) }}
                            </p>
                        </div>
                    </div>

                    <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-blue-700">
                                    {{ getDaysRemainingDisplay.label }}
                                </label>
                                <p class="text-3xl font-bold" :class="getDaysRemainingColor">
                                    {{ getDaysRemainingDisplay.value ?? 'N/A' }}
                                </p>
                            </div>
                            <span class="mdi text-5xl text-blue-300" :class="mdiClockOutline"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Duración Total</label>
                        <p class="text-base text-gray-900">
                            {{ userMembership.membership?.duration_days }} días
                        </p>
                    </div>
                </div>
            </CardBox>

            <!-- Sesiones -->
            <CardBox>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="mdi text-2xl text-orange-600" :class="mdiDumbbell"></span>
                    <h3 class="text-xl font-bold text-gray-800">Sesiones</h3>
                </div>

                <BaseDivider />

                <div class="space-y-4">
                    <div class="p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-orange-700">Sesiones Restantes</label>
                            <p class="text-3xl font-bold" :class="getSessionsColor">
                                {{ userMembership.remaining_sessions }}
                            </p>
                        </div>

                        <!-- Barra de progreso -->
                        <div class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div
                                    class="h-3 rounded-full transition-all duration-300"
                                    :class="{
                                        'bg-green-500': (userMembership.remaining_sessions / userMembership.membership?.sessions) > 0.5,
                                        'bg-yellow-500': (userMembership.remaining_sessions / userMembership.membership?.sessions) > 0.2 && (userMembership.remaining_sessions / userMembership.membership?.sessions) <= 0.5,
                                        'bg-red-500': (userMembership.remaining_sessions / userMembership.membership?.sessions) <= 0.2
                                    }"
                                    :style="{ width: `${(userMembership.remaining_sessions / userMembership.membership?.sessions) * 100}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total de Sesiones</label>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ userMembership.membership?.sessions }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Sesiones Usadas</label>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ (userMembership.membership?.sessions || 0) - userMembership.remaining_sessions }}
                            </p>
                        </div>
                    </div>
                </div>
            </CardBox>
        </div>

        <!-- Información de Pago -->
        <CardBox class="mt-6">
            <div class="flex items-center space-x-2 mb-4">
                <span class="mdi text-2xl text-green-600" :class="mdiCash"></span>
                <h3 class="text-xl font-bold text-gray-800">Información de Pago</h3>
            </div>

            <BaseDivider />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Monto Pagado</label>
                    <p class="text-2xl font-bold text-green-600">
                        ${{ parseFloat(userMembership.payment_amount || userMembership.amount_paid).toFixed(2) }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Método de Pago</label>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="mdi text-lg" :class="mdiCreditCard"></span>
                        <p class="text-base font-semibold text-gray-900">{{ paymentMethodLabel }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Fecha de Registro</label>
                    <p class="text-base text-gray-900">
                        {{ new Date(userMembership.created_at).toLocaleDateString('es-MX', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) }}
                    </p>
                </div>
            </div>
        </CardBox>

        <!-- Notas -->
        <CardBox v-if="userMembership.notes" class="mt-6">
            <div class="flex items-center space-x-2 mb-4">
                <span class="mdi text-2xl text-gray-600" :class="mdiNoteText"></span>
                <h3 class="text-xl font-bold text-gray-800">Notas Adicionales</h3>
            </div>

            <BaseDivider />

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-gray-700 whitespace-pre-wrap">{{ userMembership.notes }}</p>
            </div>
        </CardBox>

        <!-- Botones de Acción -->
        <div class="mt-6">
            <BaseButtons>
                <BaseButton
                    :href="route(`${routeName}index`)"
                    :icon="mdiArrowLeft"
                    label="Volver al Listado"
                    color="info"
                    outline
                />
                <BaseButton
                    :href="route(`${routeName}edit`, userMembership.id)"
                    :icon="mdiPencil"
                    label="Editar Membresía"
                    color="success"
                    outline
                />
            </BaseButtons>
        </div>
    </LayoutMain>
</template>
