<script lang="ts" setup>
import moment from 'moment'
import { avatarText } from '@/@core/utils/formatters'
import AddEditEducationDialog from '@/components/dialogs/AddEditEducationDialog.vue'
import { AddEditEducation , deleteEducation } from '@/services/EducationService'
import { toast } from 'vue3-toastify'


interface ProfileEducations{
  id?: number
  school_name: string
  image_url: string
  start_date: string
  end_date: string
  degree: string
  field_of_study: string
  description: string
}

interface Props {
  educationsData: ProfileEducations[]
}

interface Emit{
  (e: "refresh", value: boolean): void;
}

// Props
const props = defineProps<Props>()
// Emit
const emit = defineEmits<Emit>();

const isEducationDialogVisible = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const isEdit = ref<boolean>(false)
const eduData = ref<ProfileEducations[]>([])

const AddEditEdu = async (education:any) => {
  try {
    
    const data = await AddEditEducation(education,education.isUpdate);
    if(data){
      if(education.isUpdate){
        // Toast message 
        toast.success('Education Updated successfully..!')
        isEdit.value = false
        eduData.value = []
        
      }else{
        // Toast message 
        toast.success('Education added successfully..!')

      }
      isEducationDialogVisible.value = false
      isLoading.value = false

      // calling emit
      emit('refresh',true)
    }
    
  } catch (error) {
    console.log(error);
    isEducationDialogVisible.value = false
    isLoading.value = false
  }
}

const deleteEdu = async (Id:any) => {
  try {
    const data = await deleteEducation(Id)
    if(data){
      toast.success(data?.data?.message)
      console.log('delete edu response',data);
      emit('refresh',true)
    }
  } catch (error) {
    console.log(error);
  }
}

const EditEdu = (item:any) =>{
  if(item){
    eduData.value = item
    isEdit.value = true
    isEducationDialogVisible.value = true
    console.log(item,'edit edu clickable');
  }
}

const closeDialog = () =>{
  isEdit.value = false
  isEducationDialogVisible.value=false
  eduData.value = []
}

</script>

<template>
  <div>
    <VCard title="Educations" class="mb-4">
      <template #append>
        <div class="me-n2">
          <VBtn icon size="30" class="rounded" :variant="'tonal'" @click="isEducationDialogVisible=true">
            <VIcon size="20" :icon="'tabler-plus'" />
          </VBtn>
        </div>
      </template>

      <VCardText>
        <VList class="card-list">
          <VListItem v-for="data in props.educationsData" :key="data.id">
            <template #prepend>
              <VAvatar size="38" :variant="!data?.image_url ? 'tonal' : undefined" :color="'secondary'">
                <VImg v-if="data?.image_url" :src="data?.image_url" />
                <span v-else>{{ avatarText(data?.school_name) }}</span>
              </VAvatar>
            </template>

            <VListItemTitle class="font-weight-medium">
              {{ data.school_name }}
            </VListItemTitle>
            <VListItemSubtitle>{{ data.degree }} , {{ data.field_of_study}}</VListItemSubtitle>
            <VListItemSubtitle>{{ moment(data.start_date).format('YYYY-MM') }} to {{
              moment(data.end_date).format('YYYY-MM') }}
            </VListItemSubtitle>

            <template #append>
              <VBtn icon size="30" class="rounded me-2" :variant="'tonal' " :color="'error'"
                @click="deleteEdu(data.id)">
                <VIcon size="20" :icon="'tabler-trash'" />
              </VBtn>
              <VBtn icon size="30" class="rounded" :variant="'tonal'" @click="EditEdu(data)">
                <VIcon size="20" :icon="'tabler-edit'" />
              </VBtn>
            </template>
          </VListItem>

          <VListItem>
            <VListItemTitle>
              <VBtn block variant="text">
                View all Educations
              </VBtn>
            </VListItemTitle>
          </VListItem>
        </VList>
      </VCardText>
    </VCard>
    <!-- Education Dialog box -->
    <AddEditEducationDialog v-if="isEducationDialogVisible" :is-drawer-open="isEducationDialogVisible"
      :is-edit="isEdit" :education-data="eduData"
      :is-loading="isLoading" @education-data="AddEditEdu" @close-dialog="closeDialog" />
  </div>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 14px;
}
</style>
