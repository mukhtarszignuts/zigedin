<script lang="ts" setup>

import {
 requiredValidator,
} from "@/@core/utils/validators";
import avatar1 from "@images/avatars/avatar-1.png";
import { VForm } from "vuetify/components/VForm";
import useFormatting from "@/composable/useFormatting";

// interface
interface ExperienceData{
  id?:number, 
  title:string, 
  start_date:string, 
  end_date?:string, 
  company_name:string, 
  description:string, 
  employment_type:string, 
  location:string, 
  location_type:string, 
}


// Interface
interface Emit {
  (e: "update:isDrawerOpen", value: boolean): void;
  (e: "experienceData", value: any): void;
  (e: "closeDialog", value: boolean): void;
}

interface Props {
  isDrawerOpen?: boolean;
  experienceData?: ExperienceData | any;
  isEdit?: boolean;
  isLoading?: boolean;
  cardtitle?: string;
  
}
// Props
const props = defineProps<Props>();

// Emit
const emit = defineEmits<Emit>();

const { empOptions , localtionTypeOptions } = useFormatting();

const isFormValid = ref(false);
const refForm = ref<VForm>();
// user info
const id = ref<number>();
const title = ref<string>("");
const start_date = ref<string>("");
const end_date = ref<string>("");
const company_name = ref<string>("");
const description = ref<string>("");
const employment_type = ref<string>("");
const location = ref<string>("");
const location_type = ref<string>("");

const cardtitle = ref<string>("Experience");

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
        title: title?.value,
        start_date: start_date?.value,
        end_date: end_date?.value,
        company_name: company_name?.value,
        employment_type: employment_type?.value,
        location: location?.value,
        location_type: location_type?.value,
        description: description?.value,
        isUpdate: props?.experienceData?.id ? true : null,
      };
      console.log("experienceData", emitObj);
      emit("experienceData", emitObj);
      refForm.value?.reset();
    }
  });
};

watchEffect(() => {
  if (props?.experienceData?.id) {
    id.value = props?.experienceData?.id;
    title.value = props?.experienceData?.title;
    start_date.value = props?.experienceData?.start_date;
    end_date.value = props?.experienceData?.end_date;
    company_name.value = props?.experienceData?.company_name;
    employment_type.value = props?.experienceData?.employment_type;
    location.value = props?.experienceData?.location;
    location_type.value = props?.experienceData?.location_type;
    description.value = props?.experienceData?.description;
    
  }
});

watch(
  async () => props?.isEdit,
  async (val) => {
    cardtitle.value = props?.isEdit ? " Edit Experience": " Add Experience";
  }
);

onMounted(() => {
  cardtitle.value = props?.isEdit ? " Edit Experience" : " Add Experience";
  
})
</script>

<template>
    <VDialog v-model="props.isDrawerOpen" max-width="600" @update:is-drawer-open="emit('closeDialog', false)"
        scroll-strategy="none">
        <DialogCloseBtn @click="emit('closeDialog', false)" />
        <!-- Dialog Content -->
        <VCard :title="cardtitle">

            <VForm ref="refForm" v-model="isFormValid" @submit.prevent="onSubmit">
                <VCardText>
                    <VRow>
                        <VCol cols="6">
                            <AppTextField v-model="title" label="Position"
                                :rules="[requiredValidator(title, 'Position')]" />
                        </VCol>
                        <VCol cols="6">
                            <AppTextField v-model="company_name" label="Company Name"
                                :rules="[requiredValidator(company_name, 'Company Name')]" />
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
                            <AppTextField v-model="location" label="Location"
                                :rules="[requiredValidator(location, 'Location')]" />
                        </VCol>
                        <VCol cols="6">
                            <AppTextField v-model="description" label="Description" />
                        </VCol>
                        <VCol cols="6">
                          <AppSelect v-model="employment_type" label="Employment Type" :items="empOptions" item-value="id" item-title="name"
                          class="mx-2" />
                        </VCol>
                        <VCol cols="6">
                          <AppSelect v-model="location_type" label="Location Type" :items="localtionTypeOptions" item-value="id" item-title="name"
                          class="mx-2" />
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
