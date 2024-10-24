<script lang="ts" setup>

import {
 requiredValidator,
} from "@/@core/utils/validators";
import avatar1 from "@images/avatars/avatar-1.png";
import { VForm } from "vuetify/components/VForm";

// interface
interface EducationData{
  id?: number
  school_name: string
  image_url: string
  start_date: any
  end_date: any
  degree: string
  field_of_study: string
  description: string
}


// Interface
interface Emit {
  (e: "update:isDrawerOpen", value: boolean): void;
  (e: "educationData", value: any): void;
  (e: "closeDialog", value: boolean): void;
}

interface Props {
  isDrawerOpen?: boolean;
  educationData?: EducationData | any;
  isEdit?: boolean;
  isLoading?: boolean;
  title?: string;
  
}
// Props
const props = defineProps<Props>();

// Emit
const emit = defineEmits<Emit>();

const isFormValid = ref(false);
const refForm = ref<VForm>();
// user info
const id = ref<number>();
const school_name = ref<string>("");
const start_date = ref<string>("");
const end_date = ref<string>("");

const image_url = ref<string>("");
const degree = ref<string>("");
const field_of_study = ref<string>("");
const description = ref<string>("");

const title = ref<string>("Education");

// 👉 drawer close
const closeNavigationDrawer = () => {
  nextTick(() => {
    refForm.value?.reset();
    refForm.value?.resetValidation();
  });
  emit("closeDialog", false);
};

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const emitObj = {
        id: id?.value,
        school_name: school_name?.value,
        start_date: start_date?.value,
        end_date: end_date?.value,
        degree: degree?.value,
        field_of_study: field_of_study?.value,
        description: description?.value,
        isUpdate: props?.educationData?.id ? true : null,
      };
      console.log("educationData", emitObj);
      emit("educationData", emitObj);
      refForm.value?.reset();
    }
  });
};

watchEffect(() => {
  if (props?.educationData?.id) {
    id.value = props?.educationData?.id;
    school_name.value = props?.educationData?.school_name;
    start_date.value = props?.educationData?.start_date;
    end_date.value = props?.educationData?.end_date;
    degree.value = props?.educationData?.degree;
    field_of_study.value = props?.educationData?.field_of_study;
    description.value = props?.educationData?.description;
    
  }
});

watch(
  async () => props?.isEdit,
  async (val) => {
    title.value = props?.isEdit ? " Edit Education": " Add Education";
  }
);

onMounted(() => {
  title.value = props?.isEdit ? " Edit Education" : " Add Education";
  
})
</script>

<template>
    <VDialog v-model="props.isDrawerOpen" max-width="600" @update:is-drawer-open="emit('closeDialog', false)"
        scroll-strategy="none">
        <DialogCloseBtn @click="emit('closeDialog', false)" />
        <!-- Dialog Content -->
        <VCard :title="title">

            <VForm ref="refForm" v-model="isFormValid" @submit.prevent="onSubmit">
                <VCardText>
                    <VRow>
                        <VCol cols="6">
                            <AppTextField v-model="school_name" label="School Name"
                                :rules="[requiredValidator(school_name, 'School Name')]" />
                        </VCol>
                        <VCol cols="6">
                            <AppTextField v-model="degree" label="Degree"
                                :rules="[requiredValidator(degree, 'Degree')]" />
                        </VCol>

                        <VCol cols="6">
                            <!-- :rules="[requiredValidator(start_date, 'Start Date')]" -->
                            <AppDateTimePicker v-model="start_date" label="Start Date" :config="{ dateFormat: 'Y-m-d' }"
                                placeholder="start date" />
                        </VCol>
                        <VCol cols="6">
                            <!-- :rules="[requiredValidator(end_date, 'End Date')]"  -->
                            <AppDateTimePicker v-model="end_date" label="End Date" :config="{ dateFormat: 'Y-m-d' }"
                                placeholder="end date" />
                        </VCol>
                        <VCol cols="6">
                            <AppTextField v-model="field_of_study" label="Field of Study"
                                :rules="[requiredValidator(field_of_study, 'Field of Study')]" />
                        </VCol>
                        <VCol cols="6">
                            <AppTextField v-model="description" label="Description" />
                        </VCol>
                    </VRow>
                </VCardText>

                <VCardText class="d-flex justify-end flex-wrap gap-2">

                    <VBtn v-if="props?.isLoading" loading="white" class="mx-2" />
                    <VBtn v-else type="submit" class="mx-2">
                        {{ props?.isEdit ? "Save" : "Submit" }}
                    </VBtn>
                    <VBtn type="reset" variant="tonal" color="secondary" class="mx-2" @click="closeNavigationDrawer">
                        Cancel
                    </VBtn>
                </VCardText>
            </VForm>
        </VCard>
    </VDialog>
</template>
