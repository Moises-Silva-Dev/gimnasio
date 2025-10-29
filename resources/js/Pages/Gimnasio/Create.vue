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
    mdiOfficeBuilding
} from "@mdi/js";

const isUploading = ref(false)

const props = defineProps({
    titulo: String,
    routeName: String,
});

const form = useForm({
    nombre: '',
    email: '',
    telefono: '',
    hora_apertura: '',
    hora_cierre: '',
    maxima_capacidad: '',
    estatus: '',
    phone: '',
    calle: '',
    colonia: '',
    cp: '',
    municipio: '',
    estado: '',
})

const handleSubmit = () => {
    console.log('Formulario enviado:', form);
    // form.post(route(`${props.routeName}store`)); // Corregida sintaxis de ruta
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <LayoutMain :title="titulo">
        <SectionTitleLineWithButton :title="props.titulo" main />
        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormField :error="form.errors.nombre" label="Nombre del Gimnasio">
                    <FormControl v-model="form.nombre" type="text" required :icon="mdiAccount" placeholder="Ingresa el nombre del gimnasio"/>
                </FormField>

                <FormField :error="form.errors.email" label="Correo Electrónico">
                    <FormControl v-model="form.email" type="email" required :icon="mdiMail" placeholder="Ingresa el correo electrónico" />
                </FormField>

                <FormField :error="form.errors.telefono" label="Teléfono">
                    <FormControl v-model="form.telefono" type="text" required :icon="mdiPhone" placeholder="Ingresa el teléfono" />
                </FormField>

                <FormField :error="form.errors.hora_apertura" label="Hora de Apertura">
                    <FormControl v-model="form.hora_apertura" type="time" required :icon="mdiAccount" placeholder="Ingresa la hora de apertura" />
                </FormField>

                <FormField :error="form.errors.hora_cierre" label="Hora de Cierre">
                    <FormControl v-model="form.hora_cierre" type="time" required :icon="mdiAccount" placeholder="Ingresa la hora de cierre" />
                </FormField>

                <FormField :error="form.errors.maxima_capacidad" label="Máxima Capacidad">
                    <FormControl v-model="form.maxima_capacidad" type="number" required :icon="mdiAccount" placeholder="Ingresa la máxima capacidad" />
                </FormField>

                <FormField :error="form.errors.estatus" label="Estatus">
                    <FormControl v-model="form.estatus" type="text" required :icon="mdiAccount" placeholder="Ingresa el estatus" />
                </FormField>

                <FormField :error="form.errors.phone" label="Número">
                    <FormControl v-model="form.phone" type="text" required :icon="mdiAccount" placeholder="Ingresa el número" />
                </FormField>

                <FormField :error="form.errors.calle" label="Calle">
                    <FormControl v-model="form.calle" type="text" required :icon="mdiAccount" placeholder="Ingresa la calle" />
                </FormField>

                <FormField :error="form.errors.colonia" label="Colonia">
                    <FormControl v-model="form.colonia" type="text" required :icon="mdiAccount" placeholder="Ingresa la colonia" />
                </FormField>

                <FormField :error="form.errors.cp" label="Código Postal">
                    <FormControl v-model="form.cp" type="text" required :icon="mdiAccount" placeholder="Ingresa el código postal" />
                </FormField>

                <FormField :error="form.errors.municipio" label="Municipio">
                    <FormControl v-model="form.municipio" type="text" required :icon="mdiAccount" placeholder="Ingresa el municipio" />
                </FormField>

                <FormField :error="form.errors.estado" label="Estado">
                    <FormControl v-model="form.estado" type="text" required :icon="mdiAccount" placeholder="Ingresa el estado" />
                </FormField>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton @click="handleSubmit" type="submit" color="info" outline label="Guardar" />
                    <BaseButton :href="route(`${props.routeName}index`)" type="reset" color="danger" outline label="Cancelar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
