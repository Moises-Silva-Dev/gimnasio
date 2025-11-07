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

import { 
    mdiAccount, 
    mdiCalendar, 
    mdiCurrencyUsd, 
    mdiOfficeBuilding,
    mdiCardAccountDetails,
    mdiPlaylistCheck
} from "@mdi/js";

// Props - desestructurar para usar directamente
const { title, routeName, memberships, gyms, users } = defineProps({
    title: String,
    routeName: String,
    memberships: Array,    
    gyms: Array,
    users: Array,
});

console.log('Gyms:', gyms);
console.log('Memberships:', memberships);
console.log('Users:', users);

// Form data
const form = useForm({
    gym_id: '',
    user_id: '',
    membership_id: '',
    payment_id: null,
    start_date: '',
    end_date: '',
    amount_paid: '',
    status: 'pending',
});

// Estado reactivo para cálculos
const selectedMembership = ref(null);
const calculatedEndDate = ref('');

// Watchers para cálculos automáticos
watch(() => form.membership_id, (newMembershipId) => {
    if (newMembershipId) {
        selectedMembership.value = memberships.find(m => m.id == newMembershipId);
        if (selectedMembership.value && form.start_date) {
            calculateEndDate();
        }
        // Auto-completar el monto si la membresía tiene precio
        if (selectedMembership.value?.price) {
            form.amount_paid = selectedMembership.value.price;
        }
    } else {
        selectedMembership.value = null;
        calculatedEndDate.value = '';
    }
});

watch(() => form.start_date, (newStartDate) => {
    if (newStartDate && selectedMembership.value) {
        calculateEndDate();
    }
});

// Calcular fecha de fin automáticamente
const calculateEndDate = () => {
    if (!form.start_date || !selectedMembership.value?.duration_days) return;
    
    const startDate = new Date(form.start_date);
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + selectedMembership.value.duration_days);
    
    calculatedEndDate.value = endDate.toISOString().split('T')[0];
    form.end_date = calculatedEndDate.value;
};

// Filtrar usuarios por gym seleccionado (si aplica)
const filteredUsers = computed(() => {
    if (!form.gym_id) return users;
    return users.filter(user => user.gym_id == form.gym_id);
});

// Manejar envío del formulario
const handleSubmit = () => {
    form.post(route(`${routeName}store`), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// Resetear formulario
const handleReset = () => {
    form.reset();
    selectedMembership.value = null;
    calculatedEndDate.value = '';
};
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="title" main :icon="mdiCardAccountDetails"/>
        
        <CardBox form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Gimnasio -->
                <FormField :error="form.errors.gym_id" label="Gimnasio *" help="Selecciona el gimnasio">
                    <FormControl 
                        v-model="form.gym_id" 
                        type="select" 
                        required 
                        :icon="mdiOfficeBuilding"
                        :options="gyms"
                        value-select="id"
                    />
                </FormField>

       

                <FormField :error="form.errors.user_id" label="Usuario *" help="Busca y selecciona un usuario">
                    <UserSearchSelect v-model="form.user_id" :gym-id="form.gym_id || 0" :disabled="!form.gym_id"/>
                </FormField>            

                <!-- Membresía -->
                <FormField :error="form.errors.membership_id" label="Membresía *" help="Selecciona el tipo de membresía">
                    <FormControl 
                        v-model="form.membership_id" 
                        type="select" 
                        required 
                        :icon="mdiCardAccountDetails"
                        :options="memberships"
                        value-select="id"
                    />
                </FormField>

                <!-- Los demás campos se mantienen igual -->
                <FormField :error="form.errors.start_date" label="Fecha de Inicio *">
                    <FormControl 
                        v-model="form.start_date" 
                        type="date" 
                        required 
                        :icon="mdiCalendar"
                        :min="new Date().toISOString().split('T')[0]"
                    />
                </FormField>

                <FormField :error="form.errors.end_date" label="Fecha de Fin *">
                    <FormControl 
                        v-model="form.end_date" 
                        type="date" 
                        required 
                        :icon="mdiCalendar"
                        :min="form.start_date"
                    />
                    <p v-if="calculatedEndDate" class="text-xs text-green-600 mt-1">
                        Calculado automáticamente
                    </p>
                </FormField>

                <FormField :error="form.errors.amount_paid" label="Monto Pagado *" help="Monto pagado por la membresía">
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
                        Precio de membresía: ${{ selectedMembership.price }}
                    </p>
                </FormField>

                <!-- Estado -->
                <FormField :error="form.errors.status" label="Estado *">
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

            <!-- Información de resumen -->
            <div v-if="selectedMembership && form.start_date && form.end_date" 
                 class="mt-6 p-4 bg-gray-50 rounded-lg border">
                <h3 class="text-lg font-semibold mb-2 flex items-center gap-2">
                    <span :class="`w-2 h-2 rounded-full ${
                        form.status === 'active' ? 'bg-green-500' : 
                        form.status === 'pending' ? 'bg-yellow-500' : 'bg-red-500'
                    }`"></span>
                    Resumen de Membresía
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>Tipo:</strong> {{ selectedMembership.name }}
                    </div>
                    <div>
                        <strong>Duración:</strong> {{ selectedMembership.duration_days }} días
                    </div>
                    <div>
                        <strong>Inicio:</strong> {{ form.start_date }}
                    </div>
                    <div>
                        <strong>Fin:</strong> {{ form.end_date }}
                    </div>
                    <div>
                        <strong>Monto:</strong> ${{ form.amount_paid || '0.00' }}
                    </div>
                    <div>
                        <strong>Estado:</strong> 
                        <span :class="{
                            'text-yellow-600': form.status === 'pending',
                            'text-green-600': form.status === 'active', 
                            'text-red-600': form.status === 'expired'
                        }" class="capitalize">
                            {{ form.status }}
                        </span>
                    </div>
                </div>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton 
                        type="submit" 
                        color="info" 
                        :label="form.processing ? 'Guardando...' : 'Guardar Membresía'" 
                        :disabled="form.processing"
                        :outline="!form.processing"
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