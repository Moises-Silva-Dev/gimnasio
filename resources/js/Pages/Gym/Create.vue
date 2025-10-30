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
    title: String,
    routeName: String,
});

const form = useForm({
    name: '',
    address: '',
    phone: '',
})

const handleSubmit = () => {
    console.log('Formulario enviado:', form);
    // form.post(route(`${props.routeName}store`)); // Corregida sintaxis de ruta
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="props.title" main />
        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormField :error="form.errors.name" label="Nombre del Gimnasio">
                    <FormControl v-model="form.name" type="text" required :icon="mdiAccount" placeholder="Ingresa el nombre del gimnasio"/>
                </FormField>

                <FormField :error="form.errors.address" label="Dirección">
                    <FormControl v-model="form.address" type="text" required :icon="mdiOfficeBuilding" placeholder="Ingresa la dirección" />
                </FormField>

                <FormField :error="form.errors.phone" label="Teléfono">
                    <FormControl v-model="form.phone" type="text" required :icon="mdiPhone" placeholder="Ingresa el teléfono" />
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
