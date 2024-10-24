<script setup lang="ts">
import PresonalInfo from '@/components/user/PresonalInfo.vue';
import axios from '@/plugins/axios';
import { useRoute } from 'vue-router';
import { useHead } from '@vueuse/head';

// composable
const route = useRoute()

// Data
const userData = ref<any>()

const getUserDetails = async() => {
    try {
    const { data: { data } } = await axios.get(`/user/${route.params.id}`)
    
    if (data)
      userData.value = data.user
  }
  catch (e) {
    console.log(e)
  }
}
onMounted(async () => {
  await getUserDetails()
})
useHead({
  title:'Laravel-Vue | User Details'
})
</script>
<template>
    <div>
        <VRow>
            <VCol cols="12" md="5" lg="4" sm="1">
                <PresonalInfo :user-data="userData" />
            </VCol>
        </VRow>

    </div>
</template>