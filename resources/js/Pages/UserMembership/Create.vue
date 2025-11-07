<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import UserSearchSelect from '@/Components/UserSearchSelect.vue';
import NotificationBar from '@/Components/NotificationBar.vue';

import {
    mdiAccount,
    mdiCalendar,
    mdiCurrencyUsd,
    mdiOfficeBuilding,
    mdiCardAccountDetails,
    mdiPlaylistCheck,
    mdiInformation,
    mdiCreditCard,
    mdiAlertCircle
} from "@mdi/js";

const { title, routeName, memberships, gyms } = defineProps({
    title: String,
    routeName: String,
    memberships: Array,
    gyms: Array,
});

const form = useForm({
    gym_id: '',
    user_id: '',
    membership_id: '',
    payment_id: null,
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    amount_paid: '',
    status: 'pending',
});

const selectedMembership = ref(null);
const selectedUser = ref(null);
const userMembershipStatus = ref(null);
const checkingUserStatus = ref(false);

// Filtrar membresías por gimnasio seleccionado
const filteredMemberships = computed(() => {
    if (!form.gym_id) return [];
    return memberships.filter(m => m.gym_id == form.gym_id);
});

// Verificar si el usuario puede recibir una nueva membresía
const checkUserMembershipStatus = async () => {
    if (!form.user_id || !form.gym_id) {
        userMembershipStatus.value = null;
        return;
    }

    checkingUserStatus.value = true;
    try {
        const response = await fetch(route('user-memberships.check-status', {
            user_id: form.user_id,
            gym_id: form.gym_id
        }));

        const data = await response.json();
        userMembershipStatus.value = data;
    } catch (error) {
        console.error('Error al verificar estado de membresía:', error);
        userMembershipStatus.value = null;
    } finally {
        checkingUserStatus.value = false;
    }
};

watch(() => form.gym_id, (newGymId) => {
    if (newGymId) {
        form.membership_id = '';
        form.user_id = '';
        selectedMembership.value = null;
        selectedUser.value = null;
        userMembershipStatus.value = null;
    }
});

watch(() => form.user_id, () => {
    checkUserMembershipStatus();
});

watch(() => form.membership_id, (newMembershipId) => {
    if (newMembershipId) {
        selectedMembership.value = filteredMemberships.value.find(m => m.id == newMembershipId);
        if (selectedMembership.value) {
            form.amount_paid = Number(selectedMembership.value.price);
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
    form.post(route('user-memberships.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedMembership.value = null;
            selectedUser.value = null;
            userMembershipStatus.value = null;
        },
        onError: (errors) => {
            console.error('Errores de validación:', errors);
        }
    });
};

const handleReset = () => {
    form.reset();
    selectedMembership.value = null;
    selectedUser.value = null;
    userMembershipStatus.value = null;
};

const onUserSelected = (user) => {
    const userId = Number(user.id);
    selectedUser.value = {
        id: userId,
        name: user.name,
        last_name: user.last_name,
        mother_last_name: user.mother_last_name,
        email: user.email
    };
    form.user_id = userId;
};

// Computed para verificar si el usuario puede recibir membresía
const canCreateMembership = computed(() => {
    if (!userMembershipStatus.value) return true;
    return !userMembershipStatus.value.has_active_membership;
});

// Computed para verificar si el formulario está completo
const isFormValid = computed(() => {
    return Boolean(
        form.gym_id &&
        form.user_id &&
        form.membership_id &&
        form.start_date &&
        form.end_date &&
        form.amount_paid &&
        form.amount_paid > 0 &&
        canCreateMembership.value
    );
});
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="title" main :icon="mdiCardAccountDetails"/>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation">
            {{ $page.props.flash.error }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <CardBox is-form @submit.prevent="handleSubmit" class="mb-6">
            <!-- Paso 1: Selección de Gimnasio -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">1</span>
                    Seleccionar Gimnasio
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <FormField :error="form.errors.gym_id" label="Gimnasio *" help="Selecciona el gimnasio donde se asignará la membresía">
                        <FormControl
                            v-model="form.gym_id"
                            type="select"
                            required
                            :icon="mdiOfficeBuilding"
                            :options="gyms"
                            value-select="id"
                            placeholder="Seleccione un gimnasio"
                        />
                    </FormField>
                </div>
            </div>

            <!-- Paso 2: Selección de Usuario -->
            <div v-if="form.gym_id" class="mb-8 animate-fade-in">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">2</span>
                    Seleccionar Usuario
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <FormField :error="form.errors.user_id" label="Usuario *" help="Busca y selecciona el usuario que recibirá la membresía">
                        <UserSearchSelect
                            v-model="form.user_id"
                            :gym-id="form.gym_id"
                            @user-selected="onUserSelected"
                        />
                    </FormField>

                    <!-- Verificando estado -->
                    <div v-if="checkingUserStatus" class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600">Verificando estado de membresía...</p>
                    </div>

                    <!-- Advertencia: Usuario tiene membresía activa -->
                    <div v-if="userMembershipStatus?.has_active_membership" class="p-4 bg-red-50 border-2 border-red-300 rounded-lg animate-fade-in">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-900 mb-1">
                                    ⚠️ No se puede asignar una nueva membresía
                                </p>
                                <p class="text-sm text-red-800 mb-2">
                                    Este usuario ya tiene una membresía <strong>{{ userMembershipStatus.current_status === 'active' ? 'ACTIVA' : 'PENDIENTE' }}</strong> en este gimnasio.
                                </p>
                                <div class="bg-white p-3 rounded border border-red-200 mt-2">
                                    <p class="text-xs font-medium text-gray-700 mb-1">Membresía actual:</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ userMembershipStatus.membership_name }}</p>
                                    <p class="text-xs text-gray-600">
                                        Válida hasta: {{ new Date(userMembershipStatus.end_date).toLocaleDateString('es-MX') }}
                                    </p>
                                </div>
                                <p class="text-xs text-red-700 mt-2">
                                    💡 Solo puedes crear una nueva membresía cuando la actual esté <strong>expirada</strong> o <strong>cancelada</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del usuario seleccionado -->
                    <div v-if="selectedUser && !userMembershipStatus?.has_active_membership" class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm font-medium text-blue-900">Usuario seleccionado:</p>
                        <p class="text-base font-semibold text-blue-800">
                            {{ selectedUser.name }} {{ selectedUser.last_name }} {{ selectedUser.mother_last_name }}
                        </p>
                        <p class="text-sm text-blue-600">{{ selectedUser.email }}</p>
                        <p v-if="userMembershipStatus" class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                            ✓ Usuario disponible para nueva membresía
                        </p>
                    </div>
                </div>
            </div>

            <!-- Paso 3: Detalles de la Membresía -->
            <div v-if="form.gym_id && form.user_id && canCreateMembership" class="mb-8 animate-fade-in">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">3</span>
                    Detalles de la Membresía
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Membresía -->
                    <FormField :error="form.errors.membership_id" label="Tipo de Membresía *" help="Selecciona el plan de membresía">
                        <FormControl
                            v-model="form.membership_id"
                            type="select"
                            required
                            :icon="mdiCardAccountDetails"
                            :options="filteredMemberships"
                            value-select="id"
                            placeholder="Seleccione una membresía"
                        />
                    </FormField>

                    <!-- Fecha de Inicio -->
                    <FormField :error="form.errors.start_date" label="Fecha de Inicio *" help="Fecha en que inicia la membresía">
                        <FormControl
                            v-model="form.start_date"
                            type="date"
                            required
                            :icon="mdiCalendar"
                            :min="new Date().toISOString().split('T')[0]"
                        />
                    </FormField>

                    <!-- Fecha de Fin -->
                    <FormField :error="form.errors.end_date" label="Fecha de Fin *" help="Fecha de vencimiento (calculada automáticamente)">
                        <FormControl
                            v-model="form.end_date"
                            type="date"
                            required
                            :icon="mdiCalendar"
                            :min="form.start_date"
                            :readonly="!!selectedMembership"
                        />
                        <p v-if="selectedMembership" class="text-xs text-green-600 mt-1 flex items-center gap-1">
                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                            Calculado automáticamente ({{ selectedMembership.duration_days }} días)
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
                            Precio regular: ${{ selectedMembership.price }}
                        </p>
                    </FormField>

                    <!-- Estado -->
                    <FormField :error="form.errors.status" label="Estado *" help="Estado inicial de la membresía">
                        <FormControl
                            v-model="form.status"
                            type="select"
                            required
                            :icon="mdiPlaylistCheck"
                            :options="[
                                { id: 'pending', name: 'Pendiente' },
                                { id: 'active', name: 'Activa' },
                                { id: 'expired', name: 'Expirada' }
                            ]"
                            value-select="id"
                        />
                    </FormField>
                </div>
            </div>

            <!-- Resumen -->
            <div v-if="selectedMembership && form.start_date && form.end_date && selectedUser && canCreateMembership"
                 class="mt-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 shadow-sm">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-blue-900">
                    <span :class="`w-3 h-3 rounded-full ${
                        form.status === 'active' ? 'bg-green-500' :
                        form.status === 'pending' ? 'bg-yellow-500' : 'bg-red-500'
                    }`"></span>
                    Resumen de la Membresía
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Usuario</p>
                        <p class="text-base font-semibold text-gray-900">
                            {{ selectedUser.name }} {{ selectedUser.last_name }} {{ selectedUser.mother_last_name }}
                        </p>
                        <p class="text-sm text-gray-600">{{ selectedUser.email }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Membresía</p>
                        <p class="text-base font-semibold text-gray-900">{{ selectedMembership.name }}</p>
                        <p class="text-sm text-gray-600">{{ selectedMembership.duration_days }} días</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Vigencia</p>
                        <p class="text-sm text-gray-900">{{ new Date(form.start_date).toLocaleDateString('es-MX') }}</p>
                        <p class="text-sm text-gray-600">hasta {{ new Date(form.end_date).toLocaleDateString('es-MX') }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Monto</p>
                        <p class="text-2xl font-bold text-green-600">${{ form.amount_paid || '0.00' }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Estado</p>
                        <span :class="{
                            'bg-yellow-100 text-yellow-800': form.status === 'pending',
                            'bg-green-100 text-green-800': form.status === 'active',
                            'bg-red-100 text-red-800': form.status === 'expired'
                        }" class="inline-flex px-3 py-1 rounded-full text-sm font-semibold capitalize">
                            {{ form.status === 'pending' ? 'Pendiente' : form.status === 'active' ? 'Activa' : 'Expirada' }}
                        </span>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Gimnasio</p>
                        <p class="text-base font-semibold text-gray-900">
                            {{ gyms.find(g => g.id == form.gym_id)?.name }}
                        </p>
                    </div>
                </div>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton
                        type="submit"
                        color="info"
                        :label="form.processing ? 'Guardando...' : 'Guardar Membresía'"
                        :disabled="form.processing || !isFormValid"
                        class="font-semibold"
                    />
                    <BaseButton
                        type="button"
                        @click="handleReset"
                        color="contrast"
                        outline
                        label="Limpiar"
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
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
