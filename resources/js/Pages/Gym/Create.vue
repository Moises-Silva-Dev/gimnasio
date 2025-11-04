<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import LayoutMain from '@/Layouts/LayoutMain.vue';
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import { mdiAccount, mdiAccountCircle, mdiAccountTie, mdiPhone, mdiMail, mdiLock, mdiOfficeBuilding, mdiMace, mdiMap, mdiLadder,mdiPlusBox } from "@mdi/js";

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
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <LayoutMain :title="title">
        <SectionTitleLineWithButton :title="props.title" main :icon="mdiPlusBox"/>
        <CardBox form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormField :error="form.errors.name" label="Nombre del Gimnasio">
                    <FormControl v-model="form.name" type="text" required :icon="mdiOfficeBuilding"
                        placeholder="Ingresa el nombre del gimnasio" />
                </FormField>

                <FormField :error="form.errors.address" label="Dirección">
                    <FormControl v-model="form.address" type="text" required :icon="mdiMap"
                        placeholder="Ingresa la dirección" />
                </FormField>

                <FormField :error="form.errors.phone" label="Teléfono">
                    <FormControl v-model="form.phone" type="text" required :icon="mdiPhone"
                        placeholder="Ingresa el teléfono" maxlength="10"/>
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
