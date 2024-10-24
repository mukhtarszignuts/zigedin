<script lang="ts" setup>
import {
  emailValidator,
  lengthValidator,
  requiredValidator,
} from "@/@core/utils/validators";
import avatar1 from "@images/avatars/avatar-1.png";
import { VForm } from "vuetify/components/VForm";
// interface
interface UserData {
  id?: number;
  first_name?: string;
  last_name?: string;
  email?: string;
  phone?: string;
  is_active?: number;
  role?: string;
  profile_image?: string;
}

interface EmployerData{
  industry?:string;
  website?:string;
  logo?:string;
  location?:string;
}

// Interface
interface Emit {
  (e: "update:isDrawerOpen", value: boolean): void;
  (e: "userData", value: any): void;
  (e: "closeDialog", value: boolean): void;
}
interface Props {
  isDrawerOpen?: boolean;
  roles?: object[];
  userData?: UserData | any;
  employerData?: EmployerData | any;
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
const first_name = ref<string>("");
const last_name = ref<string>("");
const email = ref<string>("");
const phone = ref<string>("");
const role = ref<string>("");
const is_active = ref<number>();
const city = ref<string>("");
const headline = ref<string>("");
const summary = ref<string>("");
// employer 
const location = ref<string>("");
const website = ref<string>("");
const industry = ref<string>("");

const title = ref<string>(" User Profile");

const profile_image = ref<any>("");
const preview = ref<string>(avatar1);


// 👉 drawer close
const closeNavigationDrawer = () => {
  nextTick(() => {
    refForm.value?.reset();
    refForm.value?.resetValidation();
  });
  emit("closeDialog", false);
};
const refInputEl = ref<HTMLElement>();

const changeAvatar = (event: Event) => {
  const fileInput = event.target as HTMLInputElement;
  const files = fileInput.files;

  if (files && files.length > 0) {
    const fileReader = new FileReader();

    fileReader.onload = () => {
      if (typeof fileReader.result === "string") {
        preview.value = fileReader.result;
        profile_image.value = files[0];
      }
    };

    fileReader.readAsDataURL(files[0]);
  }
};


// reset avatar image
const resetAvatar = () => {
  preview.value = avatar1;
};

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const emitObj = {
        id: id?.value,
        first_name: first_name?.value,
        last_name: last_name?.value,
        role: role?.value,
        is_active: is_active?.value,
        phone: phone?.value,
        city: city?.value,
        email: email?.value,
        headline: headline?.value,
        summary: summary?.value,
        profile_image: profile_image?.value,
        isUpdate: props?.userData?.id ? true : null,
      };

      console.log("userData", emitObj);

      emit("userData", emitObj);
      refForm.value?.reset();
    }
  });
};

watchEffect(() => {
  if (props?.userData?.id) {
    id.value = props?.userData?.id;
    first_name.value = props?.userData?.first_name;
    last_name.value = props?.userData?.last_name;
    email.value = props?.userData?.email;
    phone.value = props?.userData?.phone;
    role.value = props?.userData?.role;
    is_active.value = props?.userData?.is_active;
    headline.value = props?.userData?.headline;
    summary.value = props?.userData?.summary;

    preview.value = props?.userData?.image_url;
  }
});

watch(
  async () => props?.isEdit,
  async (val) => {
    title.value = props?.isEdit ? " Edit " : " Add ";
  }
);


onMounted(() => {
  title.value = props?.isEdit ? " Edit " : " Add ";
  
})
</script>

<template>
  <VDialog v-model="props.isDrawerOpen" max-width="600" @update:is-drawer-open="emit('closeDialog', false)">
    <!-- Dialog Content -->
    <VCard :title="title + ' User Profile'">
      <VCardText class="d-flex">
        <!-- 👉 Avatar -->
        <VAvatar rounded size="100" class="me-6" :image="preview" />

        <!-- 👉 Upload Photo -->
        <div class="d-flex flex-column justify-center gap-4">
          <div class="d-flex flex-wrap gap-2">
            <VBtn color="primary" @click="refInputEl?.click()">
              <VIcon icon="tabler-cloud-upload" class="d-sm-none" />
              <span class="d-none d-sm-block">Upload new photo</span>
            </VBtn>

            <input ref="refInputEl" type="file" name="file" accept=".jpeg,.png,.jpg,GIF" hidden @input="changeAvatar" />

            <VBtn type="reset" color="secondary" variant="tonal" @click="resetAvatar">
              <span class="d-none d-sm-block">Reset</span>
              <VIcon icon="tabler-refresh" class="d-sm-none" />
            </VBtn>
          </div>

          <p class="text-body-1 mb-0">
            Allowed JPG, GIF or PNG. Max size of 2MB
          </p>
        </div>
      </VCardText>
      <VDivider />

      <VForm ref="refForm" v-model="isFormValid" @submit.prevent="onSubmit">
        <VCardText>
          <VRow>
            <VCol cols="6">
              <AppTextField v-model="first_name" label="First Name"
                :rules="[requiredValidator(first_name, 'First Name')]" />
            </VCol>
            <VCol cols="6">
              <AppTextField v-model="last_name" label="Last Name"
                :rules="[requiredValidator(last_name, 'Last Name')]" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="email" type="email" label="Email" :rules="[
                  requiredValidator(email, 'Email'),
                  emailValidator,
                  lengthValidator(email, 0, 128, 'Email'),
                ]" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="phone" label="Phone" :rules="[requiredValidator(phone, 'Phone')]" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="city" label="City (Optional)" type="text" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="headline" label="Headline" type="text" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="summary" label="Summary" type="text" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="industry" label="Industry" type="text" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="location" label="Location (Optional)" type="text" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="website" label="website (Optional)" type="text" />
            </VCol>

            <VCol cols="6">
              <AppSelect v-model="role" label="Select Role" :items="props.roles" item-title="name" item-value="id"
                placeholder="Select Role" clearable :rules="[requiredValidator(role, 'role')]" />
            </VCol>

          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end flex-wrap gap-2">
          <!-- <VBtn variant=" tonal" color="secondary" @click="emit('closeDialog', false)">
                    Close
                    </VBtn>
                    <VBtn @click="emit('closeDialog', false)">
                        Save
                    </VBtn> -->

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
