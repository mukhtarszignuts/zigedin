<script lang="ts" setup>

interface UserData {
  id: number;
  first_name: string;
  last_name: string;
  email:string
  city: string;
  profile_image: string;
  created_at: string; 
  role: string; 
  headline: string; 
  phone: string;
  is_active: boolean;
  image_url: string;
}

interface Props {
  userData?:UserData
}

interface Emit {
  (e: "update:isDrawerOpen", value: boolean): void;
}

const props = defineProps<Props>()
const emit = defineEmits<Emit>()

</script>

<template>
  <div>
    <VCard class="mb-4">
      <VCardText>
        <p class="text-xs">
          ABOUT
        </p>

        <VList class="card-list text-medium-emphasis">
          <VListItem>
            <template #prepend>
              <VIcon :icon="'tabler-user'" size="20" class="me-2" />
            </template>
            <VListItemTitle>
              <span class="font-weight-medium me-1"> Full Name :</span>
              <span> {{ props?.userData?.first_name }} {{ props?.userData?.last_name }}</span>
            </VListItemTitle>
          </VListItem>
          <VListItem>
            <template #prepend>
              <VIcon :icon="'tabler-check'" size="20" class="me-2" />
            </template>
            <VListItemTitle>
              <span class="font-weight-medium me-1"> Status :</span>
              <span> {{ props?.userData?.is_active===true ?'Active':'In Active' }}</span>
            </VListItemTitle>
          </VListItem>
          <VListItem>
            <template #prepend>
              <VIcon :icon="'tabler-star'" size="20" class="me-2" />
            </template>
            <VListItemTitle>
              <span class="font-weight-medium me-1"> Role :</span>
              <span> {{ props?.userData?.role==='C' ?'Candidate':props?.userData?.role==='A'?'Admin':'Employer'
                }}</span>
            </VListItemTitle>
          </VListItem>
        </VList>

        <p class="text-xs mt-5">
          CONTACTS
        </p>

        <VList class="card-list text-medium-emphasis">
          <VListItem>
            <template #prepend>
              <VIcon :icon="'tabler-phone'" size="20" class="me-2" />
            </template>
            <VListItemTitle>
              <span class="font-weight-medium me-1">Phone :</span>
              <span>{{ props?.userData?.phone }}</span>
            </VListItemTitle>
          </VListItem>
          <VListItem>
            <template #prepend>
              <VIcon :icon="'tabler-mail'" size="20" class="me-2" />
            </template>
            <VListItemTitle>
              <span class="font-weight-medium me-1">Email :</span>
              <span>{{ props?.userData?.email }}</span>
            </VListItemTitle>
          </VListItem>
        </VList>
      </VCardText>

      <VCardText class="d-flex justify-center">
        <VBtn variant="elevated" class="me-4" @click="emit('update:isDrawerOpen',true)">
          Edit
        </VBtn>
      </VCardText>
    </VCard>
   
  </div>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 16px;
}
</style>
