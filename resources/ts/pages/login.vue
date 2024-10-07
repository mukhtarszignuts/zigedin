<script setup lang="ts">
import { VForm } from 'vuetify/components/VForm'

import { emailValidator, requiredValidator } from '@/@core/utils/validators'
import axios from '@/plugins/axios'
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png'
import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png'
import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png'
import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { useHead } from '@vueuse/head'
import CryptoJs from 'crypto-js'


import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'

interface LoginData {
  email: string
  password: string
  isRememberMe: boolean
  isPasswordVisible: boolean
}

const router = useRouter()
const isLoading = ref<boolean>(false)
const loginData = ref<LoginData>({
  email: '',
  password: '',
  isRememberMe: false,
  isPasswordVisible: false,
})


const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)

const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

const loginVForm = ref<VForm>()


// function for login
const login = async () => {
  const input = {
    email: loginData.value.email,
    password: loginData.value.password,
    isRememberMe:loginData.value.isRememberMe
  }
  loginVForm.value?.validate().then(async (res: any) => {
    if (res?.valid) {
      if (!loginData.value?.isRememberMe)
        localStorage.setItem('auth-data', '')
      
      isLoading.value = true

      axios.post('login', input).then(response => {
        const data = response.data.data
        
        if (data.user) {
          isLoading.value = false

          console.log(data.user.role)
          
          localStorage.setItem('auth-token', data.token)
          // store userData to the local storage
          const userData = CryptoJs.AES.encrypt(JSON.stringify(data.user), import.meta.env.VITE_CRYPTO_SECURE_KEY).toString()
        
          localStorage.setItem('user-data', userData)
          toast.success('login successfully..!')
           
           data.user.role !=='C' ? router.push({ path: 'admin/dashboard' }) :router.push({ path: '/' })
        }

       
       // Remember Me 
        if (loginData.value.isRememberMe) {
          const rememberMeData = {
            email: loginData.value.email,
            password: loginData.value.password,
            token: data.token,
            isRememberMe: loginData.value.isRememberMe,
          }

          const authData = CryptoJs.AES.encrypt(JSON.stringify(rememberMeData), import.meta.env.VITE_CRYPTO_SECURE_KEY).toString()

          localStorage.setItem('auth-data', authData)
        }
        else {
          if (localStorage.getItem('auth-data')) {
            if (data && data.isRememberMe === false)
              localStorage.removeItem('auth-data')
          }
        }
      }).catch(e => {
        console.log('error', e)
        toast.error(e.response.data.message)
      })
    }
  })

}


// change page title using this useHead
useHead({
  title: 'Vue-Laravel | Login',
})

// for check remember me functionality
onMounted(() => {
  const encryptedData = localStorage.getItem('auth-data')
  const data = encryptedData ? JSON.parse(CryptoJs.AES.decrypt(encryptedData || '', import.meta.env.VITE_CRYPTO_SECURE_KEY).toString(CryptoJs.enc.Utf8)) : null
  if (data && data.isRememberMe) {
    loginData.value.email = data.email
    loginData.value.password = data.password
    loginData.value.isRememberMe = data.isRememberMe
  }
})
</script>

<template>
  <div>
    <VRow no-gutters class="auth-wrapper bg-surface">
      <VCol lg="8" class="d-none d-lg-flex">
        <div class="position-relative bg-background rounded-lg w-100 ma-8 me-0">
          <div class="d-flex align-center justify-center w-100 h-100">
            <VImg max-width="505" :src="authThemeImg" class="auth-illustration mt-16 mb-2" />
          </div>

          <VImg :src="authThemeMask" class="auth-footer-mask" />
        </div>
      </VCol>

      <VCol cols="12" lg="4" class="auth-card-v2 d-flex align-center justify-center">
        <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
          <VCardText>
            <VNodeRenderer :nodes="themeConfig.app.logo" class="mb-6" />

            <h5 class="text-h5 mb-1">
              Welcome to <span class="text-capitalize"> {{ themeConfig.app.title }} </span>! 👋🏻
            </h5>

            <p class="mb-0">
              Please sign-in to your account and start the adventure
            </p>
          </VCardText>

          <!-- <VCardText>
            <VAlert color="primary" variant="tonal">
              <p class="text-caption mb-2">
                Admin Email: <strong>admin@demo.com</strong> / Pass: <strong>admin</strong>
              </p>
  
              <p class="text-caption mb-0">
                Client Email: <strong>client@demo.com</strong> / Pass: <strong>client</strong>
              </p>
            </VAlert>
          </VCardText> -->

          <VCardText>

            <VForm ref="loginVForm" @submit.prevent="login">
              <VRow>
                <!-- email -->
                <VCol cols="12">
                  <AppTextField v-model="loginData.email" label="Email" type="email" autofocus
                    :rules="[requiredValidator(loginData.email, 'Email'), emailValidator]" />
                </VCol>

                <!-- password -->
                <VCol cols="12">
                  <AppTextField v-model="loginData.password" label="Password"
                    :type="loginData.isPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="loginData.isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                    :rules="[requiredValidator(loginData.password, 'Password')]"
                    @click:append-inner="loginData.isPasswordVisible = !loginData.isPasswordVisible" />

                  <div class="d-flex align-center flex-wrap justify-space-between mt-2 mb-4">
                    <VCheckbox v-model="loginData.isRememberMe" label="Remember me" />

                    <RouterLink class="text-primary ms-2 mb-1" :to="{ name: 'forgot-password' }">
                      Forgot Password?
                    </RouterLink>
                  </div>

                  <VBtn block type="submit">
                    Login
                  </VBtn>
                </VCol>

                <!-- create account -->
                <VCol cols="12" class="text-center">
                  <span>New on our platform?</span>
                  <RouterLink class="text-primary ms-2" :to="{ name: 'registration' }">
                    Create an account
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
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
