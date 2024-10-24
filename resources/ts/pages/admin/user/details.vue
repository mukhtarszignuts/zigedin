<script setup lang="ts">
import ChangePassword from '@/components/ChangePassword.vue';
import CompanyInfo from '@/components/user/CompanyInfo.vue';
import PresonalInfo from '@/components/user/PresonalInfo.vue';
import { useUserData } from '@/store/getUserData';
import { useHead } from '@vueuse/head';

// composable
const user = useUserData()
const { getUserDetails } = useUserData()
const { userData: loginUser } = storeToRefs(user)

console.log('loginUser',loginUser.value);

onMounted(async () => {
  await getUserDetails()
})

useHead({
  title:'Laravel-Vue | Admin Details'
})
</script>
<template>
    <div>
        <VRow>
            <VCol cols="4">
                <PresonalInfo :user-data="loginUser" />
            </VCol>
            <VCol cols="8">
                <ChangePassword />
                <CompanyInfo />
            </VCol>
        </VRow>
    </div>
</template>