<script setup lang="ts">
import axios from '@/plugins/axios';
import avatar1 from '@images/avatars/avatar-1.png';
import CryptoJs from 'crypto-js';
import { useRouter } from 'vue-router';

const router = useRouter()
const name = ref<string>('')
const role = ref<string>('')
const ProfileImage = ref<string>('')

const logout = ()=>{
  // const token = localStorage.getItem('auth-token')
  // console.log(token);

   axios.post('logout').then(() => {
    // removes auth-token
    localStorage.removeItem('auth-token')

    // removes userData
    localStorage.removeItem('user-data')

    
    // if (['Customer','C'].includes(role.value))
      router.push({ name: 'login' })

    // else
      // router.push({ name: 'admin-login' })

  }).catch(e => {
    console.log('error', e)
  })
}

// get userdata from local
onMounted(() => {
  const encryptedData = localStorage.getItem('user-data')
  const userData = encryptedData ? JSON.parse(CryptoJs.AES.decrypt(encryptedData || '', import.meta.env.VITE_CRYPTO_SECURE_KEY).toString(CryptoJs.enc.Utf8)) : null
  if (userData) {
    name.value = userData.name
    role.value = userData.role === 'A' ? 'Admin' : (userData.role === 'M' ? 'Manager' : 'Customer')
    ProfileImage.value = userData.profile_image
    // console.log('userData',userData);
  }
})

</script>

<template>
  <VBadge dot location="bottom right" offset-x="3" offset-y="3" bordered color="success">
    <VAvatar class="cursor-pointer" color="primary" variant="tonal">
      <VImg :src="avatar1" />

      <!-- SECTION Menu -->
      <VMenu activator="parent" width="230" location="bottom end" offset="14px">
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge dot location="bottom right" offset-x="3" offset-y="3" color="success">
                  <VAvatar color="primary" variant="tonal">
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ name }}
            </VListItemTitle>
            <VListItemSubtitle>{{ role }}</VListItemSubtitle>
          </VListItem>

          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          
          <VListItem
            @click="router.push(['Customer'].includes(role) ? '/user/details' : '/admin/user/details')">
            <template #prepend>
              <VIcon class="me-2" icon="tabler-user" size="22" />
            </template>

            <VListItemTitle>Profile</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout">
            <template #prepend>
              <VIcon class="me-2" icon="tabler-logout" size="22" />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
