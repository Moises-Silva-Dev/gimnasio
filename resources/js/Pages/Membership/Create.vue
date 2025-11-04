<script setup>
import { ref, defineProps, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import { mdiWalletMembership, mdiTextBoxEdit, mdiSunClock, mdiGoogleClassroom, mdiCash, mdiOfficeBuilding,mdiCreditCardPlus } from "@mdi/js";

const props = defineProps({
    title: String,
    routeName: String,
    gyms: Array,
});

const form = useForm({
    gym_id: '',
    name: '',
    description: '',
    duration_days: '',
    sessions: '',
    price: '',
})

const handleSubmit = () => {
    console.log('Formulario enviado:', form);
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="props.title" main :icon="mdiCreditCardPlus"/>
        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormField :error="form.errors.gym_id" label="Gimnasio">
                    <FormControl v-model="form.gym_id" :options="gyms" type="select" label-key="nombre_gimnasio"
                        value-key="id" :icon="mdiOfficeBuilding" required />
                </FormField>

                <FormField :error="form.errors.name" label="Nombre de la Membresía">
                    <FormControl v-model="form.name" type="text" required :icon="mdiWalletMembership"
                        placeholder="Ingresa el nombre del gimnasio" />
                </FormField>

                <FormField :error="form.errors.description" label="Descripción">
                    <FormControl v-model="form.description" type="text" required :icon="mdiTextBoxEdit"
                        placeholder="Ingresa la descripción de la membresía" />
                </FormField>

                <FormField :error="form.errors.duration_days" label="Duración (días)">
                    <FormControl v-model="form.duration_days" type="text" required :icon="mdiSunClock"
                        placeholder="Ingresa el numero de dias" />
                </FormField>

                <FormField :error="form.errors.sessions" label="Sesiones">
                    <FormControl v-model="form.sessions" type="text" required :icon="mdiGoogleClassroom"
                        placeholder="Ingresa el numeros de sesiones" />
                </FormField>

                <FormField :error="form.errors.price" label="Precio">
                    <FormControl v-model="form.price" type="text" required :icon="mdiCash"
                        placeholder="Ingresa el precio de la membresia" />
                </FormField>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Guardar" />
                    <BaseButton :href="route(`${props.routeName}index`)" type="reset" color="danger" outline
                        label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
