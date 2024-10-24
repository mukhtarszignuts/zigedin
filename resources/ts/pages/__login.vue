<script setup lang="ts">
import headerBG from "@images/pages/user-profile-header-bg.png";
import useUserData from "@/composable/useFetchUserData";
import { useHead } from "@vueuse/head";
import avatar1 from '@images/avatars/avatar-1.png';
// resources/images/avatars/avatar-1.png

// import moment from "moment";
// import axios from "@/plugins/axios";


const isLoading = ref<boolean>(false);

const role = ref<string>("");

const { fetchUserData } = useUserData();

onMounted(() => {
  const userData = fetchUserData();
  if (userData) {
    console.log(userData);
    
  } 
});

useHead({
  title: "zigedin | Dashboard",
});
</script>
<template>
<div>
      <VCard  class="mb-5">
      <VImg :src="headerBG" min-height="125" max-height="250" cover />

      <VCardText
        class="d-flex align-bottom flex-sm-row flex-column justify-center gap-x-5"
      >
        <div class="d-flex h-0">
          <VAvatar
            rounded
            size="120"
            :src="avatar1"
            class="user-profile-avatar mx-auto"
          />
        </div>

        <div class="user-profile-info w-100 mt-16 pt-6 pt-sm-0 mt-sm-0">
          <h6 class="text-h6 text-center text-sm-start font-weight-medium mb-3">
            hghg
          </h6>

          <div
            class="d-flex align-center justify-center justify-sm-space-between flex-wrap gap-4"
          >
            <div
              class="d-flex flex-wrap justify-center justify-sm-start flex-grow-1 gap-2"
            >
              <span class="d-flex">
                <VIcon size="20" icon="tabler-user" class="me-1" />
                <span class="text-body-1">
                  
                </span>
              </span>
              <span class="d-flex">
                <VIcon size="20" icon="tabler-users-group" class="me-1" />
                <span class="text-body-1">
                 
                </span>
              </span>

              <span class="d-flex align-center">
                <VIcon size="20" icon="tabler-star" class="me-2" />
                <span class="text-body-1">
                  <VChip
                    label
                    size="small"
                    class="text-capitalize"
                  >
                    
                  </VChip>
                </span>
              </span>

              <span class="d-flex align-center">
                <VIcon size="20" icon="tabler-mail" class="me-2" />
                <span class="text-body-1">
                 
                </span>
              </span>
              <span class="d-flex align-center">
                <VIcon size="20" icon="tabler-phone" class="me-2" />
                <span class="text-body-1">
               
                </span>
              </span>

              <span class="d-flex align-center">
                <VIcon size="20" icon="tabler-calendar" class="me-2" />
                <span class="text-body-1">
                 
                </span>
              </span>
            </div>

            <VBtn prepend-icon="tabler-edit" @click=""> Edit </VBtn>
          </div>
        </div>
      </VCardText>
    </VCard>
    </div>
</template>
