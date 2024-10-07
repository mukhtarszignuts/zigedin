<script setup lang="ts">
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

import { emailValidator, requiredValidator } from '@/@core/utils/validators'
import axios from '@/plugins/axios'
import router from '@/router'
import authV2ForgotPasswordIllustrationDark from '@images/pages/auth-v2-forgot-password-illustration-dark.png'
import authV2ForgotPasswordIllustrationLight from '@images/pages/auth-v2-forgot-password-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { useHead } from '@vueuse/head'
import { toast } from 'vue3-toastify'


const email = ref('')
const isLoading = ref<boolean>(false)
const forgetVForm = ref<any>()
const authThemeImg = useGenerateImageVariant(authV2ForgotPasswordIllustrationLight, authV2ForgotPasswordIllustrationDark)

const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

function sendLink() {
  forgetVForm.value?.validate().then(({ valid }: any) => {
    if (valid) {
      isLoading.value = !!email.value

      const input = {
        email: email.value,
      }

      if (isLoading) {
         axios.post('forget-password', input).then(() => {
           toast.success('Mail Sent Successfully.')
            // router.push({
            //   name: 'reset-password',
            //   query: { email: email.value },
            // })
            forgetVForm.value.reset();
          isLoading.value = false
        }).catch(e => {
          console.error(e)
          isLoading.value = false
        })
      }
    }
  })
}

useHead({
  title:'Laravel-Vue | Forgot Password'
})
</script>

<template>
  <VRow class="auth-wrapper bg-surface" no-gutters>
    <VCol lg="8" class="d-none d-lg-flex">
      <div class="position-relative bg-background rounded-lg w-100 ma-8 me-0">
        <div class="d-flex align-center justify-center w-100 h-100">
          <VImg max-width="368" :src="authThemeImg" class="auth-illustration mt-16 mb-2" />
        </div>

        <VImg class="auth-footer-mask" :src="authThemeMask" />
      </div>
    </VCol>

    <VCol cols="12" lg="4" class="d-flex align-center justify-center">
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <VNodeRenderer :nodes="themeConfig.app.logo" class="mb-6" />
          <h5 class="text-h5 mb-1">
            Forgot Password? 🔒
          </h5>
          <p class="mb-0">
            Enter your email and we'll send you instructions to reset your password
          </p>
        </VCardText>

        <VCardText>
          <VForm ref="forgetVForm" @submit.prevent="sendLink">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField v-model="email" autofocus label="Email" type="email"
                  :rules="[requiredValidator(email, 'email'), emailValidator]" />
              </VCol>

              <!-- Reset link -->
              <VCol cols="12">
                <!-- <VBtn block type="submit">
                  Send Reset Link
                </VBtn> -->
                <VBtn
                  v-if="isLoading"
                  block
                  loading="white"
                  type="submit"
                />
                <VBtn
                  v-else
                  block
                  type="submit"
                >
                  Send Link
                </VBtn>
              </VCol>

              <!-- back to login -->
              <VCol cols="12">
                <RouterLink class="d-flex align-center justify-center" :to="{ name: 'login' }">
                  <VIcon icon="tabler-chevron-left" class="flip-in-rtl" />
                  <span>Back to login</span>
                </RouterLink>
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
