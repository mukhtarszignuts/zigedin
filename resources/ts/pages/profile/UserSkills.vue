<script lang="ts" setup>

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
  (e: "update:isDrawerOpen", value: boolean): void;
  (e: "skillData", value: any): void;
  (e: "deleteSkill", value: any): void;
  (e: "closeDialog", value: boolean): void;
}

const props = defineProps<Props>()
const emit = defineEmits<Emit>()

const skillEdit = (item:any) =>{
  emit('skillData',item)
} 
</script>

<template>
  <VCard title="Skills" class="mb-4">
    <template #append>
      <div class="me-n2">
        <VBtn icon size="30" class="rounded" :variant="'tonal'" :color="'primary'"
          @click="emit('update:isDrawerOpen',true)">
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
              @click="emit('deleteSkill',data.id)">
              <VIcon size="20" :icon="'tabler-trash'" />
            </VBtn>
            <VBtn icon size="30" class="rounded" :variant="'tonal'">
              <VIcon size="20" :icon="'tabler-edit'" @click="skillEdit(data)" />
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
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 14px;
}
</style>
