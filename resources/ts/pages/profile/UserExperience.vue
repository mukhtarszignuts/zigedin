<script lang="ts" setup>
import avatar2 from '@images/avatars/avatar-2.png'
import building from '@images/building.png';
import moment from 'moment';
import useFormatting from "@/composable/useFormatting";
import AddEditExperienceDialog from '@/components/dialogs/AddEditExperienceDialog.vue'
import { toast } from 'vue3-toastify';
import { AddEditExperience , deleteExperience } from '@/services/ExperienceService';



interface ProfileExperience{
  id?:number, 
  title:string, 
  start_date:string, 
  end_date?:string, 
  company_name:string, 
  description:string, 
  employment_type:string, 
  location:string, 
  location_type:string, 
}

interface Props {
  experienceData:ProfileExperience | any
}

interface Emit{
  (e: "refresh", value: boolean): void;
}

// props
const props = defineProps<Props>();

// Emit
const emit = defineEmits<Emit>();

const { empOptionsVariant,formatDateRange } = useFormatting();

const isExperienceDialogVisible = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const isEdit = ref<boolean>(false)
const expData = ref<ProfileExperience[]>([])

const AddEditExp = async (experience:any) => {
  try {
    
    const data = await AddEditExperience(experience,experience.isUpdate);
    if(data){
      if(experience.isUpdate){
        // Toast message 
        toast.success('Experience Updated successfully..!')
        isEdit.value = false
        expData.value = []
        
      }else{
        // Toast message 
        toast.success('Experience added successfully..!')

      }
      isExperienceDialogVisible.value = false
      isLoading.value = false

      // calling emit
      emit('refresh',true)
    }
    
  } catch (error) {
    console.log(error);
    isExperienceDialogVisible.value = false
    isLoading.value = false
  }
}

const deleteExp = async (Id:any) => {
  try {
    const data = await deleteExperience(Id)
    if(data){
      toast.success(data?.data?.message)
      emit('refresh',true)
    }
  } catch (error) {
    console.log(error);
  }
}

const EditExp = (item:any) =>{
  if(item){
    expData.value = item
    isEdit.value = true
    isExperienceDialogVisible.value = true
    console.log(item,'edit exp clickable');
  }
}

</script>

<template>
  <VCard>
    <VCardItem>
      <template #prepend>
        <VIcon icon="tabler-timeline" />
      </template>

      <VCardTitle>Experiences </VCardTitle>

      <template #append>
        <div class="">
          <VBtn icon size="30" class="rounded" :variant="'tonal'" @click="isExperienceDialogVisible=true">
            <VIcon size="20" :icon="'tabler-plus'" />
          </VBtn>
        </div>
      </template>
    </VCardItem>

    <VCardText>
      <!-- {{ props?.experienceData }} -->
      <VList class="card-list">
        <VListItem v-if="props.experienceData" v-for="data,index in Object.keys (props.experienceData)">
          <!-- 
          <template #prepend>
            <VAvatar size="38" :variant="'tonal'" :color="'secondary'">
              <VImg :src="building" />
            </VAvatar>
          </template> -->

          <VListItemTitle class="font-weight-medium">
            <VAvatar size="38" :variant="'tonal'" :color="'secondary'">
              <VImg :src="building" />
            </VAvatar>
            <!-- for company name -->
            {{ data }}
          </VListItemTitle>

          <VTimeline density="compact" align="start" truncate-line="both" class="v-timeline-density-compact">
            <VTimelineItem v-for="item in props.experienceData[data]" dot-color="primary" size="x-small">
              <div class="d-flex justify-space-between align-center flex-wrap">
                <span class="app-timeline-title mb-1">
                  {{ item.title }}
                </span>
                <span class="app-timeline-meta">
                  <VBtn icon size="30" class="rounded me-2" :variant="'tonal' " :color="'error'"
                    @click="deleteExp(item.id)">
                    <VIcon size="20" :icon="'tabler-trash'" />
                  </VBtn>
                  <VBtn icon size="30" class="rounded" :variant="'tonal'" @click="EditExp(item)">
                    <VIcon size="20" :icon="'tabler-edit'" />
                  </VBtn>

                </span>
              </div>
              <p class="app-timeline-text mb-1">
                {{ formatDateRange({ start_date: item.start_date, end_date: item.end_date }) }}
              </p>
              <p class="app-timeline-text mb-1">
                {{ empOptionsVariant(item.employment_type).text }}
              </p>
              <p class="app-timeline-text mb-1">
                {{ item.location }}
              </p>

              <!-- <div class="d-flex align-center mt-3">
                <VAvatar size="38" class="me-3" :image="avatar2" />
                <div>
                  <h6 class="text-sm font-weight-medium mb-n1">
                    Lester McCarthy (Client)
                  </h6>
                  <span class="app-timeline-meta">
                    CEO of Infidel
                  </span>
                </div>
              </div> -->
            </VTimelineItem>
          </VTimeline>
          <!-- <VListItemSubtitle>{{ data.degree }} , {{ data.field_of_study}}</VListItemSubtitle>
          <VListItemSubtitle>{{ moment(data.start_date).format('YYYY-MM') }} to {{
            moment(data.end_date).format('YYYY-MM') }}
          </VListItemSubtitle> -->

          <!-- <template #append>
            <VBtn icon size="30" class="rounded me-2" :variant="'tonal' " :color="'error'" @click="true">
              <VIcon size="20" :icon="'tabler-trash'" />
            </VBtn>
            <VBtn icon size="30" class="rounded" :variant="'tonal'" @click="true">
              <VIcon size="20" :icon="'tabler-edit'" />
            </VBtn>
          </template> -->
          <v-divider />
        </VListItem>
      </VList>

      <!-- <VTimeline density="compact" align="start" truncate-line="both" class="v-timeline-density-compact">
        <VTimelineItem dot-color="warning" size="x-small">
          <div class="d-flex justify-space-between align-center flex-wrap">
            <span class="app-timeline-title">
              Client Meeting
            </span>
            <span class="app-timeline-meta">Today</span>
          </div>
          <p class="app-timeline-text mb-2">
            Project meeting with john @10:15am
          </p>

          <div class="d-flex align-center mt-3">
            <VAvatar size="38" class="me-3" :image="avatar2" />
            <div>
              <h6 class="text-sm font-weight-medium mb-n1">
                Lester McCarthy (Client)
              </h6>
              <span class="app-timeline-meta">
                CEO of Infidel
              </span>
            </div>
          </div>
        </VTimelineItem>

        <VTimelineItem dot-color="primary" size="x-small">
          <div class="d-flex justify-space-between align-center flex-wrap">
            <span class="app-timeline-title">
              Create a new project for client 😎
            </span>
            <span class="app-timeline-meta">2 Day Ago</span>
          </div>

          <p class="app-timeline-text mb-1">
            Add files to new design folder
          </p>
        </VTimelineItem>

        <VTimelineItem dot-color="info" size="x-small">
          <div class="d-flex justify-space-between align-center flex-wrap">
            <span class="app-timeline-title">
              Shared 2 New Project Files
            </span>
            <span class="app-timeline-meta">6 Day Ago</span>
          </div>
          <p class="app-timeline-text mb-0">
            Sent by Mollie Dixon
          </p>
          <div class="d-flex align-center mt-3">
            <VIcon color="warning" icon="tabler-file-text" size="20" class="me-2" />
            <h6 class="font-weight-medium text-xs me-3">
              App Guidelines
            </h6>

            <VIcon color="success" icon="tabler-table" size="20" class="me-2" />
            <h6 class="font-weight-medium text-xs">
              Testing Results
            </h6>
          </div>
        </VTimelineItem>

        <VTimelineItem dot-color="secondary" size="x-small">
          <div class="d-flex justify-space-between align-center flex-wrap">
            <span class="app-timeline-title">
              Project status updated
            </span>
            <span class="app-timeline-meta">10 Day Ago</span>
          </div>
          <p class="app-timeline-text mb-1">
            WooCommerce iOS App Completed
          </p>
        </VTimelineItem>
      </VTimeline> -->
    </VCardText>
  </VCard>

  <!--Add Dialog Experience -->
  <AddEditExperienceDialog v-if="isExperienceDialogVisible" :is-loading="isLoading"
    :is-drawer-open="isExperienceDialogVisible" :is-edit="isEdit" :experience-data="expData"
    @close-dialog="isExperienceDialogVisible=false" @experience-data="AddEditExp" />

</template>
