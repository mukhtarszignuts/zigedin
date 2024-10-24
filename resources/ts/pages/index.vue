<script setup lang="ts">
import profile_image from '@images/avatars/avatar-1.png';
import { avatarText } from '@/@core/utils/formatters';
import headerBG from "@images/pages/user-profile-header-bg.png";
import { useHead } from "@vueuse/head";
import moment from "moment";
import About from "./profile/About.vue";
import UserExperience from "./profile/UserExperience.vue";
import Connection from "./profile/Connection.vue";
import UserConnection from "./profile/UserConnection.vue";
import UserEducation from "./profile/UserEducation.vue";
import UserSkills from "./profile/UserSkills.vue";
import { useUserData } from '@/store/getUserData';
import AddEditUserDialog from '@/components/dialogs/AddEditUserDialog.vue';
import { toast } from 'vue3-toastify';
import { AddEditUser } from '@/services/UserService';
import { connectionRequest } from '@/services/ConnectionService';
import AddEditSkillDialog from '@/components/dialogs/AddEditSkillDialog.vue';
import { AddEditSkill , deleteSkill } from '@/services/SkillService';



// tabs
const tabs = [
  { title: "Details", icon: "tabler-user-check", tab: "profile" },
  { title: "Connections", icon: "tabler-link", tab: "connections" },
  { title: "Job", icon: "tabler-briefcase", tab: "jobs" },
  { title: "Posts", icon: "tabler-list-details", tab: "posts" },
];

const activeTab = ref("profile");
const isAddNewUserDrawerVisible = ref<boolean>(false);
const isSkillDialogvisiable = ref<boolean>(false);
const isLoading = ref<boolean>(false);
const isEdit = ref<boolean>(false);
const isDelete = ref<boolean>(false);
const skillData = ref([]);

const user = useUserData()
const { getUserDetails } = useUserData()
const { userData:profileData } = storeToRefs(user)

const changetab = async (tab:string) =>{
  if(tab==='c'){
    activeTab.value='connections';
  }
}
// 👉 Add new user
const AddNewUser = async (userData: any) => {
  try {
    isLoading.value = true;
    const input = {
      first_name: userData.first_name,
      last_name: userData.last_name,
      phone: userData.phone,
      is_active: userData.is_active,
      role: userData.role,
      email: userData.email,
      city: userData.city,
      headline: userData.headline,
      profile_image: userData.profile_image,
      id: userData.isUpdate === true ? userData.id : null,
    };

    const formData = new FormData();

    // Append non-file data to FormData
    Object.keys(input).forEach((key) => {
      if (input[key] !== null && input[key] !== undefined) {
        formData.append(key, input[key]);
      }
    });

    const data = await AddEditUser(formData, userData.isUpdate);

    if (data) {
      toast.success(data.message);
      isAddNewUserDrawerVisible.value = false;
      await getUserDetails()
      isLoading.value = false;
    } else {
      isAddNewUserDrawerVisible.value = false;
      isLoading.value = false;
    }
  } catch (e) {
    toast.error(e.data.data.message);
    isLoading.value = false;
  }
};

// 👉 Add Edit Skill
const AddNewSkill = async (skillData:any)=>{
  try {
    isLoading.value = true;
    const data = await AddEditSkill(skillData,skillData.isUpdate)
    if(data){
      isSkillDialogvisiable.value = false
      isLoading.value = false;
      isEdit.value = false
    }
    await getUserDetails() 
    
  } catch (error) {
    console.log(error);
    isLoading.value = false;
    isEdit.value = false
  }
  
}

const handleRequest = async (item:any) =>{
  const data = await connectionRequest(item)
  await getUserDetails() 
  
}

const EditSkill = (skill:any) =>{
  if(skill!=true){
    skillData.value = skill
    isEdit.value = true
  }else{
    skillData.value = []
    isEdit.value = false
  }
  isSkillDialogvisiable.value = true
}

const DeleteSkill = async (id:number) =>{
  try {
    const data = await deleteSkill(id)
    console.log('delete id',id);
    isSkillDialogvisiable.value = false
    await getUserDetails() 
  } catch (error) {
    console.log(error);
    isSkillDialogvisiable.value = false
  }
  
}

const refresh = async ()=>{
  await getUserDetails()  
}

onMounted(async () => {
  await getUserDetails()  
});

useHead({
  title: "ZigedIn | Dashboard",
});
</script>
<template>
  <div>
    <VCard v-if="profileData" class="mb-5">
      <VImg :src="headerBG" min-height="125" max-height="250" cover />

      <VCardText class="d-flex align-bottom flex-sm-row flex-column justify-center gap-x-6">
        <div class="d-flex h-0">
          <VAvatar rounded size="120" class="user-profile-avatar mx-auto" :variant="'tonal'">
            <VImg v-if="profileData?.image_url" :src="profileData?.image_url" />
            <h1 v-else>{{ avatarText(profileData?.first_name) }}</h1>
          </VAvatar>
          <!-- <VAvatar size="100" :variant="!data?.user?.image_url ? 'tonal' : 'elevated'" :color="'warning'">
                <VImg v-if="data?.user?.image_url" :src="data?.user?.image_url" />
                <span v-else>{{ avatarText(data?.user?.first_name) }}</span>
              </VAvatar> -->
        </div>

        <div class="user-profile-info w-100 mt-16 pt-6 pt-sm-0 mt-sm-0">
          <h6 class="text-h6 text-center text-sm-start font-weight-medium mb-3">
            {{ profileData?.first_name }} {{ profileData?.last_name }}
          </h6>

          <div class="d-flex align-center justify-center justify-sm-space-between flex-wrap gap-4">
            <div class="d-flex flex-wrap justify-center justify-sm-start flex-grow-1 gap-2">
              <span class="d-flex">
                <VIcon size="20" icon="tabler-color-swatch" class="me-1" />
                <span class="text-body-1">
                  {{ profileData?.headline }}
                </span>
              </span>

              <span class="d-flex align-center">
                <VIcon size="20" icon="tabler-map-pin" class="me-2" />
                <span class="text-body-1">
                  {{ profileData?.city }}
                </span>
              </span>

              <span class="d-flex align-center">
                <VIcon size="20" icon="tabler-calendar" class="me-2" />
                <span class="text-body-1">
                  {{ moment(profileData?.created_at).format("YYYY-MM-DD") }}
                </span>
              </span>
            </div>

            <VBtn prepend-icon="tabler-check">
              Connected
            </VBtn>
          </div>
        </div>
      </VCardText>
    </VCard>


    <VTabs v-model="activeTab" class="v-tabs-pill">
      <VTab v-for="item in tabs" :key="item.icon" :value="item.tab">
        <VIcon size="20" start :icon="item.icon" />
        {{ item.title }}
      </VTab>
    </VTabs>

    <VWindow v-model="activeTab" class="mt-5 disable-tab-transition" :touch="false">
      <!-- Profile -->
      <VWindowItem value="profile">
        <VRow>
          <VCol md="4" cols="12">
            <About :user-data="profileData" @update:is-drawer-open="isAddNewUserDrawerVisible = true" />

            <UserConnection v-if="profileData?.invite_connections?.length"
              :connections-data="profileData?.invite_connections" @view-all="changetab"
              @handle-request="handleRequest" />

            <UserSkills :skills-data="profileData?.skills" @update:is-drawer-open="EditSkill" @skill-data="EditSkill"
              @delete-skill="DeleteSkill" />

            <UserEducation :educations-data="profileData?.educations" @refresh="refresh" />
          </VCol>
          <VCol md="8" cols="12">
            <UserExperience :experience-data="profileData?.experiences" />
          </VCol>
        </VRow>
      </VWindowItem>

      <!-- Connection -->
      <VWindowItem value="connections">
        <Connection :connections-data="profileData?.connections" />
      </VWindowItem>
    </VWindow>
    <!-- profile Dialog -->
    <AddEditUserDialog v-if="isAddNewUserDrawerVisible" :is-drawer-open="isAddNewUserDrawerVisible"
      @close-dialog="isAddNewUserDrawerVisible = false" @user-data="AddNewUser" :user-data="profileData" />

    <!-- Skill Dialog -->
    <AddEditSkillDialog v-if="isSkillDialogvisiable" :is-drawer-open="isSkillDialogvisiable" :is-edit="isEdit"
      :is-loading="isLoading" @close-dialog="isSkillDialogvisiable=false" @skill-data="AddNewSkill"
      :skill-data="skillData" />

  </div>
</template>

<style lang="scss">
.user-profile-avatar {
  border: 5px solid rgb(var(--v-theme-surface));
  background-color: rgb(var(--v-theme-surface)) !important;
  inset-block-start: -3rem;

  .v-img__img {
    border-radius: 0.125rem;
  }
}
</style>