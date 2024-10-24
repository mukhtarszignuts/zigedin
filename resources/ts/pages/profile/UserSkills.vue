<script lang="ts" setup>

import AddEditSkillDialog from '@/components/dialogs/AddEditSkillDialog.vue';
import { AddEditSkill , deleteSkill } from '@/services/SkillService';
import { toast } from 'vue3-toastify';

interface ProfileSkills{
  id?:number
  name: string
  avatar: string
}

interface Props {
  skillsData: ProfileSkills[]
}

// Interface
interface Emit {
  // (e: "update:isDrawerOpen", value: boolean): void;
  // (e: "skillData", value: any): void;
  (e: "refresh", value: boolean): void;
 
}

const props = defineProps<Props>()
const emit = defineEmits<Emit>()

const isSkillDialogvisiable = ref<boolean>(false);
const isLoading = ref<boolean>(false);
const isEdit = ref<boolean>(false);
const isDelete = ref<boolean>(false);
const skillData = ref([]);

const DeleteSkill = async (id:number) =>{
  try {
    const data = await deleteSkill(id)
    toast.success(data.data.message);
    isSkillDialogvisiable.value = false 
    emit('refresh',true)
  } catch (error) {
    console.log(error);
    emit('refresh',true)
    isSkillDialogvisiable.value = false
  }
  
}
// 👉 Add Edit Skill
const AddNewSkill = async (skillData:any)=>{
  try {
    isLoading.value = true;
    const data = await AddEditSkill(skillData,skillData.isUpdate)
    if(data){
      toast.success(data.data.message);
      isSkillDialogvisiable.value = false
      isLoading.value = false;
      isEdit.value = false
      emit('refresh',true)
    }
    
  } catch (error) {
    console.log(error);
    isLoading.value = false;
    isEdit.value = false
    emit('refresh',true)
  }
  
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

</script>

<template>
  <div>
    <VCard title="Skills" class="mb-4">
      <template #append>
        <div class="me-n2">
          <VBtn icon size="30" class="rounded" :variant="'tonal'" :color="'primary'"
            @click="isSkillDialogvisiable = true">
            <VIcon size="20" :icon="'tabler-plus'" />
          </VBtn>
          <!-- <VBtn icon size="30" :variant="'undefinded'" :color="'secondary'" @click="emit('update:isDrawerOpen',true)">
            <VIcon size="30" :icon="'tabler-edit'" />
          </VBtn> -->
        </div>
      </template>

      <VCardText>
        <VList class="card-list">
          <VListItem v-for="data in props.skillsData" :key="data.name">
            <!-- <template #prepend>
              <VAvatar size="38" :image="data.avatar" />
            </template> -->

            <VListItemTitle class="font-weight-medium">
              {{ data.name }}
            </VListItemTitle>
            <VListItemSubtitle>Experiences</VListItemSubtitle>

            <template #append>
              <VBtn icon size="30" class="rounded me-2" :variant="'tonal'" :color="'error'"
                @click="DeleteSkill(data.id)">
                <VIcon size="20" :icon="'tabler-trash'" />
              </VBtn>
              <VBtn icon size="30" class="rounded" :variant="'tonal'">
                <VIcon size="20" :icon="'tabler-edit'" @click="EditSkill(data)" />
              </VBtn>
            </template>
          </VListItem>

          <VListItem>
            <VListItemTitle>
              <VBtn block variant="text">
                View all Skills
              </VBtn>
            </VListItemTitle>
          </VListItem>
        </VList>
      </VCardText>
    </VCard>
    <!-- Skill Dialog -->
    <AddEditSkillDialog v-if="isSkillDialogvisiable" :is-drawer-open="isSkillDialogvisiable" :is-edit="isEdit"
      :is-loading="isLoading" @close-dialog="isSkillDialogvisiable=false" @skill-data="AddNewSkill"
      :skill-data="skillData" />
  </div>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 14px;
}
</style>
