<script setup lang="ts">
import { VForm } from 'vuetify/components/VForm'
// import type { RegisterResponse } from '@/@fake-db/types'
import authV2RegisterIllustrationBorderedDark from '@images/pages/auth-v2-register-illustration-bordered-dark.png'
import authV2RegisterIllustrationBorderedLight from '@images/pages/auth-v2-register-illustration-bordered-light.png'
import authV2RegisterIllustrationDark from '@images/pages/auth-v2-register-illustration-dark.png'
import authV2RegisterIllustrationLight from '@images/pages/auth-v2-register-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
// import { useAppAbility } from '@/plugins/casl/useAppAbility'
import axios from '@/plugins/axios'
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { alphaDashValidator, confirmedValidator, emailValidator, passwordValidator, requiredValidator } from '@validators'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import { useHead } from '@vueuse/head'


interface RegisterData {
  name: string
  city: string
  phone?: number | string | any
  email: string
  password: string
  confirmPassword: string
}

const registerData = ref<RegisterData>({
  name: '',
  city: '',
  phone: '',
  email: '',
  password: '',
  confirmPassword: ''
})

// Composable
const router = useRouter()

const refVForm = ref<VForm>()
const isLoading = ref<boolean>(false)
const privacyPolicies = ref<boolean>(false)

const register = async () => {
  try {
        isLoading.value = true
        
        const input = {
          name: registerData.value.name,
          phone: registerData.value.phone,
          city: registerData.value.city,
          email: registerData.value.email,
          password: registerData.value.password,
          confirm_password: registerData.value.confirmPassword,
        }

        const { data } = await axios.post('registration', input)
        if (data) {
          router.push({ 
            name: 'login'
          })
          isLoading.value = false
        }
      }
      catch (e) {
        isLoading.value = false
        console.log('error', e)
      }
}

const imageVariant = useGenerateImageVariant(
  authV2RegisterIllustrationLight,
  authV2RegisterIllustrationDark, authV2RegisterIllustrationBorderedLight,
  authV2RegisterIllustrationBorderedDark,
  true,
)

const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const onSubmit = () => {
  refVForm.value?.validate()
    .then(({ valid: isValid }) => {
      if (isValid)
        register()
    })
}

useHead({
  title:'Laravel-Vue | Registration'
})

</script>

<template>
  <VRow no-gutters class="auth-wrapper bg-surface">
    <VCol lg="8" class="d-none d-lg-flex">
      <div class="position-relative bg-background rounded-lg w-100 ma-8 me-0">
        <div class="d-flex align-center justify-center w-100 h-100">
          <VImg max-width="441" :src="imageVariant" class="auth-illustration mt-16 mb-2" />
        </div>

        <VImg class="auth-footer-mask" :src="authThemeMask" />
      </div>
    </VCol>

    <VCol cols="12" lg="4" class="d-flex align-center justify-center">
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <VNodeRenderer :nodes="themeConfig.app.logo" class="mb-6" />
          <h5 class="text-h5 mb-1">
            Adventure starts here 🚀
          </h5>
          <p class="mb-0">
            Make your app management easy and fun!
          </p>
        </VCardText>

        <VCardText>
          <VForm ref="refVForm" @submit.prevent="onSubmit">
            <VRow>
              <!-- Username -->
              <VCol cols="12">
                <AppTextField v-model="registerData.name" autofocus
                  :rules="[requiredValidator(registerData.name,'Username'), alphaDashValidator]" label="Username" />
              </VCol>

              <!-- email -->
              <VCol cols="12">
                <AppTextField v-model="registerData.email"
                  :rules="[requiredValidator(registerData.email,'Email'), emailValidator]" label="Email" type="email" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="registerData.phone" :rules="[requiredValidator(registerData.phone,'Phone')]"
                  label="Phone" type="text" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="registerData.city" :rules="[requiredValidator(registerData.city,'City')]"
                  label="City" type="text" />
              </VCol>

              <VCol cols="12">
                <AppTextField v-model="registerData.password"
                  :rules="[requiredValidator(registerData.password,'Password'),passwordValidator(registerData.password)]"
                  label="Password" :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible" />

              </VCol>
              <VCol cols="12">
                <AppTextField v-model="registerData.confirmPassword"
                  :rules="[requiredValidator(registerData.confirmPassword,'Confirm Password'),confirmedValidator(registerData.password,registerData.confirmPassword)]"
                  label="Confirm Password" :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible" />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <div class="d-flex align-center mt-2 mb-4">
                  <VCheckbox id="privacy-policy" v-model="privacyPolicies" inline>
                    <template #label>
                      <span class="me-1">
                        I agree to
                        <a href="javascript:void(0)" class="text-primary">privacy policy & terms</a>
                      </span>
                    </template>
                  </VCheckbox>
                </div>

                <VBtn block type="submit">
                  Sign up
                </VBtn>
              </VCol>

              <!-- create account -->
              <VCol cols="12" class="text-center text-base">
                <span>Already have an account?</span>
                <RouterLink class="text-primary ms-2" :to="{ name: 'login' }">
                  Sign in instead
                </RouterLink>
              </VCol>

              <VCol cols="12" class="d-flex align-center">
                <VDivider />
                <span class="mx-4">or</span>
                <VDivider />
              </VCol>

              <!-- auth providers -->
              <VCol cols="12" class="text-center">
                <AuthProvider />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
  action: read
  subject: Auth
  redirectIfLoggedIn: true
</route>
