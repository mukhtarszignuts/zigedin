<script setup lang="ts">
import { requiredValidator } from '@/@core/utils/validators'
import { VForm } from "vuetify/components/VForm";



interface SkillData{
  name:string
  
}
interface Props {
  isDrawerOpen?: boolean
  isEdit: boolean
  isLoading: boolean
  skillData:SkillData | any
}

// Interface
interface Emit {
  (e: "update:isDrawerOpen", value: boolean): void;
  (e: "skillData", value: any): void;
  (e: "closeDialog", value: boolean): void;
}

const props = defineProps<Props>()

const emit = defineEmits<Emit>()

const refForm = ref<VForm>();

const name = ref('')
const id = ref<number>()

const isFormValid = ref(false);

const onReset = () => {
  refForm.value?.reset();
  refForm.value?.resetValidation();
  emit('closeDialog', false)
  
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const emitObj = {
        id:id?.value,
        name: name?.value,
        isUpdate: props?.skillData?.id ? true : false,
      };
      console.log("skillData", emitObj);
      emit("skillData", emitObj);
      refForm.value?.reset();
    }
  });
};


watchEffect(() => {
  if (props?.skillData?.id) {
    id.value = props?.isEdit==true? props?.skillData?.id:null;
    name.value = props?.skillData?.name;
  }
});
</script>

<template>
  <VDialog :width="$vuetify.display.smAndDown ? 'auto' : 600" :model-value="props.isDrawerOpen"
    @update:model-value="onReset">
    <!-- 👉 dialog close btn -->
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-sm-8 pa-5">
      <!-- 👉 Title -->
      <VCardItem class="text-center">
        <VCardTitle class="text-h5">
          {{ props.isEdit ? 'Edit' : 'Add' }} Skill
        </VCardTitle>

      </VCardItem>

      <!-- <VCardText class="mt-1"> -->
      <VForm ref="refForm" v-model="isFormValid" @submit.prevent="onSubmit">
        <VCardText>
          <VRow>
            <VCol cols="12">
              <AppTextField v-model="name" label="Name" :rules="[requiredValidator(name, 'Name')]" />
            </VCol>
          </VRow>
        </VCardText>
        <VCardText class="d-flex justify-end flex-wrap gap-2">
          <VBtn v-if="props?.isLoading" loading="white" class="mx-2" />
          <VBtn v-else type="submit" class="mx-2">
            {{ props?.isEdit ? "Save" : "Submit" }}
          </VBtn>
          <VBtn type="reset" variant="tonal" color="secondary" class="mx-2" @click="onReset">
            Cancel
          </VBtn>
        </VCardText>
      </VForm>

      <!-- </VCardText> -->
    </VCard>
  </VDialog>
</template>
