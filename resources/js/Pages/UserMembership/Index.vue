<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import BaseButton from '@/Components/BaseButton.vue';
import NotificationBar from '@/Components/NotificationBar.vue';
import FormControl from '@/Components/FormControl.vue';
import Pagination from '@/Shared/Pagination.vue';
import {
    mdiCardAccountDetails,
    mdiPlus,
    mdiPencil,
    mdiTrashCan,
    mdiEye,
    mdiCheckboxMarked,
    mdiMagnify,
    mdiTicketPercent,
    mdiClockAlert,
    mdiCancel,
    mdiMinusCircle
} from '@mdi/js';

const props = defineProps({
    title: String,
    userMemberships: Object,
    routeName: String,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const searchMemberships = () => {
    router.get(route(`${props.routeName}index`), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteMembership = (id) => {
    if (confirm('¿Estás seguro de eliminar esta membresía?')) {
        router.delete(route(`${props.routeName}destroy`, id));
    }
};

const decrementSession = (id) => {
    if (confirm('¿Descontar una sesión a esta membresía?')) {
        router.post(route(`${props.routeName}decrement-session`, id));
    }
};

const cancelMembership = (id) => {
    if (confirm('¿Cancelar esta membresía?')) {
        router.post(route(`${props.routeName}cancel`, id));
    }
};

const getStatusBadge = (membership) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const startDate = new Date(membership.start_date);
    startDate.setHours(0, 0, 0, 0);

    // Si aún no ha comenzado
    if (startDate > today) {
        return { color: 'bg-blue-100 text-blue-800', text: 'Programada' };
    }

    if (membership.is_active) {
        return { color: 'bg-green-100 text-green-800', text: 'Activa' };
    } else if (membership.status === 'expired') {
        return { color: 'bg-red-100 text-red-800', text: 'Expirada' };
    } else if (membership.status === 'cancelled') {
        return { color: 'bg-gray-100 text-gray-800', text: 'Cancelada' };
    }
    return { color: 'bg-yellow-100 text-yellow-800', text: 'Inactiva' };
};

const getDaysRemainingColor = (days) => {
    if (days === null) return 'text-blue-600';
    if (days > 30) return 'text-green-600';
    if (days > 7) return 'text-yellow-600';
    return 'text-red-600';
};

const getDaysDisplay = (membership) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const startDate = new Date(membership.start_date);
    startDate.setHours(0, 0, 0, 0);

    // Si aún no ha comenzado, no mostrar nada
    if (startDate > today) {
        return null;
    }

    // Si days_remaining es null o undefined
    if (membership.days_remaining === null || membership.days_remaining === undefined) {
        return null;
    }

    return `${membership.days_remaining} días restantes`;
};
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton
            :title="title"
            :icon="mdiCardAccountDetails"
            main
        >
            <BaseButton
                :href="route(`${routeName}create`)"
                :icon="mdiPlus"
                label="Asignar Membresía"
                color="info"
                rounded-full
                small
            />
        </SectionTitleLineWithButton>

        <NotificationBar
            v-if="$page.props.flash.success"
            color="success"
            :icon="mdiCheckboxMarked"
        >
            {{ $page.props.flash.success }}
        </NotificationBar>

        <CardBox class="mb-6" has-table>
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <FormControl
                            v-model="search"
                            :icon="mdiMagnify"
                            placeholder="Buscar por usuario..."
                            @keyup.enter="searchMemberships"
                        />
                    </div>

                    <div>
                        <FormControl
                            v-model="status"
                            type="select"
                            :options="[
                { id: '', name: 'Todos los estados' },
                { id: 'active', name: 'Activas' },
                { id: 'expired', name: 'Expiradas' },
                { id: 'cancelled', name: 'Canceladas' }
              ]"
                            label-key="name"
                            value-key="id"
                            @change="searchMemberships"
                        />
                    </div>

                    <div class="flex gap-2">
                        <BaseButton
                            @click="searchMemberships"
                            color="info"
                            :icon="mdiMagnify"
                            label="Buscar"
                            outline
                        />
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Membresía
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Gimnasio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vigencia
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sesiones
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in userMemberships.data" :key="item.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ item.user?.name }} {{ item.user?.last_name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ item.user?.email }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ item.membership?.name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ item.membership?.description }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ item.membership?.gyms?.name || 'N/A' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ new Date(item.start_date).toLocaleDateString('es-MX') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                hasta {{ new Date(item.end_date).toLocaleDateString('es-MX') }}
                            </div>
                            <div v-if="getDaysDisplay(item)" class="text-xs font-medium mt-1" :class="getDaysRemainingColor(item.days_remaining)">
                                {{ getDaysDisplay(item) }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium"
                                 :class="item.remaining_sessions > 0 ? 'text-green-600' : 'text-red-600'">
                                {{ item.remaining_sessions }} / {{ item.membership?.sessions }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusBadge(item).color"
                >
                  {{ getStatusBadge(item).text }}
                </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <BaseButtons type="justify-end" no-wrap>
                                <BaseButton
                                    v-if="item.is_active && item.remaining_sessions > 0"
                                    @click="decrementSession(item.id)"
                                    :icon="mdiMinusCircle"
                                    color="warning"
                                    small
                                    title="Descontar sesión"
                                />

                                <BaseButton
                                    v-if="item.status === 'active'"
                                    @click="cancelMembership(item.id)"
                                    :icon="mdiCancel"
                                    color="danger"
                                    small
                                    title="Cancelar membresía"
                                />

                                <BaseButton
                                    :href="route(`${routeName}show`, item.id)"
                                    :icon="mdiEye"
                                    color="info"
                                    small
                                    title="Ver detalles"
                                />

                                <BaseButton
                                    :href="route(`${routeName}edit`, item.id)"
                                    :icon="mdiPencil"
                                    color="success"
                                    small
                                    title="Editar"
                                />

                                <BaseButton
                                    @click="deleteMembership(item.id)"
                                    :icon="mdiTrashCan"
                                    color="danger"
                                    small
                                    title="Eliminar"
                                />
                            </BaseButtons>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="p-4 border-t border-gray-200">
                <Pagination :links="userMemberships.links" />
            </div>
        </CardBox>
    </LayoutMain>
</template>
