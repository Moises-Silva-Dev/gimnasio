<script setup>
import { router } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import Swal from "sweetalert2";
import { mdiTagEdit, mdiDeleteOutline, mdiInformation, mdiPlus, mdiCardAccountDetails, mdiEye } from "@mdi/js";
import Pagination from '@/Shared/Pagination.vue';
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import { ref, computed } from 'vue';
import SearchBar from '@/Components/SearchBar.vue';

const props = defineProps({
    title: String,
    userMemberships: Object,
    routeName: String,
    filters: Object,
});

const destroy = (id) => {
    Swal.fire({
        title: "¿Está seguro?",
        text: "Esta acción no se puede revertir",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar registro!",
        cancelButtonText: "Cancelar"
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(route(`${props.routeName}destroy`, id));
        }
    });
};

const filters = ref({ ...props.filters });

const getStatusBadge = (status) => {
    const badges = {
        'active': { class: 'bg-green-100 text-green-800', text: 'Activa' },
        'pending': { class: 'bg-yellow-100 text-yellow-800', text: 'Pendiente' },
        'expired': { class: 'bg-red-100 text-red-800', text: 'Expirada' },
        'cancelled': { class: 'bg-gray-100 text-gray-800', text: 'Cancelada' }
    };
    return badges[status] || badges['pending'];
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(amount);
};
</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :title="props.title" main :icon="mdiCardAccountDetails">
            <BaseButton
                :href="route(`${props.routeName}create`)"
                color="warning"
                label="Registrar Nueva Membresía"
                :icon="mdiPlus"
            />
        </SectionTitleLineWithButton>

        <SearchBar
            v-model="filters.search"
            :routeName="routeName"
            placeholder="Buscar por nombre, email o teléfono del usuario..."
        />

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>

        <CardBox v-if="userMemberships.data.length < 1">
            <CardBoxComponentEmpty />
        </CardBox>

        <CardBox v-else class="mb-6" has-table>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="border p-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th class="border p-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Gimnasio
                        </th>
                        <th class="border p-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Membresía
                        </th>
                        <th class="border p-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Vigencia
                        </th>
                        <th class="border p-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Monto
                        </th>
                        <th class="border p-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="border p-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in userMemberships.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <!-- Usuario -->
                        <td class="border p-3">
                            <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">
                                        {{ item.user?.name }} {{ item.user?.last_name }} {{ item.user?.mother_last_name }}
                                    </span>
                                <span class="text-sm text-gray-500">{{ item.user?.email }}</span>
                                <span v-if="item.user?.phone" class="text-xs text-gray-400">
                                        {{ item.user?.phone }}
                                    </span>
                            </div>
                        </td>

                        <!-- Gimnasio -->
                        <td class="border p-3">
                            <span class="font-medium text-gray-800">{{ item.gym?.name }}</span>
                        </td>

                        <!-- Membresía -->
                        <td class="border p-3">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-800">{{ item.membership?.name }}</span>
                                <span class="text-xs text-gray-500">
                                        {{ item.membership?.duration_days }} días
                                    </span>
                                <span v-if="item.remaining_sessions !== null" class="text-xs text-blue-600">
                                        Sesiones: {{ item.remaining_sessions }}
                                    </span>
                            </div>
                        </td>

                        <!-- Vigencia -->
                        <td class="border p-3">
                            <div class="flex flex-col text-sm">
                                    <span class="text-gray-600">
                                        <span class="font-medium">Inicio:</span> {{ formatDate(item.start_date) }}
                                    </span>
                                <span class="text-gray-600">
                                        <span class="font-medium">Fin:</span> {{ formatDate(item.end_date) }}
                                    </span>
                                <span v-if="item.days_remaining !== null" class="text-xs mt-1"
                                      :class="item.days_remaining > 7 ? 'text-green-600' : 'text-red-600'">
                                        {{ item.days_remaining > 0 ? `${item.days_remaining} días restantes` : 'Expirada' }}
                                    </span>
                            </div>
                        </td>

                        <!-- Monto -->
                        <td class="border p-3">
                                <span class="font-bold text-green-600">
                                    {{ formatCurrency(item.amount_paid) }}
                                </span>
                        </td>

                        <!-- Estado -->
                        <td class="border p-3">
                                <span
                                    :class="getStatusBadge(item.status).class"
                                    class="inline-flex px-3 py-1 rounded-full text-xs font-semibold"
                                >
                                    {{ getStatusBadge(item.status).text }}
                                </span>
                        </td>

                        <!-- Acciones -->
                        <td class="border p-3">
                            <BaseButtons type="justify-center" no-wrap>
                                <BaseButton
                                    color="success"
                                    :icon="mdiEye"
                                    small
                                    :href="route(`${props.routeName}show`, item.id)"
                                    title="Ver detalles"
                                />
                                <BaseButton
                                    color="info"
                                    :icon="mdiTagEdit"
                                    small
                                    :href="route(`${props.routeName}edit`, item.id)"
                                    title="Editar"
                                />
                                <BaseButton
                                    color="danger"
                                    :icon="mdiDeleteOutline"
                                    small
                                    @click="destroy(item.id)"
                                    title="Eliminar"
                                />
                            </BaseButtons>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <Pagination
                    :currentPage="userMemberships.current_page"
                    :links="userMemberships.links"
                    :total="userMemberships.last_page"
                />
            </div>
        </CardBox>
    </LayoutMain>
</template>

<style scoped>
/* Estilos opcionales para mejorar la responsividad en móviles */
@media (max-width: 768px) {
    table {
        font-size: 0.875rem;
    }

    th, td {
        padding: 0.5rem !important;
    }
}
</style>
